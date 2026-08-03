<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Support\Collection;

class MatchingService
{
    /**
     * A transparent recommendation score: skills 60%, location 30%, reputation 10%.
     * It is deliberately explainable, so it can later be replaced by an ML API.
     */
    public function recommendJobsFor(User $candidate, int $limit = 6): Collection
    {
        return JobListing::query()
            ->active()
            ->with('category')
            ->latest()
            ->get()
            ->map(function (JobListing $job) use ($candidate): JobListing {
                $job->match_score = $this->scoreJobForCandidate($job, $candidate);
                return $job;
            })
            ->filter(fn (JobListing $job): bool => $job->match_score >= 20)
            ->sortByDesc('match_score')
            ->take($limit)
            ->values();
    }

    public function recommendCandidatesFor(JobListing $job, int $limit = 20): Collection
    {
        return User::query()
            ->active()
            ->candidates()
            ->with(['profile', 'ratingsReceived'])
            ->withAvg('ratingsReceived', 'score')
            ->get()
            ->map(function (User $candidate) use ($job): User {
                $candidate->match_score = $this->scoreCandidateForJob($candidate, $job);
                $candidate->distance_km = $this->distanceInKm($candidate->latitude, $candidate->longitude, $job->latitude, $job->longitude);
                $candidate->predicted_rating = $this->predictedRatingFor($candidate);
                return $candidate;
            })
            ->filter(fn (User $candidate): bool => $candidate->match_score >= 20)
            ->sortByDesc('match_score')
            ->take($limit)
            ->values();
    }

    public function suggestedSkillsFor(User $candidate, int $limit = 5): Collection
    {
        $knownSkills = collect($candidate->profile?->skills_array ?? [])
            ->map(fn ($skill) => $this->normalise((string) $skill))
            ->filter()
            ->all();

        return JobListing::active()
            ->whereNotNull('tags')
            ->get()
            ->flatMap(fn (JobListing $job) => $job->tags_array)
            ->map(fn ($tag) => trim((string) $tag))
            ->filter(fn (string $tag) => $tag !== '' && ! in_array($this->normalise($tag), $knownSkills, true))
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take($limit)
            ->values();
    }

    public function scoreJobForCandidate(JobListing $job, User $candidate): int
    {
        $skills = $candidate->profile?->skills_array ?? [];
        $corpus = $this->normalise($job->title.' '.$job->tags.' '.$job->requirements.' '.$job->description);
        $skillScore = $this->skillScore($corpus, $skills, 60);
        $locationScore = $this->locationScore($candidate, $job, 30);
        $ratingScore = min(10, (float) $candidate->ratingsReceived()->avg('score') * 2);

        return (int) round(min(100, $skillScore + $locationScore + $ratingScore));
    }

    public function scoreCandidateForJob(User $candidate, JobListing $job): int
    {
        $skills = $candidate->profile?->skills_array ?? [];
        $corpus = $this->normalise($job->title.' '.$job->tags.' '.$job->requirements.' '.$job->description);
        $skillScore = $this->skillScore($corpus, $skills, 65);
        $locationScore = $this->locationScore($candidate, $job, 25);
        $ratingScore = min(10, $this->predictedRatingFor($candidate) * 2);

        return (int) round(min(100, $skillScore + $locationScore + $ratingScore));
    }

    public function distanceInKm(?float $firstLatitude, ?float $firstLongitude, ?float $secondLatitude, ?float $secondLongitude): ?float
    {
        if ($firstLatitude === null || $firstLongitude === null || $secondLatitude === null || $secondLongitude === null) {
            return null;
        }

        $earthRadius = 6371;
        $latitudeDelta = deg2rad($secondLatitude - $firstLatitude);
        $longitudeDelta = deg2rad($secondLongitude - $firstLongitude);
        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($firstLatitude)) * cos(deg2rad($secondLatitude)) * sin($longitudeDelta / 2) ** 2;

        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
    }

    /** Small, explainable prediction based on rating history and review sentiment. */
    public function predictedRatingFor(User $user): float
    {
        $ratings = $user->relationLoaded('ratingsReceived') ? $user->ratingsReceived : $user->ratingsReceived()->get();
        if ($ratings->isEmpty()) {
            return 3.0;
        }

        $positiveWords = ['excellent', 'reliable', 'professional', 'great', 'good', 'skilled', 'helpful'];
        $negativeWords = ['poor', 'late', 'unreliable', 'bad', 'issue', 'problem'];
        $sentiment = $ratings->sum(function ($rating) use ($positiveWords, $negativeWords): int {
            $comment = $this->normalise((string) $rating->comment);
            $positive = collect($positiveWords)->filter(fn ($word) => str_contains($comment, $word))->count();
            $negative = collect($negativeWords)->filter(fn ($word) => str_contains($comment, $word))->count();
            return $positive - $negative;
        });

        return round(max(1, min(5, (float) $ratings->avg('score') + ($sentiment * 0.08))), 1);
    }

    private function skillScore(string $jobCorpus, array $skills, int $maximum): float
    {
        $skills = collect($skills)
            ->map(fn ($skill) => $this->normalise((string) $skill))
            ->filter(fn (string $skill): bool => strlen($skill) > 1)
            ->unique()
            ->values();

        if ($skills->isEmpty()) {
            return 0;
        }

        $hits = $skills->filter(fn (string $skill): bool => str_contains($jobCorpus, $skill))->count();

        return ($hits / $skills->count()) * $maximum;
    }

    private function locationScore(User $candidate, JobListing $job, int $maximum): float
    {
        if ($job->job_type === 'remote') {
            return $maximum;
        }

        $distance = $this->distanceInKm($candidate->latitude, $candidate->longitude, $job->latitude, $job->longitude);
        if ($distance !== null) {
            return match (true) {
                $distance <= 10 => $maximum,
                $distance <= 30 => $maximum * 0.8,
                $distance <= 60 => $maximum * 0.55,
                $distance <= 120 => $maximum * 0.25,
                default => 0,
            };
        }

        $candidateLocation = $this->normalise((string) $candidate->location);
        $jobLocation = $this->normalise((string) $job->location);

        if ($candidateLocation !== '' && $jobLocation !== '' && (str_contains($candidateLocation, $jobLocation) || str_contains($jobLocation, $candidateLocation))) {
            return $maximum * 0.65;
        }

        return 0;
    }

    private function normalise(string $value): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $value) ?? ''));
    }
}
