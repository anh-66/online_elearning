<?php

namespace App\Services;

use App\Filesystem\CloudinaryAdapter;
use App\Models\CloudinaryTrash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

    /**
     * Số giây tối đa dành cho việc xóa vĩnh viễn trong một lần bấm. PHP trên Render dừng request sau
     * 60 giây (docker/php/app.ini), mà Cloudinary xóa từng ảnh một, nên xóa nhiều ảnh thì chia làm nhiều lượt.
     */
    public const REQUEST_SECONDS = 40;

    /**
     * Thời gian dành cho việc tự dọn ảnh hết hạn khi admin mở trang, để trang không bị chậm quá lâu.
     */
    public const PAGE_LOAD_SECONDS = 15;

    public function __construct(private readonly CloudinaryImageAssigner $assigner) {}

    /**
     * Chuyển ảnh vào thùng rác.
     *
     * @param  array<string, mixed>  $image  một ảnh trong CloudinaryImageLibrary::images()
     * @param  array<string, array<int, array<string, mixed>>>|null  $knownPlaces  kết quả CloudinaryImageAssigner::places() nếu đã có sẵn
     */
    public function trash(array $image, ?User $by = null, ?array $knownPlaces = null): CloudinaryTrash
    {
        $places = $this->assigner->unlinkAll($image['path'], $knownPlaces);

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
     * Chuyển nhiều ảnh vào thùng rác cùng lúc (chỉ sửa database nên làm hết trong một transaction).
     *
     * @param  array<int, array<string, mixed>>  $images  các ảnh trong CloudinaryImageLibrary::images()
     * @return array{trashed: int, unlinked: int}
     */
    public function trashMany(array $images, ?User $by = null): array
    {
        return DB::transaction(function () use ($images, $by): array {
            $places = $this->assigner->places();
            $trashed = 0;
            $unlinked = 0;

            foreach ($images as $image) {
                $item = $this->trash($image, $by, $places);
                $trashed++;
                $unlinked += count((array) $item->places);
            }

            return ['trashed' => $trashed, 'unlinked' => $unlinked];
        });
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
     * Xóa vĩnh viễn nhiều ảnh. Hết $seconds giây thì dừng, những ảnh chưa kịp xóa vẫn nằm trong thùng rác
     * (đếm vào "remaining") để lần sau xóa tiếp. $seconds = null là không giới hạn (dùng cho lệnh artisan).
     *
     * @param  iterable<int, CloudinaryTrash>  $items
     * @return array{purged: int, failed: int, remaining: int}
     */
    public function purgeMany(iterable $items, ?float $seconds = null): array
    {
        $deadline = $seconds === null ? null : microtime(true) + $seconds;
        $purged = 0;
        $failed = 0;
        $remaining = 0;

        foreach ($items as $item) {
            if ($deadline !== null && microtime(true) >= $deadline) {
                $remaining++;

                continue;
            }

            try {
                $this->purge($item);
                $purged++;
            } catch (RuntimeException $exception) {
                report($exception);
                $failed++;
            }
        }

        return ['purged' => $purged, 'failed' => $failed, 'remaining' => $remaining];
    }

    /**
     * Xóa vĩnh viễn những ảnh đã nằm trong thùng rác quá DAYS ngày.
     * Render gói free không có cron miễn phí, nên hàm này được gọi khi admin mở trang
     * Ảnh Cloudinary và mỗi lần server khởi động (lệnh cloudinary:purge-trash).
     *
     * @return array{purged: int, failed: int, remaining: int}
     */
    public function purgeExpired(?float $seconds = null): array
    {
        if (! Storage::disk('public')->getAdapter() instanceof CloudinaryAdapter) {
            return ['purged' => 0, 'failed' => 0, 'remaining' => 0];
        }

        return $this->purgeMany(CloudinaryTrash::query()->where('purge_at', '<=', now())->orderBy('purge_at')->get(), $seconds);
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
