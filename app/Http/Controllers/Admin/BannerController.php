<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        $banners = Banner::orderBy('order')->orderByDesc('id')->paginate(15);

        return view('admin.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banners.form', ['banner' => new Banner(['is_active' => true, 'order' => 0, 'bg_color' => '#019D91'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->filled('cropped_image')) {
            $data['image_path'] = $this->saveBase64Image($request->input('cropped_image'));
        } elseif ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        } else {
            $data['image_path'] = null;
        }

        unset($data['image'], $data['cropped_image'], $data['remove_image']);

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Đã thêm banner mới.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->boolean('remove_image')) {
            $this->deleteImage($banner);
            $data['image_path'] = null;
        } elseif ($request->filled('cropped_image')) {
            $this->deleteImage($banner);
            $data['image_path'] = $this->saveBase64Image($request->input('cropped_image'));
        } elseif ($request->hasFile('image')) {
            $this->deleteImage($banner);
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }

        unset($data['image'], $data['cropped_image'], $data['remove_image']);

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Đã cập nhật banner.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->deleteImage($banner);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Đã xóa banner.');
    }

    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        $message = $banner->is_active ? 'Đã bật banner.' : 'Đã tắt banner.';

        return back()->with('success', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'cropped_image' => ['nullable', 'string'],
            'remove_image' => ['nullable', 'boolean'],
            'bg_color' => ['nullable', 'string', 'max:150'],
            'show_text' => ['nullable', 'boolean'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['order'] = (int) ($data['order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');
        $data['show_text'] = $request->boolean('show_text');
        $data['bg_color'] = $data['bg_color'] ?: '#019D91';

        return $data;
    }

    private function saveBase64Image(string $base64Data): ?string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $ext = strtolower($type[1]);
            if ($ext === 'jpeg') {
                $ext = 'jpg';
            }
            $decoded = base64_decode($data);
            if ($decoded !== false) {
                $path = 'banners/'.Str::random(40).'.'.$ext;
                Storage::disk('public')->put($path, $decoded);

                return $path;
            }
        }

        return null;
    }

    private function deleteImage(Banner $banner): void
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
    }
}
