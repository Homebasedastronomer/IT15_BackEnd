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
            ->withCount('students')
            ->orderByDesc('students_count')
            ->get()
            ->map(fn(Course $course) => [
                'course' => $course->name,
                'short' => $course->code,
                'students' => (int) $course->students_count,
            ])
            ->values();

        return response()->json($distribution);
    }

    public function attendanceTrend(): JsonResponse
    {
        $startDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now()->endOfDay();

        $attendance = SchoolDay::query()
            ->where('is_holiday', false)
            ->whereBetween('school_date', [$startDate, $endDate])
            ->orderBy('school_date')
            ->get()
            ->map(fn(SchoolDay $day) => [
                'date' => $day->school_date->format('Y-m-d'),
                'label' => $day->school_date->format('M d'),
                'attendance' => (float) $day->attendance_rate,
            ])
            ->values();

        return response()->json($attendance);
    }
}
