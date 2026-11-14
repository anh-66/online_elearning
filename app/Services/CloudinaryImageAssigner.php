<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Course;
use App\Models\CourseImage;
use App\Models\Feedback;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Cho admin biết ảnh nào trên Cloudinary đang được dùng ở đâu, và gắn lại một ảnh có sẵn
 * vào banner, ảnh bìa khóa học hoặc avatar (chỉ sửa database, không upload hay xóa file).
 */
class CloudinaryImageAssigner
{
    /**
     * Những chỗ có thể gắn ảnh: loại => [tên hiển thị, model, cột lưu đường dẫn].
     *
     * @var array<string, array{label: string, model: class-string<Model>, column: string}>
     */
    public const TYPES = [
        'banner' => ['label' => 'Banner trang chủ', 'model' => Banner::class, 'column' => 'image_path'],
        'course' => ['label' => 'Ảnh bìa khóa học', 'model' => Course::class, 'column' => 'thumbnail'],
        'user' => ['label' => 'Ảnh đại diện', 'model' => User::class, 'column' => 'avatar'],
    ];

    /**
     * Thư mục mà website biết chắc ảnh trong đó được lưu ở cột nào trong database.
     * Ảnh trong bài viết (editor) nằm trong nội dung HTML nên không xét.
     *
     * @var array<int, string>
     */
    public const TRACKED_FOLDERS = ['avatars', 'banners', 'courses/thumbnails', 'courses/gallery', 'feedbacks', 'lessons/attachments'];

    /**
     * Loại gợi ý sẵn theo thư mục của ảnh.
     *
     * @var array<string, string>
     */
    public const FOLDER_DEFAULT_TYPES = [
        'banners' => 'banner',
        'courses/thumbnails' => 'course',
        'courses/gallery' => 'course',
        'avatars' => 'user',
    ];

    /**
     * Những chỗ có thể thay ảnh đang dùng bằng ảnh khác: loại => thư mục gợi ý khi chọn ảnh mới.
     * File đính kèm bài học / phản hồi không nằm trong đây vì chúng không chỉ là ảnh.
     *
     * @var array<string, string>
     */
    public const REPLACE_FOLDERS = [
        'banner' => 'banners',
        'course' => 'courses/thumbnails',
        'course_image' => 'courses/gallery',
        'user' => 'avatars',
    ];

    /**
     * Bỏ đuôi file để so khớp: Cloudinary có thể trả định dạng khác đuôi lưu trong database (jpeg/jpg).
     */
    public static function key(string $path): string
    {
        return (string) preg_replace('/\.[^.\/]+$/', '', ltrim($path, '/'));
    }

    /**
     * Những chỗ trong database có lưu đường dẫn ảnh: loại => [model, cột, cột tên, tiền tố hiển thị].
     * Ảnh thư viện khóa học (course_image) là cả một dòng riêng nên tháo ảnh = xóa dòng đó.
     *
     * @var array<string, array{model: class-string<Model>, column: string, name: ?string, prefix: string}>
     */
    public const PLACES = [
        'banner' => ['model' => Banner::class, 'column' => 'image_path', 'name' => 'title', 'prefix' => 'Banner'],
        'course' => ['model' => Course::class, 'column' => 'thumbnail', 'name' => 'title', 'prefix' => 'Ảnh bìa'],
        'course_image' => ['model' => CourseImage::class, 'column' => 'image_path', 'name' => null, 'prefix' => 'Thư viện ảnh'],
        'user' => ['model' => User::class, 'column' => 'avatar', 'name' => 'name', 'prefix' => 'Avatar'],
        'lesson' => ['model' => Lesson::class, 'column' => 'attachment_path', 'name' => 'title', 'prefix' => 'Đính kèm bài học'],
        'feedback' => ['model' => Feedback::class, 'column' => 'attachment', 'name' => 'title', 'prefix' => 'Đính kèm phản hồi'],
    ];

    /**
     * Đường dẫn (đã bỏ đuôi) => danh sách chỗ đang dùng ảnh đó (tên hiển thị).
     *
     * @return array<string, array<int, string>>
     */
    public function usage(): array
    {
        return array_map(fn (array $places): array => array_column($places, 'label'), $this->places());
    }

