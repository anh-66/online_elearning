<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function index(Course $course): View
    {
        Gate::authorize('manage', $course);

        return view('admin.lessons.index', [
            'course' => $course,
            'lessons' => $course->lessons()->paginate(15),
        ]);
    }

    public function create(Course $course): View
    {
        Gate::authorize('manage', $course);

        return view('admin.lessons.form', [
            'course' => $course,
            'lesson' => new Lesson(['position' => (int) $course->lessons()->max('position') + 1, 'duration_minutes' => 10]),
        ]);
    }

    public function store(LessonRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $course->lessons()->create([
            ...$request->validated(),
            'position' => $request->integer('position') ?: (int) $course->lessons()->max('position') + 1,
        ]);

        return to_route('admin.courses.lessons.index', $course)->with('success', 'Đã thêm bài học.');
    }

    public function edit(Course $course, Lesson $lesson): View
    {
        Gate::authorize('manage', $course);

        return view('admin.lessons.form', compact('course', 'lesson'));
    }

    public function update(LessonRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $lesson->update([
            ...$request->validated(),
            'position' => $request->integer('position', $lesson->position),
        ]);

        return to_route('admin.courses.lessons.index', $course)->with('success', 'Đã cập nhật bài học.');
    }

    public function destroy(Course $course, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $lesson->delete();

        return back()->with('success', 'Đã xoá bài học.');
    }
}
