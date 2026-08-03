<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    /** Public, privacy-conscious candidate directory (resume files remain private). */
    public function candidates(Request $request): View
    {
        $filters = $request->only(['skill', 'location', 'min_rating']);
        $candidates = UserProfile::with('user')
            ->whereHas('user', fn ($query) => $query->active()->candidates())
            ->when($filters['skill'] ?? null, fn ($query, $skill) => $query->where('skills', 'like', "%{$skill}%"))
            ->when($filters['location'] ?? null, fn ($query, $location) => $query->whereHas('user', fn ($users) => $users->where('location', 'like', "%{$location}%")))
            ->withAvg('user.ratingsReceived', 'score')
            ->when($filters['min_rating'] ?? null, fn ($query, $rating) => $query->having('user_ratings_received_avg_score', '>=', (float) $rating))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('candidates', compact('candidates', 'filters'));
    }

    /** Public company directory built from employer profiles and their active jobs. */
    public function employers(): View
    {
        $employers = User::active()
            ->employers()
            ->with('profile')
            ->withCount(['jobListings as active_jobs_count' => fn ($query) => $query->active()])
            ->orderByDesc('active_jobs_count')
            ->latest()
            ->paginate(12);

        return view('employers', compact('employers'));
    }
}
