@extends('layouts.instructor')

@section('title', 'Quản lý học viên')
@section('page_title', 'Học viên theo khóa học')

@section('content')
<div class="space-y-6">
    <form method="GET" action="{{ route('instructor.students.index') }}" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-3">
        <select name="course_id" class="md:w-80 rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Tất cả khóa học</option>
            @foreach($courses as $courseOption)
                <option value="{{ $courseOption->id }}" @selected((string) request('course_id') === (string) $courseOption->id)>{{ $courseOption->title }}</option>
            @endforeach
        </select>
        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tên hoặc email học viên..."
               class="flex-1 rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Lọc</button>
        @if(request()->hasAny(['course_id', 'keyword']))
            <a href="{{ route('instructor.students.index') }}" class="px-5 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 text-center">Xóa lọc</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">
                {{ $selectedCourse ? 'Học viên của khóa: '.$selectedCourse->title : 'Tất cả học viên' }}
                <span class="text-sm font-normal text-gray-500">({{ $enrollments->total() }} lượt ghi danh)</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Học viên</th>
                        <th class="px-4 py-3 text-left font-semibold">Khóa học</th>
                        <th class="px-4 py-3 text-left font-semibold">Ngày ghi danh</th>
                        <th class="px-4 py-3 text-left font-semibold">Hình thức</th>
                        <th class="px-6 py-3 text-left font-semibold w-64">Tiến độ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($enrollments as $enrollment)
                        @php $studentProgress = $progress[$enrollment->id] ?? ['completed' => 0, 'total' => 0, 'percent' => 0]; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    @if($enrollment->user->avatar)
                                        <img src="{{ asset('storage/'.$enrollment->user->avatar) }}" alt="{{ $enrollment->user->name }}" class="w-9 h-9 rounded-full object-cover">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm flex items-center justify-center">
                                            {{ mb_strtoupper(mb_substr($enrollment->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ $enrollment->user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $enrollment->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('instructor.courses.show', $enrollment->course) }}" class="text-gray-700 hover:text-indigo-600 line-clamp-2">{{ $enrollment->course->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $enrollment->enrolled_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                @if($enrollment->status === \App\Models\Enrollment::STATUS_PAID)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700">Trả phí</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Miễn phí</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $studentProgress['percent'] >= 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $studentProgress['percent'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 w-10 text-right">{{ $studentProgress['percent'] }}%</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">{{ $studentProgress['completed'] }}/{{ $studentProgress['total'] }} bài học</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có học viên nào ghi danh.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $enrollments->links() }}</div>
</div>
@endsection
