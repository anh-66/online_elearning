<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ReordersSiblings;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ChapterController extends Controller
{
    use ReordersSiblings;

    public function store(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('update', $course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ], [], ['title' => 'tên chương']);

        $course->chapters()->create([
            'title' => $validated['title'],
            'order' => (int) $course->chapters()->max('order') + 1,
        ]);

        return back()->with('success', 'Đã thêm chương mới.');
    }

    public function update(Request $request, Chapter $chapter): RedirectResponse
    {
        Gate::authorize('update', $chapter->course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ], [], ['title' => 'tên chương']);

        $chapter->update($validated);

        return back()->with('success', 'Đã cập nhật tên chương.');
    }

    /**
     * Xóa chương kèm toàn bộ bài học (cascade) và file đính kèm của bài học.
     */
    public function destroy(Chapter $chapter): RedirectResponse
    {
        Gate::authorize('update', $chapter->course);

        $chapter->lessons
            ->pluck('attachment_path')
            ->filter()
            ->each(fn (string $path) => Storage::disk('public')->delete($path));

        $chapter->delete();

        return back()->with('success', 'Đã xóa chương và các bài học trong chương.');
    }

    public function move(Request $request, Chapter $chapter): RedirectResponse
    {
        Gate::authorize('update', $chapter->course);

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        $siblings = $chapter->course->chapters()->orderBy('id')->get();
        $this->moveWithinSiblings($siblings, $chapter, $validated['direction']);

        return back();
    }
}
