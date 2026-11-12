<?php

namespace App\Services;

use App\Filesystem\CloudinaryAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Đọc danh sách ảnh dự án đã lưu trên Cloudinary cho trang quản trị.
 *
 * Kết quả được cache vài phút để chuyển trang/lọc không phải gọi lại Admin API
 * (gói Free giới hạn 500 lần gọi mỗi giờ).
 */
class CloudinaryImageLibrary
{
    /**
     * Admin API trả tối đa 500 ảnh mỗi lần, nên tối đa 2.500 ảnh.
     */
    private const MAX_PAGES = 5;

    private const IMAGES_CACHE_SECONDS = 300;

    private const USAGE_CACHE_SECONDS = 600;

    /**
     * Tên tiếng Việt cho các thư mục mà website upload vào.
     *
     * @var array<string, string>
     */
    private const FOLDER_LABELS = [
        '' => 'Thư mục gốc',
        'avatars' => 'Ảnh đại diện',
        'banners' => 'Banner trang chủ',
        'courses/thumbnails' => 'Ảnh bìa khóa học',
        'courses/gallery' => 'Thư viện ảnh khóa học',
        'editor' => 'Ảnh trong bài viết',
        'feedbacks' => 'Đính kèm phản hồi',
        'lessons/attachments' => 'Đính kèm bài học',
    ];

    public function __construct(private readonly CloudinaryAdapter $cloudinary) {}

    /**
     * Trả về null khi disk "public" không dùng Cloudinary (chưa đặt CLOUDINARY_URL).
     */
    public static function fromPublicDisk(): ?self
    {
        $adapter = Storage::disk('public')->getAdapter();

        return $adapter instanceof CloudinaryAdapter ? new self($adapter) : null;
    }

    public function folder(): string
    {
        return $this->cloudinary->folder();
    }

    public static function folderLabel(string $folder): string
    {
        return self::FOLDER_LABELS[$folder] ?? $folder;
    }

    /**
     * Ảnh mới nhất trước.
     *
     * @return array{images: array<int, array<string, mixed>>, truncated: bool, fetched_at: string}
     *
     * @throws RuntimeException khi không lấy được danh sách từ Cloudinary
     */
    public function images(): array
    {
        return Cache::remember($this->cacheKey('images'), self::IMAGES_CACHE_SECONDS, fn (): array => $this->fetchImages());
    }

    /**
     * Mức dùng gói Cloudinary, null nếu Cloudinary không trả về số liệu.
     *
     * @return array{plan: ?string, credits_used: float, credits_limit: float, credits_percent: float, storage: string, bandwidth: string}|null
     */
    public function usage(): ?array
    {
        // Bọc trong mảng để cả kết quả "không có số liệu" cũng được cache, tránh gọi lại mỗi lần tải trang.
        return Cache::remember($this->cacheKey('usage'), self::USAGE_CACHE_SECONDS, function (): array {
            try {
                $usage = $this->cloudinary->usage();
            } catch (Throwable $exception) {
                report($exception);

                return ['summary' => null];
            }

            $limit = (float) data_get($usage, 'credits.limit', 0);

            if ($limit <= 0) {
                return ['summary' => null];
            }

            $used = (float) data_get($usage, 'credits.usage', 0);

            return ['summary' => [
                'plan' => data_get($usage, 'plan'),
                'credits_used' => round($used, 2),
                'credits_limit' => round($limit, 2),
                'credits_percent' => min(100, round((float) data_get($usage, 'credits.used_percent', $used / $limit * 100), 1)),
                'storage' => self::formatBytes((int) data_get($usage, 'storage.usage', 0)),
                'bandwidth' => self::formatBytes((int) data_get($usage, 'bandwidth.usage', 0)),
            ]];
        })['summary'];
    }

    /**
     * Xóa cache để lần xem sau lấy dữ liệu mới từ Cloudinary.
     */
    public function forget(): void
    {
        Cache::forget($this->cacheKey('images'));
        Cache::forget($this->cacheKey('usage'));
    }

    public static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = max(0, $bytes);
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        $formatted = $unit === 0 ? (string) $size : number_format($size, 1, ',', '.');

        return (str_ends_with($formatted, ',0') ? substr($formatted, 0, -2) : $formatted).' '.$units[$unit];
    }

    /**
     * @return array{images: array<int, array<string, mixed>>, truncated: bool, fetched_at: string}
     */
    private function fetchImages(): array
    {
        $images = [];
        $cursor = null;
        $pages = 0;

        do {
            $page = $this->cloudinary->listImages($cursor);

            foreach ($page['resources'] as $resource) {
                if (is_array($resource) && filled($resource['public_id'] ?? null)) {
                    $images[] = $this->present($resource);
                }
            }

            $cursor = $page['next_cursor'];
            $pages++;
        } while (filled($cursor) && $pages < self::MAX_PAGES);

        usort($images, fn (array $a, array $b): int => strcmp($b['created_at'], $a['created_at']));

        return [
            'images' => $images,
            'truncated' => filled($cursor),
            'fetched_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @param  array<string, mixed>  $resource
     * @return array<string, mixed>
     */
    private function present(array $resource): array
    {
        $publicId = (string) $resource['public_id'];
        $root = $this->folder();
        $relative = $root !== '' && str_starts_with($publicId, $root.'/')
            ? substr($publicId, strlen($root) + 1)
            : $publicId;
        $format = (string) ($resource['format'] ?? '');
        $path = $format === '' ? $relative : $relative.'.'.$format;
        $folder = str_contains($relative, '/') ? dirname($relative) : '';
        $url = (string) ($resource['secure_url'] ?? $resource['url'] ?? '');
        $createdAt = (string) ($resource['created_at'] ?? '');

        return [
            'public_id' => $publicId,
            'path' => $path,
            'name' => basename($path),
            'folder' => $folder,
            'folder_label' => self::folderLabel($folder),
            'format' => strtoupper($format),
            'width' => (int) ($resource['width'] ?? 0),
            'height' => (int) ($resource['height'] ?? 0),
            'bytes' => (int) ($resource['bytes'] ?? 0),
            'size' => self::formatBytes((int) ($resource['bytes'] ?? 0)),
            'created_at' => $createdAt,
            'uploaded_at' => $createdAt === ''
                ? ''
                : Carbon::parse($createdAt)->timezone(config('app.timezone'))->format('d/m/Y H:i'),
            'url' => $url,
            // Ảnh nhỏ do Cloudinary tạo sẵn để trang tải nhanh và tốn ít băng thông.
            'thumb_url' => $this->transform($url, 'c_fill,w_400,h_300/f_auto,q_auto'),
            'preview_url' => $this->transform($url, 'c_limit,w_1600,h_1600/f_auto,q_auto'),
        ];
    }

    private function transform(string $url, string $transformation): string
    {
        return preg_replace('#/image/upload/#', '/image/upload/'.$transformation.'/', $url, 1) ?? $url;
    }

    private function cacheKey(string $name): string
    {
        return 'cloudinary-library:'.$name.':'.md5($this->folder());
    }
}
