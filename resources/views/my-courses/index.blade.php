@extends('layouts.public')

@section('title', 'Khóa học của tôi')

@section('content')
<div class="bg-neutral-50 py-8 sm:py-12 dark:bg-neutral-950">
    <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Đã ghi danh</p>
                <p class="mt-1 text-2xl font-bold text-neutral-950 dark:text-white">{{ $enrollmentTotal }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Đã hoàn thành</p>
                <p class="mt-1 text-2xl font-bold text-neutral-950 dark:text-white">{{ $completedTotal }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Tiến độ trung bình</p>
                <p class="mt-1 text-2xl font-bold text-brand-700 dark:text-brand-400">{{ $averageProgress }}%</p>
            </div>
        </div>
        <section>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-700 dark:text-brand-400">Học tập</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-neutral-950 dark:text-neutral-100 sm:text-3xl">Khóa học của tôi</h1>
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">Tiếp tục các khóa học bạn đã ghi danh.</p>
                </div>
                <a href="{{ route('courses.index') }}" class="inline-flex self-start rounded-md bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 sm:self-auto">
                    Khám phá khóa học
                </a>
            </div>

            @if ($courses->isEmpty())
                <div class="mt-6 rounded-xl border border-dashed border-neutral-300 bg-white px-6 py-12 text-center dark:border-neutral-700 dark:bg-neutral-900">
                    <p class="font-semibold text-neutral-900 dark:text-neutral-100">Bạn chưa ghi danh khóa học nào</p>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Hãy chọn một khóa học phù hợp để bắt đầu hành trình học tập.</p>
                    <a href="{{ route('courses.index') }}" class="mt-5 inline-flex rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">Xem danh sách khóa học</a>
                </div>
            @else
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($courses as $course)
                        <div class="space-y-3">
                            @include('partials.course-card', ['course' => $course])
                            <div class="px-1">
                                <div class="mb-1 flex justify-between text-xs font-semibold text-neutral-600 dark:text-neutral-300">
                                    <span>Tiến độ học tập</span>
                                    <span>{{ $course->pivot->progress_percent ?? 0 }}%</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-800">
                                    <div class="h-full rounded-full bg-brand-600" style="width: {{ min(100, max(0, (int) ($course->pivot->progress_percent ?? 0))) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($courses->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $courses->links() }}
                    </div>
                @endif
            @endif
        </section>

        <section>
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-700 dark:text-brand-400">Lưu lại</p>
                    <h2 class="mt-1 text-xl font-bold text-neutral-950 dark:text-neutral-100">Danh sách yêu thích</h2>
                </div>
            </div>
            @if ($wishlistCourses->isEmpty())
                <p class="mt-4 text-sm text-neutral-500 dark:text-neutral-400">Bạn chưa thêm khóa học nào vào danh sách yêu thích.</p>
            @else
                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($wishlistCourses as $course)
                        @include('partials.course-card', ['course' => $course])
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
