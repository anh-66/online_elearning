<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function week(Request $request): JsonResponse
    {
        $today = today();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->addDays(6);
        $attendances = $request->user()->attendances()
            ->whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->get()
            ->keyBy(fn (Attendance $attendance): string => $attendance->attendance_date->toDateString());

        $days = collect(range(0, 6))->map(function (int $offset) use ($startOfWeek, $attendances, $today): array {
            $date = $startOfWeek->copy()->addDays($offset);
            $attendance = $attendances->get($date->toDateString());

            return [
                'date' => $date->toDateString(),
                'day' => ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'][$offset],
                'number' => $date->day,
                'is_today' => $date->isSameDay($today),
                'checked_in' => $attendance !== null,
            ];
        });

        $latestAttendance = $request->user()->attendances()->latest('attendance_date')->first();

        return response()->json([
            'days' => $days,
            'streak' => $latestAttendance?->streak ?? 0,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = today();

        $attendance = DB::transaction(function () use ($user, $today): Attendance {
            $existingAttendance = $user->attendances()
                ->whereDate('attendance_date', $today)
                ->first();

            if ($existingAttendance !== null) {
                return $existingAttendance;
            }

            $yesterday = $today->copy()->subDay();
            $previousAttendance = $user->attendances()
                ->whereDate('attendance_date', $yesterday)
                ->first();

            return $user->attendances()->create([
                'attendance_date' => $today,
                'streak' => $previousAttendance !== null ? $previousAttendance->streak + 1 : 1,
            ]);
        });

        $alreadyCheckedIn = $attendance->wasRecentlyCreated === false;

        return response()->json([
            'checked_in' => true,
            'already_checked_in' => $alreadyCheckedIn,
            'streak' => $attendance->streak,
            'date' => $attendance->attendance_date->toDateString(),
        ]);
    }
}
