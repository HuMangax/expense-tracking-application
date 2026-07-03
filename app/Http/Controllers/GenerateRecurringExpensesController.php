<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

class GenerateRecurringExpensesController extends Controller
{
    /**
     * Generate any due recurring expenses. Meant to be hit once a day by an
     * external scheduler (e.g. cron-job.org) on hosts without a real cron.
     * Guarded by a secret token; returns 404 unless CRON_SECRET is configured
     * and matches. See DEPLOY.md.
     */
    public function __invoke(string $token): Response
    {
        $secret = (string) config('app.cron_secret');
        abort_unless($secret !== '' && hash_equals($secret, $token), 404);

        Artisan::call('expenses:generate-recurring-expense');

        return response('Recurring expenses generated.');
    }
}
