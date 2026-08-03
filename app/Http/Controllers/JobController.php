<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\SavedJob;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Services\MatchingService;
use App\Services\SmartNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class JobController extends Controller
{
    /* ── Public: Browse Jobs ────────────────────────────── */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'location', 'category', 'job_type', 'min_salary', 'max_salary', 'radius']);

        $jobQuery = JobListing::with('category')
            ->active()
            ->filter($filters);

        $radius = (float) ($filters['radius'] ?? 0);
        $candidate = Auth::user();
        if ($radius > 0 && $candidate?->isCandidate() && $candidate->latitude !== null && $candidate->longitude !== null) {
            $distanceFormula = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';
            $jobQuery->select('job_listings.*')
                ->selectRaw($distanceFormula.' as distance_km', [$candidate->latitude, $candidate->longitude, $candidate->latitude])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->having('distance_km', '<=', min($radius, 500));
        }

        $jobs = $jobQuery
            ->orderByDesc('is_featured')
            ->latest('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::active()->withCount(['activeJobs'])->get();

        return view('browse-jobs', compact('jobs', 'categories', 'filters'));
    }

    /* ── Public: Single Job Detail ──────────────────────── */
    public function show(string $slug): View
    {
        $job = JobListing::with(['category', 'employer'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $job->incrementViews();

        $applied   = false;
        $saved     = false;
        if (Auth::check()) {
            $applied = JobApplication::where('job_listing_id', $job->id)
                ->where('user_id', Auth::id())->exists();
            $saved   = SavedJob::where('job_listing_id', $job->id)
                ->where('user_id', Auth::id())->exists();
        }

        $related = JobListing::active()
            ->where('id', '!=', $job->id)
            ->where('category_id', $job->category_id)
            ->latest()->take(4)->get();

        return view('job-details', compact('job', 'applied', 'saved', 'related'));
    }

    /* ── Public: Categories ─────────────────────────────── */
    public function categories(): View
    {
        $categories = Category::active()
            ->withCount(['activeJobs'])
            ->orderByDesc('active_jobs_count')
            ->get();

        return view('browse-categories', compact('categories'));
    }

    /* ── Employer: Create / Post Job ────────────────────── */
    public function create(): View|RedirectResponse
    {
        if (! Auth::user()->isEmployer() && ! Auth::user()->isAdmin()) {
            return redirect()->route('my-account')
                ->with('error', 'An employer account is required to post jobs.');
        }

        $categories = Category::active()->get();
        return view('post-job', compact('categories'));
    }

    public function store(StoreJobRequest $request): RedirectResponse
    {
        // Must be logged in to post a job
        if (!Auth::check()) {
            return redirect()->route('my-account')
                ->with('error', 'Please login or register to post a job.');
        }

        if (! Auth::user()->isEmployer() && ! Auth::user()->isAdmin()) {
            return redirect()->route('my-account')
                ->with('error', 'An employer account is required to post jobs.');
        }

        $validated = $request->validated();

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')
                ->store('company-logos', 'public');
        }

        $job = JobListing::create($validated);
        app(SmartNotificationService::class)->alertCandidatesAboutJob($job);

        return redirect()->route('manage-jobs')
            ->with('success', 'Job posted successfully!');
    }

    /* ── Employer: Edit Job ─────────────────────────────── */
    public function edit(string $slug): View|RedirectResponse
    {
        $job = JobListing::where('slug', $slug)->firstOrFail();

        if (! Auth::user()->isEmployer() && ! Auth::user()->isAdmin()) {
            return redirect()->route('my-account')
                ->with('error', 'An employer account is required to manage jobs.');
        }

        // Only the employer who posted it (or an admin) can edit.
        if (!Auth::user()->isAdmin()) {
            abort_unless($job->user_id === Auth::id(), 403);
        }

        $categories = Category::active()->get();
        return view('manage-job-page', compact('job', 'categories'));
    }

    public function update(UpdateJobRequest $request, string $slug): RedirectResponse
    {
        $job = JobListing::where('slug', $slug)->firstOrFail();

        if (! Auth::user()->isEmployer() && ! Auth::user()->isAdmin()) {
            return redirect()->route('my-account')
                ->with('error', 'An employer account is required to manage jobs.');
        }

        if (!Auth::user()->isAdmin()) {
            abort_unless($job->user_id === Auth::id(), 403);
        }

        $validated = $request->validated();

        if ($request->hasFile('company_logo')) {
            if ($job->company_logo) Storage::disk('public')->delete($job->company_logo);
            $validated['company_logo'] = $request->file('company_logo')
                ->store('company-logos', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $job->update($validated);

        return redirect()->route('manage-jobs')
            ->with('success', 'Job updated successfully!');
    }

    /* ── Employer: Delete Job ───────────────────────────── */
    public function destroy(string $slug): RedirectResponse
    {
        $job = JobListing::where('slug', $slug)->firstOrFail();

        if (! Auth::user()->isEmployer() && ! Auth::user()->isAdmin()) {
            return redirect()->route('my-account')
                ->with('error', 'An employer account is required to manage jobs.');
        }

        if (!Auth::user()->isAdmin()) {
            abort_unless($job->user_id === Auth::id(), 403);
        }

        $job->delete();

        return back()->with('success', 'Job deleted.');
    }

    /* ── Employer: Manage My Jobs ───────────────────────── */
    public function manageIndex(): View|RedirectResponse
    {
        if (! Auth::user()->isEmployer() && ! Auth::user()->isAdmin()) {
            return redirect()->route('my-account')
                ->with('error', 'An employer account is required to manage jobs.');
        }

        $jobs = JobListing::with('category')
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('manage-jobs', compact('jobs'));
    }

    /* ── Employer: Applications Received ───────────────── */
    public function applications(): View|RedirectResponse
    {
        if (! Auth::user()->isEmployer() && ! Auth::user()->isAdmin()) {
            return redirect()->route('my-account')
                ->with('error', 'An employer account is required to manage applications.');
        }

        $applications = JobApplication::with(['job', 'applicant'])
            ->whereHas('job', fn ($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->paginate(15);

        return view('manage-applications', compact('applications'));
    }

    /* ── Employer: Update Application Status ────────────── */
    public function updateApplicationStatus(Request $request, int $id): RedirectResponse
    {
        abort_unless(Auth::user()->isEmployer() || Auth::user()->isAdmin(), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewing,shortlisted,rejected,hired'],
            'employer_notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $application = JobApplication::with(['job', 'applicant'])->findOrFail($id);
        abort_unless($application->job->user_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $previousStatus = $application->status;
        $previousNotes = trim((string) $application->employer_notes);
        $notes = trim((string) ($validated['employer_notes'] ?? ''));
        $statusChanged = $previousStatus !== $validated['status'];
        $notesChanged = $previousNotes !== $notes;

        DB::transaction(function () use ($application, $validated, $notes, $statusChanged, $notesChanged): void {
            $application->update([
                'status' => $validated['status'],
                'employer_notes' => $notes ?: null,
            ]);

            if (! $statusChanged && ! $notesChanged) {
                return;
            }

            $body = $statusChanged
                ? "Your application for {$application->job->title} is now ".str_replace('_', ' ', $validated['status']).'.'
                : "Your employer added a note to your application for {$application->job->title}.";

            app(SmartNotificationService::class)->create(
                $application->applicant,
                'application_status',
                'Application update: '.$application->job->title,
                $body,
                route('my-applications'),
                ['application_id' => $application->id, 'status' => $validated['status']],
            );
        });

        return back()->with('success', $statusChanged || $notesChanged
            ? 'Application update sent to the candidate.'
            : 'No changes were made to this application.');
    }

    /* ── Seeker: Apply for Job ──────────────────────────── */
    public function apply(Request $request, string $slug): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('my-account')
                ->with('error', 'Please login to apply for jobs.');
        }

        if (! Auth::user()->isCandidate()) {
            return redirect()->route('my-account')
                ->with('error', 'Only candidate accounts can apply for jobs.');
        }

        $job = JobListing::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($job->closing_date?->isBefore(today())) {
            return back()->with('error', 'Applications for this job are closed.');
        }

        // Check duplicate
        if (JobApplication::where('job_listing_id', $job->id)->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already applied for this job.');
        }

        $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:2000'],
            'cv'           => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ]);

        $profile = Auth::user()->profile;

        if (! $request->hasFile('cv') && ! $profile?->resume_path) {
            return back()->withErrors(['cv' => 'Upload a CV or add a resume to your profile before applying.'])->withInput();
        }

        $data = [
            'job_listing_id' => $job->id,
            'user_id'        => Auth::id(),
            'cover_letter'   => $request->cover_letter,
        ];

        if ($request->hasFile('cv')) {
            $data['cv_path']          = $request->file('cv')->store('cvs', 'local');
            $data['cv_original_name'] = $request->file('cv')->getClientOriginalName();
        } else {
            // Use profile resume if available
            if ($profile && $profile->resume_path) {
                $data['cv_path']          = $profile->resume_path;
                $data['cv_original_name'] = $profile->resume_original_name;
            }
        }

        $application = DB::transaction(function () use ($data, $job): JobApplication {
            $application = JobApplication::create($data);

            app(SmartNotificationService::class)->create(
                $job->employer,
                'new_application',
                'New application: '.$job->title,
                Auth::user()->name." applied for {$job->title}. Review the candidate and choose an application status.",
                route('manage-applications'),
                ['application_id' => $application->id, 'job_id' => $job->id],
            );

            return $application;
        });

        return redirect()->route('my-applications')
            ->with('success', 'Your application was submitted. The employer has been notified.');
    }

    /* ── Seeker: Save / Unsave Job ──────────────────────── */
    public function saveJob(string $slug): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('my-account')
                ->with('error', 'Please login to save jobs.');
        }

        if (! Auth::user()->isCandidate()) {
            return redirect()->route('my-account')
                ->with('error', 'Only candidate accounts can save jobs.');
        }

        $job     = JobListing::where('slug', $slug)->firstOrFail();
        $existing = SavedJob::where('job_listing_id', $job->id)->where('user_id', Auth::id())->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Job removed from saved list.');
        }

        SavedJob::create(['user_id' => Auth::id(), 'job_listing_id' => $job->id]);
        return back()->with('success', 'Job saved to your bookmarks!');
    }

    /* ── Seeker: Saved Jobs ─────────────────────────────── */
    public function savedJobs(): View|RedirectResponse
    {
        if (! Auth::user()->isCandidate()) {
            return redirect()->route('my-account')
                ->with('error', 'Only candidate accounts can view saved jobs.');
        }

        $savedJobs = SavedJob::with('job.category')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('bookmarked', compact('savedJobs'));
    }

    /* ── Employer Dashboard ─────────────────────────────── */
    public function employerDashboard(): View|RedirectResponse
    {
        if (! Auth::user()->isEmployer() && ! Auth::user()->isAdmin()) {
            return redirect()->route('my-account')
                ->with('error', 'An employer account is required to access this dashboard.');
        }

        $employerId = Auth::id();

        return view('employer-dashboard', [
            'totalJobs' => JobListing::where('user_id', $employerId)->count(),
            'activeJobs' => JobListing::where('user_id', $employerId)->active()->count(),
            'applicationCount' => JobApplication::whereHas('job', fn ($query) => $query->where('user_id', $employerId))->count(),
            'recentJobs' => JobListing::where('user_id', $employerId)->latest('created_at')->orderByDesc('id')->take(5)->get(),
            'recentApplications' => JobApplication::with(['job', 'applicant'])
                ->whereHas('job', fn ($query) => $query->where('user_id', $employerId))
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    /* ── Seeker Dashboard ───────────────────────────────── */
    public function candidateDashboard(): View|RedirectResponse
    {
        if (! Auth::user()->isCandidate()) {
            return redirect()->route('my-account')
                ->with('error', 'A candidate account is required to access this dashboard.');
        }

        $candidateId = Auth::id();

        return view('candidate-dashboard', [
            'savedCount' => SavedJob::where('user_id', $candidateId)->count(),
            'applicationCount' => JobApplication::where('user_id', $candidateId)->count(),
            'pendingApplications' => JobApplication::where('user_id', $candidateId)->whereIn('status', ['pending', 'reviewing'])->count(),
            'recentApplications' => JobApplication::with('job')->where('user_id', $candidateId)->latest()->take(5)->get(),
            'recommendedJobs' => app(MatchingService::class)->recommendJobsFor(Auth::user()),
            'skillSuggestions' => app(MatchingService::class)->suggestedSkillsFor(Auth::user()),
            'unreadNotifications' => Auth::user()->notifications()->whereNull('read_at')->count(),
        ]);
    }

    /** Application history belongs only to the signed-in candidate. */
    public function myApplications(): View
    {
        $applications = JobApplication::with(['job.category', 'ratings'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('my-applications', compact('applications'));
    }

    public function matches(string $slug): View|RedirectResponse
    {
        $job = JobListing::where('slug', $slug)->firstOrFail();
        abort_unless(Auth::user()->isAdmin() || $job->user_id === Auth::id(), 403);

        $matches = app(MatchingService::class)->recommendCandidatesFor($job);
        return view('job-matches', compact('job', 'matches'));
    }

}
