<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            ->map(fn(Course $course) => $this->transformProgram($course))
            ->values();

        return response()->json($programs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['Active', 'Inactive', 'active', 'inactive'])],
        ]);

        $course = Course::query()->create([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'department' => trim($validated['department']),
            'is_active' => strtolower($validated['status']) === 'active',
            'credits' => 3,
            'enrolled_count' => 0,
        ]);

        return response()->json($this->transformProgram($course), 201);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'code')->ignore($course->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['Active', 'Inactive', 'active', 'inactive'])],
        ]);

        $course->update([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'department' => trim($validated['department']),
            'is_active' => strtolower($validated['status']) === 'active',
        ]);

        return response()->json($this->transformProgram($course->fresh()));
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully.',
        ]);
    }

    private function transformProgram(Course $course): array
    {
        return [
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
        ];
    }
}
