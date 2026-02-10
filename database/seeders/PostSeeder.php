<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->get(['id', 'name']);

        if ($categories->isEmpty()) {
            return;
        }

        $postTemplates = [
            'Announcements' => [
                ['title' => 'Welcome to the Community Hub', 'description' => 'A friendly introduction to the site, what you can do here, and how to find the latest updates quickly.'],
                ['title' => 'Platform Update', 'description' => 'A quick summary of recent improvements, bug fixes, and the small changes that make the experience smoother.'],
            ],
            'Events' => [
                ['title' => 'Community Meetup', 'description' => 'Join us for a casual local meetup to connect, share ideas, and learn what others are building.'],
                ['title' => 'Workshop Day', 'description' => 'Hands-on sessions with live demos, practical tips, and time for Q and A at the end.'],
            ],
            'Guides' => [
                ['title' => 'Getting Started', 'description' => 'A step by step walkthrough of the main pages, where content lives, and how to browse by category.'],
                ['title' => 'Posting Tips', 'description' => 'Write clear titles and useful summaries so readers can understand your post at a glance.'],
            ],
            'Tips' => [
                ['title' => 'Quick Shortcuts', 'description' => 'Use the category list to filter fast, then open a post card to read the full description.'],
                ['title' => 'Keep It Simple', 'description' => 'Short posts are easier to read, but a little context helps readers understand your point.'],
            ],
        ];

        foreach ($categories as $category) {
            $templates = $postTemplates[$category->name] ?? [];

            foreach ($templates as $template) {
                Post::query()->firstOrCreate([
                    'category_id' => $category->id,
                    'title' => $template['title'],
                ], [
                    'description' => $template['description'],
                ]);
            }
        }
    }
}
