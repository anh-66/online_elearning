<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryImageLibrary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use RuntimeException;

class CloudinaryController extends Controller
{
    private const PER_PAGE = 24;

    /**
     * Thư viện ảnh mà website đã upload lên Cloudinary.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'folder' => ['nullable', 'string', 'max:200'],
        ]);

        $keyword = trim((string) ($validated['q'] ?? ''));
        $folder = $validated['folder'] ?? null;
        $library = CloudinaryImageLibrary::fromPublicDisk();

        if ($library === null) {
            return view('admin.cloudinary.index', ['configured' => false]);
        }

        if ($request->boolean('refresh')) {
            $library->forget();

            return redirect()
                ->route('admin.cloudinary.index', array_filter(['q' => $keyword, 'folder' => $folder], 'filled'))
                ->with('success', 'Đã tải lại danh sách ảnh mới nhất từ Cloudinary.');
        }

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

        $all = collect($result['images']);
        $filtered = $all
            ->when($folder !== null && $folder !== '', fn ($images) => $images->where('folder', $folder === '/' ? '' : $folder))
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
}
