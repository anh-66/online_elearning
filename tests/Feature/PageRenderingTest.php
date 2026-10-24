<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_public_pages_render_for_guests(): void
    {
        $course = Course::published()->first();
        $announcement = Announcement::published()->first();

        foreach ([
            route('home'),
            route('courses.index'),
            route('courses.index', ['q' => 'Laravel', 'level' => 'beginner', 'sort' => 'popular', 'page' => 2]),
            route('courses.show', $course),
            route('announcements.index'),
            route('announcements.show', $announcement),
            route('login'),
            route('register'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_student_pages_render(): void
    {
        $student = User::where('email', 'student@gmail.com')->firstOrFail();
        $course = $student->enrolledCourses()->first();

        $this->actingAs($student);

        $this->get(route('dashboard'))->assertOk()->assertSee($course->title);
        $this->get(route('learn.show', $course))->assertOk();
        $this->get(route('profile.edit'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $course = Course::first();
        $lesson = $course->lessons()->first();

        $this->actingAs($admin);

        foreach ([
            route('admin.dashboard'),
            route('admin.courses.index', ['q' => 'a', 'status' => 'published']),
            route('admin.courses.create'),
            route('admin.courses.edit', $course),
            route('admin.courses.lessons.index', $course),
            route('admin.courses.lessons.create', $course),
            route('admin.courses.lessons.edit', [$course, $lesson]),
            route('admin.categories.index'),
            route('admin.categories.create'),
            route('admin.users.index', ['role' => 'teacher']),
            route('admin.users.create'),
            route('admin.users.edit', $admin),
            route('admin.announcements.index'),
            route('admin.announcements.create'),
            route('admin.announcements.edit', Announcement::first()),
            route('admin.reviews.index'),
            route('admin.enrollments.index'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_teacher_sees_only_training_pages(): void
    {
        $teacher = User::where('email', 'teacher@gmail.com')->firstOrFail();

        $this->actingAs($teacher);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.courses.index'))->assertOk();
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.categories.index'))->assertForbidden();
        $this->get(route('admin.announcements.index'))->assertForbidden();
    }
}
