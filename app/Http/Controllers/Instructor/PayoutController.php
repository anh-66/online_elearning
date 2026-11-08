<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    /**
     * Hiển thị trang quản lý ví thu nhập và yêu cầu rút tiền của giảng viên.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $earnings = $user->earnings()
            ->with(['course', 'enrollment.user'])
            ->latest()
            ->paginate(15, ['*'], 'earnings_page');

        $payoutRequests = $user->payoutRequests()
            ->latest()
            ->paginate(10, ['*'], 'payouts_page');

        $totalCourseSales = (float) $user->earnings()->sum('order_amount');
        $instructorShare = $user->instructorTotalEarnings();
        $availableBalance = $user->instructorAvailableBalance();
        $pendingPayouts = $user->instructorPendingPayouts();
        $paidPayouts = $user->instructorPaidPayouts();

        $stats = [
            'total_sales' => $totalCourseSales,
            'instructor_share' => $instructorShare,
            'available_balance' => $availableBalance,
            'pending_payouts' => $pendingPayouts,
            'paid_payouts' => $paidPayouts,
            'commission_rate' => 70, // Giảng viên hưởng 70%
            'platform_fee_rate' => 30, // Nền tảng 30%
        ];

        return view('instructor.payouts.index', compact('earnings', 'payoutRequests', 'stats'));
    }

    /**
     * Gửi yêu cầu rút tiền về tài khoản ngân hàng.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $availableBalance = $user->instructorAvailableBalance();

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:50000',
                'max:'.$availableBalance,
            ],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_name' => ['required', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'amount.min' => 'Số tiền rút tối thiểu là 50.000 ₫.',
            'amount.max' => 'Số tiền yêu cầu vượt quá số dư khả dụng hiện tại ('.number_format($availableBalance, 0, ',', '.').' ₫).',
            'bank_name.required' => 'Vui lòng chọn hoặc nhập tên ngân hàng.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
        ]);

        if ($availableBalance < 50000) {
            return back()->with('error', 'Số dư khả dụng chưa đạt mức tối thiểu 50.000 ₫ để rút tiền.');
        }

        PayoutRequest::create([
            'instructor_id' => $user->id,
            'amount' => $validated['amount'],
            'bank_name' => $validated['bank_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_name' => mb_strtoupper($validated['bank_account_name']),
            'note' => $validated['note'] ?? null,
            'status' => PayoutRequest::STATUS_PENDING,
        ]);

        return back()->with('success', 'Yêu cầu rút tiền '.number_format((float) $validated['amount'], 0, ',', '.').' ₫ đã được gửi thành công. Admin sẽ kiểm tra và chuyển khoản trong 1-3 ngày làm việc.');
    }
}
