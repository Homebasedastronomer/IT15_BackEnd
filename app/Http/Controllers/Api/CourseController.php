<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Course::query()->orderBy('name')->get()
        );
    }

    public function programs(): JsonResponse
    {
        $programs = Course::query()
            ->orderBy('name')
            ->get()
            ->map(fn(Course $course) => [
                'id' => $course->id,
                'code' => $course->code,
                'name' => $course->name,
                'department' => $course->department,
                'type' => str_starts_with($course->code, 'D') ? 'Diploma' : "Bachelor's",
                'duration' => str_starts_with($course->code, 'D') ? '2 Years' : '4 Years',
                'totalUnits' => str_starts_with($course->code, 'D') ? 96 : 160,
                'status' => $course->is_active ? 'Active' : 'Inactive',
                'description' => sprintf('%s under the %s department.', $course->name, $course->department),
                'createdAt' => optional($course->created_at)->toDateString(),
            ])
            ->values();

        return response()->json($programs);
    }
}
