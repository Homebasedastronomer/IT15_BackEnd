<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryNames = [
            'Announcements',
            'Events',
            'Guides',
            'Tips',
        ];

        foreach ($categoryNames as $name) {
            Category::query()->firstOrCreate([
                'name' => $name,
            ]);
        }
    }
}
