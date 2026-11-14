<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Achievement;
use App\Models\PaymentMethod;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, AchievementService $achievementService): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
            'achievementsData' => $achievementService->getProfileAchievementsData($user),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Đổi email không cần xác thực lại: mã OTP chỉ gửi lúc đăng ký và khi quên mật khẩu.
        $user->fill($request->safe()->only(['name', 'email', 'bio']));

        if ($request->hasFile('avatar')) {
            $previousAvatar = $user->avatar;
            $newAvatar = $request->file('avatar')->store('avatars', 'public');

            if (! is_string($newAvatar) || $newAvatar === '') {
                return back()->withErrors([
                    'avatar' => 'Không thể lưu ảnh avatar. Vui lòng thử lại.',
                ])->withInput();
            }

            $user->avatar = $newAvatar;

            if (is_string($previousAvatar) && $previousAvatar !== '') {
                Storage::disk('public')->delete($previousAvatar);
            }
        } elseif ($request->filled('avatar_generated')) {
            $previousAvatar = $user->avatar;
            $payload = (string) $request->string('avatar_generated');
            $encodedImage = substr($payload, strpos($payload, ',') + 1);
            $image = base64_decode($encodedImage, true);
            $path = 'avatars/'.Str::uuid().'.png';

            if ($image === false || ! Storage::disk('public')->put($path, $image)) {
                return back()->withErrors([
                    'avatar_generated' => 'Không thể lưu ảnh avatar. Vui lòng thử lại.',
                ])->withInput();
            }

            $user->avatar = $path;

            if (is_string($previousAvatar) && $previousAvatar !== '') {
                Storage::disk('public')->delete($previousAvatar);
            }
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if (is_string($user->avatar) && $user->avatar !== '') {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Delete a saved payment method.
     */
    public function destroyPaymentMethod(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->user_id !== $request->user()->id) {
            abort(403);
        }

        $wasDefault = (bool) $paymentMethod->is_default;
        $paymentMethod->delete();

        if ($wasDefault) {
            $next = $request->user()->paymentMethods()->latest()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return redirect()->to(route('profile.edit').'#billing')->with('status', 'payment-method-deleted');
    }

    /**
     * Export all personal user data as JSON download.
     */
    public function exportData(Request $request): StreamedResponse
    {
        $user = $request->user()->load(['enrollments.course', 'reviews.course', 'comments.lesson']);

        $data = [
            'platform' => 'EduLearn Online E-Learning',
            'exported_at' => now()->toIso8601String(),
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'bio' => $user->bio,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
            'enrolled_courses' => $user->enrollments->map(fn ($e) => [
                'course_id' => $e->course_id,
                'course_title' => $e->course?->title,
                'enrolled_at' => $e->enrolled_at?->toIso8601String(),
                'status' => $e->status,
            ]),
            'reviews' => $user->reviews->map(fn ($r) => [
                'course_title' => $r->course?->title,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'created_at' => $r->created_at?->toIso8601String(),
            ]),
            'lesson_comments' => $user->comments->map(fn ($c) => [
                'lesson_title' => $c->lesson?->title,
                'content' => $c->content,
                'created_at' => $c->created_at?->toIso8601String(),
            ]),
            'achievements' => $user->userAchievements()->with('achievement')->get()->map(fn ($ua) => [
                'achievement' => $ua->achievement?->name,
                'tier' => $ua->achievement?->tier,
                'points' => $ua->achievement?->points,
                'progress' => $ua->progress,
                'is_unlocked' => $ua->is_unlocked,
                'unlocked_at' => $ua->unlocked_at?->toIso8601String(),
            ]),
        ];

        $filename = 'edulearn-personal-data-'.Str::slug($user->name ?: 'user').'-'.now()->format('Ymd').'.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Nhận phần thưởng cho thành tựu đã hoàn thành mục tiêu.
     */
    public function claimAchievement(Request $request, Achievement $achievement): JsonResponse
    {
        $user = $request->user();

        $record = UserAchievement::where('user_id', $user->id)
            ->where('achievement_id', $achievement->id)
            ->first();

        if (! $record || ! $record->is_unlocked) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa hoàn thành mục tiêu của thành tựu này để nhận thưởng.',
            ], 400);
        }

        if ($record->is_claimed) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã nhận phần thưởng cho thành tựu này rồi.',
            ], 400);
        }

        $record->is_claimed = true;
        $record->claimed_at = now();
        $record->save();

        return response()->json([
            'success' => true,
            'message' => "Chúc mừng! Bạn đã nhận thành công +{$achievement->points} XP.",
            'achievement' => [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'points' => $achievement->points,
                'icon' => $achievement->icon,
                'claimed_at' => $record->claimed_at->toIso8601String(),
            ],
        ]);
    }
}
