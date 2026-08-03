<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\BlogPost;
use App\Models\JobListing;
use App\Models\Testimonial;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $featuredJobs = JobListing::with('category')
            ->active()
            ->featured()
            ->latest('created_at')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $latestJobs = JobListing::with('category')
            ->active()
            ->orderByDesc('is_featured')
            ->latest('created_at')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $categories = Category::active()
            ->withCount(['activeJobs'])
            ->orderByDesc('active_jobs_count')
            ->take(8)
            ->get();

        $featuredEmployers = User::active()
            ->employers()
            ->with('profile')
            ->withCount(['jobListings as active_jobs_count' => fn ($query) => $query->active()])
            ->orderByDesc('active_jobs_count')
            ->take(6)
            ->get();

        $testimonials = collect();
        $latestPosts = BlogPost::published()->latest('published_at')->take(3)->get();

        $totalJobs = JobListing::active()->count();
        $totalEmployers = User::active()->employers()->count();
        $totalCandidates = User::active()->candidates()->count();

        return view('index-03', compact(
            'featuredJobs',
            'latestJobs',
            'categories',
            'featuredEmployers',
            'testimonials',
            'latestPosts',
            'totalJobs',
            'totalEmployers',
            'totalCandidates',
        ));
    }
}
