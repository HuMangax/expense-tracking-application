<?php

namespace Tests\Feature;

use App\Livewire\BulkExpenseForm;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BulkExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('expenses.bulk'))->assertOk();
    }

    public function test_can_save_multiple_expenses_at_once(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(BulkExpenseForm::class)
            ->set('rows.0.title', 'Coffee')
            ->set('rows.0.amount', '4.50')
            ->set('rows.0.date', '2026-06-01')
            ->set('rows.1.title', 'Lunch')
            ->set('rows.1.amount', '12.00')
            ->set('rows.1.date', '2026-06-02')
            ->set('rows.2.title', 'Notebook')
            ->set('rows.2.amount', '8.25')
            ->set('rows.2.date', '2026-06-03')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('expenses.index'));

        $this->assertSame(3, Expense::where('user_id', $user->id)->count());
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'title' => 'Coffee',
            'type' => 'one-time',
        ]);
    }

    public function test_validation_blocks_incomplete_rows(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(BulkExpenseForm::class)
            ->call('save')
            ->assertHasErrors(['rows.0.title', 'rows.0.amount']);

        $this->assertSame(0, Expense::where('user_id', $user->id)->count());
    }

    public function test_can_add_and_remove_rows(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(BulkExpenseForm::class)
            ->assertCount('rows', 3)
            ->call('addRow')
            ->assertCount('rows', 4);

        $firstRowId = $component->get('rows')[0]['id'];

        $component->call('removeRow', $firstRowId)
            ->assertCount('rows', 3);
    }
}
