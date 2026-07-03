<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Safety net: .env sets DB_URL to the production (Neon) database, and DB_URL
// overrides DB_CONNECTION/DB_DATABASE. This test fails loudly if that URL ever
// leaks into the test environment, where RefreshDatabase would wipe real data.
class DbSafetyGuardTest extends TestCase
{
    public function test_tests_never_target_the_production_database(): void
    {
        $this->assertEmpty(config('database.connections.sqlite.url'), 'DB_URL leaked into testing!');
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
    }
}
