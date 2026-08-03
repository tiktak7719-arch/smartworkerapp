<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Job posting tests require the pdo_sqlite PHP extension.');
        }

        parent::setUp();
    }

    public function test_an_employer_can_post_a_job(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);

        $response = $this->actingAs($employer)->post(route('jobs.store'), [
            'title' => 'Laravel Developer',
            'company' => 'Smart Worker',
            'location' => 'Karachi',
            'job_type' => 'full-time',
            'description' => 'Build accessible and reliable Laravel applications for our customers.',
        ]);

        $response->assertRedirect(route('manage-jobs'));
        $this->assertDatabaseHas('job_listings', [
            'title' => 'Laravel Developer',
            'company' => 'Smart Worker',
            'user_id' => $employer->id,
        ]);
    }

    public function test_a_candidate_cannot_access_employer_job_posting_routes(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($candidate)->get(route('post-job'))->assertForbidden();
        $this->actingAs($candidate)->post(route('jobs.store'), [
            'title' => 'Blocked',
            'company' => 'Blocked',
            'job_type' => 'full-time',
            'description' => 'This candidate must not be able to create a job listing.',
        ])->assertForbidden();
    }
}
