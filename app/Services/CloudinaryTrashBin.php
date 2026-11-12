<?php

namespace App\Services;

use App\Filesystem\CloudinaryAdapter;
use App\Models\CloudinaryTrash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Thùng rác cho ảnh Cloudinary: xóa ảnh trong admin thì ảnh được tháo khỏi mọi chỗ đang dùng
 * và giữ lại DAYS ngày. Hết hạn (hoặc admin bấm "Xóa vĩnh viễn") mới xóa thật trên Cloudinary.
 */
class CloudinaryTrashBin
{
    public const DAYS = 7;

    public function __construct(private readonly CloudinaryImageAssigner $assigner) {}

    /**
     * Chuyển ảnh vào thùng rác.
     *
     * @param  array<string, mixed>  $image  một ảnh trong CloudinaryImageLibrary::images()
     */
    public function trash(array $image, ?User $by = null): CloudinaryTrash
    {
        $places = $this->assigner->unlinkAll($image['path']);

        return CloudinaryTrash::query()->updateOrCreate(['path' => $image['path']], [
            'name' => $image['name'],
            'folder' => $image['folder'],
            'format' => $image['format'],
            'width' => $image['width'],
            'height' => $image['height'],
            'bytes' => $image['bytes'],
            'url' => $image['url'],
            'thumb_url' => $image['thumb_url'],
            'places' => $places,
            'deleted_by' => $by?->getKey(),
            'purge_at' => now()->addDays(self::DAYS),
        ]);
    }

    /**
     * Lấy ảnh ra khỏi thùng rác và gắn lại vào những chỗ cũ còn đang trống.
     *
     * @return array{relinked: int, skipped: int}
     */
    public function restore(CloudinaryTrash $item): array
    {
        $relinked = 0;
        $skipped = 0;

        foreach ((array) $item->places as $place) {
            $this->assigner->relink((array) $place, $item->path) ? $relinked++ : $skipped++;
        }

        $item->delete();

        return ['relinked' => $relinked, 'skipped' => $skipped];
    }

    /**
     * Xóa vĩnh viễn: xóa ảnh trên Cloudinary rồi bỏ khỏi thùng rác.
     *
     * @throws RuntimeException khi chưa kết nối Cloudinary hoặc Cloudinary báo lỗi
     */
    public function purge(CloudinaryTrash $item): void
    {
        $adapter = Storage::disk('public')->getAdapter();

        if (! $adapter instanceof CloudinaryAdapter) {
            throw new RuntimeException('Website chưa kết nối Cloudinary nên chưa xóa được ảnh.');
        }

        try {
            // Cloudinary trả "not found" (vẫn là HTTP 200) nếu ảnh đã bị xóa từ trước, coi như xóa xong.
            $adapter->delete($item->path);
        } catch (Throwable $exception) {
            throw new RuntimeException('Cloudinary chưa xóa được ảnh '.$item->name.': '.$exception->getMessage(), 0, $exception);
        }

        $item->delete();
        CloudinaryImageLibrary::fromPublicDisk()?->forget();
    }

    /**
     * Xóa vĩnh viễn những ảnh đã nằm trong thùng rác quá DAYS ngày.
     * Render gói free không có cron miễn phí, nên hàm này được gọi khi admin mở trang
     * Ảnh Cloudinary và mỗi lần server khởi động (lệnh cloudinary:purge-trash).
     *
     * @return array{purged: int, failed: int}
     */
    public function purgeExpired(): array
    {
        $purged = 0;
        $failed = 0;

        if (! Storage::disk('public')->getAdapter() instanceof CloudinaryAdapter) {
            return ['purged' => 0, 'failed' => 0];
        }

        foreach (CloudinaryTrash::query()->where('purge_at', '<=', now())->get() as $item) {
            try {
                $this->purge($item);
                $purged++;
            } catch (RuntimeException $exception) {
                report($exception);
                $failed++;
            }
        }

        return ['purged' => $purged, 'failed' => $failed];
    }

    /**
     * Đường dẫn các ảnh đang trong thùng rác (để ẩn khỏi thư viện ảnh).
     *
     * @return array<int, string>
     */
    public function paths(): array
    {
        return CloudinaryTrash::query()->pluck('path')->all();
    }
}
