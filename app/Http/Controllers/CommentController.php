<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonCommentRequest;
use App\Models\Lesson;
use App\Models\LessonComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Hỏi đáp/bình luận dưới mỗi bài học (bảng lesson_comments) - TV4.
 * Người dùng chỉ được sửa/xóa bình luận của chính mình.
 */
class CommentController extends Controller
{
    public function store(LessonCommentRequest $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('create', [LessonComment::class, $lesson]);

        $parentId = $request->validated('parent_id');

        if ($parentId !== null) {
            $parent = $lesson->comments()->find($parentId);

            if ($parent === null) {
                return back()->with('error', 'Bình luận bạn muốn trả lời không tồn tại.');
            }

            // Chỉ hỗ trợ một cấp trả lời: trả lời một phản hồi sẽ được gắn vào bình luận gốc.
            $parentId = $parent->parent_id ?? $parent->id;
        }

        $comment = $lesson->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $parentId,
            'content' => $request->validated('content'),
        ]);

        return back()->with('success', 'Bình luận của bạn đã được gửi!');
    }

    public function update(LessonCommentRequest $request, LessonComment $comment): RedirectResponse
    {
        Gate::authorize('update', $comment);

        $comment->update(['content' => $request->validated('content')]);

        return back()->with('success', 'Đã cập nhật bình luận.');
    }

    public function destroy(LessonComment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}
