<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

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
}
