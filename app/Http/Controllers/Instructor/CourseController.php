<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\CourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Danh sách khóa học của giảng viên, có lọc theo trạng thái và tìm theo tên.
     */
    public function index(Request $request): View
    {
        $courses = $request->user()->courses()
            ->with('category')
            ->withCount(['chapters', 'lessons', 'enrollments'])
            ->withAvg('reviews', 'rating')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('keyword'), fn ($query) => $query->where('title', 'like', '%'.$request->keyword.'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusCounts = $request->user()->courses()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('instructor.courses.index', compact('courses', 'statusCounts'));
    }

    public function create(): View
    {
        return view('instructor.courses.create', [
            'course' => new Course(['level' => Course::LEVEL_BEGINNER, 'price' => 0]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Tạo khóa học mới, trạng thái ban đầu luôn là draft.
     */
    public function store(CourseRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'images']);
        $data['instructor_id'] = $request->user()->id;
        $data['slug'] = $this->makeUniqueSlug($data['title']);
        $data['status'] = Course::STATUS_DRAFT;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course = Course::create($data);
        $this->storeGalleryImages($course, $request->file('images', []));

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Đã tạo khóa học (bản nháp). Hãy thêm chương và bài học cho khóa học.');
    }

    /**
     * Trang quản lý nội dung khóa học: chương, bài học và quiz.
     */
    public function show(Course $course): View
    {
        Gate::authorize('view', $course);

        $course->load([
            'category',
            'chapters.lessons',
            'quizzes' => fn ($query) => $query->with('chapter')->withCount('questions'),
        ])->loadCount(['lessons', 'enrollments']);

        return view('instructor.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        Gate::authorize('update', $course);

        $course->load('images');

        return view('instructor.courses.edit', [
            'course' => $course,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('update', $course);

        $data = $request->safe()->except(['thumbnail', 'images']);

        // Khóa đã publish giữ nguyên slug để không làm hỏng link công khai.
        if ($data['title'] !== $course->title && ! $course->isPublished()) {
            $data['slug'] = $this->makeUniqueSlug($data['title'], $course->id);
        }

        if ($request->hasFile('thumbnail')) {
            $this->deletePublicFile($course->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course->update($data);
        $this->storeGalleryImages($course, $request->file('images', []));

        return redirect()->route('instructor.courses.edit', $course)
            ->with('success', 'Đã cập nhật thông tin khóa học.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('delete', $course);

        if ($course->enrollments()->exists()) {
            return back()->with('error', 'Không thể xóa khóa học đã có học viên ghi danh.');
        }

        $course->load(['images', 'lessons']);
        $this->deletePublicFile($course->thumbnail);
        $course->images->each(fn (CourseImage $image) => $this->deletePublicFile($image->image_path));
        $course->lessons->each(fn ($lesson) => $this->deletePublicFile($lesson->attachment_path));

        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Đã xóa khóa học.');
    }

    /**
     * Gửi khóa học cho admin duyệt: draft/rejected -> pending.
     */
    public function submit(Course $course): RedirectResponse
    {
        Gate::authorize('submit', $course);

        if (! $course->lessons()->exists()) {
            return back()->with('error', 'Khóa học cần có ít nhất 1 chương và 1 bài học trước khi gửi duyệt.');
        }

        $course->update([
            'status' => Course::STATUS_PENDING,
            'reject_reason' => null,
        ]);

        return back()->with('success', 'Đã gửi khóa học cho quản trị viên duyệt.');
    }

    /**
     * Rút lại yêu cầu duyệt: pending -> draft để tiếp tục chỉnh sửa.
     */
    public function withdraw(Course $course): RedirectResponse
    {
        Gate::authorize('withdraw', $course);

        $course->update(['status' => Course::STATUS_DRAFT]);

        return back()->with('success', 'Đã rút lại yêu cầu duyệt. Khóa học trở về bản nháp.');
    }

    public function destroyImage(Course $course, CourseImage $image): RedirectResponse
    {
        Gate::authorize('update', $course);

        $this->deletePublicFile($image->image_path);
        $image->delete();

        return back()->with('success', 'Đã xóa ảnh khỏi thư viện.');
    }

    /**
     * @param  array<int, UploadedFile>  $files
     */
    private function storeGalleryImages(Course $course, array $files): void
    {
        foreach ($files as $file) {
            $course->images()->create([
                'image_path' => $file->store('courses/gallery', 'public'),
            ]);
        }
    }

    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'khoa-hoc';
        $slug = $baseSlug;
        $suffix = 2;

        while (Course::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
