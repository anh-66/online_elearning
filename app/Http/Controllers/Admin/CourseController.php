<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Course moderation: instructors build courses, admins approve, reject, hide or delete them.
 */
class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::query()
            ->with(['category', 'instructor'])
            ->withCount(['lessons', 'enrollments', 'quizzes'])
            ->filter($request->only('q'))
            ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->integer('category_id'), fn ($query, int $categoryId) => $query->where('category_id', $categoryId))
            // Courses waiting for review come first.
            ->orderByRaw('case when status = ? then 0 else 1 end', [CourseStatus::Pending->value])
            ->latest('submitted_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'statuses' => CourseStatus::cases(),
            'pendingCount' => Course::where('status', CourseStatus::Pending)->count(),
        ]);
    }

    /**
     * Publish a pending course, or show a hidden one again.
     */
    public function approve(Course $course): RedirectResponse
    {
        if (! in_array($course->status, [CourseStatus::Pending, CourseStatus::Hidden], true)) {
            return back()->with('error', 'Chỉ duyệt được khoá học đang chờ duyệt hoặc đã ẩn.');
        }

        $course->update([
            'status' => CourseStatus::Published,
            'rejection_reason' => null,
            'published_at' => $course->published_at ?? now(),
        ]);

        return back()->with('success', "Đã duyệt và xuất bản khoá học \"{$course->title}\".");
    }

    public function reject(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate(
            ['rejection_reason' => ['required', 'string', 'max:1000']],
            [],
            ['rejection_reason' => 'lý do từ chối'],
        );

        if ($course->status !== CourseStatus::Pending) {
            return back()->with('error', 'Chỉ từ chối được khoá học đang chờ duyệt.');
        }

        $course->update([
            'status' => CourseStatus::Rejected,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', "Đã từ chối khoá học \"{$course->title}\".");
    }

    public function hide(Course $course): RedirectResponse
    {
        if ($course->status !== CourseStatus::Published) {
            return back()->with('error', 'Chỉ ẩn được khoá học đang xuất bản.');
        }

        $course->update(['status' => CourseStatus::Hidden]);

        return back()->with('success', "Đã ẩn khoá học \"{$course->title}\" khỏi trang công khai.");
    }

    public function destroy(Course $course): RedirectResponse
    {
        Storage::disk('public')->delete(array_filter([$course->thumbnail, ...$course->images->pluck('path')]));
        $course->delete();

        return back()->with('success', 'Đã xoá khoá học.');
    }
}
