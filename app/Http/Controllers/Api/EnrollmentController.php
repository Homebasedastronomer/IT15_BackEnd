<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnrollmentController extends Controller
{
    public function options(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:30'],
            'term' => ['nullable', 'string', 'max:60'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:6'],
        ]);

        $student = Student::query()
            ->with('course:id,code,name')
            ->where('student_number', $validated['student_id'])
            ->first();

        if (! $student) {
            return response()->json([
                'message' => 'Student ID not found.',
            ], 404);
        }

        $courseId = (int) $student->course_id;
        $term = isset($validated['term'])
            ? $this->normalizeTerm($validated['term'])
            : $this->getCurrentAcademicTerm();

        $course = Course::query()->find($courseId);

        if (! $course) {
            return response()->json([
                'message' => 'Course not found for enrollment options.',
            ], 404);
        }

        $subjectsQuery = Subject::query()
            ->where('course_id', $courseId)
            ->where('offered_in', $term)
            ->orderBy('year_level')
            ->orderBy('code');

        $subjects = $subjectsQuery->get();

        $availableByYear = collect(range(1, 4))->map(function (int $year) use ($subjects) {
            $yearSubjects = $subjects
                ->where('year_level', $year)
                ->map(fn(Subject $subject) => [
                    'id' => $subject->id,
                    'code' => $subject->code,
                    'title' => $subject->title,
                    'units' => $subject->units,
                    'offered_in' => $subject->offered_in,
                    'term_indicator' => $subject->term_indicator,
                ])
                ->values();

            return [
                'year_level' => $year,
                'label' => $this->toYearLabel($year),
                'subjects' => $yearSubjects,
            ];
        })->values();

        $selectedYear = (int) ($validated['year_level'] ?? $student->year_level ?? 1);

        return response()->json([
            'student' => [
                'id' => $student->id,
                'student_number' => $student->student_number,
                'full_name' => trim(sprintf('%s %s', $student->first_name, $student->last_name)),
                'status' => $student->status,
                'current_year_level' => $student->year_level,
                'current_course' => [
                    'id' => $student->course?->id,
                    'code' => $student->course?->code,
                    'name' => $student->course?->name,
                ],
            ],
            'selected' => [
                'course' => [
                    'id' => $course->id,
                    'code' => $course->code,
                    'name' => $course->name,
                ],
                'term' => $term,
                'year_level' => $selectedYear,
                'label' => $this->toYearLabel($selectedYear),
            ],
            'current_term' => $this->getCurrentAcademicTerm(),
            'available_by_year' => $availableByYear,
            'available_for_selected_year' => $availableByYear
                ->firstWhere('year_level', $selectedYear)['subjects'] ?? [],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:30'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:6'],
            'term' => ['nullable', 'string', 'max:60'],
            'status' => ['nullable', 'string', 'max:30'],
        ]);

        $student = Student::query()
            ->where('student_number', $validated['student_id'])
            ->first();

        if (! $student) {
            return response()->json([
                'message' => 'Student ID not found.',
            ], 404);
        }

        $normalizedTerm = isset($validated['term'])
            ? $this->normalizeTerm($validated['term'])
            : $this->getCurrentAcademicTerm();
        $newCourseId = (int) $student->course_id;
        $selectedYearLevel = (int) ($validated['year_level'] ?? $student->year_level);

        $student->update([
            'course_id' => $newCourseId,
            'year_level' => $selectedYearLevel,
            'status' => $validated['status'] ?? 'Enrolled',
            'enrolled_at' => Carbon::now(),
        ]);

        $subjectsForSelection = Subject::query()
            ->where('course_id', $newCourseId)
            ->where('year_level', $selectedYearLevel)
            ->where('offered_in', $normalizedTerm)
            ->orderBy('code')
            ->get(['id', 'code', 'title', 'units', 'offered_in', 'term_indicator']);

        $course = Course::query()->find($newCourseId);

        return response()->json([
            'message' => 'Student enrollment updated successfully.',
            'student' => [
                'student_number' => $student->student_number,
                'full_name' => trim(sprintf('%s %s', $student->first_name, $student->last_name)),
                'year_level' => $student->year_level,
                'status' => $student->status,
            ],
            'course' => [
                'id' => $course?->id,
                'code' => $course?->code,
                'name' => $course?->name,
            ],
            'term' => $normalizedTerm,
            'available_subjects' => $subjectsForSelection,
        ]);
    }

    private function normalizeTerm(?string $term): string
    {
        $normalized = strtolower(trim((string) $term));

        if (str_contains($normalized, 'summer')) {
            return 'Summer Term';
        }

        if (str_contains($normalized, 'second') || str_contains($normalized, '2nd')) {
            return '2nd Semester';
        }

        return '1st Semester';
    }

    private function getCurrentAcademicTerm(): string
    {
        $month = Carbon::now()->month;

        if ($month >= 8 && $month <= 12) {
            return '1st Semester';
        }

        if ($month >= 1 && $month <= 5) {
            return '2nd Semester';
        }

        return 'Summer Term';
    }

    private function toYearLabel(int $year): string
    {
        return match ($year) {
            1 => '1st Year',
            2 => '2nd Year',
            3 => '3rd Year',
            default => '4th Year',
        };
    }
}
