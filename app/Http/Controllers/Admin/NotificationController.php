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
    /**
     * Các loại thông báo cho phép (khớp ghi chú trong migration: system, course, promotion).
     */
    public const TYPES = [
        'system' => 'Hệ thống',
        'course' => 'Khóa học',
        'promotion' => 'Khuyến mãi',
    ];

    public function index(): View
    {
        $notifications = Notification::latest()->paginate(15);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'types' => self::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('admin.notifications.form', [
            'notification' => new Notification(['type' => 'system']),
            'types' => self::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Notification::create($this->validated($request));

        return redirect()->route('admin.notifications.index')->with('success', 'Đã đăng thông báo mới.');
    }

    public function edit(Notification $notification): View
    {
        return view('admin.notifications.form', [
            'notification' => $notification,
            'types' => self::TYPES,
        ]);
    }

    public function update(Request $request, Notification $notification): RedirectResponse
    {
        $notification->update($this->validated($request));

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
            'content' => ['required', 'string', 'max:5000'],
            'type' => ['required', Rule::in(array_keys(self::TYPES))],
        ]);
    }
}