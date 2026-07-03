<?php

namespace App\Livewire;

use App\Models\Budget;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $selectedMonth;

    public $selectedYear;

    public $totalSpent;

    public $monthlyBudget;

    public $percentageUsed;

    public $expenseByCategory;

    public $recentExpenses;

    public $monthlyComparison;

    public $topCategories;

    public $recurringExpenseCount;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $userId = Auth::user()->id;

        // total amount spent in a month
        $this->totalSpent = Expense::forUser($userId)
            ->inMonth($this->selectedMonth, $this->selectedYear)
            ->sum('amount');

        // monthly budget
        $budget = Budget::where('user_id', $userId)
            ->where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->sum('amount');

        $this->monthlyBudget = $budget ? $budget : 0;

        // percentage used
        $this->percentageUsed = $this->monthlyBudget > 0
            ? round(($this->totalSpent / $this->monthlyBudget) * 100, 1)
            : 0;

        // Expense by category
        $this->expenseByCategory = Expense::select('categories.name', 'categories.color', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', $userId)
            ->whereMonth('expenses.date', $this->selectedMonth)
            ->whereYear('expenses.date', $this->selectedYear)
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderBy('total', 'desc')
            ->get();

        // recent expenses
        $this->recentExpenses = Expense::with('category')
            ->forUser($userId)
            ->whereMonth('date', $this->selectedMonth)
            ->whereYear('date', $this->selectedYear)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Monthly comparison — one windowed query bucketed in PHP, instead of six
        // sequential SUM round-trips (each of which is costly when the DB is remote).
        // Kept portable across sqlite (local/tests) and Postgres (prod).
        $windowStart = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonths(5)->startOfMonth();
        $windowEnd = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $monthlyTotals = Expense::forUser($userId)
            ->whereBetween('date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->get(['amount', 'date'])
            ->groupBy(fn ($expense) => $expense->date->format('Y-m'))
            ->map(fn ($group) => $group->sum('amount'));

        $this->monthlyComparison = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonths($i);
            $this->monthlyComparison->push([
                'month' => $date->format('M'),
                'total' => (float) ($monthlyTotals[$date->format('Y-m')] ?? 0),
            ]);
        }

        // top categories
        $this->topCategories = $this->expenseByCategory->take(3);

        // recurring expenses
        $this->recurringExpenseCount = Expense::forUser($userId)
            ->recurring()
            ->count();

        // Push fresh chart data to the browser so the charts re-render on month change.
        $this->dispatch(
            'dashboard-updated',
            trendLabels: $this->monthlyComparison->pluck('month')->all(),
            trendData: $this->monthlyComparison->pluck('total')->all(),
            catLabels: $this->expenseByCategory->pluck('name')->all(),
            catData: $this->expenseByCategory->pluck('total')->all(),
            catColors: $this->expenseByCategory->pluck('color')->all(),
        );
    }

    public function updatedSelectedMonth()
    {
        $this->loadDashboardData();
    }

    public function updatedSelectedYear()
    {
        $this->loadDashboardData();
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        // Assigning properties inside an action doesn't fire the updatedSelected*
        // hooks, so refresh the stats + charts explicitly.
        $this->loadDashboardData();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->loadDashboardData();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
