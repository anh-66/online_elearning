@extends('layouts.public')

@section('title', 'Bảng điều khiển')

@section('content')
@php
    $user = Auth::user();
    $quickLinks = [
        ['label' => 'Khám phá khóa học', 'text' => 'Tìm khóa học mới theo chủ đề, trình độ và mức giá.', 'url' => route('courses.index')],
        ['label' => 'Khóa học miễn phí', 'text' => 'Bắt đầu ngay với các khóa học không tốn phí.', 'url' => route('courses.index', ['price' => 'free'])],
        ['label' => 'Hồ sơ cá nhân', 'text' => 'Cập nhật thông tin, ảnh đại diện và mật khẩu.', 'url' => route('profile.edit')],
    ];
    if (Route::has('my-courses')) {
        array_unshift($quickLinks, ['label' => 'Khóa học của tôi', 'text' => 'Tiếp tục các bài học bạn đang theo dõi.', 'url' => route('my-courses')]);
    }
@endphp

<div class="bg-neutral-50 py-8 sm:py-12 dark:bg-neutral-950">
    <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
        @include('partials.latest-notifications', ['latestNotifications' => $latestNotifications])

        <section class="relative overflow-hidden rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 via-white to-brand-100/60 dark:from-neutral-900 dark:via-neutral-950 dark:to-brand-950/40 p-6 text-neutral-900 dark:border-neutral-800 dark:text-white sm:p-8">
            <div class="absolute -right-16 -top-24 h-64 w-64 rounded-full bg-brand-300/30 blur-3xl dark:bg-brand-700/20"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-700 dark:text-brand-400">Bảng điều khiển</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Xin chào, {{ $user->name }}</h1>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-neutral-600 dark:text-neutral-300">Hôm nay bạn muốn học gì? Tiếp tục khóa học đang dở hoặc khám phá chủ đề mới.</p>
                </div>
                <div class="flex gap-4">
                    <div class="rounded-xl bg-white/70 px-5 py-4 ring-1 ring-brand-100 dark:bg-white/5 dark:ring-white/10">
                        <p class="text-2xl font-bold">{{ number_format($enrollmentTotal) }}</p>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Khóa học đã ghi danh</p>
                    </div>
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center self-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-800">
                        Tìm khóa học
                    </a>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-lg font-bold text-neutral-950 dark:text-neutral-100">Truy cập nhanh</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 {{ count($quickLinks) > 3 ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">
                @foreach ($quickLinks as $link)
                    <a href="{{ $link['url'] }}" class="group rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 transition hover:border-brand-700 hover:shadow-sm">
                        <p class="font-semibold text-neutral-900 dark:text-neutral-100 group-hover:text-brand-700 dark:group-hover:text-brand-300">{{ $link['label'] }} &rarr;</p>
                        <p class="mt-1 text-sm leading-6 text-neutral-500 dark:text-neutral-400">{{ $link['text'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <section>
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-lg font-bold text-neutral-950 dark:text-neutral-100">Khóa học gần đây của bạn</h2>
                @if (Route::has('my-courses') && $enrollmentTotal > $enrolledCourses->count())
                    <a href="{{ route('my-courses') }}" class="text-sm font-medium text-brand-700 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">Xem tất cả</a>
                @endif
            </div>

            @if ($enrolledCourses->isEmpty())
                <div class="mt-4 rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-6 py-12 text-center">
                    <p class="font-semibold text-neutral-900 dark:text-neutral-100">Bạn chưa ghi danh khóa học nào</p>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Hãy chọn một khóa học phù hợp để bắt đầu hành trình học tập.</p>
                    <a href="{{ route('courses.index') }}" class="mt-5 inline-flex rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">Khám phá khóa học</a>
                </div>
            @else
                <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($enrolledCourses as $course)
                        @include('partials.course-card', ['course' => $course])
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
