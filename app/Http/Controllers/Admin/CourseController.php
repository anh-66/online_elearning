<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $courses = Course::query()
            ->with(['category', 'instructor'])
            ->withCount(['lessons', 'enrollments'])
            ->when(! $user->isAdmin(), fn ($query) => $query->where('instructor_id', $user->id))
            ->filter($request->only('q'))
            ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->integer('category_id'), fn ($query, int $categoryId) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'statuses' => CourseStatus::cases(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Course::class);

        return view('admin.courses.form', [
            'course' => new Course(['level' => CourseLevel::Beginner, 'status' => CourseStatus::Draft, 'price' => 0]),
            ...$this->formOptions(),
        ]);
    }

    public function store(CourseRequest $request): RedirectResponse
    {
        Gate::authorize('create', Course::class);

        $course = DB::transaction(function () use ($request): Course {
            $course = Course::create([
                ...$this->courseAttributes($request),
                'instructor_id' => $request->user()->isAdmin() ? $request->integer('instructor_id') : $request->user()->id,
                'slug' => UniqueSlug::for(Course::class, $request->string('title')),
                'thumbnail' => $request->file('thumbnail')?->store('courses/thumbnails', 'public'),
            ]);

            $this->storeGalleryImages($request, $course);

            return $course;
        });

        return to_route('admin.courses.edit', $course)->with('success', 'Đã tạo khoá học. Bạn có thể thêm bài học ngay bây giờ.');
    }

    public function edit(Course $course): View
    {
        Gate::authorize('manage', $course);

        $course->load('images')->loadCount('lessons');

        return view('admin.courses.form', [
            'course' => $course,
            ...$this->formOptions(),
        ]);
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('manage', $course);

        DB::transaction(function () use ($request, $course): void {
            $attributes = [
                ...$this->courseAttributes($request, $course),
                'slug' => UniqueSlug::for(Course::class, $request->string('title'), $course->id),
            ];

            if ($request->user()->isAdmin()) {
                $attributes['instructor_id'] = $request->integer('instructor_id');
            }

            if ($request->hasFile('thumbnail')) {
                if ($course->thumbnail) {
                    Storage::disk('public')->delete($course->thumbnail);
                }

                $attributes['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            }

            $course->update($attributes);

            $imagesToDelete = $course->images()->whereIn('id', $request->input('delete_images', []))->get();
            Storage::disk('public')->delete($imagesToDelete->pluck('path')->all());
            $course->images()->whereKey($imagesToDelete->modelKeys())->delete();

            $this->storeGalleryImages($request, $course);
        });

        return to_route('admin.courses.edit', $course)->with('success', 'Đã lưu thay đổi khoá học.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('manage', $course);

        Storage::disk('public')->delete(array_filter([$course->thumbnail, ...$course->images->pluck('path')]));
        $course->delete();

        return to_route('admin.courses.index')->with('success', 'Đã xoá khoá học.');
    }

    /**
     * @return array<string, mixed>
     */
    private function courseAttributes(CourseRequest $request, ?Course $course = null): array
    {
        $status = CourseStatus::from($request->string('status'));

        return [
            ...$request->safe()->only(['title', 'category_id', 'summary', 'description', 'price', 'level']),
            'status' => $status,
            'published_at' => $status === CourseStatus::Published ? ($course?->published_at ?? now()) : null,
        ];
    }

    private function storeGalleryImages(CourseRequest $request, Course $course): void
    {
        $nextSortOrder = (int) $course->images()->max('sort_order') + 1;

        foreach ($request->file('images', []) as $image) {
            $course->images()->create([
                'path' => $image->store("courses/{$course->id}/gallery", 'public'),
                'sort_order' => $nextSortOrder++,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'instructors' => User::whereIn('role', [UserRole::Admin, UserRole::Teacher])->orderBy('name')->get(['id', 'name']),
            'levels' => CourseLevel::cases(),
            'statuses' => CourseStatus::cases(),
        ];
    }
}
