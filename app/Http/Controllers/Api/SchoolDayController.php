<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolDay;
use Illuminate\Http\JsonResponse;

class SchoolDayController extends Controller
{
    public function index(): JsonResponse
    {
        $schoolDays = SchoolDay::query()
            ->orderByDesc('school_date')
            ->limit(180)
            ->get();

        return response()->json($schoolDays);
    }
}
