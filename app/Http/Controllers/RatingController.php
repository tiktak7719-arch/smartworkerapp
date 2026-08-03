<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Rating;
use App\Services\SmartNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(JobApplication $application, Request $request): RedirectResponse
    {
        $application->load(['job', 'applicant']);
        abort_unless($application->status === 'hired', 422, 'Ratings are available after a successful hire.');

        $reviewer = $request->user();
        $employerId = $application->job->user_id;
        $candidateId = $application->user_id;
        abort_unless(in_array($reviewer->id, [$employerId, $candidateId], true), 403);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $ratedUserId = $reviewer->id === $candidateId ? $employerId : $candidateId;
        $rating = Rating::updateOrCreate(
            ['job_application_id' => $application->id, 'reviewer_id' => $reviewer->id],
            ['rated_user_id' => $ratedUserId, 'score' => $validated['score'], 'comment' => $validated['comment'] ?? null],
        );

        $notificationUrl = $ratedUserId === $candidateId
            ? route('my-applications')
            : route('employer.dashboard');

        app(SmartNotificationService::class)->create(
            $rating->ratedUser,
            'rating_received',
            'You received a new rating',
            $reviewer->name.' gave you '.$rating->score.' out of 5 stars.',
            $notificationUrl,
            ['rating_id' => $rating->id],
        );

        return back()->with('success', 'Your rating has been saved.');
    }
}
