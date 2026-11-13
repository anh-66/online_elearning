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
     * Những chỗ trong database có lưu đường dẫn ảnh: loại => [model, cột, cột tên, tiền tố hiển thị].
     * Ảnh thư viện khóa học (course_image) là cả một dòng riêng nên tháo ảnh = xóa dòng đó.
     *
     * @var array<string, array{model: class-string<Model>, column: string, name: ?string, prefix: string}>
     */
    public const PLACES = [
        'banner' => ['model' => Banner::class, 'column' => 'image_path', 'name' => 'title', 'prefix' => 'Banner'],
        'course' => ['model' => Course::class, 'column' => 'thumbnail', 'name' => 'title', 'prefix' => 'Ảnh bìa'],
        'course_image' => ['model' => CourseImage::class, 'column' => 'image_path', 'name' => null, 'prefix' => 'Thư viện ảnh'],
        'user' => ['model' => User::class, 'column' => 'avatar', 'name' => 'name', 'prefix' => 'Avatar'],
        'lesson' => ['model' => Lesson::class, 'column' => 'attachment_path', 'name' => 'title', 'prefix' => 'Đính kèm bài học'],
        'feedback' => ['model' => Feedback::class, 'column' => 'attachment', 'name' => 'title', 'prefix' => 'Đính kèm phản hồi'],
    ];

    /**
     * Đường dẫn (đã bỏ đuôi) => danh sách chỗ đang dùng ảnh đó (tên hiển thị).
     *
     * @return array<string, array<int, string>>
     */
    public function usage(): array
    {
        return array_map(fn (array $places): array => array_column($places, 'label'), $this->places());
    }

    /**
     * Đường dẫn (đã bỏ đuôi) => danh sách chỗ đang dùng ảnh đó, kèm loại và id để tháo hoặc gắn lại.
     *
     * @return array<string, array<int, array{type: string, id: int, label: string, course_id?: int}>>
     */
    public function places(): array
    {
        $places = [];

        foreach (self::PLACES as $type => $config) {
            $query = $config['model']::query()->whereNotNull($config['column']);

            if ($type === 'course_image') {
                $query->with('course:id,title');
            }

            foreach ($query->get() as $record) {
                $path = $record->getAttribute($config['column']);

                if (! is_string($path) || $path === '' || preg_match('#^https?://#i', $path)) {
                    continue;
                }

                $places[self::key($path)][] = $this->describe($type, $record);
            }
        }

        return $places;
    }

    /**
     * Tháo ảnh khỏi một chỗ (để trống), ảnh vẫn giữ trên Cloudinary.
     * Trả về null nếu chỗ đó không còn dùng ảnh này nữa.
     *
     * @return array{type: string, id: int, label: string, course_id?: int}|null
     *
     * @throws InvalidArgumentException khi loại không hợp lệ
     */
    public function unlink(string $type, int $id, string $path): ?array
    {
        $config = self::PLACES[$type] ?? throw new InvalidArgumentException('Loại không hợp lệ: '.$type);
        $record = $config['model']::query()->find($id);

        if ($record === null || ! is_string($record->getAttribute($config['column']))
            || self::key($record->getAttribute($config['column'])) !== self::key($path)) {
            return null;
        }

        $place = $this->describe($type, $record);

        if ($type === 'course_image') {
            $record->delete();
        } else {
            $record->forceFill([$config['column'] => null])->save();
        }

        return $place;
    }

    /**
     * Tháo ảnh khỏi mọi chỗ đang dùng. Trả về danh sách chỗ đã tháo để sau này khôi phục.
     * Khi xóa nhiều ảnh cùng lúc, truyền sẵn kết quả places() để không phải đọc lại database cho từng ảnh.
     *
     * @param  array<string, array<int, array{type: string, id: int, label: string, course_id?: int}>>|null  $places
     * @return array<int, array{type: string, id: int, label: string, course_id?: int}>
     */
    public function unlinkAll(string $path, ?array $places = null): array
    {
        $unlinked = [];

        foreach (($places ?? $this->places())[self::key($path)] ?? [] as $place) {
            if ($done = $this->unlink($place['type'], $place['id'], $path)) {
                $unlinked[] = $done;
            }
        }

        return $unlinked;
    }

    /**
     * Gắn lại ảnh vào chỗ cũ khi khôi phục từ thùng rác. Không ghi đè nếu chỗ đó đã có ảnh khác.
     *
     * @param  array{type?: string, id?: int, course_id?: int}  $place
     */
    public function relink(array $place, string $path): bool
    {
        $type = (string) ($place['type'] ?? '');
        $config = self::PLACES[$type] ?? null;

        if ($config === null) {
            return false;
        }

        if ($type === 'course_image') {
            $courseId = (int) ($place['course_id'] ?? 0);

            if (! Course::query()->whereKey($courseId)->exists()) {
                return false;
            }

            CourseImage::query()->firstOrCreate(['course_id' => $courseId, 'image_path' => ltrim($path, '/')]);

            return true;
        }

        $record = $config['model']::query()->find((int) ($place['id'] ?? 0));

        if ($record === null || filled($record->getAttribute($config['column']))) {
            return false;
        }

        $record->forceFill([$config['column'] => ltrim($path, '/')])->save();

        return true;
    }

    /**
     * @return array{type: string, id: int, label: string, course_id?: int}
     */
    private function describe(string $type, Model $record): array
    {
        $config = self::PLACES[$type];

        if ($type === 'course_image') {
            /** @var CourseImage $record */
            return [
                'type' => $type,
                'id' => (int) $record->getKey(),
                'label' => $config['prefix'].': '.($record->course?->title ?? 'khóa học #'.$record->course_id),
                'course_id' => (int) $record->course_id,
            ];
        }

        return [
            'type' => $type,
            'id' => (int) $record->getKey(),
            'label' => $config['prefix'].': '.$record->getAttribute($config['name']),
        ];
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
