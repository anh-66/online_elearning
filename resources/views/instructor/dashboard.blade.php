@extends('layouts.instructor')

@section('title', 'Studio Giảng viên - Tổng quan')
@section('page_title', 'Bảng điều khiển Giảng viên')

@section('content')
<div class="space-y-6">
    <!-- Thống kê nhanh mẫu -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng số khóa học</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ Auth::user()->courses()->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lượt học viên</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    {{ \App\Models\Enrollment::whereIn('course_id', Auth::user()->courses()->pluck('id'))->count() }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Đánh giá trung bình</p>
                <p class="text-2xl font-bold text-amber-500 mt-1">4.8 ★</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Thông báo hướng dẫn TV5 -->
    <div class="p-6 bg-indigo-50 border border-indigo-100 rounded-2xl">
        <h3 class="text-base font-bold text-indigo-900 mb-2">📌 Khu vực dành riêng cho Thành viên 5 (Studio Giảng viên)</h3>
        <p class="text-sm text-indigo-700 leading-relaxed">
            Bạn có thể thoải mái thêm các route vào file <code class="bg-indigo-100 px-2 py-0.5 rounded font-mono text-indigo-900">routes/instructor.php</code> 
            và xây dựng các trang view trong thư mục <code class="bg-indigo-100 px-2 py-0.5 rounded font-mono text-indigo-900">resources/views/instructor/</code>.
        </p>
    </div>
</div>
@endsection
