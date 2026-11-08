<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorEarning;
use App\Models\PayoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    /**
     * Danh sách tất cả yêu cầu rút tiền của giảng viên toàn sàn.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'all');

        $query = PayoutRequest::with(['instructor', 'processor'])->latest();

        if ($status !== 'all' && in_array($status, [PayoutRequest::STATUS_PENDING, PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_REJECTED], true)) {
            $query->where('status', $status);
        }

        $payouts = $query->paginate(15);

        $stats = [
            'total_platform_fee' => (float) InstructorEarning::sum('admin_share'), // 30% sàn thu
            'total_paid' => (float) PayoutRequest::where('status', PayoutRequest::STATUS_APPROVED)->sum('amount'),
            'total_pending' => (float) PayoutRequest::where('status', PayoutRequest::STATUS_PENDING)->sum('amount'),
            'pending_count' => PayoutRequest::where('status', PayoutRequest::STATUS_PENDING)->count(),
        ];

        return view('admin.payouts.index', compact('payouts', 'stats', 'status'));
    }

    /**
     * Admin duyệt và xác nhận đã chuyển khoản cho giảng viên.
     */
    public function approve(Request $request, PayoutRequest $payout): RedirectResponse
    {
        if (! $payout->isPending()) {
            return back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:255'],
        ]);

        $payout->update([
            'status' => PayoutRequest::STATUS_APPROVED,
            'admin_note' => $validated['admin_note'] ?? 'Đã chuyển khoản thành công',
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Đã duyệt yêu cầu rút tiền '.number_format((float) $payout->amount, 0, ',', '.').' ₫ cho giảng viên '.$payout->instructor->name.'.');
    }

    /**
     * Admin từ chối yêu cầu rút tiền (hoàn trả số dư lại cho giảng viên).
     */
    public function reject(Request $request, PayoutRequest $payout): RedirectResponse
    {
        if (! $payout->isPending()) {
            return back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'max:255'],
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối để giảng viên nắm được thông tin.',
        ]);

        $payout->update([
            'status' => PayoutRequest::STATUS_REJECTED,
            'admin_note' => $validated['admin_note'],
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Đã từ chối yêu cầu rút tiền. Số tiền đã được hoàn trả lại số dư khả dụng của giảng viên.');
    }
}
