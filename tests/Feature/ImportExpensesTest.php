<?php

namespace Tests\Feature;

use App\Livewire\ImportExpenses;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ImportExpensesTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_page_renders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('expenses.import'))->assertOk();
    }

    public function test_uploading_csv_advances_to_mapping_and_guesses_columns(): void
    {
        $user = User::factory()->create();
        $csv = "Date,Description,Amount\n2026-06-01,Coffee Shop,-4.50\n2026-06-02,Salary,2000.00\n";
        $file = UploadedFile::fake()->createWithContent('statement.csv', $csv);

        Livewire::actingAs($user)
            ->test(ImportExpenses::class)
            ->set('file', $file)
            ->assertSet('step', 'map')
            ->assertSet('dateColumn', 0)
            ->assertSet('descriptionColumn', 1)
            ->assertSet('amountColumn', 2)
            ->assertSet('totalRows', 2);
    }

    public function test_import_creates_expenses_and_skips_income(): void
    {
        $user = User::factory()->create();
        $csv = "Date,Description,Amount\n"
            ."2026-06-01,Coffee Shop,-4.50\n"
            ."2026-06-02,Groceries,-30.00\n"
            ."2026-06-03,Salary,2000.00\n";
        $file = UploadedFile::fake()->createWithContent('statement.csv', $csv);

        Livewire::actingAs($user)
            ->test(ImportExpenses::class)
            ->set('file', $file)
            ->call('import')
            ->assertRedirect(route('expenses.index'));

        $this->assertSame(2, Expense::where('user_id', $user->id)->count());
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'title' => 'Coffee Shop',
            'amount' => 4.50,
            'type' => 'one-time',
        ]);
        $this->assertDatabaseMissing('expenses', ['title' => 'Salary']);
    }

    public function test_separate_debit_credit_columns(): void
    {
        $user = User::factory()->create();
        $csv = "Date,Details,Debit,Credit\n"
            ."2026-06-01,Rent,1200.00,\n"
            ."2026-06-02,Refund,,50.00\n";
        $file = UploadedFile::fake()->createWithContent('bank.csv', $csv);

        Livewire::actingAs($user)
            ->test(ImportExpenses::class)
            ->set('file', $file)
            ->assertSet('amountMode', 'separate')
            ->assertSet('debitColumn', 2)
            ->call('import');

        $this->assertSame(1, Expense::where('user_id', $user->id)->count());
        $this->assertDatabaseHas('expenses', ['title' => 'Rent', 'amount' => 1200.00]);
    }

    public function test_duplicate_rows_are_skipped(): void
    {
        $user = User::factory()->create();

        Expense::create([
            'user_id' => $user->id,
            'amount' => 4.50,
            'title' => 'Coffee Shop',
            'date' => '2026-06-01',
            'type' => 'one-time',
        ]);

        $csv = "Date,Description,Amount\n2026-06-01,Coffee Shop,-4.50\n";
        $file = UploadedFile::fake()->createWithContent('statement.csv', $csv);

        Livewire::actingAs($user)
            ->test(ImportExpenses::class)
            ->set('file', $file)
            ->call('import');

        // Still only the original — the duplicate was skipped.
        $this->assertSame(1, Expense::where('user_id', $user->id)->count());
    }
}
