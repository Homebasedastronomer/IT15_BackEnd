<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentController extends Controller
{
    public function index(): JsonResponse
    {
        $students = Student::query()
            ->with('course:id,code,name,department')
            ->latest('enrolled_at')
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
            'department' => ['required', 'string', 'max:120'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $course = Course::query()->find($validated['course_id']);

        if (! $course || strcasecmp($course->department, $validated['department']) !== 0) {
            return response()->json([
                'message' => 'Selected course does not belong to the selected department.',
            ], 422);
        }

        $nextId = ((int) Student::query()->max('id')) + 1;

        $student = Student::query()->create([
            ...$validated,
            'student_number' => sprintf('UM-%04d', $nextId),
            'enrolled_at' => Carbon::now(),
        ]);

        Course::query()->where('id', $student->course_id)->increment('enrolled_count');

        return response()->json(
            $student->load('course:id,code,name,department'),
            201
        );
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:120'],
        ]);

        $query = strtolower(trim($validated['q']));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $students = Student::query()
            ->with('course:id,code,name,department')
            ->where(function ($builder) use ($query) {
                $builder
                    ->whereRaw('LOWER(student_number) LIKE ?', [$query . '%'])
                    ->orWhereRaw('LOWER(first_name) LIKE ?', [$query . '%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$query . '%']);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(10)
            ->get();

        return response()->json(
            $students->map(fn(Student $student) => [
                'student_id' => $student->student_number,
                'full_name' => trim(sprintf('%s %s', $student->first_name, $student->last_name)),
                'year_level' => $student->year_level,
                'program' => [
                    'id' => $student->course?->id,
                    'code' => $student->course?->code,
                    'name' => $student->course?->name,
                    'department' => $student->course?->department,
                ],
            ])->values()
        );
    }

    public function enrollmentHistory(string $studentNumber): JsonResponse
    {
        $student = Student::query()
            ->with('course:id,code,name,department')
            ->where('student_number', $studentNumber)
            ->first();

        if (! $student) {
            return response()->json([
                'message' => 'Student ID not found.',
            ], 404);
        }

        $currentYearLevel = max(1, min(4, (int) $student->year_level));

        $subjects = Subject::query()
            ->where('course_id', $student->course_id)
            ->where('year_level', '<=', $currentYearLevel)
            ->orderBy('year_level')
            ->orderBy('code')
            ->get(['id', 'code', 'title', 'units', 'year_level', 'offered_in', 'term_indicator']);

        $termOrder = ['1st Semester', '2nd Semester', 'Summer Term'];
        $currentTerm = $this->getCurrentAcademicTerm();

        $history = collect(range(1, $currentYearLevel))->map(function (int $yearLevel) use ($subjects, $termOrder, $currentYearLevel, $currentTerm) {
            $subjectsByYear = $subjects->where('year_level', $yearLevel);

            if ($yearLevel === $currentYearLevel) {
                $currentTermIndex = array_search($currentTerm, $termOrder, true);
                $currentTermIndex = $currentTermIndex === false ? 0 : $currentTermIndex;
                $termsToShow = array_slice($termOrder, 0, $currentTermIndex + 1);
            } else {
                $termsToShow = $termOrder;
            }

            $visibleSubjects = $subjectsByYear
                ->filter(fn(Subject $subject) => in_array($subject->offered_in, $termsToShow, true));

            $terms = collect($termsToShow)->map(function (string $term) use ($subjectsByYear) {
                $termSubjects = $subjectsByYear
                    ->where('offered_in', $term)
                    ->map(fn(Subject $subject) => [
                        'id' => $subject->id,
                        'code' => $subject->code,
                        'title' => $subject->title,
                        'units' => $subject->units,
                        'term_indicator' => $subject->term_indicator,
                    ])
                    ->values();

                return [
                    'term' => $term,
                    'subjects' => $termSubjects,
                ];
            })->values();

            return [
                'year_level' => $yearLevel,
                'label' => $this->toYearLabel($yearLevel),
                'is_current' => $yearLevel === $currentYearLevel,
                'total_subjects' => $visibleSubjects->count(),
                'total_units' => (int) $visibleSubjects->sum('units'),
                'terms' => $terms,
            ];
        })->values();

        return response()->json([
            'student' => [
                'id' => $student->id,
                'student_number' => $student->student_number,
                'full_name' => trim(sprintf('%s %s', $student->first_name, $student->last_name)),
                'status' => $student->status,
                'year_level' => $student->year_level,
            ],
            'program' => [
                'id' => $student->course?->id,
                'code' => $student->course?->code,
                'name' => $student->course?->name,
                'department' => $student->course?->department,
            ],
            'current_term' => $currentTerm,
            'history' => $history,
        ]);
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
