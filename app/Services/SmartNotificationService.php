<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\SmartNotification;
use App\Models\User;

class SmartNotificationService
{
    public function create(User $user, string $type, string $title, string $body, ?string $url = null, array $data = []): SmartNotification
    {
        return SmartNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'data' => $data,
        ]);
    }

    public function alertCandidatesAboutJob(JobListing $job): void
    {
        $matches = app(MatchingService::class)->recommendCandidatesFor($job, 100);

        foreach ($matches as $candidate) {
            if (! $candidate->job_alerts_enabled || $candidate->match_score < 45) {
                continue;
            }

            $this->create(
                $candidate,
                'job_match',
                'New job matching your profile',
                "{$job->title} at {$job->company} is a {$candidate->match_score}% skills and location match.",
                route('jobs.show', $job->slug),
                ['job_id' => $job->id, 'match_score' => $candidate->match_score],
            );
        }
    }
}
