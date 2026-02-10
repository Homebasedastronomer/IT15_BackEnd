<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assertable;
use Tests\TestCase;

class PostsIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_it_shows_categories_and_posts(): void
    {
        $category = Category::factory()->create([
            'name' => 'News',
        ]);

        $post = Post::factory()->for($category)->create([
            'title' => 'First Post',
            'description' => 'This is the first post description.',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertInertia(
            fn(Assertable $page) => $page
                ->component('posts/index')
                ->has('categories', 1)
                ->has('posts', 1)
                ->where('posts.0.title', $post->title)
                ->where('posts.0.description', $post->description)
                ->where('selectedCategoryId', null)
        );
    }

    public function test_it_filters_posts_by_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        Post::factory()->for($categoryA)->create([
            'title' => 'A Post',
        ]);

        Post::factory()->for($categoryB)->create([
            'title' => 'B Post',
        ]);

        $response = $this->get(route('home', ['category' => $categoryB->id]));

        $response->assertOk();
        $response->assertInertia(
            fn(Assertable $page) => $page
                ->component('posts/index')
                ->has('posts', 1)
                ->where('posts.0.category_id', $categoryB->id)
                ->where('selectedCategoryId', $categoryB->id)
        );
    }
}
