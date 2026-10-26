<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin mẫu
        User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Admin System',
            'password' => '12345678',
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        // Giảng viên mẫu
        User::updateOrCreate([
            'email' => 'instructor@gmail.com',
        ], [
            'name' => 'Nguyễn Văn Giảng Viên',
            'password' => '12345678',
            'role' => User::ROLE_INSTRUCTOR,
            'bio' => 'Giảng viên cấp cao với hơn 10 năm kinh nghiệm trong lĩnh vực Công nghệ thông tin và Phát triển Web.',
            'status' => User::STATUS_ACTIVE,
        ]);

        // Học viên mẫu
        User::updateOrCreate([
            'email' => 'student@gmail.com',
        ], [
            'name' => 'Trần Học Viên',
            'password' => '12345678',
            'role' => User::ROLE_STUDENT,
            'status' => User::STATUS_ACTIVE,
        ]);
    }
}
