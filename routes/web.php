<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', App\Livewire\Dashboard::class)
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
    Route::get('categories', App\Livewire\Categories::class)->name('categories.index');
    Route::get('budgets', App\Livewire\BudgetList::class)->name('budgets.index');
    Route::get('budgets/create', App\Livewire\BudgetForm::class)->name('budget.create');
    Route::get('budgets/{budgetId}/edit', App\Livewire\BudgetForm::class)->name('budgets.edit');

    //expenses
    Route::get('expenses', App\Livewire\ExpenseList::class)->name('expenses.index');
    Route::get('/expenses/create', App\Livewire\ExpenseForm::class)->name('expenses.create');
    Route::get('/expenses/bulk', App\Livewire\BulkExpenseForm::class)->name('expenses.bulk');
    Route::get('/expenses/import', App\Livewire\ImportExpenses::class)->name('expenses.import');
    Route::get('expenses/{expenseId}/edit', App\Livewire\ExpenseForm::class)->name('expenses.edit');
    Route::get('recurring-expenses', App\Livewire\RecurringExpense::class)->name('recurring-expenses.index');
});

require __DIR__ . '/settings.php';
