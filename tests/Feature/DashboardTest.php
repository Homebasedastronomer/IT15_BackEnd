<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assertable;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $course = Course::query()->create([
            'code' => 'BSIT',
            'name' => 'Bachelor of Science in Information Technology',
            'department' => 'Computer Studies',
            'credits' => 3,
            'enrolled_count' => 1,
            'is_active' => true,
        ]);

        Student::query()->create([
            'student_number' => 'UM-0001',
            'first_name' => 'Mark',
            'last_name' => 'Kian',
            'email' => 'mark@example.test',
            'gender' => 'Male',
            'birth_date' => now()->subYears(19)->toDateString(),
            'course_id' => $course->id,
            'year_level' => 1,
            'status' => 'Enrolled',
            'enrolled_at' => now(),
        ]);

        SchoolDay::query()->create([
            'school_date' => now()->toDateString(),
            'is_holiday' => false,
            'attendance_rate' => 92.50,
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(
            fn(Assertable $page) => $page
                ->component('dashboard')
                ->where('overview.students', 1)
                ->where('overview.courses', 1)
                ->where('overview.school_days', 1)
                ->where('overview.average_attendance', 92.5)
                ->has('latestStudents', 1)
                ->where('latestStudents.0.student_number', 'UM-0001')
                ->where('latestStudents.0.course.code', 'BSIT')
                ->has('courseDistribution', 1)
                ->where('courseDistribution.0.code', 'BSIT')
        );
    }
}
