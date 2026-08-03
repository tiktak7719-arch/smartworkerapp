<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Role access tests require the pdo_sqlite PHP extension.');
        }

        parent::setUp();
    }

    public function test_an_employer_cannot_open_candidate_only_pages(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);

        $this->actingAs($employer)->get(route('candidate.dashboard'))->assertForbidden();
        $this->actingAs($employer)->get(route('saved-jobs'))->assertForbidden();
        $this->actingAs($employer)->get(route('my-applications'))->assertForbidden();
    }

    public function test_a_candidate_cannot_open_employer_only_pages(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($candidate)->get(route('employer.dashboard'))->assertForbidden();
        $this->actingAs($candidate)->get(route('manage-jobs'))->assertForbidden();
        $this->actingAs($candidate)->get(route('manage-applications'))->assertForbidden();
    }
}
