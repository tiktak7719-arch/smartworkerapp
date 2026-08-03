<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::published()->latest('published_at')->paginate(9);

        return view('blog', compact('posts'));
    }

    public function show(BlogPost $post): View
    {
        abort_unless($post->is_published && (! $post->published_at || $post->published_at->isPast()), 404);

        $relatedPosts = BlogPost::published()
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('single-post', compact('post', 'relatedPosts'));
    }
}
