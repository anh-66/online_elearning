<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseCheckoutController extends Controller
{
    public function show(Course $course): View|RedirectResponse
    {
        if (Auth::user()->id === $course->instructor_id || Auth::user()->isAdmin()) {
            return redirect()->route('courses.show', $course->slug);
        }

        if (Auth::user()->isEnrolledIn($course)) {
            return redirect()->route('learning.player', $course->slug);
        }

        return view('courses.checkout', [
            'course' => $course,
            'savedPaymentMethod' => Auth::user()->defaultPaymentMethod(),
        ]);
    }

    public function process(Request $request, Course $course): RedirectResponse|JsonResponse
    {
        $request->merge(['payment_type' => $request->input('payment_type', 'card')]);
        $validated = $request->validate([
            'payment_type' => ['required', 'string', 'in:card,momo,bank,cod,saved'],
            'momo_phone' => ['nullable', 'string', 'max:30'],
            'card_number' => ['nullable', 'string', 'max:30'],
        ]);

        $user = Auth::user();
        if ($user->id === $course->instructor_id || $user->isAdmin()) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Bạn không thể mua khóa học của chính mình.');
        }

        if ($user->isEnrolledIn($course)) {
            return redirect()->route('learning.player', $course->slug);
        }

        if ($course->price <= 0) {
            return redirect()->route('courses.enroll', $course);
        }

        $paymentType = $validated['payment_type'];
        if ($paymentType === 'saved' && ! $user->defaultPaymentMethod()) {
            return back()->withErrors(['payment_type' => 'Không tìm thấy phương thức thanh toán đã lưu.']);
        }

        if ($paymentType !== 'saved') {
            $title = $paymentType === 'momo'
                ? 'Ví điện tử MoMo'
                : ($paymentType === 'card' ? 'Kết thúc bằng •••• '.substr($validated['card_number'] ?? '4242', -4) : 'Thanh toán '.strtoupper($paymentType));
            $subtitle = $paymentType === 'momo'
                ? $this->maskPhone($validated['momo_phone'] ?? '')
                : null;

            $user->paymentMethods()->update(['is_default' => false]);
            $user->paymentMethods()->create([
                'type' => $paymentType,
                'title' => $title,
                'subtitle' => $subtitle,
                'icon' => $paymentType === 'momo' ? 'logos:momo' : 'logos:mastercard',
                'is_default' => true,
            ]);
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_PAID,
            'progress_percent' => 0,
            'enrolled_at' => now(),
        ]);

        if ($course->price > 0 && $course->instructor_id) {
            $orderAmount = (float) $course->price;
            $instructorShare = round($orderAmount * 0.70, 2);
            $adminShare = round($orderAmount * 0.30, 2);

            \App\Models\InstructorEarning::create([
                'instructor_id' => $course->instructor_id,
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'order_amount' => $orderAmount,
                'instructor_rate' => 70.00,
                'admin_rate' => 30.00,
                'instructor_share' => $instructorShare,
                'admin_share' => $adminShare,
                'status' => \App\Models\InstructorEarning::STATUS_AVAILABLE,
                'available_at' => now(),
            ]);
        }

        $payload = [
            'order_code' => '#EDU-'.str_pad((string) $course->id, 5, '0', STR_PAD_LEFT),
            'paid_at' => now()->format('H:i, d/m/Y'),
            'payment_type_label' => $paymentType,
            'player_url' => route('learning.player', $course->slug),
            'course_url' => route('courses.show', $course->slug),
            'course_title' => $course->title,
            'course_price' => number_format((float) $course->price, 0, ',', '.').' ₫',
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return redirect()->route('learning.player', $course->slug)
            ->with('success', 'Thanh toán và ghi danh thành công.');
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '0988123456';

        return substr($digits, 0, 4).'***'.substr($digits, -3);
    }
}
