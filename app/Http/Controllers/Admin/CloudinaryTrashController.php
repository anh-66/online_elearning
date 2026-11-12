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
     * Thùng rác: ảnh đã xóa, tự xóa vĩnh viễn trên Cloudinary sau 7 ngày.
     */
    public function index(CloudinaryTrashBin $trash): View
    {
        $configured = CloudinaryImageLibrary::fromPublicDisk() !== null;
        $purged = $configured ? $trash->purgeExpired() : ['purged' => 0, 'failed' => 0];

        return view('admin.cloudinary.trash', [
            'configured' => $configured,
            'items' => CloudinaryTrash::query()->with('deleter:id,name')->orderBy('purge_at')->paginate(24),
            'purged' => $purged,
            'days' => CloudinaryTrashBin::DAYS,
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
        $purged = 0;
        $failed = 0;

        foreach (CloudinaryTrash::query()->get() as $item) {
            try {
                $trash->purge($item);
                $purged++;
            } catch (RuntimeException $exception) {
                report($exception);
                $failed++;
            }
        }

        if ($purged === 0 && $failed === 0) {
            return back()->with('info', 'Thùng rác đang trống.');
        }

        $message = "Đã xóa vĩnh viễn {$purged} ảnh trên Cloudinary.";

        return $failed > 0
            ? back()->with('error', $message." {$failed} ảnh chưa xóa được, thử lại sau.")
            : back()->with('success', $message);
    }
}
