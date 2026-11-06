<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('courses')
            ->when($request->string('keyword')->trim()->toString(), function ($query, string $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->makeUniqueSlug($data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục mới.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);

        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->makeUniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $courseCount = $category->courses()->count();

        if ($courseCount > 0) {
            return back()->with('error', "Không thể xóa danh mục \"{$category->name}\" vì đang có {$courseCount} khóa học. Vui lòng chuyển các khóa học sang danh mục khác trước khi xóa.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục thành công.');
    }

    /**
     * @return array{name: string, icon: ?string, description: ?string}
     */
    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category?->id)],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'danh-muc';
        $slug = $baseSlug;
        $suffix = 2;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }
}
