<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $baseCourses = [
            ['code' => 'BSIT', 'name' => 'BS Information Technology', 'department' => 'Computing'],
            ['code' => 'BSCS', 'name' => 'BS Computer Science', 'department' => 'Computing'],
            ['code' => 'BSIS', 'name' => 'BS Information Systems', 'department' => 'Computing'],
            ['code' => 'BSCY', 'name' => 'BS Cybersecurity', 'department' => 'Computing'],
            ['code' => 'BSCE', 'name' => 'BS Computer Engineering', 'department' => 'Engineering'],
            ['code' => 'BSEE', 'name' => 'BS Electrical Engineering', 'department' => 'Engineering'],
            ['code' => 'BSME', 'name' => 'BS Mechanical Engineering', 'department' => 'Engineering'],
            ['code' => 'BSIE', 'name' => 'BS Industrial Engineering', 'department' => 'Engineering'],
            ['code' => 'BSBA', 'name' => 'BS Business Administration', 'department' => 'Business'],
            ['code' => 'BSA', 'name' => 'BS Accountancy', 'department' => 'Business'],
            ['code' => 'BSHM', 'name' => 'BS Hospitality Management', 'department' => 'Business'],
            ['code' => 'BSTM', 'name' => 'BS Tourism Management', 'department' => 'Business'],
            ['code' => 'BSN', 'name' => 'BS Nursing', 'department' => 'Health Sciences'],
            ['code' => 'BSPH', 'name' => 'BS Public Health', 'department' => 'Health Sciences'],
            ['code' => 'BSMLS', 'name' => 'BS Medical Laboratory Science', 'department' => 'Health Sciences'],
            ['code' => 'BSRT', 'name' => 'BS Radiologic Technology', 'department' => 'Health Sciences'],
            ['code' => 'BSED', 'name' => 'BS Secondary Education', 'department' => 'Education'],
            ['code' => 'BEED', 'name' => 'BS Elementary Education', 'department' => 'Education'],
            ['code' => 'BPED', 'name' => 'BS Physical Education', 'department' => 'Education'],
            ['code' => 'BTVTED', 'name' => 'BS Technical-Vocational Teacher Education', 'department' => 'Education'],
        ];

        $seededCodes = array_column($baseCourses, 'code');

        // Keep the programs set deterministic for demos and grading checks.
        Course::query()->whereNotIn('code', $seededCodes)->delete();

        foreach ($baseCourses as $course) {
            Course::query()->updateOrCreate(
                ['code' => $course['code']],
                [
                    'name' => $course['name'],
                    'department' => $course['department'],
                    'credits' => 3,
                    'enrolled_count' => 0,
                    'is_active' => true,
                ]
            );
        }
    }
}
