<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Authentication tests require the pdo_sqlite PHP extension.');
        }

        parent::setUp();
    }

    public function test_a_candidate_can_register_and_is_redirected_to_their_dashboard(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Ali Khan',
            'email' => 'ali@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'candidate',
        ]);

        $response->assertRedirect(route('candidate.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'ali@example.com', 'role' => 'candidate']);
        $this->assertDatabaseHas('user_profiles', ['user_id' => User::where('email', 'ali@example.com')->value('id')]);
    }

    public function test_an_employer_is_redirected_to_the_employer_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'employer@example.com',
            'password' => 'secret-pass',
            'role' => 'employer',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'employer@example.com',
            'password' => 'secret-pass',
        ]);

        $response->assertRedirect(route('employer.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
