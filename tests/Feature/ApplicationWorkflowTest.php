<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Application workflow tests require the pdo_sqlite PHP extension.');
        }

        parent::setUp();
    }

    public function test_application_notifies_employer_and_status_update_notifies_candidate(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $candidate = User::factory()->create(['role' => 'candidate']);
        UserProfile::create([
            'user_id' => $candidate->id,
            'resume_path' => 'resumes/candidate-cv.pdf',
            'resume_original_name' => 'candidate-cv.pdf',
        ]);
        $job = JobListing::create([
            'user_id' => $employer->id,
            'title' => 'Electrician',
            'company' => 'SmartWorker Services',
            'job_type' => 'full-time',
            'description' => 'Install and maintain electrical systems for customer sites.',
        ]);

        $this->actingAs($candidate)
            ->post(route('jobs.apply', $job->slug), ['cover_letter' => 'I am ready to start.'])
            ->assertRedirect(route('my-applications'));

        $this->assertDatabaseHas('job_applications', [
            'job_listing_id' => $job->id,
            'user_id' => $candidate->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('smart_notifications', [
            'user_id' => $employer->id,
            'type' => 'new_application',
        ]);

        $applicationId = $candidate->applications()->firstOrFail()->id;
        $this->actingAs($employer)
            ->patch(route('applications.status', $applicationId), [
                'status' => 'shortlisted',
                'employer_notes' => 'Please be ready for an interview.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('job_applications', [
            'id' => $applicationId,
            'status' => 'shortlisted',
            'employer_notes' => 'Please be ready for an interview.',
        ]);
        $this->assertDatabaseHas('smart_notifications', [
            'user_id' => $candidate->id,
            'type' => 'application_status',
        ]);
    }
}
