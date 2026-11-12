<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryImageAssigner;
use App\Services\CloudinaryImageLibrary;
use App\Services\CloudinaryTrashBin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class CloudinaryController extends Controller
{
    private const PER_PAGE = 24;

    /**
     * Thư viện ảnh mà website đã upload lên Cloudinary.
     */
    public function index(Request $request, CloudinaryImageAssigner $assigner, CloudinaryTrashBin $trash): View|RedirectResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'folder' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', Rule::in(['used', 'unused'])],
        ]);

        $keyword = trim((string) ($validated['q'] ?? ''));
        $folder = $validated['folder'] ?? null;
        $status = $validated['status'] ?? null;
        $library = CloudinaryImageLibrary::fromPublicDisk();

        if ($library === null) {
            return view('admin.cloudinary.index', ['configured' => false]);
        }

        if ($request->boolean('refresh')) {
            $library->forget();

            return redirect()
                ->route('admin.cloudinary.index', array_filter(['q' => $keyword, 'folder' => $folder, 'status' => $status], 'filled'))
                ->with('success', 'Đã tải lại danh sách ảnh mới nhất từ Cloudinary.');
        }

        // Render free không có cron: dọn ảnh hết hạn trong thùng rác mỗi khi admin mở trang này.
        $trash->purgeExpired();
        $trashed = array_flip($trash->paths());

        try {
            $result = $library->images();
        } catch (RuntimeException $exception) {
            report($exception);

            return view('admin.cloudinary.index', [
                'configured' => true,
                'rootFolder' => $library->folder(),
                'error' => $exception->getMessage(),
            ]);
        }

        // Ghi chú ảnh nào đang được dùng ở đâu, để admin tìm ra ảnh bị mất liên kết.
        // Ảnh trong thùng rác được ẩn khỏi thư viện.
        $places = $assigner->places();
        $all = collect($result['images'])
            ->reject(fn (array $image): bool => isset($trashed[$image['path']]))
            ->map(function (array $image) use ($places): array {
                $image['tracked'] = in_array($image['folder'], CloudinaryImageAssigner::TRACKED_FOLDERS, true);
                $image['places'] = $places[CloudinaryImageAssigner::key($image['path'])] ?? [];
                $image['used_by'] = array_column($image['places'], 'label');

                return $image;
            })
            ->values();
        $unused = $all->filter(fn (array $image): bool => $image['tracked'] && $image['used_by'] === []);

        $filtered = $all
            ->when($folder !== null && $folder !== '', fn ($images) => $images->where('folder', $folder === '/' ? '' : $folder))
            ->when($status === 'unused', fn ($images) => $images->filter(fn (array $image): bool => $image['tracked'] && $image['used_by'] === []))
            ->when($status === 'used', fn ($images) => $images->filter(fn (array $image): bool => $image['used_by'] !== []))
            ->when($keyword !== '', fn ($images) => $images->filter(
                fn (array $image): bool => str_contains(mb_strtolower($image['path']), mb_strtolower($keyword)),
            ))
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $images = new LengthAwarePaginator(
            $filtered->forPage($page, self::PER_PAGE)->values(),
            $filtered->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        $folders = $all->countBy('folder')
            ->sortKeys()
            ->map(fn (int $count, string $name): array => [
                'value' => $name === '' ? '/' : $name,
                'label' => CloudinaryImageLibrary::folderLabel($name),
                'count' => $count,
            ])
            ->values();

        return view('admin.cloudinary.index', [
            'configured' => true,
            'rootFolder' => $library->folder(),
            'images' => $images,
            'folders' => $folders,
            'keyword' => $keyword,
            'folder' => $folder,
            'status' => $status,
            'unusedCount' => $unused->count(),
            'targets' => $assigner->targets($all->pluck('path')->all()),
            'folderDefaultTypes' => CloudinaryImageAssigner::FOLDER_DEFAULT_TYPES,
            'trashCount' => count($trashed),
            'trashDays' => CloudinaryTrashBin::DAYS,
            'truncated' => $result['truncated'],
            'fetchedAt' => Carbon::parse($result['fetched_at'])->timezone(config('app.timezone')),
            'stats' => [
                'total' => $all->count(),
                'size' => CloudinaryImageLibrary::formatBytes((int) $all->sum('bytes')),
                'folders' => $folders->count(),
                'latest' => $all->first()['uploaded_at'] ?? null,
            ],
            'usage' => $library->usage(),
        ]);
    }

    /**
     * Gắn một ảnh có sẵn trên Cloudinary vào banner, ảnh bìa khóa học hoặc avatar.
     * Chỉ sửa đường dẫn trong database, không upload hay xóa file nào trên Cloudinary.
     */
    public function assign(Request $request, CloudinaryImageAssigner $assigner): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'type' => ['required', Rule::in(array_keys(CloudinaryImageAssigner::TYPES))],
            'target_id' => ['required', 'integer', 'min:1'],
        ], [
            'target_id.required' => 'Hãy chọn nơi muốn gắn ảnh.',
        ]);

        $library = CloudinaryImageLibrary::fromPublicDisk();

        if ($library === null) {
            return back()->with('error', 'Website chưa kết nối Cloudinary.');
        }

        try {
            $images = collect($library->images()['images']);
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->with('error', 'Không lấy được danh sách ảnh từ Cloudinary: '.$exception->getMessage());
        }

        // Chỉ cho gắn ảnh thật sự có trên Cloudinary, tránh lưu đường dẫn tới file không tồn tại.
        if (! $images->contains('path', $validated['path'])) {
            return back()->with('error', 'Không tìm thấy ảnh này trên Cloudinary. Bấm "Làm mới" rồi thử lại.');
        }

        try {
            $message = $assigner->assign($validated['type'], (int) $validated['target_id'], $validated['path']);
        } catch (ModelNotFoundException) {
            return back()->with('error', 'Không tìm thấy nơi cần gắn ảnh, có thể nó vừa bị xóa.');
        }

        return back()->with('success', $message);
    }

    /**
     * Tháo ảnh khỏi một chỗ đang dùng (chỗ đó thành trống ảnh), ảnh vẫn giữ trên Cloudinary.
     */
    public function unlink(Request $request, CloudinaryImageAssigner $assigner): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'type' => ['required', Rule::in(array_keys(CloudinaryImageAssigner::PLACES))],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $place = $assigner->unlink($validated['type'], (int) $validated['id'], $validated['path']);

        if ($place === null) {
            return back()->with('error', 'Chỗ này không còn dùng ảnh đó nữa. Bấm "Làm mới" để xem lại.');
        }

        return back()->with('success', 'Đã tháo ảnh khỏi '.$place['label'].'. Ảnh vẫn còn trên Cloudinary.');
    }
}
