<x-admin-layout title="Khoá học">
    <div class="card">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-4 lg:flex-row lg:items-center lg:justify-between">
            <x-search-bar placeholder="Tìm theo tên khoá học..." class="flex-1">
                <select name="category_id" class="form-input mt-0 sm:w-48">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request()->integer('category_id') === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-input mt-0 sm:w-40">
                    <option value="">Mọi trạng thái</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </x-search-bar>
            @can('create', App\Models\Course::class)
                <a href="{{ route('admin.courses.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Thêm khoá học</a>
            @endcan
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>Khoá học</th><th>Danh mục</th><th>Giảng viên</th><th class="text-center">Bài học</th><th class="text-center">Học viên</th><th>Học phí</th><th>Trạng thái</th><th class="text-right">Thao tác</th></tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <x-course-thumbnail :course="$course" class="h-12 w-20 shrink-0 rounded-md" />
                                    <div class="min-w-0">
                                        <p class="max-w-xs truncate font-medium text-gray-900">{{ $course->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $course->level->label() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $course->category?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap">{{ $course->instructor->name }}</td>
                            <td class="text-center">{{ $course->lessons_count }}</td>
                            <td class="text-center">{{ $course->enrollments_count }}</td>
                            <td class="whitespace-nowrap">{{ $course->price_label }}</td>
                            <td><span class="badge {{ $course->status->badgeClasses() }}">{{ $course->status->label() }}</span></td>
                            <td class="whitespace-nowrap text-right">
                                <a href="{{ route('courses.show', $course) }}" target="_blank" class="btn-ghost btn-sm" title="Xem trang khoá học"><x-icon name="eye" class="h-4 w-4" /></a>
                                <a href="{{ route('admin.courses.lessons.index', $course) }}" class="btn-ghost btn-sm" title="Bài học"><x-icon name="list" class="h-4 w-4" /></a>
                                <a href="{{ route('admin.courses.edit', $course) }}" class="btn-ghost btn-sm" title="Sửa"><x-icon name="pencil" class="h-4 w-4" /></a>
                                <x-delete-button :action="route('admin.courses.destroy', $course)" confirm="Xoá khoá học này cùng toàn bộ bài học, ghi danh và đánh giá?" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state icon="book" title="Không có khoá học nào" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $courses->links() }}</div>
    </div>
</x-admin-layout>
