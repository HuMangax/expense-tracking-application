<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Add Multiple Expenses')]
class BulkExpenseForm extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $rows = [];

    public function mount(): void
    {
        $this->rows = [
            $this->blankRow(),
            $this->blankRow(),
            $this->blankRow(),
        ];
    }

    protected function blankRow(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'title' => '',
            'amount' => '',
            'category_id' => '',
            'date' => now()->format('Y-m-d'),
        ];
    }

    public function addRow(): void
    {
        if (count($this->rows) >= 50) {
            return;
        }

        $this->rows[] = $this->blankRow();
    }

    public function removeRow(string $id): void
    {
        $this->rows = array_values(array_filter(
            $this->rows,
            fn ($row) => $row['id'] !== $id
        ));

        if (empty($this->rows)) {
            $this->rows[] = $this->blankRow();
        }
    }

    protected function rules(): array
    {
        return [
            'rows' => 'required|array|min:1',
            'rows.*.title' => 'required|string|max:255',
            'rows.*.amount' => 'required|numeric|min:0.01',
            'rows.*.category_id' => 'nullable|exists:categories,id',
            'rows.*.date' => 'required|date',
        ];
    }

    protected function validationAttributes(): array
    {
        $attributes = [];

        foreach (array_keys($this->rows) as $i) {
            $n = $i + 1;
            $attributes["rows.$i.title"] = "row $n title";
            $attributes["rows.$i.amount"] = "row $n amount";
            $attributes["rows.$i.category_id"] = "row $n category";
            $attributes["rows.$i.date"] = "row $n date";
        }

        return $attributes;
    }

    #[Computed]
    public function categories()
    {
        return Category::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function total(): float
    {
        return collect($this->rows)
            ->sum(fn ($row) => is_numeric($row['amount']) ? (float) $row['amount'] : 0);
    }

    public function save()
    {
        $validated = $this->validate();

        $userId = Auth::id();

        DB::transaction(function () use ($validated, $userId) {
            foreach ($validated['rows'] as $row) {
                Expense::create([
                    'user_id' => $userId,
                    'category_id' => $row['category_id'] ?: null,
                    'amount' => $row['amount'],
                    'title' => $row['title'],
                    'date' => $row['date'],
                    'type' => 'one-time',
                ]);
            }
        });

        $count = count($validated['rows']);
        session()->flash('message', $count.' '.Str::plural('expense', $count).' added successfully.');

        return redirect()->route('expenses.index');
    }

    public function render()
    {
        return view('livewire.bulk-expense-form');
    }
}
