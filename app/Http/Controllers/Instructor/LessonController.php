<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ReordersSiblings;
use App\Http\Requests\Instructor\LessonRequest;
use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LessonController extends Controller
{
    use ReordersSiblings;

    public function create(Chapter $chapter): View
    {
        Gate::authorize('update', $chapter->course);

        return view('instructor.lessons.create', [
            'chapter' => $chapter,
            'course' => $chapter->course,
            'lesson' => new Lesson(['chapter_id' => $chapter->id]),
        ]);
    }

    public function store(LessonRequest $request, Chapter $chapter): RedirectResponse
    {
        Gate::authorize('update', $chapter->course);

        $data = $request->safe()->only(['title', 'content', 'video_url']);
        $data['order'] = (int) $chapter->lessons()->max('order') + 1;

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('lessons/attachments', 'public');
        }

        $chapter->lessons()->create($data);

        return redirect()->route('instructor.courses.show', $chapter->course_id)
            ->with('success', 'Đã thêm bài học mới.');
    }

    public function edit(Lesson $lesson): View
    {
        $course = $lesson->chapter->course;
        Gate::authorize('update', $course);

        return view('instructor.lessons.edit', [
            'lesson' => $lesson,
            'chapter' => $lesson->chapter,
            'course' => $course,
            'chapters' => $course->chapters,
        ]);
    }

    public function update(LessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $course = $lesson->chapter->course;
        Gate::authorize('update', $course);

        $data = $request->safe()->only(['title', 'content', 'video_url']);

        // Cho phép chuyển bài học sang chương khác của cùng khóa học, đặt ở cuối chương mới.
        $targetChapterId = (int) $request->input('chapter_id', $lesson->chapter_id);
        if ($targetChapterId !== $lesson->chapter_id) {
            $targetChapter = $course->chapters()->whereKey($targetChapterId)->first();

            if (! $targetChapter) {
                throw ValidationException::withMessages(['chapter_id' => 'Chương được chọn không thuộc khóa học này.']);
            }

            $data['chapter_id'] = $targetChapter->id;
            $data['order'] = (int) $targetChapter->lessons()->max('order') + 1;
        }

        if ($request->hasFile('attachment') || $request->boolean('remove_attachment')) {
            $this->deleteAttachment($lesson);
            $data['attachment_path'] = $request->hasFile('attachment')
                ? $request->file('attachment')->store('lessons/attachments', 'public')
                : null;
        }

        $lesson->update($data);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Đã cập nhật bài học.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        Gate::authorize('update', $lesson->chapter->course);

        $this->deleteAttachment($lesson);
        $lesson->delete();

        return back()->with('success', 'Đã xóa bài học.');
    }

    public function move(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('update', $lesson->chapter->course);

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        $siblings = $lesson->chapter->lessons()->orderBy('id')->get();
        $this->moveWithinSiblings($siblings, $lesson, $validated['direction']);

        return back();
    }

    private function deleteAttachment(Lesson $lesson): void
    {
        if ($lesson->attachment_path) {
            Storage::disk('public')->delete($lesson->attachment_path);
        }
    }
}