    /**
     * Đường dẫn (đã bỏ đuôi) => danh sách chỗ đang dùng ảnh đó, kèm loại và id để tháo hoặc gắn lại.
     *
     * @return array<string, array<int, array{type: string, id: int, label: string, course_id?: int}>>
     */
    public function places(): array
    {
        $places = [];

        foreach (self::PLACES as $type => $config) {
            $query = $config['model']::query()->whereNotNull($config['column']);

            if ($type === 'course_image') {
                $query->with('course:id,title');
            }

            foreach ($query->get() as $record) {
                $path = $record->getAttribute($config['column']);

                if (! is_string($path) || $path === '' || preg_match('#^https?://#i', $path)) {
                    continue;
                }

                $places[self::key($path)][] = $this->describe($type, $record);
            }
        }

        return $places;
    }

    /**
     * Tháo ảnh khỏi một chỗ (để trống), ảnh vẫn giữ trên Cloudinary.
     * Trả về null nếu chỗ đó không còn dùng ảnh này nữa.
     *
     * @return array{type: string, id: int, label: string, course_id?: int}|null
     *
     * @throws InvalidArgumentException khi loại không hợp lệ
     */
    public function unlink(string $type, int $id, string $path): ?array
    {
        $config = self::PLACES[$type] ?? throw new InvalidArgumentException('Loại không hợp lệ: '.$type);
        $record = $config['model']::query()->find($id);

        if ($record === null || ! is_string($record->getAttribute($config['column']))
            || self::key($record->getAttribute($config['column'])) !== self::key($path)) {
            return null;
        }

        $place = $this->describe($type, $record);

        if ($type === 'course_image') {
            $record->delete();
        } else {
            $record->forceFill([$config['column'] => null])->save();
        }

        return $place;
    }

    /**
     * Tháo ảnh khỏi mọi chỗ đang dùng. Trả về danh sách chỗ đã tháo để sau này khôi phục.
     * Khi xóa nhiều ảnh cùng lúc, truyền sẵn kết quả places() để không phải đọc lại database cho từng ảnh.
     *
     * @param  array<string, array<int, array{type: string, id: int, label: string, course_id?: int}>>|null  $places
     * @return array<int, array{type: string, id: int, label: string, course_id?: int}>
     */
    public function unlinkAll(string $path, ?array $places = null): array
    {
        $unlinked = [];

        foreach (($places ?? $this->places())[self::key($path)] ?? [] as $place) {
            if ($done = $this->unlink($place['type'], $place['id'], $path)) {
                $unlinked[] = $done;
            }
        }

        return $unlinked;
    }

    /**
     * Thay ảnh đang dùng ở một chỗ bằng ảnh khác (chỉ sửa database, không upload hay xóa file).
     * Trả về null nếu chỗ đó không còn dùng ảnh cũ nữa (vd: admin khác vừa đổi).
     *
     * @return array{type: string, id: int, label: string, course_id?: int}|null
     *
     * @throws InvalidArgumentException khi loại không thay được ảnh
     */
    public function replace(string $type, int $id, string $oldPath, string $newPath): ?array
    {
        if (! array_key_exists($type, self::REPLACE_FOLDERS)) {
            throw new InvalidArgumentException('Không thay được ảnh cho loại: '.$type);
        }

        $config = self::PLACES[$type];
        $record = $config['model']::query()->find($id);
        $current = $record?->getAttribute($config['column']);

        if (! is_string($current) || self::key($current) !== self::key($oldPath)) {
            return null;
        }

        $record->forceFill([$config['column'] => ltrim($newPath, '/')])->save();

        return $this->describe($type, $record);
    }

    /**
     * Gắn lại ảnh vào chỗ cũ khi khôi phục từ thùng rác. Không ghi đè nếu chỗ đó đã có ảnh khác.
     *
     * @param  array{type?: string, id?: int, course_id?: int}  $place
     */
    public function relink(array $place, string $path): bool
    {
        $type = (string) ($place['type'] ?? '');
        $config = self::PLACES[$type] ?? null;

        if ($config === null) {
            return false;
        }

        if ($type === 'course_image') {
            $courseId = (int) ($place['course_id'] ?? 0);

            if (! Course::query()->whereKey($courseId)->exists()) {
                return false;
            }

            CourseImage::query()->firstOrCreate(['course_id' => $courseId, 'image_path' => ltrim($path, '/')]);

            return true;
        }

        $record = $config['model']::query()->find((int) ($place['id'] ?? 0));

        if ($record === null || filled($record->getAttribute($config['column']))) {
            return false;
        }

        $record->forceFill([$config['column'] => ltrim($path, '/')])->save();

        return true;
    }

