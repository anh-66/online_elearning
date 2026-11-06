<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    /**
     * Danh sách phản hồi và báo cáo sự cố từ người dùng.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['all', Feedback::STATUS_PENDING, Feedback::STATUS_IN_PROGRESS, Feedback::STATUS_RESOLVED, Feedback::STATUS_CLOSED])],
            'category' => ['nullable', Rule::in(['all', Feedback::CATEGORY_BUG, Feedback::CATEGORY_FEATURE, Feedback::CATEGORY_QUESTION, Feedback::CATEGORY_OTHER])],
            'priority' => ['nullable', Rule::in(['all', Feedback::PRIORITY_LOW, Feedback::PRIORITY_MEDIUM, Feedback::PRIORITY_HIGH])],
        ]);

        $keyword = $validated['keyword'] ?? null;
        $status = $validated['status'] ?? 'all';
        $category = $validated['category'] ?? 'all';
        $priority = $validated['priority'] ?? 'all';

        $feedbacks = Feedback::query()
            ->with('user')
            ->when($keyword, function ($query, string $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('sender_name', 'like', "%{$keyword}%")
                        ->orWhere('sender_email', 'like', "%{$keyword}%")
                        ->orWhereHas('user', function ($uq) use ($keyword) {
                            $uq->where('name', 'like', "%{$keyword}%")
                                ->orWhere('email', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($status !== 'all' && $status !== null, fn ($query) => $query->where('status', $status))
            ->when($category !== 'all' && $category !== null, fn ($query) => $query->where('category', $category))
            ->when($priority !== 'all' && $priority !== null, fn ($query) => $query->where('priority', $priority))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => Feedback::count(),
            'pending' => Feedback::where('status', Feedback::STATUS_PENDING)->count(),
            'in_progress' => Feedback::where('status', Feedback::STATUS_IN_PROGRESS)->count(),
            'resolved' => Feedback::where('status', Feedback::STATUS_RESOLVED)->count(),
        ];

        return view('admin.feedbacks.index', compact('feedbacks', 'stats', 'keyword', 'status', 'category', 'priority'));
    }

    /**
     * Chi tiết phản hồi.
     */
    public function show(Feedback $feedback): View
    {
        $feedback->load('user');

        return view('admin.feedbacks.show', compact('feedback'));
    }

    /**
     * Cập nhật trạng thái và ghi chú nội bộ của admin.
     */
    public function update(Request $request, Feedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([Feedback::STATUS_PENDING, Feedback::STATUS_IN_PROGRESS, Feedback::STATUS_RESOLVED, Feedback::STATUS_CLOSED])],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $feedback->status = $validated['status'];
        $feedback->admin_notes = $validated['admin_notes'];

        if ($validated['status'] === Feedback::STATUS_RESOLVED && ! $feedback->resolved_at) {
            $feedback->resolved_at = now();
        }

        $feedback->save();

        return back()->with('success', 'Đã cập nhật trạng thái phản hồi thành công.');
    }

    /**
     * Xóa phản hồi.
     */
    public function destroy(Feedback $feedback): RedirectResponse
    {
        if ($feedback->attachment && Storage::disk('public')->exists($feedback->attachment)) {
            Storage::disk('public')->delete($feedback->attachment);
        }

        $feedback->delete();

        return redirect()->route('admin.feedbacks.index')->with('success', 'Đã xóa phản hồi.');
    }
}
