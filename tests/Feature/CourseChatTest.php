<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Services\CourseAdvisor;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CourseChatTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_URL = 'generativelanguage.googleapis.com/v1beta/models/gemini-test:generateContent';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config(['services.gemini.key' => 'test-gemini-key', 'services.gemini.model' => 'gemini-test']);
        Http::preventStrayRequests();
    }

    public function test_widget_is_shown_only_when_gemini_key_is_configured(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('id="course-chat"', false);

        config(['services.gemini.key' => null]);

        $this->get(route('home'))->assertOk()->assertDontSee('id="course-chat"', false);
    }

    public function test_chat_sends_published_courses_to_gemini_and_returns_reply(): void
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR, 'name' => 'Nguyễn Văn Giảng']);
        $category = Category::create(['name' => 'Lập trình Web', 'slug' => 'lap-trinh-web']);
        $published = $this->createCourse($instructor, $category, 'Laravel căn bản', Course::STATUS_PUBLISHED, [
            'price' => 499000,
            'description' => '<p>Học Laravel từ <strong>con số 0</strong> &amp; làm dự án thật.</p>',
        ]);
        $this->createCourse($instructor, $category, 'Khóa nháp bí mật', Course::STATUS_DRAFT);

        Http::fake([self::GEMINI_URL => $this->geminiReply('Bạn nên học [Laravel căn bản](/courses/laravel-can-ban) nhé.')]);

        $this->postJson(route('course-chat.send'), [
            'message' => 'Mình mới học web thì nên học khóa nào?',
            'history' => [
                ['role' => 'assistant', 'content' => 'Lời chào chỉ hiển thị trên trình duyệt'],
                ['role' => 'user', 'content' => 'Chào bạn'],
                ['role' => 'assistant', 'content' => 'Chào bạn, mình giúp gì được?'],
            ],
        ])
            ->assertOk()
            ->assertExactJson(['reply' => 'Bạn nên học [Laravel căn bản](/courses/laravel-can-ban) nhé.']);

        Http::assertSent(function (Request $request) use ($published) {
            $system = $request['systemInstruction']['parts'][0]['text'];
            $contents = $request['contents'];

            return $request->hasHeader('x-goog-api-key', 'test-gemini-key')
                && str_contains($system, '[Laravel căn bản](/courses/'.$published->slug.')')
                && str_contains($system, 'Giá: 499.000 ₫')
                && str_contains($system, 'Giảng viên: Nguyễn Văn Giảng')
                && str_contains($system, 'Mô tả: Học Laravel từ con số 0 & làm dự án thật.')
                && ! str_contains($system, 'Khóa nháp bí mật')
                && array_column($contents, 'role') === ['user', 'model', 'user']
                && $contents[2]['parts'][0]['text'] === 'Mình mới học web thì nên học khóa nào?';
        });
    }

    public function test_consecutive_messages_from_the_same_side_are_merged(): void
    {
        Http::fake([self::GEMINI_URL => $this->geminiReply('Ok')]);

        $this->postJson(route('course-chat.send'), [
            'message' => 'Còn khóa nào về AI không?',
            'history' => [
                ['role' => 'user', 'content' => 'Chào bạn'],
                ['role' => 'assistant', 'content' => 'Chào bạn, mình giúp gì được?'],
                ['role' => 'user', 'content' => 'Câu hỏi bị lỗi lúc trước'],
            ],
        ])->assertOk();

        Http::assertSent(fn (Request $request) => array_column($request['contents'], 'role') === ['user', 'model', 'user']
            && $request['contents'][2]['parts'][0]['text'] === "Câu hỏi bị lỗi lúc trước\n\nCòn khóa nào về AI không?");
    }

    public function test_ai_is_told_to_refuse_questions_outside_the_website(): void
    {
        $instruction = app(CourseAdvisor::class)->systemInstruction();

        $this->assertStringContainsString('PHẠM VI (BẮT BUỘC)', $instruction);
        $this->assertStringContainsString('Mình chỉ hỗ trợ tư vấn khóa học trên '.config('app.name').' thôi nha.', $instruction);
    }

    public function test_course_catalog_is_cached_between_messages(): void
    {
        Http::fake([self::GEMINI_URL => $this->geminiReply('Ok')]);

        $this->postJson(route('course-chat.send'), ['message' => 'Xin chào'])->assertOk();

        $this->assertTrue(Cache::has(CourseAdvisor::CATALOG_CACHE_KEY));
    }

    public function test_message_is_required_and_limited_to_500_characters(): void
    {
        $this->postJson(route('course-chat.send'), ['message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');

        $this->postJson(route('course-chat.send'), ['message' => str_repeat('a', 501)])
            ->assertUnprocessable()
            ->assertJsonPath('errors.message.0', 'Câu hỏi tối đa 500 ký tự.');

        Http::assertNothingSent();
    }

    public function test_chat_returns_friendly_message_when_not_configured(): void
    {
        config(['services.gemini.key' => null]);

        $this->postJson(route('course-chat.send'), ['message' => 'Có khóa nào miễn phí không?'])
            ->assertServiceUnavailable()
            ->assertJsonPath('message', 'Chat AI hiện chưa được bật. Bạn có thể xem các khóa học ở trang Khóa học nhé.');

        Http::assertNothingSent();
    }

    public function test_chat_returns_friendly_message_when_gemini_free_quota_is_used_up(): void
    {
        Http::fake([self::GEMINI_URL => Http::response(['error' => ['code' => 429, 'status' => 'RESOURCE_EXHAUSTED']], 429)]);

        $this->postJson(route('course-chat.send'), ['message' => 'Có khóa nào miễn phí không?'])
            ->assertServiceUnavailable()
            ->assertJsonPath('message', 'AI đang quá tải hoặc đã hết lượt miễn phí hôm nay. Bạn thử lại sau ít phút nhé.');
    }

    public function test_chat_returns_friendly_message_when_gemini_returns_no_text(): void
    {
        Http::fake([self::GEMINI_URL => Http::response(['promptFeedback' => ['blockReason' => 'SAFETY']])]);

        $this->postJson(route('course-chat.send'), ['message' => 'Xin chào'])
            ->assertServiceUnavailable()
            ->assertJsonPath('message', 'AI đang gặp sự cố, bạn thử lại sau ít phút nhé.');
    }

    public function test_each_visitor_can_send_five_messages_per_minute(): void
    {
        Http::fake([self::GEMINI_URL => $this->geminiReply('Ok')]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson(route('course-chat.send'), ['message' => 'Câu hỏi '.$attempt])->assertOk();
        }

        $this->postJson(route('course-chat.send'), ['message' => 'Câu hỏi 6'])
            ->assertTooManyRequests()
            ->assertJsonPath('message', 'Bạn gửi hơi nhanh, đợi khoảng 1 phút rồi hỏi tiếp nhé.');

        Http::assertSentCount(5);
    }

    private function geminiReply(string $text): PromiseInterface
    {
        return Http::response([
            'candidates' => [[
                'content' => ['role' => 'model', 'parts' => [['text' => $text]]],
                'finishReason' => 'STOP',
            ]],
        ]);
    }

    private function createCourse(User $instructor, Category $category, string $title, string $status, array $attributes = []): Course
    {
        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => str($title)->slug().'-'.fake()->unique()->randomNumber(5),
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
            'status' => $status,
            ...$attributes,
        ]);
    }
}
