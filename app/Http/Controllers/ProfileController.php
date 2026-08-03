<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\MatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** My Profile / Add Resume page */
    public function edit(): View
    {
        $user    = Auth::user();
        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);

        return view('add-resume', compact('user', 'profile'));
    }

    /** Save profile updates */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Update user core fields
        $userData = $request->safe()->only(['name', 'phone', 'location', 'latitude', 'longitude', 'bio']);
        $userData['job_alerts_enabled'] = $request->boolean('job_alerts_enabled');
        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        $user->update($userData);

        // Update profile
        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);
        $profileData = $user->isEmployer() || $user->isAdmin()
            ? $request->safe()->only(['company_name', 'company_website', 'industry', 'company_size', 'company_description'])
            : $request->safe()->only(['skills', 'experience_years', 'education', 'linkedin', 'github', 'portfolio']);

        $resumeUploaded = false;

        if (($user->isEmployer() || $user->isAdmin()) && $request->hasFile('company_logo')) {
            if ($profile->company_logo) {
                Storage::disk('public')->delete($profile->company_logo);
            }
            $profileData['company_logo'] = $request->file('company_logo')->store('company-logos', 'public');
        }

        if ($user->isCandidate() && $request->hasFile('resume')) {
            if ($profile->resume_path) {
                Storage::disk('local')->delete($profile->resume_path);
                Storage::disk('public')->delete($profile->resume_path);
            }
            $profileData['resume_path']          = $request->file('resume')->store('resumes', 'local');
            $profileData['resume_original_name'] = $request->file('resume')->getClientOriginalName();
            $resumeUploaded = true;
        }
        $profile->update($profileData);

        return back()->with('success', $resumeUploaded
            ? 'Profile and resume uploaded successfully!'
            : 'Profile updated successfully!');
    }

    /** Change password page */
    public function showChangePassword(): View
    {
        return view('change-password');
    }

    /** Handle password change */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully!');
    }

    /** Browse resumes (employers / admin can see) */
    public function browseResumes(Request $request): View
    {
        $filters = $request->only(['skill', 'location', 'min_rating', 'radius']);
        $employer = $request->user();
        $matching = app(MatchingService::class);

        $profiles = UserProfile::with(['user.ratingsReceived'])
            ->whereNotNull('resume_path')
            ->whereHas('user', fn ($q) => $q->where('role', 'candidate')->where('is_active', true))
            ->when($filters['skill'] ?? null, fn ($query, $skill) => $query->where('skills', 'like', "%{$skill}%"))
            ->when($filters['location'] ?? null, fn ($query, $location) => $query->whereHas('user', fn ($users) => $users->where('location', 'like', "%{$location}%")))
            ->latest()
            ->get()
            ->map(function (UserProfile $profile) use ($employer, $matching): UserProfile {
                $profile->average_rating = round((float) $profile->user->ratingsReceived->avg('score'), 1);
                $profile->distance_km = $matching->distanceInKm(
                    $employer->latitude,
                    $employer->longitude,
                    $profile->user->latitude,
                    $profile->user->longitude,
                );

                return $profile;
            })
            ->filter(function (UserProfile $profile) use ($filters): bool {
                if (($filters['min_rating'] ?? null) && $profile->average_rating < (float) $filters['min_rating']) {
                    return false;
                }

                if (($filters['radius'] ?? null) && ($profile->distance_km === null || $profile->distance_km > (float) $filters['radius'])) {
                    return false;
                }

                return true;
            })
            ->when($employer->latitude !== null && $employer->longitude !== null, fn ($profiles) => $profiles->sortBy(fn (UserProfile $profile) => $profile->distance_km ?? PHP_FLOAT_MAX))
            ->values();

        $perPage = 12;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $profiles = new LengthAwarePaginator(
            $profiles->forPage($page, $perPage)->values(),
            $profiles->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('browse-resumes', compact('profiles', 'filters'));
    }

    /** My resume page */
    public function myResume(): View
    {
        $user    = Auth::user();
        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);

        return view('resume', compact('user', 'profile'));
    }
}