    /**
     * @return array{type: string, id: int, label: string, course_id?: int}
     */
    private function describe(string $type, Model $record): array
    {
        $config = self::PLACES[$type];

        if ($type === 'course_image') {
            /** @var CourseImage $record */
            return [
                'type' => $type,
                'id' => (int) $record->getKey(),
                'label' => $config['prefix'].': '.($record->course?->title ?? 'khóa học #'.$record->course_id),
                'course_id' => (int) $record->course_id,
            ];
        }

        return [
            'type' => $type,
            'id' => (int) $record->getKey(),
            'label' => $config['prefix'].': '.$record->getAttribute($config['name']),
        ];
    }

    /**
     * Danh sách banner/khóa học/người dùng để chọn khi gắn ảnh. Chỗ đang trống ảnh
     * hoặc trỏ tới file không còn trên Cloudinary được xếp lên đầu.
     *
     * @param  array<string, string>  $thumbs  đường dẫn ảnh đang có trên Cloudinary => link ảnh nhỏ của nó
     * @return array<string, array{label: string, items: array<int, array{id: int, label: string, status: string, path: ?string, thumb_url: ?string}>}>
     */
    public function targets(array $thumbs): array
    {
        $existing = [];

        foreach ($thumbs as $path => $thumb) {
            $existing[self::key((string) $path)] = $thumb;
        }

        // Ảnh nhỏ của ảnh hiện tại (null nếu trống hoặc file không còn trên Cloudinary).
        $thumbOf = function (?string $path) use ($existing): ?string {
            if (! is_string($path) || trim($path) === '') {
                return null;
            }

            return preg_match('#^https?://#i', $path) ? $path : ($existing[self::key($path)] ?? null);
        };

        $status = function (?string $path) use ($existing): string {
            if (! is_string($path) || trim($path) === '') {
                return 'empty';
            }

            if (preg_match('#^https?://#i', $path)) {
                return 'ok';
            }

            return isset($existing[self::key($path)]) ? 'ok' : 'broken';
        };

        $build = fn ($records, callable $label, string $pathColumn): array => $records
            ->map(fn (Model $record): array => [
                'id' => (int) $record->getKey(),
                'label' => (string) $label($record),
                'status' => $status($record->getAttribute($pathColumn)),
                // Ảnh hiện tại, để admin thấy mình sắp thay ảnh nào.
                'path' => filled($record->getAttribute($pathColumn)) ? (string) $record->getAttribute($pathColumn) : null,
                'thumb_url' => $thumbOf($record->getAttribute($pathColumn)),
            ])
            ->sortBy(fn (array $item): string => ($item['status'] === 'ok' ? '1' : '0').mb_strtolower($item['label']))
            ->values()
            ->all();

        return [
            'banner' => [
                'label' => self::TYPES['banner']['label'],
                'items' => $build(Banner::query()->get(['id', 'title', 'image_path']), fn (Banner $banner) => $banner->title, 'image_path'),
            ],
            'course' => [
                'label' => self::TYPES['course']['label'],
                'items' => $build(Course::query()->get(['id', 'title', 'thumbnail']), fn (Course $course) => $course->title, 'thumbnail'),
            ],
            'user' => [
                'label' => self::TYPES['user']['label'],
                'items' => $build(User::query()->get(['id', 'name', 'email', 'avatar']), fn (User $user) => $user->name.' ('.$user->email.')', 'avatar'),
            ],
        ];
    }

    /**
     * Gắn ảnh vào chỗ đã chọn, ghi đè ảnh cũ nếu chỗ đó đang có ảnh.
     * Trả về tên chỗ đó để báo cho admin, và đường dẫn ảnh cũ (nếu có) để xử lý tiếp.
     *
     * @return array{name: string, previous: ?string}
     *
     * @throws InvalidArgumentException khi loại không hợp lệ
     */
    public function assign(string $type, int $id, string $path): array
    {
        $config = self::TYPES[$type] ?? throw new InvalidArgumentException('Loại không hợp lệ: '.$type);

        /** @var Model $record */
        $record = $config['model']::query()->findOrFail($id);
        $previous = $record->getAttribute($config['column']);
        $record->forceFill([$config['column'] => ltrim($path, '/')])->save();

        $name = match ($type) {
            'banner' => 'banner “'.$record->getAttribute('title').'”',
            'course' => 'ảnh bìa khóa học “'.$record->getAttribute('title').'”',
            'user' => 'ảnh đại diện của '.$record->getAttribute('name'),
        };

        return ['name' => $name, 'previous' => is_string($previous) && trim($previous) !== '' ? $previous : null];
    }
}
