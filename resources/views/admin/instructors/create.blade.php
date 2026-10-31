@extends('layouts.admin')

@section('title', 'Tạo tài khoản giảng viên')
@section('page_title', 'Tạo tài khoản giảng viên')

@section('content')
<div class="max-w-2xl rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
    <p class="mb-6 text-sm text-gray-500">Chỉ quản trị viên mới có thể tạo tài khoản giảng viên.</p>

    <form method="POST" action="{{ route('admin.instructors.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700">Họ và tên</label>
            <input id="name" name="name" value="{{ old('name') }}" required autofocus class="mt-2 block w-full rounded-md border-gray-300">
            @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="mt-2 block w-full rounded-md border-gray-300">
            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700">Mật khẩu</label>
            <input id="password" type="password" name="password" required class="mt-2 block w-full rounded-md border-gray-300">
            @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Xác nhận mật khẩu</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="mt-2 block w-full rounded-md border-gray-300">
        </div>

        <button type="submit" class="rounded-md bg-rose-600 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-700">
            Tạo tài khoản giảng viên
        </button>
    </form>
</div>
@endsection
