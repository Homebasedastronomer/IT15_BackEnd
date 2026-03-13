<?php

namespace Database\Seeders;

use App\Models\SchoolDay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SchoolDaySeeder extends Seeder
{
    public function run(): void
    {
        $startDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now()->endOfYear();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $isHoliday = fake()->boolean(8);

            SchoolDay::query()->updateOrCreate(
                ['school_date' => $date->format('Y-m-d')],
                [
                    'is_holiday' => $isHoliday,
                    'event_name' => $isHoliday ? fake()->randomElement([
                        'National Holiday',
                        'Campus Foundation Day',
                        'Faculty Development Day',
                    ]) : fake()->optional(0.12)->randomElement([
                        'College Week',
                        'Midterm Exams',
                        'Final Exams',
                        'Student Assembly',
                    ]),
                    'attendance_rate' => $isHoliday ? 0 : fake()->randomFloat(2, 78, 99.5),
                ]
            );
        }
    }
}
