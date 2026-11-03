<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Danh sách khóa học để duyệt. Mặc định hiển thị khóa chờ duyệt (pending).
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['all', Course::STATUS_DRAFT, Course::STATUS_PENDING, Course::STATUS_PUBLISHED, Course::STATUS_REJECTED])],
            'keyword' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer'],
        ]);

        $status = $validated['status'] ?? Course::STATUS_PENDING;

        $courses = Course::query()
            ->with(['instructor', 'category'])
            ->withCount('enrollments')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($validated['keyword'] ?? null, function ($query, string $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhereHas('instructor', fn ($query) => $query->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->when($validated['category_id'] ?? null, fn ($query, int $categoryId) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusCounts = Course::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.courses.index', compact('courses', 'status', 'statusCounts', 'categories'));
    }

    /**
     * Duyệt khóa học: pending -> published.
     */
    public function approve(Course $course): RedirectResponse
    {
        if (! $course->isPending()) {
            return back()->with('error', 'Chỉ có thể duyệt khóa học đang ở trạng thái chờ duyệt.');
        }

        $course->update([
            'status' => Course::STATUS_PUBLISHED,
            'reject_reason' => null,
        ]);

        return back()->with('success', "Đã duyệt và xuất bản khóa học \"{$course->title}\".");
    }

    /**
     * Từ chối khóa chờ duyệt, hoặc ẩn khóa đã xuất bản vi phạm: -> rejected kèm lý do.
     */
    public function reject(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:1000'],
        ], [
            'reject_reason.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        if (! $course->isPending() && ! $course->isPublished()) {
            return back()->with('error', 'Chỉ có thể từ chối khóa học đang chờ duyệt hoặc ẩn khóa học đã xuất bản.');
        }

        $wasPublished = $course->isPublished();

        $course->update([
            'status' => Course::STATUS_REJECTED,
            'reject_reason' => $validated['reject_reason'],
        ]);

        $message = $wasPublished
            ? "Đã ẩn khóa học \"{$course->title}\" khỏi trang công khai."
            : "Đã từ chối khóa học \"{$course->title}\".";

        return back()->with('success', $message);
    }

    /**
     * Ẩn khóa học đã xuất bản: published -> draft.
     */
    public function hide(Course $course): RedirectResponse
    {
        $course->update([
            'status' => Course::STATUS_DRAFT,
        ]);

        return back()->with('success', "Đã ẩn khóa học \"{$course->title}\".");
    }

    /**
     * Xóa khóa học vi phạm (kéo theo chương, bài học, ghi danh... nhờ cascade).
     */
    public function destroy(Course $course): RedirectResponse
    {
        $title = $course->title;
        $course->delete();

        return back()->with('success', "Đã xóa khóa học \"{$title}\".");
    }
}
