<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyPageRoutesTest extends TestCase
{
    public function test_legacy_html_links_redirect_to_laravel_routes(): void
    {
        $this->get('/manage-jobs.html')
            ->assertMovedPermanently()
            ->assertRedirect('/manage-jobs');
    }

    public function test_unknown_html_pages_remain_not_found(): void
    {
        $this->get('/not-a-page.html')->assertNotFound();
    }

    public function test_the_old_malformed_post_job_url_redirects_to_the_form(): void
    {
        $this->get('/href=')
            ->assertMovedPermanently()
            ->assertRedirect('/post-job');
    }
}
