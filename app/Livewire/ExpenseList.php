<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Expenses')]
class ExpenseList extends Component
{
    use WithPagination;

    // public properties
    public $search = '';

    public $selectedCategory = '';

    public $startDate = '';

    public $endDate = '';

    public $sortBy = 'date';

    public $sortDirection = 'desc';

    public $showFilters = false;

    public function mount()
    {
        // default to current month
        if (empty($this->startDate)) {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
        }
        if (empty($this->endDate)) {
            $this->endDate = now()->endOfMonth()->format('Y-m-d');
        }
    }

    // sorting
    public array $sortableFields = ['date', 'title', 'amount', 'category'];

    public function sortByField($field)
    {
        if (! in_array($field, $this->sortableFields, true)) {
            return;
        }

        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    // deleting the expense
    public function deleteExpense($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);

        if ($expense->user_id !== Auth::user()->id) {
            abort(403, 'Your Not Authorized to Perform this function');
        }

        $expense->delete();

        session()->flash('message', 'Expense deleted successfully!');
    }

    /**
     * The user's expenses with the active search and filters applied.
     *
     * Built in one place so the list and the total can't drift apart: the search
     * term's two LIKEs MUST stay grouped in their own closure, otherwise SQL's
     * AND-binds-tighter-than-OR turns the user scope into
     * `(user_id = X AND title LIKE ...) OR (description LIKE ...)` and the
     * second branch matches every user's rows.
     */
    protected function filteredQuery()
    {
        $query = Expense::forUser(Auth::user()->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        return $query;
    }

    // computed property of expenses
    #[Computed]
    public function expenses()
    {
        $query = $this->filteredQuery()->with('category');

        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        if ($this->sortBy === 'category') {
            // category name lives on a related table — order by a correlated
            // subquery so we don't have to join (which collides with user_id)
            $query->orderBy(
                Category::select('name')->whereColumn('categories.id', 'expenses.category_id'),
                $direction
            );
        } else {
            $column = in_array($this->sortBy, ['date', 'title', 'amount'], true) ? $this->sortBy : 'date';
            $query->orderBy($column, $direction);
        }

        return $query->paginate(10);
    }

    #[Computed]
    public function total()
    {
        return $this->filteredQuery()->sum('amount');
    }

    #[Computed]
    public function categories()
    {
        return Category::where('user_id', Auth::user()->id)
            ->orderBy('name')
            ->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.expense-list', [
            'expenses' => $this->expenses,
            'total' => $this->total,
            'categories' => $this->categories,
        ]);
    }
}
