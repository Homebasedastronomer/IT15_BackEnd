<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentController extends Controller
{
    public function index(): JsonResponse
    {
        $students = Student::query()
            ->with('course:id,code,name')
            ->latest('enrolled_at')
            ->limit(500)
            ->get();

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'gender' => ['required', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $nextId = ((int) Student::query()->max('id')) + 1;

        $student = Student::query()->create([
            ...$validated,
            'student_number' => sprintf('UM-%04d', $nextId),
            'enrolled_at' => Carbon::now(),
        ]);

        Course::query()->where('id', $student->course_id)->increment('enrolled_count');

        return response()->json(
            $student->load('course:id,code,name'),
            201
        );
    }
}
