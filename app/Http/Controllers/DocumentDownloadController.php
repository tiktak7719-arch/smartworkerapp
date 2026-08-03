<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\UserProfile;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    /** Download a candidate's profile resume only when the requester is authorised. */
    public function profileResume(UserProfile $profile): StreamedResponse|Response
    {
        $user = request()->user();

        abort_unless(
            $user->isAdmin() || $user->isEmployer() || $user->id === $profile->user_id,
            403,
            'You do not have permission to access this resume.'
        );

        abort_unless($profile->resume_path, 404);

        return $this->download($profile->resume_path, $profile->resume_original_name ?: 'resume');
    }

    /** Download an application CV only for its applicant, owning employer, or an admin. */
    public function applicationCv(JobApplication $application): StreamedResponse|Response
    {
        $application->loadMissing('job');
        $user = request()->user();

        abort_unless(
            $user->isAdmin()
            || $user->id === $application->user_id
            || $user->id === $application->job->user_id,
            403,
            'You do not have permission to access this application document.'
        );

        abort_unless($application->cv_path, 404);

        return $this->download($application->cv_path, $application->cv_original_name ?: 'cv');
    }

    /**
     * New documents are private. The public-disk fallback keeps any legacy
     * document available after this security upgrade.
     */
    private function download(string $path, string $name): StreamedResponse|Response
    {
        $disk = Storage::disk('local')->exists($path) ? 'local' : 'public';

        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->download($path, basename($name));
    }
}
