<?php

use App\Http\Controllers\GenerateRecurringExpensesController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Generates due recurring expenses. Meant to be hit once a day by an external
// scheduler (e.g. cron-job.org) on hosts without a real cron. Guarded by a
// secret token; returns 404 unless CRON_SECRET is configured. See DEPLOY.md.
Route::get('/_cron/recurring/{token}', GenerateRecurringExpensesController::class)
    ->name('cron.recurring');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', App\Livewire\Dashboard::class)
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
    Route::get('categories', App\Livewire\Categories::class)->name('categories.index');
    Route::get('budgets', App\Livewire\BudgetList::class)->name('budgets.index');
    Route::get('budgets/create', App\Livewire\BudgetForm::class)->name('budget.create');
    Route::get('budgets/{budgetId}/edit', App\Livewire\BudgetForm::class)->name('budgets.edit');

    // expenses
    Route::get('expenses', App\Livewire\ExpenseList::class)->name('expenses.index');
    Route::get('/expenses/create', App\Livewire\ExpenseForm::class)->name('expenses.create');
    Route::get('/expenses/bulk', App\Livewire\BulkExpenseForm::class)->name('expenses.bulk');
    Route::get('/expenses/import', App\Livewire\ImportExpenses::class)->name('expenses.import');
    Route::get('expenses/{expenseId}/edit', App\Livewire\ExpenseForm::class)->name('expenses.edit');
    Route::get('recurring-expenses', App\Livewire\RecurringExpense::class)->name('recurring-expenses.index');
});

// TEMP (local only): auto-login the first user for README screenshots. Removed after.
if (app()->environment('local')) {
    Route::get('/__dev_login', function () {
        auth()->login(App\Models\User::first());

        return redirect()->route('dashboard');
    });
}

require __DIR__.'/settings.php';
