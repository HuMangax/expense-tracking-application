<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use App\Services\BudgetAIService;
use Carbon\Carbon;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Resources\GenerativeModel;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Cover for two bugs the AI path had while it had no tests at all:
//  1. getHistoricalSpending() returned from inside its `for ($i = 1; $i <= 3)`
//     loop, so the "last 3 months" analysis only ever saw one month. Every
//     trend came out 'stable' and every confidence came out 'low'.
//  2. createPrompt() wrote "\${expense['total']}" — no leading $ inside the
//     braces — so PHP printed the array expression literally and the model
//     never received the monthly figures.
class BudgetRecommendationTest extends TestCase
{
    use RefreshDatabase;

    /** Spend in each of the three months before July 2026, rising over time. */
    private function seedThreeMonthsOfHistory(User $user): void
    {
        $months = [
            ['2026-04-10', 100, 'April Groceries'],
            ['2026-05-10', 200, 'May Groceries'],
            ['2026-06-10', 400, 'June Groceries'],
        ];

        foreach ($months as [$date, $amount, $title]) {
            Expense::create([
                'user_id' => $user->id,
                'title' => $title,
                'amount' => $amount,
                'date' => $date,
                'type' => 'one-time',
            ]);
        }
    }

    private function fakeGeminiReply(string $text): void
    {
        Gemini::fake([
            GenerateContentResponse::fake([
                'candidates' => [
                    ['content' => ['parts' => [['text' => $text]]]],
                ],
            ]),
        ]);
    }

    public function test_prompt_lists_every_month_with_its_real_total(): void
    {
        $this->travelTo(Carbon::create(2026, 7, 15));

        $user = User::factory()->create();
        $this->seedThreeMonthsOfHistory($user);

        $this->fakeGeminiReply('{"recommended": 250, "min": 200, "max": 300, "explanation": "x", "tip": "y"}');

        (new BudgetAIService)->getBudgetRecommendation(null, $user->id, 7, 2026);

        Gemini::assertSent(GenerativeModel::class, callback: function (string $method, array $args) {
            $prompt = $args[0];

            $this->assertSame('generateContent', $method);

            // All three months reach the model, not just the most recent one.
            $this->assertStringContainsString('April 2026: $100.00', $prompt);
            $this->assertStringContainsString('May 2026: $200.00', $prompt);
            $this->assertStringContainsString('June 2026: $400.00', $prompt);

            // The un-interpolated array expression must not survive into the prompt.
            $this->assertStringNotContainsString("expense['total']", $prompt);

            $this->assertStringContainsString('Trend: increasing', $prompt);
            $this->assertStringContainsString('Average: $233.33', $prompt);

            return true;
        });
    }

    public function test_statistics_are_computed_across_all_three_months(): void
    {
        $this->travelTo(Carbon::create(2026, 7, 15));

        $user = User::factory()->create();
        $this->seedThreeMonthsOfHistory($user);

        // Unparseable reply, so the deterministic fallback runs and its numbers
        // expose the statistics the service actually gathered.
        $this->fakeGeminiReply('Sorry, I cannot help with that.');

        $recommendation = (new BudgetAIService)->getBudgetRecommendation(null, $user->id, 7, 2026);

        $average = (100 + 200 + 400) / 3;

        $this->assertNotNull($recommendation);
        $this->assertEqualsWithDelta(round($average * 1.1, 2), $recommendation['recommended'], 0.01);
        $this->assertEqualsWithDelta(round($average * 0.95, 2), $recommendation['min'], 0.01);
        $this->assertEqualsWithDelta(round($average * 1.2, 2), $recommendation['max'], 0.01);

        // Three months of data, so the service is as confident as it gets.
        $this->assertSame('high', $recommendation['confidence']);
    }

    public function test_trend_reflects_the_direction_across_months(): void
    {
        $this->travelTo(Carbon::create(2026, 7, 15));

        $user = User::factory()->create();

        // Falling: April is the largest, June the smallest.
        foreach ([['2026-04-10', 400], ['2026-05-10', 200], ['2026-06-10', 100]] as [$date, $amount]) {
            Expense::create([
                'user_id' => $user->id,
                'title' => 'Groceries',
                'amount' => $amount,
                'date' => $date,
                'type' => 'one-time',
            ]);
        }

        $this->fakeGeminiReply('{"recommended": 150, "min": 120, "max": 180, "explanation": "x", "tip": "y"}');

        (new BudgetAIService)->getBudgetRecommendation(null, $user->id, 7, 2026);

        Gemini::assertSent(GenerativeModel::class, callback: function (string $method, array $args) {
            $this->assertStringContainsString('Trend: decreasing', $args[0]);

            return true;
        });
    }

    public function test_no_history_returns_null_without_calling_the_model(): void
    {
        $this->travelTo(Carbon::create(2026, 7, 15));

        $user = User::factory()->create();

        $this->fakeGeminiReply('{"recommended": 1}');

        $this->assertNull((new BudgetAIService)->getBudgetRecommendation(null, $user->id, 7, 2026));

        Gemini::assertNothingSent();
    }

    public function test_history_is_scoped_to_the_requested_category(): void
    {
        $this->travelTo(Carbon::create(2026, 7, 15));

        $user = User::factory()->create();
        $food = \App\Models\Category::create(['user_id' => $user->id, 'name' => 'Food', 'color' => '#111111']);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $food->id,
            'title' => 'Groceries',
            'amount' => 60,
            'date' => '2026-06-10',
            'type' => 'one-time',
        ]);
        // Uncategorised spending in the same month must not inflate the figures.
        Expense::create([
            'user_id' => $user->id,
            'title' => 'Flight',
            'amount' => 900,
            'date' => '2026-06-10',
            'type' => 'one-time',
        ]);

        $this->fakeGeminiReply('not json');

        $recommendation = (new BudgetAIService)->getBudgetRecommendation($food->id, $user->id, 7, 2026);

        $this->assertNotNull($recommendation);
        $this->assertEqualsWithDelta(round(60 * 1.1, 2), $recommendation['recommended'], 0.01);
        // One month of data only.
        $this->assertSame('low', $recommendation['confidence']);
    }
}
