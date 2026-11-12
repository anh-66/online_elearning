<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <img src="{{ $course->thumbnail ? Storage::disk('public')->url($course->thumbnail) : 'https://via.placeholder.com/800x400' }}" alt="" class="w-full h-64 object-cover rounded-lg mb-6">
                
                <h1 class="text-3xl font-bold mb-4">{{ $course->title }}</h1>
                <p class="text-gray-600 mb-4">Giảng viên: {{ $course->instructor->name ?? 'Đang cập nhật' }}</p>
                <p class="text-gray-600 mb-4">Giá: {{ number_format($course->price) }} VNĐ</p>
                <p class="text-gray-600 mb-6">Trình độ: {{ $course->level }}</p>
                
                <div class="prose max-w-none mb-8">
                    {!! $course->description !!}
                </div>

                <!-- Nút Đăng ký (Phần của Thành viên 3) -->
                @auth
                    @if(!auth()->user()->enrolledCourses->contains($course->id))
                        <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-700 transition">
                                Đăng ký học ngay
                            </button>
                        </form>
                    @else
                        <a href="{{ route('learning.player', $course->slug) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 transition">
                            Vào học
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg font-bold">
                        Đăng nhập để đăng ký
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>