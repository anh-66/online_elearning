<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in([Enrollment::STATUS_PAID, Enrollment::STATUS_FREE])],
        ]);

        $enrollments = Enrollment::query()
            ->with(['user', 'course'])
            ->when($validated['keyword'] ?? null, function ($query, string $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->whereHas('user', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    })->orWhereHas('course', fn ($query) => $query->where('title', 'like', "%{$keyword}%"));
                });
            })
            ->when($validated['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest('enrolled_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        $enrollment->delete();

        return back()->with('success', 'Đã hủy lượt ghi danh.');
    }
}
