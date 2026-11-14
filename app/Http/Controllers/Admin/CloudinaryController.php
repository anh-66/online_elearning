<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryImageAssigner;
use App\Services\CloudinaryImageLibrary;
use App\Services\CloudinaryTrashBin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

        // Không cần cron: dọn ảnh hết hạn trong thùng rác mỗi khi admin mở trang này.
        $trash->purgeExpired(CloudinaryTrashBin::PAGE_LOAD_SECONDS);
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

        // Dữ liệu cho ô tích chọn nhiều ảnh: ảnh trên trang này, mọi ảnh khớp bộ lọc (để "chọn tất cả"
        // cả các trang khác) và những ảnh đang được dùng (để cảnh báo trước khi xóa).
        $selection = [
            'pageKeys' => $images->pluck('path')->values()->all(),
            'allKeys' => $filtered->pluck('path')->values()->all(),
            'usedKeys' => $filtered->filter(fn (array $image): bool => $image['places'] !== [])->pluck('path')->values()->all(),
        ];

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
            'targets' => $assigner->targets($all->pluck('thumb_url', 'path')->all()),
            'folderDefaultTypes' => CloudinaryImageAssigner::FOLDER_DEFAULT_TYPES,
            'replaceFolders' => CloudinaryImageAssigner::REPLACE_FOLDERS,
            'selection' => $selection,
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
     * Danh sách gọn mọi ảnh (không chỉ trang đang xem) để chọn ảnh thay thế.
     * Chỉ tải khi admin bấm "Thay thế", để trang thư viện không phải nhúng sẵn hàng nghìn ảnh.
     */
    public function picker(CloudinaryTrashBin $trash): JsonResponse
    {
        $images = $this->availableImages($trash);

        if ($images instanceof RedirectResponse) {
            return response()->json(['message' => session()->pull('error', 'Không lấy được danh sách ảnh.')], 503);
        }

        return response()->json([
            'images' => $images->map(fn (array $image): array => [
                'path' => $image['path'],
                'folder' => $image['folder'],
                'thumb_url' => $image['thumb_url'],
            ])->values()->all(),
        ]);
    }

    /**
     * Gắn một ảnh có sẵn trên Cloudinary vào banner, ảnh bìa khóa học hoặc avatar.
     * Nếu mục đó đang có ảnh thì ảnh mới thay ảnh cũ. Chỉ sửa đường dẫn trong database;
     * ảnh cũ chỉ bị chuyển vào thùng rác khi admin chọn và nó không còn dùng ở đâu.
     */
    public function assign(Request $request, CloudinaryImageAssigner $assigner, CloudinaryTrashBin $trash): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'type' => ['required', Rule::in(array_keys(CloudinaryImageAssigner::TYPES))],
            'target_id' => ['required', 'integer', 'min:1'],
            'trash_old' => ['nullable', 'boolean'],
        ], [
            'target_id.required' => 'Hãy chọn nơi muốn gắn ảnh.',
        ]);

        $images = $this->availableImages($trash);

        if ($images instanceof RedirectResponse) {
            return $images;
        }

        // Chỉ cho gắn ảnh thật sự có trên Cloudinary, tránh lưu đường dẫn tới file không tồn tại.
        if (! $images->contains('path', $validated['path'])) {
            return back()->with('error', 'Không tìm thấy ảnh này trên Cloudinary (hoặc ảnh đang trong thùng rác). Bấm "Làm mới" rồi thử lại.');
        }

        try {
            $result = $assigner->assign($validated['type'], (int) $validated['target_id'], $validated['path']);
        } catch (ModelNotFoundException) {
            return back()->with('error', 'Không tìm thấy nơi cần gắn ảnh, có thể nó vừa bị xóa.');
        }

        // "Thay" khi mục đó đang có ảnh chạy được (ảnh trên Cloudinary hoặc link ngoài); ảnh lỗi thì coi như gắn mới.
        $previous = $result['previous'];
        $replaced = $previous !== null
            && CloudinaryImageAssigner::key($previous) !== CloudinaryImageAssigner::key($validated['path'])
            && (preg_match('#^https?://#i', $previous) === 1 || $this->findImage($images, $previous) !== null);

        if (! $replaced) {
            return back()->with('success', 'Đã gắn ảnh cho '.$result['name'].'.');
        }

        return back()->with('success', 'Đã thay ảnh cho '.$result['name'].'.'
            .$this->handleOldImage($previous, $request->boolean('trash_old'), $images, $assigner, $trash, $request));
    }

    /**
     * Thay ảnh đang dùng ở một chỗ (banner, ảnh bìa, thư viện ảnh khóa học, avatar) bằng ảnh khác có sẵn trên Cloudinary.
     */
    public function replace(Request $request, CloudinaryImageAssigner $assigner, CloudinaryTrashBin $trash): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'type' => ['required', Rule::in(array_keys(CloudinaryImageAssigner::REPLACE_FOLDERS))],
            'id' => ['required', 'integer', 'min:1'],
            'new_path' => ['required', 'string', 'max:500'],
            'trash_old' => ['nullable', 'boolean'],
        ], [
            'new_path.required' => 'Hãy chọn ảnh mới để thay.',
        ]);

        if (CloudinaryImageAssigner::key($validated['new_path']) === CloudinaryImageAssigner::key($validated['path'])) {
            return back()->with('error', 'Ảnh mới trùng với ảnh đang dùng, hãy chọn ảnh khác.');
        }

        $images = $this->availableImages($trash);

        if ($images instanceof RedirectResponse) {
            return $images;
        }

        if (! $images->contains('path', $validated['new_path'])) {
            return back()->with('error', 'Không tìm thấy ảnh mới trên Cloudinary (hoặc ảnh đang trong thùng rác). Bấm "Làm mới" rồi thử lại.');
        }

        $place = $assigner->replace($validated['type'], (int) $validated['id'], $validated['path'], $validated['new_path']);

        if ($place === null) {
            return back()->with('error', 'Chỗ này không còn dùng ảnh đó nữa. Bấm "Làm mới" để xem lại.');
        }

        return back()->with('success', 'Đã thay ảnh cho '.$place['label'].'.'
            .$this->handleOldImage($validated['path'], $request->boolean('trash_old'), $images, $assigner, $trash, $request));
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

    /**
     * Ảnh đang có trên Cloudinary, trừ ảnh trong thùng rác (sắp bị xóa thật nên không cho gắn).
     *
     * @return Collection<int, array<string, mixed>>|RedirectResponse
     */
    private function availableImages(CloudinaryTrashBin $trash): Collection|RedirectResponse
    {
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

        $trashed = array_flip($trash->paths());

        return $images->reject(fn (array $image): bool => isset($trashed[$image['path']]))->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $images
     * @return array<string, mixed>|null
     */
    private function findImage(Collection $images, string $path): ?array
    {
        $key = CloudinaryImageAssigner::key($path);

        return $images->first(fn (array $image): bool => CloudinaryImageAssigner::key($image['path']) === $key);
    }

    /**
     * Sau khi thay ảnh: giữ ảnh cũ, hoặc chuyển vào thùng rác nếu admin chọn và ảnh không còn dùng ở đâu.
     * Trả về câu ghi chú để nối vào thông báo.
     *
     * @param  Collection<int, array<string, mixed>>  $images
     */
    private function handleOldImage(string $oldPath, bool $trashOld, Collection $images, CloudinaryImageAssigner $assigner, CloudinaryTrashBin $trash, Request $request): string
    {
        $old = $this->findImage($images, $oldPath);

        if ($old === null) {
            return '';
        }

        if (! $trashOld) {
            return ' Ảnh cũ vẫn còn trên Cloudinary.';
        }

        // Ảnh trong bài viết nằm trong nội dung HTML, website không biết bài nào đang dùng nên không xóa.
        if (! in_array($old['folder'], CloudinaryImageAssigner::TRACKED_FOLDERS, true)) {
            return ' Ảnh cũ được giữ lại vì có thể đang nằm trong nội dung bài viết.';
        }

        if (($assigner->places()[CloudinaryImageAssigner::key($old['path'])] ?? []) !== []) {
            return ' Ảnh cũ vẫn đang dùng ở chỗ khác nên được giữ lại.';
        }

        $trash->trash($old, $request->user());

        return ' Ảnh cũ không còn dùng ở đâu nên đã vào thùng rác, khôi phục được trong '.CloudinaryTrashBin::DAYS.' ngày.';
    }
}
