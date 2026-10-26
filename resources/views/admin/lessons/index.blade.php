<x-admin-layout :title="'Bài học: '.$course->title">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.courses.edit', $course) }}" class="flex items-center gap-1 text-sm text-gray-500 hover:text-brand-700"><x-icon name="chevron-left" class="h-4 w-4" /> Về khoá học</a>
        <a href="{{ route('admin.courses.lessons.create', $course) }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Thêm bài học</a>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th class="w-16 text-center">Thứ tự</th><th>Tên bài học</th><th>Video</th><th class="text-center">Thời lượng</th><th class="text-center">Học thử</th><th class="text-right">Thao tác</th></tr>
                </thead>
                <tbody>
                    @forelse ($lessons as $lesson)
                        <tr>
                            <td class="text-center font-semibold text-brand-700">{{ $lesson->position }}</td>
                            <td class="font-medium text-gray-900">{{ $lesson->title }}</td>
                            <td>
                                @if ($lesson->video_url)
                                    <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener" class="flex items-center gap-1 text-brand-700"><x-icon name="play" class="h-4 w-4" /> Có video</a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $lesson->duration_minutes }} phút</td>
                            <td class="text-center">
                                @if ($lesson->is_preview)
                                    <span class="badge bg-amber-100 text-amber-700">Có</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-right">
                                <a href="{{ route('learn.show', [$course, $lesson]) }}" target="_blank" class="btn-ghost btn-sm" title="Xem"><x-icon name="eye" class="h-4 w-4" /></a>
                                <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" class="btn-ghost btn-sm" title="Sửa"><x-icon name="pencil" class="h-4 w-4" /></a>
                                <x-delete-button :action="route('admin.courses.lessons.destroy', [$course, $lesson])" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="book" title="Khoá học chưa có bài học" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $lessons->links() }}</div>
    </div>
</x-admin-layout>
