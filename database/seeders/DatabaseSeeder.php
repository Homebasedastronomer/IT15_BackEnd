<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            CategorySeeder::class,
            PostSeeder::class,
            CourseSeeder::class,
            SubjectSeeder::class,
            StudentSeeder::class,
            SchoolDaySeeder::class,
        ]);

        User::query()->updateOrCreate(
            ['email' => 'registrar@dollente.edu'],
            [
                'name' => 'Registrar User',
                'password' => Hash::make('password'),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
