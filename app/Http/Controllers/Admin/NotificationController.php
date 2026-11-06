<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['system', 'course', 'promotion'])],
        ]);

        $notifications = Notification::query()
            ->when($validated['keyword'] ?? null, function ($query, string $keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            })
            ->when($validated['type'] ?? null, fn ($query, string $type) => $query->where('type', $type))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create(): View
    {
        return view('admin.notifications.form', [
            'notification' => new Notification(['type' => 'system']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        Notification::create($validated);

        return redirect()->route('admin.notifications.index')->with('success', 'Đã thêm thông báo mới.');
    }

    public function edit(Notification $notification): View
    {
        return view('admin.notifications.form', compact('notification'));
    }

    public function update(Request $request, Notification $notification): RedirectResponse
    {
        $validated = $this->validated($request);

        $notification->update($validated);

        return redirect()->route('admin.notifications.index')->with('success', 'Đã cập nhật thông báo.');
    }

    public function destroy(Notification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')->with('success', 'Đã xóa thông báo.');
    }

    /**
     * @return array{title: string, content: string, type: string}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', Rule::in(['system', 'course', 'promotion'])],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề thông báo.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung thông báo.',
            'type.required' => 'Vui lòng chọn loại thông báo.',
            'type.in' => 'Loại thông báo không hợp lệ.',
        ]);
    }
}
