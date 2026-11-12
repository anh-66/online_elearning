<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Course;
use App\Models\CourseImage;
use App\Models\Feedback;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Cho admin biết ảnh nào trên Cloudinary đang được dùng ở đâu, và gắn lại một ảnh có sẵn
 * vào banner, ảnh bìa khóa học hoặc avatar (chỉ sửa database, không upload hay xóa file).
 */
class CloudinaryImageAssigner
{
    /**
     * Những chỗ có thể gắn ảnh: loại => [tên hiển thị, model, cột lưu đường dẫn].
     *
     * @var array<string, array{label: string, model: class-string<Model>, column: string}>
     */
    public const TYPES = [
        'banner' => ['label' => 'Banner trang chủ', 'model' => Banner::class, 'column' => 'image_path'],
        'course' => ['label' => 'Ảnh bìa khóa học', 'model' => Course::class, 'column' => 'thumbnail'],
        'user' => ['label' => 'Ảnh đại diện', 'model' => User::class, 'column' => 'avatar'],
    ];

    /**
     * Thư mục mà website biết chắc ảnh trong đó được lưu ở cột nào trong database.
     * Ảnh trong bài viết (editor) nằm trong nội dung HTML nên không xét.
     *
     * @var array<int, string>
     */
    public const TRACKED_FOLDERS = ['avatars', 'banners', 'courses/thumbnails', 'courses/gallery', 'feedbacks', 'lessons/attachments'];

    /**
     * Loại gợi ý sẵn theo thư mục của ảnh.
     *
     * @var array<string, string>
     */
    public const FOLDER_DEFAULT_TYPES = [
        'banners' => 'banner',
        'courses/thumbnails' => 'course',
        'courses/gallery' => 'course',
        'avatars' => 'user',
    ];

    /**
     * Bỏ đuôi file để so khớp: Cloudinary có thể trả định dạng khác đuôi lưu trong database (jpeg/jpg).
     */
    public static function key(string $path): string
    {
        return (string) preg_replace('/\.[^.\/]+$/', '', ltrim($path, '/'));
    }

    /**
     * Đường dẫn (đã bỏ đuôi) => danh sách chỗ đang dùng ảnh đó.
     *
     * @return array<string, array<int, string>>
     */
    public function usage(): array
    {
        $usage = [];
        $add = function (?string $path, string $label) use (&$usage): void {
            if (is_string($path) && $path !== '' && ! preg_match('#^https?://#i', $path)) {
                $usage[self::key($path)][] = $label;
            }
        };

        Banner::query()->whereNotNull('image_path')->get(['id', 'title', 'image_path'])
            ->each(fn (Banner $banner) => $add($banner->image_path, 'Banner: '.$banner->title));
        Course::query()->whereNotNull('thumbnail')->get(['id', 'title', 'thumbnail'])
            ->each(fn (Course $course) => $add($course->thumbnail, 'Ảnh bìa: '.$course->title));
        CourseImage::query()->with('course:id,title')->get()
            ->each(fn (CourseImage $image) => $add($image->image_path, 'Thư viện ảnh: '.($image->course?->title ?? 'khóa học #'.$image->course_id)));
        User::query()->whereNotNull('avatar')->get(['id', 'name', 'avatar'])
            ->each(fn (User $user) => $add($user->avatar, 'Avatar: '.$user->name));
        Lesson::query()->whereNotNull('attachment_path')->get(['id', 'title', 'attachment_path'])
            ->each(fn (Lesson $lesson) => $add($lesson->attachment_path, 'Đính kèm bài học: '.$lesson->title));
        Feedback::query()->whereNotNull('attachment')->get(['id', 'title', 'attachment'])
            ->each(fn (Feedback $feedback) => $add($feedback->attachment, 'Đính kèm phản hồi: '.$feedback->title));

        return $usage;
    }

    /**
     * Danh sách banner/khóa học/người dùng để chọn khi gắn ảnh. Chỗ đang trống ảnh
     * hoặc trỏ tới file không còn trên Cloudinary được xếp lên đầu.
     *
     * @param  array<int, string>  $existingPaths  đường dẫn các ảnh đang có trên Cloudinary
     * @return array<string, array{label: string, items: array<int, array{id: int, label: string, status: string}>}>
     */
    public function targets(array $existingPaths): array
    {
        $existing = array_flip(array_map(self::key(...), $existingPaths));

        $status = function (?string $path) use ($existing): string {
            if (! is_string($path) || trim($path) === '') {
                return 'empty';
            }

            if (preg_match('#^https?://#i', $path)) {
                return 'ok';
            }

            return isset($existing[self::key($path)]) ? 'ok' : 'broken';
        };

        $build = fn ($records, callable $label, string $pathColumn): array => $records
            ->map(fn (Model $record): array => [
                'id' => (int) $record->getKey(),
                'label' => (string) $label($record),
                'status' => $status($record->getAttribute($pathColumn)),
            ])
            ->sortBy(fn (array $item): string => ($item['status'] === 'ok' ? '1' : '0').mb_strtolower($item['label']))
            ->values()
            ->all();

        return [
            'banner' => [
                'label' => self::TYPES['banner']['label'],
                'items' => $build(Banner::query()->get(['id', 'title', 'image_path']), fn (Banner $banner) => $banner->title, 'image_path'),
            ],
            'course' => [
                'label' => self::TYPES['course']['label'],
                'items' => $build(Course::query()->get(['id', 'title', 'thumbnail']), fn (Course $course) => $course->title, 'thumbnail'),
            ],
            'user' => [
                'label' => self::TYPES['user']['label'],
                'items' => $build(User::query()->get(['id', 'name', 'email', 'avatar']), fn (User $user) => $user->name.' ('.$user->email.')', 'avatar'),
            ],
        ];
    }

    /**
     * Gắn ảnh vào chỗ đã chọn và trả về câu mô tả để báo cho admin.
     *
     * @throws InvalidArgumentException khi loại không hợp lệ
     */
    public function assign(string $type, int $id, string $path): string
    {
        $config = self::TYPES[$type] ?? throw new InvalidArgumentException('Loại không hợp lệ: '.$type);

        /** @var Model $record */
        $record = $config['model']::query()->findOrFail($id);
        $record->forceFill([$config['column'] => ltrim($path, '/')])->save();

        $name = match ($type) {
            'banner' => 'banner “'.$record->getAttribute('title').'”',
            'course' => 'ảnh bìa khóa học “'.$record->getAttribute('title').'”',
            'user' => 'ảnh đại diện của '.$record->getAttribute('name'),
        };

        return 'Đã gắn ảnh cho '.$name.'.';
    }
}
