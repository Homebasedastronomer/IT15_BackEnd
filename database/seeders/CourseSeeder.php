<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Computing',
            'Business',
            'Engineering',
            'Education',
            'Arts and Sciences',
        ];

        $baseCourses = [
            ['code' => 'BSIT', 'name' => 'BS Information Technology'],
            ['code' => 'BSCS', 'name' => 'BS Computer Science'],
            ['code' => 'BSIS', 'name' => 'BS Information Systems'],
            ['code' => 'BSBA', 'name' => 'BS Business Administration'],
            ['code' => 'BSA', 'name' => 'BS Accountancy'],
            ['code' => 'BSHM', 'name' => 'BS Hospitality Management'],
            ['code' => 'BSTM', 'name' => 'BS Tourism Management'],
            ['code' => 'BSED', 'name' => 'BS Secondary Education'],
            ['code' => 'BEED', 'name' => 'BS Elementary Education'],
            ['code' => 'BSN', 'name' => 'BS Nursing'],
            ['code' => 'BSCE', 'name' => 'BS Civil Engineering'],
            ['code' => 'BSEE', 'name' => 'BS Electrical Engineering'],
            ['code' => 'BSME', 'name' => 'BS Mechanical Engineering'],
            ['code' => 'BSCHE', 'name' => 'BS Chemical Engineering'],
            ['code' => 'BSARCH', 'name' => 'BS Architecture'],
            ['code' => 'ABCOMM', 'name' => 'AB Communication'],
            ['code' => 'ABPSY', 'name' => 'AB Psychology'],
            ['code' => 'BSPH', 'name' => 'BS Public Health'],
            ['code' => 'BSMATH', 'name' => 'BS Mathematics'],
            ['code' => 'BSBIO', 'name' => 'BS Biology'],
            ['code' => 'BSCHEM', 'name' => 'BS Chemistry'],
            ['code' => 'BSECON', 'name' => 'BS Economics'],
        ];

        foreach ($baseCourses as $index => $course) {
            Course::query()->updateOrCreate(
                ['code' => $course['code']],
                [
                    'name' => $course['name'],
                    'department' => $departments[$index % count($departments)],
                    'credits' => fake()->numberBetween(3, 5),
                    'enrolled_count' => fake()->numberBetween(120, 950),
                    'is_active' => true,
                ]
            );
        }
    }
}
