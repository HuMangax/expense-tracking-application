<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CronEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_cron_endpoint_is_disabled_when_no_secret_configured(): void
    {
        config(['app.cron_secret' => null]);

        $this->get('/_cron/recurring/anything')->assertNotFound();
    }

    public function test_cron_endpoint_rejects_a_wrong_token(): void
    {
        config(['app.cron_secret' => 'topsecret']);

        $this->get('/_cron/recurring/wrong-token')->assertNotFound();
    }

    public function test_cron_endpoint_runs_with_the_correct_token(): void
    {
        config(['app.cron_secret' => 'topsecret']);

        $this->get('/_cron/recurring/topsecret')
            ->assertOk()
            ->assertSee('Recurring expenses generated.');
    }
}
