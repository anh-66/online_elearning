@extends('layouts.public')

@section('title', 'Thiết kế Avatar')

@push('scripts')
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
@endpush

@section('content')
<div class="py-8 sm:py-12 bg-white dark:bg-[#0d1117] min-h-[calc(100vh-140px)]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Thiết kế Avatar cá nhân</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tùy biến phong cách hoạt hình độc đáo với kiểu tóc, trang phục, kính và màu sắc yêu thích.</p>
        </div>

        <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-3xl p-4 sm:p-6 shadow-sm">
            <x-avatar-designer mode="standalone" />
        </div>
    </div>
</div>
@endsection
