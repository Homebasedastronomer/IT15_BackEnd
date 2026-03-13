<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function overview(): JsonResponse
    {
        $studentCount = Student::query()->count();
        $courseCount = Course::query()->count();
        $attendanceAvg = SchoolDay::query()
            ->where('is_holiday', false)
            ->avg('attendance_rate');

        return response()->json([
            'students' => $studentCount,
            'courses' => $courseCount,
            'school_days' => SchoolDay::query()->count(),
            'average_attendance' => round((float) $attendanceAvg, 2),
        ]);
    }

    public function enrollmentTrend(): JsonResponse
    {
        $months = collect(range(0, 5))->map(function (int $offset) {
            $date = Carbon::now()->startOfMonth()->subMonths(5 - $offset);
            $count = Student::query()
                ->whereYear('enrolled_at', $date->year)
                ->whereMonth('enrolled_at', $date->month)
                ->count();

            return [
                'month' => $date->format('M'),
                'enrolled' => $count,
                'target' => max(70, (int) round($count * 0.95)),
            ];
        });

        return response()->json($months->values());
    }

    public function courseDistribution(): JsonResponse
    {
        $distribution = Course::query()
            ->select(['name as course', 'code as short', 'enrolled_count as students'])
            ->orderByDesc('enrolled_count')
            ->limit(8)
            ->get();

        return response()->json($distribution);
    }

    public function attendanceTrend(): JsonResponse
    {
        $attendance = SchoolDay::query()
            ->where('is_holiday', false)
            ->orderByDesc('school_date')
            ->limit(30)
            ->get()
            ->sortBy('school_date')
            ->map(fn(SchoolDay $day) => [
                'date' => $day->school_date->format('M d'),
                'attendance' => (float) $day->attendance_rate,
            ])
            ->values();

        return response()->json($attendance);
    }
}
