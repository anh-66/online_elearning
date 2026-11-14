<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Services\CertificateIssuer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CertificateController extends Controller
{
    /**
     * Trang "Chứng chỉ của tôi".
     */
    public function index(Request $request, CertificateIssuer $issuer): View
    {
        $user = $request->user();

        // Học viên đã học xong trước khi có tính năng chứng chỉ vẫn nhận được chứng chỉ.
        $issuer->issueMissing($user);

        $certificates = $user->certificates()
            ->with('course.category')
            ->latest('issued_at')
            ->get();

        $inProgress = $user->enrolledCourses()
            ->wherePivot('progress_percent', '<', 100)
            ->with('instructor')
            ->orderByPivot('progress_percent', 'desc')
            ->take(6)
            ->get();

        return view('certificates.index', compact('certificates', 'inProgress'));
    }

    /**
     * Nút "Xem chứng chỉ" của một khóa học: cấp chứng chỉ (nếu chưa có) rồi mở ra.
     */
    public function forCourse(Request $request, Course $course, CertificateIssuer $issuer): RedirectResponse
    {
        $user = $request->user();
        $certificate = $issuer->issue($user, $course);

        if ($certificate === null) {
            $progress = $issuer->progress($user, $course);
            $message = (int) $user->getKey() === (int) $course->instructor_id
                ? 'Giảng viên không nhận chứng chỉ cho khóa học của chính mình.'
                : sprintf('Học xong tất cả bài học để nhận chứng chỉ nhé (bạn đã xong %d/%d bài).', $progress['completed'], $progress['total']);

            return redirect()->route('learning.player', $course->slug)->with('error', $message);
        }

        return redirect()->route('certificates.show', $certificate->code);
    }

    /**
     * Trang chứng chỉ công khai: ai có link hoặc quét mã QR đều xem và xác minh được.
     */
    public function show(Request $request, string $code): View|Response
    {
        $certificate = Certificate::query()
            ->with(['course.category', 'course.instructor', 'user'])
            ->where('code', Certificate::normalizeCode($code))
            ->first();

        if ($certificate === null) {
            return response()->view('certificates.verify', ['code' => $code, 'notFound' => true], 404);
        }

        $isOwner = $certificate->isOwnedBy($request->user());

        return view('certificates.show', [
            'certificate' => $certificate,
            'isOwner' => $isOwner,
            'nameChanged' => $isOwner && $request->user()->name !== $certificate->recipient_name,
        ]);
    }

    /**
     * Tra cứu chứng chỉ bằng mã (cho nhà tuyển dụng, người được gửi mã).
     */
    public function verify(Request $request): View|RedirectResponse
    {
        $code = Certificate::normalizeCode($request->query('code'));

        if ($code === '') {
            return view('certificates.verify', ['code' => '', 'notFound' => false]);
        }

        if (Certificate::query()->where('code', $code)->exists()) {
            return redirect()->route('certificates.show', $code);
        }

        return view('certificates.verify', ['code' => $code, 'notFound' => true]);
    }

    /**
     * Học viên đổi tên trong hồ sơ (ví dụ sửa lỗi chính tả) thì cập nhật lại tên trên chứng chỉ.
     */
    public function refreshName(Request $request, Certificate $certificate): RedirectResponse
    {
        abort_unless($certificate->isOwnedBy($request->user()), 403);

        $certificate->update(['recipient_name' => $request->user()->name]);

        return redirect()->route('certificates.show', $certificate->code)
            ->with('success', 'Đã cập nhật tên trên chứng chỉ.');
    }
}
