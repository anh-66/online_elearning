<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    /**
     * Hiển thị bảng xếp hạng học viên theo điểm EXP thành tựu tích lũy.
     */
    public function index(Request $request): View
    {
        $timeframe = $request->query('timeframe', 'all');
        if (! in_array($timeframe, ['all', 'month', 'week'], true)) {
            $timeframe = 'all';
        }

        $query = User::where('role', User::ROLE_STUDENT)
            ->where('status', User::STATUS_ACTIVE)
            ->select('users.*')
            ->selectRaw('COALESCE(SUM(CASE WHEN user_achievements.is_claimed = 1 THEN achievements.points ELSE (CASE WHEN user_achievements.is_unlocked = 1 THEN achievements.points ELSE 0 END) END), 0) as total_exp')
            ->selectRaw('COUNT(CASE WHEN user_achievements.is_unlocked = 1 THEN 1 END) as achievements_count')
            ->leftJoin('user_achievements', function ($join) use ($timeframe) {
                $join->on('users.id', '=', 'user_achievements.user_id');
                if ($timeframe === 'month') {
                    $join->where('user_achievements.unlocked_at', '>=', now()->startOfMonth());
                } elseif ($timeframe === 'week') {
                    $join->where('user_achievements.unlocked_at', '>=', now()->startOfWeek());
                }
            })
            ->leftJoin('achievements', function ($join) {
                $join->on('user_achievements.achievement_id', '=', 'achievements.id')
                    ->where('achievements.is_active', true);
            })
            ->groupBy('users.id')
            ->orderByDesc('total_exp')
            ->orderByDesc('achievements_count')
            ->orderBy('users.id');

        $topStudents = $query->take(10)->get();

        $rank1 = $topStudents->get(0);
        $rank2 = $topStudents->get(1);
        $rank3 = $topStudents->get(2);
        $remaining = $topStudents->slice(3)->values();

        // Xác định vị trí của học viên hiện tại nếu đang đăng nhập
        $currentUserRank = null;
        $currentUserExp = 0;
        $user = $request->user();

        if ($user && $user->isStudent()) {
            $currentRankIndex = $topStudents->search(fn ($s) => $s->id === $user->id);
            if ($currentRankIndex !== false) {
                $currentUserRank = $currentRankIndex + 1;
                $currentUserExp = (int) $topStudents->get($currentRankIndex)->total_exp;
            } else {
                $currentUserExp = (int) $user->userAchievements()
                    ->when($timeframe === 'month', fn ($q) => $q->where('unlocked_at', '>=', now()->startOfMonth()))
                    ->when($timeframe === 'week', fn ($q) => $q->where('unlocked_at', '>=', now()->startOfWeek()))
                    ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                    ->where('achievements.is_active', true)
                    ->selectRaw('COALESCE(SUM(CASE WHEN user_achievements.is_claimed = 1 THEN achievements.points ELSE (CASE WHEN user_achievements.is_unlocked = 1 THEN achievements.points ELSE 0 END) END), 0) as total')
                    ->value('total') ?? 0;
            }
        }

        return view('leaderboard.index', [
            'topStudents' => $topStudents,
            'rank1' => $rank1,
            'rank2' => $rank2,
            'rank3' => $rank3,
            'remaining' => $remaining,
            'timeframe' => $timeframe,
            'currentUserRank' => $currentUserRank,
            'currentUserExp' => $currentUserExp,
        ]);
    }
}
