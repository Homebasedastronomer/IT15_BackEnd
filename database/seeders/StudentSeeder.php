<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::query()->get();

        if ($courses->isEmpty()) {
            return;
        }

        Student::query()->delete();

        for ($i = 1; $i <= 520; $i++) {
            $course = $courses->random();

            Student::query()->create([
                'student_number' => sprintf('UM-%04d', $i),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => sprintf('student%04d@umroll.edu.ph', $i),
                'gender' => fake()->randomElement(['Male', 'Female', 'Non-binary']),
                'birth_date' => fake()->dateTimeBetween('-25 years', '-16 years')->format('Y-m-d'),
                'course_id' => $course->id,
                'year_level' => fake()->numberBetween(1, 4),
                'status' => fake()->randomElement(['Enrolled', 'Pending', 'Advised']),
                'enrolled_at' => Carbon::now()->subDays(fake()->numberBetween(1, 240)),
            ]);
        }

        // Keep enrolled_count synced with seeded students.
        foreach ($courses as $course) {
            $course->update([
                'enrolled_count' => Student::query()->where('course_id', $course->id)->count(),
            ]);
        }
    }
}
