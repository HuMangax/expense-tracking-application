<?php

namespace Tests\Feature;

use App\Livewire\ExpenseForm;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BiweeklyRecurringTest extends TestCase
{
    use RefreshDatabase;

    public function test_biweekly_next_occurrence_advances_two_weeks(): void
    {
        $user = User::factory()->create();

        $expense = Expense::create([
            'user_id' => $user->id,
            'title' => 'Biweekly Paycheck Transfer',
            'amount' => 100,
            'date' => '2026-06-01',
            'type' => 'recurring',
            'recurring_frequency' => 'biweekly',
            'recurring_start_date' => '2026-06-01',
        ]);

        $this->assertSame('2026-06-15', $expense->getNextOccurrenceDate()->format('Y-m-d'));
    }

    public function test_expense_form_accepts_biweekly_frequency(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ExpenseForm::class)
            ->set('title', 'Gym Membership')
            ->set('amount', '40')
            ->set('date', '2026-06-01')
            ->set('type', 'recurring')
            ->set('recurring_frequency', 'biweekly')
            ->set('recurring_start_date', '2026-06-01')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'title' => 'Gym Membership',
            'type' => 'recurring',
            'recurring_frequency' => 'biweekly',
        ]);
    }

    public function test_invalid_frequency_is_rejected(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ExpenseForm::class)
            ->set('title', 'Bad')
            ->set('amount', '10')
            ->set('date', '2026-06-01')
            ->set('type', 'recurring')
            ->set('recurring_frequency', 'hourly')
            ->set('recurring_start_date', '2026-06-01')
            ->call('save')
            ->assertHasErrors('recurring_frequency');
    }
}
