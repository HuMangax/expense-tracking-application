<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use App\Support\CategoryLibrary;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Import Expenses')]
class ImportExpenses extends Component
{
    use WithFileUploads;

    public $file;
    public string $step = 'upload'; // upload | map

    public array $headers = [];
    public array $sampleRows = [];
    public int $totalRows = 0;
    public string $delimiter = ',';

    // Column mapping (values are column indexes)
    public $dateColumn = null;
    public $descriptionColumn = null;
    public string $amountMode = 'single';   // single | separate
    public $amountColumn = null;
    public $debitColumn = null;
    public string $dateFormat = 'auto';
    public bool $onlyOutflows = true;
    public bool $hasHeaderRow = true;
    public $defaultCategoryId = '';
    public bool $skipDuplicates = true;
    public bool $autoCategorize = true;

    public array $dateFormats = [
        'auto' => 'Auto-detect',
        'm/d/Y' => 'MM/DD/YYYY (US)',
        'd/m/Y' => 'DD/MM/YYYY (UK/EU)',
        'Y-m-d' => 'YYYY-MM-DD',
        'd-m-Y' => 'DD-MM-YYYY',
        'm-d-Y' => 'MM-DD-YYYY',
        'd.m.Y' => 'DD.MM.YYYY',
    ];

    public function updatedFile(): void
    {
        $this->resetErrorBag();
        $this->validate(['file' => 'required|file|max:5120']);

        $ext = strtolower((string) $this->file->getClientOriginalExtension());
        if (! in_array($ext, ['csv', 'txt'])) {
            $this->addError('file', 'Please upload a .csv file exported from your bank.');
            return;
        }

        $this->loadFileMeta();
        $this->autoGuessMapping();
        $this->step = 'map';
    }

    protected function loadFileMeta(): void
    {
        $path = $this->file->getRealPath();
        $this->delimiter = $this->detectDelimiter($path);

        $sample = $this->readRows($path, 16);
        $this->headers = $sample[0] ?? [];

        $dataSample = $this->hasHeaderRow ? array_slice($sample, 1) : $sample;
        $this->sampleRows = array_values($dataSample);

        $this->totalRows = max(0, $this->countRows($path) - ($this->hasHeaderRow ? 1 : 0));
    }

    protected function detectDelimiter(string $path): string
    {
        $first = '';
        if (($h = fopen($path, 'r')) !== false) {
            $first = fgets($h) ?: '';
            fclose($h);
        }

        $counts = [
            ',' => substr_count($first, ','),
            ';' => substr_count($first, ';'),
            "\t" => substr_count($first, "\t"),
            '|' => substr_count($first, '|'),
        ];
        arsort($counts);
        $best = array_key_first($counts);

        return $counts[$best] > 0 ? $best : ',';
    }

    protected function readRows(string $path, ?int $limit = null): array
    {
        $rows = [];
        if (($h = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($h, 0, $this->delimiter)) !== false) {
                // skip fully empty lines
                if ($data === [null] || (count($data) === 1 && trim((string) $data[0]) === '')) {
                    continue;
                }
                $rows[] = $data;
                if ($limit !== null && count($rows) >= $limit) {
                    break;
                }
            }
            fclose($h);
        }

