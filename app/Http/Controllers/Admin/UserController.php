<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Danh sách người dùng, có tìm kiếm theo tên/email và lọc theo role, trạng thái.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in([User::ROLE_ADMIN, User::ROLE_INSTRUCTOR, User::ROLE_STUDENT])],
            'status' => ['nullable', Rule::in([User::STATUS_ACTIVE, User::STATUS_BLOCKED])],
        ]);

        $users = User::query()
            ->when($validated['keyword'] ?? null, function ($query, string $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when($validated['role'] ?? null, fn ($query, string $role) => $query->where('role', $role))
            ->when($validated['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Đổi role của người dùng (admin / instructor / student).
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_INSTRUCTOR, User::ROLE_STUDENT])],
        ]);

        if ($user->is($request->user())) {
            return back()->with('error', 'Bạn không thể tự đổi vai trò của chính mình.');
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('success', "Đã đổi vai trò của {$user->name} thành {$validated['role']}.");
    }

    /**
     * Khóa / mở khóa tài khoản (active <-> blocked).
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        $newStatus = $user->isActive() ? User::STATUS_BLOCKED : User::STATUS_ACTIVE;
        $user->update(['status' => $newStatus]);

        $message = $newStatus === User::STATUS_BLOCKED
            ? "Đã khóa tài khoản {$user->name}."
            : "Đã mở khóa tài khoản {$user->name}.";

        return back()->with('success', $message);
    }
}
