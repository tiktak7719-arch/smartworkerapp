<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The home page test requires the pdo_sqlite PHP extension.');
        }

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
