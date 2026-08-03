<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403, 'Admin access required.');
    }

    public function dashboard()
    {
        $this->authorizeAdmin();

        return view('admin.dashboard', [
            'totalJobs'      => JobListing::count(),
            'activeJobs'     => JobListing::where('is_active', true)->count(),
            'totalContacts'  => Contact::count(),
            'unreadContacts' => Contact::where('is_read', false)->count(),
            'totalUsers'     => User::count(),
            'totalEmployers' => User::employers()->count(),
            'totalCandidates'=> User::candidates()->count(),
            'totalApps'      => JobApplication::count(),
            'verifiedUsers'  => User::where('is_verified', true)->count(),
            'topCategories'  => Category::active()->withCount('activeJobs')->orderByDesc('active_jobs_count')->take(5)->get(),
            'applicationTrends' => JobApplication::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'recentJobs'     => JobListing::with('employer')->latest()->take(5)->get(),
            'recentContacts' => Contact::latest()->take(5)->get(),
        ]);
    }

    /* ── Jobs ───────────────────────────────────────────── */
    public function jobs()
    {
        $this->authorizeAdmin();
        $jobs = JobListing::with(['employer', 'category'])->latest()->paginate(15);
        return view('admin.jobs', compact('jobs'));
    }

    public function toggleJob(JobListing $job): RedirectResponse
    {
        $this->authorizeAdmin();
        $job->update(['is_active' => !$job->is_active]);
        return back()->with('success', 'Job status updated.');
    }

    public function destroyJob(JobListing $job): RedirectResponse
    {
        $this->authorizeAdmin();
        $job->delete();
        return back()->with('success', 'Job deleted.');
    }

    /* ── Contacts ───────────────────────────────────────── */
    public function contacts()
    {
        $this->authorizeAdmin();
        $contacts = Contact::latest()->paginate(15);
        return view('admin.contacts', compact('contacts'));
    }

    public function showContact(Contact $contact)
    {
        $this->authorizeAdmin();
        $contact->update(['is_read' => true]);
        return view('admin.contact-show', compact('contact'));
    }

    public function destroyContact(Contact $contact): RedirectResponse
    {
        $this->authorizeAdmin();
        $contact->delete();
        return back()->with('success', 'Message deleted.');
    }

    /* ── Users ──────────────────────────────────────────── */
    public function users()
    {
        $this->authorizeAdmin();
        $users = User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function toggleUser(User $user): RedirectResponse
    {
        $this->authorizeAdmin();
        abort_if($user->isAdmin(), 403, 'Cannot suspend an admin.');
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        $this->authorizeAdmin();
        abort_if($user->id === auth()->id(), 403, 'Cannot delete yourself.');
        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    public function toggleUserVerification(User $user): RedirectResponse
    {
        $this->authorizeAdmin();
        abort_if($user->isAdmin(), 403, 'Admin accounts are already trusted.');
        $user->update(['is_verified' => ! $user->is_verified]);
        return back()->with('success', 'User verification status updated.');
    }

    /* ── Applications ───────────────────────────────────── */
    public function applications()
    {
        $this->authorizeAdmin();
        $applications = JobApplication::with(['job', 'applicant'])->latest()->paginate(20);
        return view('admin.applications', compact('applications'));
    }
}
