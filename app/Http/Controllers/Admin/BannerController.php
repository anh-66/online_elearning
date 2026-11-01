<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        return view('admin.banners.form', ['banner' => new Banner(['is_active' => true, 'order' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, imageRequired: true);
        $data['image_path'] = $request->file('image')->store('banners', 'public');
        unset($data['image']);

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Đã thêm banner mới.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $this->validated($request, imageRequired: false);

        if ($request->hasFile('image')) {
            $this->deleteImage($banner);
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }
        unset($data['image']);

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Đã cập nhật banner.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->deleteImage($banner);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Đã xóa banner.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $imageRequired): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['order'] = (int) ($data['order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function deleteImage(Banner $banner): void
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
    }
}
