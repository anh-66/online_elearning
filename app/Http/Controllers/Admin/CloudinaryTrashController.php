<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CloudinaryTrash;
use App\Services\CloudinaryImageLibrary;
use App\Services\CloudinaryTrashBin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CloudinaryTrashController extends Controller
{
    /**
     * Số mục tối đa trong một lần chọn nhiều (thư viện chỉ tải tối đa 2.500 ảnh từ Cloudinary).
     */
    private const MAX_SELECTION = 2500;

    /**
     * Thùng rác: ảnh đã xóa, tự xóa vĩnh viễn trên Cloudinary sau 7 ngày.
     */
    public function index(CloudinaryTrashBin $trash): View
    {
        $configured = CloudinaryImageLibrary::fromPublicDisk() !== null;
        $purged = $configured ? $trash->purgeExpired(CloudinaryTrashBin::PAGE_LOAD_SECONDS) : ['purged' => 0, 'failed' => 0, 'remaining' => 0];
        $items = CloudinaryTrash::query()->with('deleter:id,name')->orderBy('purge_at')->paginate(24);

        return view('admin.cloudinary.trash', [
            'configured' => $configured,
            'items' => $items,
            'purged' => $purged,
            'days' => CloudinaryTrashBin::DAYS,
            'selection' => [
                'pageKeys' => $items->getCollection()->map(fn (CloudinaryTrash $item): string => (string) $item->getKey())->values()->all(),
                'allKeys' => CloudinaryTrash::query()->orderBy('purge_at')->pluck('id')->map(fn ($id): string => (string) $id)->all(),
                'usedKeys' => [],
            ],
        ]);
    }

    /**
     * Xóa ảnh: tháo khỏi mọi chỗ đang dùng rồi chuyển vào thùng rác (chưa xóa trên Cloudinary).
     */
    public function store(Request $request, CloudinaryTrashBin $trash): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
        ]);

        $library = CloudinaryImageLibrary::fromPublicDisk();

        if ($library === null) {
            return back()->with('error', 'Website chưa kết nối Cloudinary.');
        }

        try {
            $image = collect($library->images()['images'])->firstWhere('path', $validated['path']);
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->with('error', 'Không lấy được danh sách ảnh từ Cloudinary: '.$exception->getMessage());
        }

        if ($image === null) {
            return back()->with('error', 'Không tìm thấy ảnh này trên Cloudinary. Bấm "Làm mới" rồi thử lại.');
        }

        $item = $trash->trash($image, $request->user());
        $unlinked = count((array) $item->places);

        $message = 'Đã chuyển ảnh vào thùng rác'
            .($unlinked > 0 ? " và tháo khỏi {$unlinked} chỗ đang dùng" : '')
            .'. Ảnh sẽ bị xóa vĩnh viễn sau '.CloudinaryTrashBin::DAYS.' ngày.';

        return back()->with('success', $message);
    }

    /**
     * Xóa nhiều ảnh đã tích chọn: tháo khỏi mọi chỗ đang dùng rồi chuyển hết vào thùng rác.
     */
    public function bulkStore(Request $request, CloudinaryTrashBin $trash): RedirectResponse
    {
        $paths = $this->selection($request, 'paths');

        if ($paths === []) {
            return back()->with('error', 'Bạn chưa chọn ảnh nào.');
        }

        $library = CloudinaryImageLibrary::fromPublicDisk();

        if ($library === null) {
            return back()->with('error', 'Website chưa kết nối Cloudinary.');
        }

        try {
            $images = collect($library->images()['images'])->keyBy('path');
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->with('error', 'Không lấy được danh sách ảnh từ Cloudinary: '.$exception->getMessage());
        }

        $inTrash = array_flip($trash->paths());
        $found = collect($paths)
            ->filter(fn (string $path): bool => $images->has($path) && ! isset($inTrash[$path]))
            ->map(fn (string $path): array => $images->get($path))
            ->values()
            ->all();

        if ($found === []) {
            return back()->with('error', 'Không tìm thấy ảnh nào đã chọn trên Cloudinary. Bấm "Làm mới" rồi thử lại.');
        }

        $result = $trash->trashMany($found, $request->user());
        $skipped = count($paths) - $result['trashed'];

        $message = "Đã chuyển {$result['trashed']} ảnh vào thùng rác"
            .($result['unlinked'] > 0 ? " và tháo khỏi {$result['unlinked']} chỗ đang dùng" : '')
            .'. Ảnh sẽ bị xóa vĩnh viễn sau '.CloudinaryTrashBin::DAYS.' ngày.'
            .($skipped > 0 ? " {$skipped} ảnh không còn trên Cloudinary hoặc đã ở trong thùng rác nên bỏ qua." : '');

        return back()->with('success', $message);
    }

    /**
     * Khôi phục ảnh và gắn lại vào những chỗ cũ còn đang trống.
     */
    public function restore(CloudinaryTrash $item, CloudinaryTrashBin $trash): RedirectResponse
    {
        $result = $trash->restore($item);

        $message = 'Đã khôi phục ảnh '.$item->name.'.';

        if ($result['relinked'] > 0) {
            $message .= " Đã gắn lại vào {$result['relinked']} chỗ cũ.";
        }

        if ($result['skipped'] > 0) {
            $message .= " {$result['skipped']} chỗ đã có ảnh khác hoặc không còn nên không gắn lại.";
        }

        return back()->with('success', $message);
    }

    /**
     * Khôi phục nhiều ảnh đã tích chọn trong thùng rác.
     */
    public function bulkRestore(Request $request, CloudinaryTrashBin $trash): RedirectResponse
    {
        $items = CloudinaryTrash::query()->whereKey($this->selection($request, 'ids'))->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Bạn chưa chọn ảnh nào, hoặc ảnh đã chọn không còn trong thùng rác.');
        }

        $relinked = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $result = $trash->restore($item);
            $relinked += $result['relinked'];
            $skipped += $result['skipped'];
        }

        $message = 'Đã khôi phục '.$items->count().' ảnh.';

        if ($relinked > 0) {
            $message .= " Đã gắn lại vào {$relinked} chỗ cũ.";
        }

        if ($skipped > 0) {
            $message .= " {$skipped} chỗ đã có ảnh khác hoặc không còn nên không gắn lại.";
        }

        return back()->with('success', $message);
    }

    /**
     * Xóa vĩnh viễn nhiều ảnh đã tích chọn (xóa luôn trên Cloudinary).
     */
    public function bulkDestroy(Request $request, CloudinaryTrashBin $trash): RedirectResponse
    {
        $items = CloudinaryTrash::query()->whereKey($this->selection($request, 'ids'))->orderBy('purge_at')->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Bạn chưa chọn ảnh nào, hoặc ảnh đã chọn không còn trong thùng rác.');
        }

        return $this->purgeResponse($trash->purgeMany($items, CloudinaryTrashBin::REQUEST_SECONDS));
    }

    /**
     * Xóa vĩnh viễn một ảnh (xóa luôn trên Cloudinary).
     */
    public function destroy(CloudinaryTrash $item, CloudinaryTrashBin $trash): RedirectResponse
    {
        try {
            $trash->purge($item);
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Đã xóa vĩnh viễn ảnh '.$item->name.' trên Cloudinary.');
    }

    /**
     * Dọn sạch thùng rác: xóa vĩnh viễn tất cả ảnh trong đó.
     */
    public function empty(CloudinaryTrashBin $trash): RedirectResponse
    {
        $items = CloudinaryTrash::query()->orderBy('purge_at')->get();

        if ($items->isEmpty()) {
            return back()->with('info', 'Thùng rác đang trống.');
        }

        return $this->purgeResponse($trash->purgeMany($items, CloudinaryTrashBin::REQUEST_SECONDS));
    }

    /**
     * Báo kết quả xóa vĩnh viễn cho admin.
     *
     * @param  array{purged: int, failed: int, remaining: int}  $result
     */
    private function purgeResponse(array $result): RedirectResponse
    {
        $message = "Đã xóa vĩnh viễn {$result['purged']} ảnh trên Cloudinary.";

        if ($result['remaining'] > 0) {
            $message .= " Còn {$result['remaining']} ảnh chưa kịp xóa vì Cloudinary xóa từng ảnh một, bấm xóa lần nữa để xóa tiếp.";
        }

        if ($result['failed'] > 0) {
            return back()->with('error', $message." {$result['failed']} ảnh chưa xóa được, thử lại sau.");
        }

        return back()->with($result['remaining'] > 0 ? 'info' : 'success', $message);
    }

    /**
     * Đọc danh sách đã tích chọn. Trang gửi lên một chuỗi JSON (thay vì nhiều ô input) để chọn
     * hàng nghìn ảnh cũng không vướng giới hạn max_input_vars của PHP.
     *
     * @return array<int, string>
     */
    private function selection(Request $request, string $field): array
    {
        $values = json_decode((string) $request->input($field, ''), true);

        if (! is_array($values)) {
            return [];
        }

        return collect($values)
            ->filter(fn ($value): bool => is_string($value) || is_int($value))
            ->map(fn ($value): string => trim((string) $value))
            ->filter(fn (string $value): bool => $value !== '' && mb_strlen($value) <= 500)
            ->unique()
            ->take(self::MAX_SELECTION)
            ->values()
            ->all();
    }
}
