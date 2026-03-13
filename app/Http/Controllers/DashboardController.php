<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $studentCount = Student::query()->count();
        $courseCount = Course::query()->count();
        $schoolDayCount = SchoolDay::query()->count();

        $averageAttendance = SchoolDay::query()
            ->where('is_holiday', false)
            ->avg('attendance_rate');

        $latestStudents = Student::query()
            ->with('course:id,code,name')
            ->latest('enrolled_at')
            ->limit(5)
            ->get();

        $courseDistribution = Course::query()
            ->select(['name', 'code', 'enrolled_count'])
            ->orderByDesc('enrolled_count')
            ->limit(5)
            ->get();

        return Inertia::render('dashboard', [
            'overview' => [
                'students' => $studentCount,
                'courses' => $courseCount,
                'school_days' => $schoolDayCount,
                'average_attendance' => round((float) $averageAttendance, 2),
            ],
            'latestStudents' => $latestStudents,
            'courseDistribution' => $courseDistribution,
        ]);
    }
}
