<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request): Response
    {
        $selectedCategoryId = $request->has('category')
            ? $request->integer('category')
            : null;

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $posts = Post::query()
            ->when(
                $selectedCategoryId,
                fn($query) => $query->where('category_id', $selectedCategoryId)
            )
            ->latest()
            ->get(['id', 'category_id', 'title', 'description', 'created_at']);

        return Inertia::render('posts/index', [
            'categories' => $categories,
            'posts' => $posts,
            'selectedCategoryId' => $selectedCategoryId,
        ]);
    }
}