        return $rows;
    }

    protected function countRows(string $path): int
    {
        $count = 0;
        if (($h = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($h, 0, $this->delimiter)) !== false) {
                if ($data === [null] || (count($data) === 1 && trim((string) $data[0]) === '')) {
                    continue;
                }
                $count++;
            }
            fclose($h);
        }

        return $count;
    }

    protected function autoGuessMapping(): void
    {
        $find = function (array $needles) {
            foreach ($this->headers as $i => $header) {
                $h = strtolower(trim((string) $header));
                foreach ($needles as $needle) {
                    if (str_contains($h, $needle)) {
                        return $i;
                    }
                }
            }
            return null;
        };

        $this->dateColumn = $find(['date']);
        $this->descriptionColumn = $find(['description', 'payee', 'name', 'memo', 'details', 'narrative', 'reference', 'transaction']);
        $this->amountColumn = $find(['amount', 'value']);
        $debit = $find(['debit', 'withdrawal', 'paid out', 'money out', 'outflow']);
        $credit = $find(['credit', 'deposit', 'paid in', 'money in', 'inflow']);

        if ($debit !== null && $credit !== null) {
            $this->amountMode = 'separate';
            $this->debitColumn = $debit;
        } else {
            $this->amountMode = 'single';
            if ($this->amountColumn === null) {
                $this->amountColumn = $debit;
            }
        }
    }

    public function updatedHasHeaderRow(): void
    {
        if ($this->file) {
            $this->loadFileMeta();
        }
    }

    #[Computed]
    public function categories()
    {
        return Category::where('user_id', Auth::id())->orderBy('name')->get();
    }

    #[Computed]
    public function columnOptions(): array
    {
        $options = [];
        foreach ($this->headers as $i => $header) {
            $label = trim((string) $header);
            $options[$i] = $label !== '' ? $label : 'Column '.($i + 1);
        }
        return $options;
    }

    /** Normalize a raw CSV row into ['date','title','amount'] or null if it should be skipped. */
    protected function normalizeRow(array $row): ?array
    {
        $date = $this->parseDate($row[$this->dateColumn] ?? null);
        if (! $date) {
            return null;
        }

        $title = trim((string) ($row[$this->descriptionColumn] ?? ''));
        $title = $title !== '' ? Str::limit($title, 255, '') : 'Imported expense';

        if ($this->amountMode === 'separate') {
            $amount = $this->parseAmount($row[$this->debitColumn] ?? '');
            if ($amount === null) {
                return null;
            }
            $amount = abs($amount);
        } else {
            $amount = $this->parseAmount($row[$this->amountColumn] ?? '');
            if ($amount === null) {
                return null;
            }
            if ($this->onlyOutflows) {
                if ($amount >= 0) {
                    return null; // income / inflow — skip
                }
                $amount = abs($amount);
            } else {
                $amount = abs($amount);
            }
        }

        if ($amount < 0.01) {
            return null;
        }

        return [
            'date' => $date->format('Y-m-d'),
            'title' => $title,
            'amount' => round($amount, 2),
            'category_id' => $this->resolveCategoryId($title),
        ];
    }

    /**
     * Pick a category for a row: auto-detect from the description when enabled,
     * otherwise (or when no keyword matches) fall back to the chosen category.
     */
    protected function resolveCategoryId(string $title): ?int
    {
        if ($this->autoCategorize) {
            $name = CategoryLibrary::guessName($title);
            if ($name !== null) {
                $id = $this->categoryIdForName($name);
                if ($id !== null) {
                    return $id;
                }
            }
        }

        return $this->defaultCategoryId ? (int) $this->defaultCategoryId : null;
    }

    protected function categoryIdForName(string $name): ?int
    {
        foreach ($this->categories as $category) {
            if (mb_strtolower($category->name) === mb_strtolower($name)) {
                return $category->id;
            }
        }

        return null;
    }

    protected function parseDate($raw): ?Carbon
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return null;
        }

        try {
            if ($this->dateFormat !== 'auto') {
                return Carbon::createFromFormat($this->dateFormat, $value)->startOfDay();
            }
        } catch (\Throwable $e) {
            // fall through to auto-parse
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function parseAmount($raw): ?float
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return null;
        }

        $negative = str_contains($value, '(') && str_contains($value, ')');
        $clean = preg_replace('/[^0-9.\-]/', '', $value);

        if ($clean === '' || $clean === '-' || $clean === '.' || ! is_numeric($clean)) {
            return null;
        }

        $number = (float) $clean;

        return $negative ? -abs($number) : $number;
    }

    #[Computed]
    public function previewRows(): array
    {
        if (empty($this->sampleRows) || $this->dateColumn === null) {
            return [];
        }

        return collect($this->sampleRows)->take(15)->map(function ($row) {
            $normalized = $this->normalizeRow($row);
            $category = $normalized && $normalized['category_id']
                ? $this->categories->firstWhere('id', $normalized['category_id'])
                : null;

            return [
                'normalized' => $normalized,
                'rawDate' => $row[$this->dateColumn] ?? '',
                'rawDescription' => $this->descriptionColumn !== null ? ($row[$this->descriptionColumn] ?? '') : '',
                'categoryName' => $category?->name,
                'categoryColor' => $category?->color,
            ];
        })->all();
    }

    public function startOver(): void
    {
        $this->reset();
        $this->resetErrorBag();
    }

    public function import()
    {
        $this->validate(
            [
                'dateColumn' => 'required',
                'amountColumn' => 'required_if:amountMode,single',
                'debitColumn' => 'required_if:amountMode,separate',
            ],
            [
                'dateColumn.required' => 'Please choose which column holds the date.',
                'amountColumn.required_if' => 'Please choose which column holds the amount.',
                'debitColumn.required_if' => 'Please choose which column holds the debit / amount spent.',
            ]
        );

        if (! $this->file) {
            $this->addError('file', 'The uploaded file is no longer available. Please upload it again.');
            $this->step = 'upload';
            return;
        }

        $rows = $this->readRows($this->file->getRealPath());
        if ($this->hasHeaderRow) {
            array_shift($rows);
        }

        $userId = Auth::id();

        $imported = 0;
        $duplicates = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, $userId, &$imported, &$duplicates, &$skipped) {
            foreach ($rows as $row) {
                $data = $this->normalizeRow($row);

                if ($data === null) {
                    $skipped++;
                    continue;
                }

                if ($this->skipDuplicates && Expense::where('user_id', $userId)
                    ->whereDate('date', $data['date'])
                    ->where('amount', $data['amount'])
                    ->where('title', $data['title'])
                    ->exists()) {
                    $duplicates++;
                    continue;
                }

                Expense::create([
                    'user_id' => $userId,
                    'category_id' => $data['category_id'],
                    'amount' => $data['amount'],
                    'title' => $data['title'],
                    'date' => $data['date'],
                    'type' => 'one-time',
                ]);

                $imported++;
            }
        });

        $message = "Imported {$imported} ".Str::plural('expense', $imported).'.';
        if ($duplicates > 0) {
            $message .= " Skipped {$duplicates} duplicate ".Str::plural('row', $duplicates).'.';
        }
        if ($skipped > 0) {
            $message .= " Ignored {$skipped} ".Str::plural('row', $skipped).' (income or unreadable).';
        }

        session()->flash('message', $message);

        return redirect()->route('expenses.index');
    }

    public function render()
    {
        return view('livewire.import-expenses');
    }
}
