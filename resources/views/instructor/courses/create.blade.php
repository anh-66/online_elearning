@extends('layouts.instructor')

@section('title', 'Tạo khóa học mới')
@section('page_title', 'Tạo khóa học mới')

@section('content')
<form method="POST" action="{{ route('instructor.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    @include('instructor.courses.partials.form')

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('instructor.courses.index') }}" class="px-5 py-2.5 rounded-xl bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800">Hủy</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-700 text-white text-sm font-semibold shadow-sm hover:bg-brand-800">Tạo khóa học</button>
    </div>
</form>
@endsection
