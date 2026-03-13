<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        $subjects = Subject::query()
            ->with('course:id,code')
            ->orderBy('code')
            ->get()
            ->map(function (Subject $subject) {
                $yearLabel = match ($subject->year_level) {
                    1 => '1st Year',
                    2 => '2nd Year',
                    3 => '3rd Year',
                    default => '4th Year',
                };

                return [
                    'id' => $subject->id,
                    'code' => $subject->code,
                    'title' => $subject->title,
                    'units' => $subject->units,
                    'yearLevel' => $yearLabel,
                    'offeredIn' => $subject->offered_in,
                    'termIndicator' => $subject->term_indicator,
                    'programCode' => $subject->course?->code,
                    'description' => $subject->description,
                    'prerequisites' => $subject->prerequisites ?? [],
                    'createdAt' => optional($subject->created_at)->toDateString(),
                ];
            })
            ->values();

        return response()->json($subjects);
    }
}
