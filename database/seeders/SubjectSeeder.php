<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        Subject::query()->delete();

        $yearLevels = [1, 2, 3, 4];
        $offeredByIndex = ['1st Semester', '1st Semester', '2nd Semester', '2nd Semester', 'Summer Term'];

        $courseBlueprintByYear = [
            1 => [
                'Fundamentals of Computing',
                'Programming Logic and Design',
                'Digital Systems and Productivity',
                'Mathematics for Computing',
                'Communication and Technical Writing',
            ],
            2 => [
                'Data Structures and Algorithms',
                'Object-Oriented Programming',
                'Database Systems',
                'Human-Computer Interaction',
                'Networking Essentials',
            ],
            3 => [
                'Web Application Development',
                'Information Security and Governance',
                'Systems Analysis and Design',
                'Cloud and Distributed Systems',
                'Research Methods in IT',
            ],
            4 => [
                'Advanced Topics Seminar',
                'Capstone Project 1',
                'Capstone Project 2',
                'Professional Practice and Ethics',
                'Industry Internship',
            ],
        ];

        $courses = Course::query()->orderBy('code')->get();

        foreach ($courses as $course) {
            foreach ($yearLevels as $yearLevel) {
                foreach ($courseBlueprintByYear[$yearLevel] as $index => $title) {
                    $code = sprintf('%s%d%02d', $course->code, $yearLevel, $index + 1);
                    $prerequisites = [];

                    if ($yearLevel > 1 && $index < 2) {
                        $prerequisites[] = sprintf('%s%d%02d', $course->code, $yearLevel - 1, $index + 1);
                    }

                    Subject::query()->create([
                        'code' => $code,
                        'title' => sprintf('%s (%s)', $title, $course->code),
                        'units' => $index === 4 ? 2 : 3,
                        'year_level' => $yearLevel,
                        'offered_in' => $offeredByIndex[$index],
                        'term_indicator' => 'Per Semester',
                        'course_id' => $course->id,
                        'description' => sprintf('%s course under %s.', $title, $course->name),
                        'prerequisites' => $prerequisites,
                    ]);
                }
            }
        }
    }
}
