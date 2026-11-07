<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Ghi danh vào khóa học miễn phí.
     */
    public function enroll(Request $request, int|string $id): RedirectResponse
    {
        $user = Auth::user();
        $course = Course::findOrFail($id);

        // 1. Giảng viên phụ trách không cần ghi danh vào khóa của chính mình
        if ($course->instructor_id === $user->id) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Bạn là giảng viên phụ trách khóa học này nên không cần ghi danh.');
        }

        // 2. Nếu đã ghi danh trước đó, đưa vào phòng học luôn
        if ($user->isEnrolledIn($course)) {
            return redirect()->route('learning.player', $course->slug)
                ->with('info', 'Bạn đã ghi danh khóa học này rồi.');
        }

        // 3. Khóa học có phí (> 0) thì BẮT BUỘC phải thanh toán, không được ghi danh tự do!
        if ($course->price > 0) {
            return redirect()->route('courses.checkout', $course->slug)
                ->with('error', 'Khóa học này có mức học phí là '.number_format($course->price, 0, ',', '.').' ₫. Vui lòng hoàn tất thanh toán để vào học.');
        }

        // 4. Khóa học miễn phí -> Tạo ghi danh hợp lệ
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_FREE,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('learning.player', $course->slug)
            ->with('success', 'Ghi danh khóa học miễn phí thành công! Chúc bạn học tập hiệu quả.');
    }

    /**
     * Giao diện thanh toán / mua khóa học có phí.
     */
    public function checkout(string $slug): View|RedirectResponse
    {
        $user = Auth::user();
        $course = Course::with(['instructor', 'category', 'chapters.lessons'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Giảng viên không thể mua khóa học của chính mình
        if ($course->instructor_id === $user->id) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Bạn là giảng viên phụ trách khóa học này.');
        }

        // Nếu đã mua rồi -> chuyển thẳng vào học
        if ($user->isEnrolledIn($course)) {
            return redirect()->route('learning.player', $course->slug)
                ->with('info', 'Bạn đã sở hữu khóa học này.');
        }

        // Nếu khóa học miễn phí -> ghi danh ngay
        if ($course->price <= 0) {
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => Enrollment::STATUS_FREE,
                'enrolled_at' => now(),
            ]);

            return redirect()->route('learning.player', $course->slug)
                ->with('success', 'Khóa học này hoàn toàn miễn phí. Đã ghi danh thành công!');
        }

        $savedPaymentMethod = $user->defaultPaymentMethod();

        return view('courses.checkout', compact('course', 'savedPaymentMethod'));
    }

    /**
     * Xử lý xác nhận thanh toán học phí khóa học.
     */
    public function processCheckout(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::user();
        $course = Course::published()->where('slug', $slug)->firstOrFail();

        if ($course->instructor_id === $user->id) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Bạn là giảng viên phụ trách khóa học này.');
        }

        // Lưu / cập nhật phương thức thanh toán vào tài khoản nếu người dùng nhập mới
        $paymentType = $request->input('payment_type', 'card');
        $last4 = '4242';
        $maskedPhone = '0988***456';

        if ($paymentType === 'card') {
            $rawNumber = preg_replace('/\D/', '', (string) $request->input('card_number', ''));
            $last4 = strlen($rawNumber) >= 4 ? substr($rawNumber, -4) : '4242';
            $holder = trim((string) $request->input('card_name', '')) ?: $user->name;
            $exp = trim((string) $request->input('card_exp', '')) ?: '12/28';

            $user->paymentMethods()->update(['is_default' => false]);
            $user->paymentMethods()->create([
                'type' => 'card',
                'title' => 'Visa kết thúc bằng •••• '.$last4,
                'subtitle' => 'Hết hạn '.$exp.' · '.$holder,
                'icon' => 'logos:visa',
                'is_default' => true,
            ]);
        } elseif ($paymentType === 'momo') {
            $phone = trim((string) $request->input('momo_phone', '')) ?: '0988 123 456';
            $holder = trim((string) $request->input('momo_name', '')) ?: $user->name;
            $cleanPhone = preg_replace('/\D/', '', $phone);
            $maskedPhone = strlen($cleanPhone) >= 7
                ? substr($cleanPhone, 0, 4).'***'.substr($cleanPhone, -3)
                : $phone;

            $user->paymentMethods()->update(['is_default' => false]);
            $user->paymentMethods()->create([
                'type' => 'momo',
                'title' => 'Ví điện tử MoMo ('.$maskedPhone.')',
                'subtitle' => 'Chủ ví: '.$holder,
                'icon' => 'simple-icons:momo',
                'is_default' => true,
            ]);
        } elseif ($paymentType === 'bank') {
            $user->paymentMethods()->update(['is_default' => false]);
            $user->paymentMethods()->create([
                'type' => 'bank',
                'title' => 'Tài khoản MB Bank',
                'subtitle' => 'Chuyển khoản nhanh 24/7 · '.$user->name,
                'icon' => 'lucide:landmark',
                'is_default' => true,
            ]);
        }

        $paymentLabel = match ($paymentType) {
            'card' => 'Thẻ tín dụng (Visa **** '.$last4.')',
            'momo' => 'Ví MoMo ('.$maskedPhone.')',
            'bank' => 'Chuyển khoản MB Bank (VietQR 24/7)',
            'cod' => 'Thanh toán sau (COD)',
            'saved' => ($saved = $user->defaultPaymentMethod()) ? $saved->title : 'Phương thức đã lưu',
            default => 'Thanh toán học phí',
        };

        $orderCode = '#ACD-'.strtoupper(substr(md5($user->id.'-'.$course->id.'-'.time()), 0, 5));

        // Kiểm tra xem đã ghi danh trước đó chưa
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            if ($existingEnrollment->status !== Enrollment::STATUS_PAID && $course->price > 0) {
                $existingEnrollment->update([
                    'status' => Enrollment::STATUS_PAID,
                    'enrolled_at' => now(),
                ]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_code' => $orderCode,
                    'paid_at' => now()->format('H:i, d/m/Y'),
                    'payment_type_label' => $paymentLabel,
                    'player_url' => route('learning.player', $course->slug),
                    'course_url' => route('courses.show', $course->slug),
                    'course_title' => $course->title,
                    'course_price' => number_format($course->price, 0, ',', '.').'đ',
                    'message' => 'Thanh toán học phí thành công!',
                ]);
            }

            return redirect()->route('learning.player', $course->slug)
                ->with('success', 'Thanh toán học phí thành công! Phương thức thanh toán đã được lưu vào tài khoản của bạn.');
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => $course->price > 0 ? Enrollment::STATUS_PAID : Enrollment::STATUS_FREE,
            'enrolled_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'order_code' => $orderCode,
                'paid_at' => now()->format('H:i, d/m/Y'),
                'payment_type_label' => $paymentLabel,
                'player_url' => route('learning.player', $course->slug),
                'course_url' => route('courses.show', $course->slug),
                'course_title' => $course->title,
                'course_price' => number_format($course->price, 0, ',', '.').'đ',
                'message' => 'Thanh toán học phí thành công!',
            ]);
        }

        return redirect()->route('learning.player', $course->slug)
            ->with('success', 'Thanh toán học phí thành công! Phương thức thanh toán đã được lưu vào tài khoản của bạn.');
    }
}
