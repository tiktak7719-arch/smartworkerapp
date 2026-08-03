<?php

namespace Tests\Feature;

use Tests\TestCase;

class PostJobPageTest extends TestCase
{
    public function test_guests_are_redirected_to_login_when_opening_the_post_job_page(): void
    {
        $this->get(route('post-job'))
            ->assertRedirect(route('my-account'));
    }
}
