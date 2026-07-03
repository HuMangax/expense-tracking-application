<div>
    <div class="mx-auto max-w-5xl px-4 pt-8 sm:px-6 lg:px-8">
        <div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Import Expenses</h1>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Upload a CSV statement from your bank and map the columns</p>
                </div>
                <a href="{{ route('expenses.index') }}" wire:navigate
                    class="inline-flex items-center justify-center rounded-lg border border-zinc-200 bg-white p-2 text-zinc-600 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800" aria-label="Cancel">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- STEP 1: UPLOAD --}}
        @if($step === 'upload')
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-8 transition-colors duration-300">
                <label for="csv-file"
                    class="flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-gray-300 dark:border-neutral-600 px-6 py-12 text-center cursor-pointer hover:border-teal-400 dark:hover:border-teal-500 hover:bg-teal-50/40 dark:hover:bg-teal-900/10 transition">
                    <div class="rounded-full bg-teal-100 dark:bg-teal-500/15 p-4 text-teal-600 dark:text-teal-400">
                        <svg wire:loading.remove wire:target="file" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.9 5 5 0 019.9-1A4.5 4.5 0 1117 16H7zm5-7v6m0-6l-2.5 2.5M12 9l2.5 2.5" />
                        </svg>
                        <svg wire:loading wire:target="file" class="h-8 w-8 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-white" wire:loading.remove wire:target="file">Click to choose a CSV file</p>
                        <p class="font-semibold text-gray-800 dark:text-white" wire:loading wire:target="file">Reading your file…</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">.csv exported from your bank · up to 5&nbsp;MB</p>
                    </div>
                    <input id="csv-file" type="file" wire:model="file" accept=".csv,text/csv,text/plain" class="hidden">
                </label>

                @error('file')
                    <p class="mt-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="mt-6 rounded-lg bg-gray-50 dark:bg-neutral-700/40 p-4 text-sm text-gray-600 dark:text-gray-300">
                    <p class="font-medium text-gray-800 dark:text-gray-100 mb-1">How to get a CSV</p>
                    Log in to your bank, open an account, and look for <span class="font-medium">Download</span> /
                    <span class="font-medium">Export</span> → choose <span class="font-medium">CSV</span>. We never ask for your
                    banking login — you only upload the file you downloaded.
                </div>
            </div>
        @endif

        {{-- STEP 2: MAP + PREVIEW --}}
        @if($step === 'map')
            <div class="space-y-6">
                {{-- Mapping --}}
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Map your columns</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $totalRows }} {{ Str::plural('row', $totalRows) }} detected</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Date column --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date column <span class="text-red-500">*</span></label>
                            <select wire:model.live="dateColumn" @class([$baseSelect = 'w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent', 'border-red-500' => $errors->has('dateColumn'), 'border-gray-300 dark:border-neutral-600' => ! $errors->has('dateColumn')])>
                                <option value="">— Select —</option>
                                @foreach($this->columnOptions as $i => $label)
                                    <option value="{{ $i }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('dateColumn') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Description column --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description column</label>
                            <select wire:model.live="descriptionColumn" class="w-full px-4 py-2.5 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">— None (use "Imported expense") —</option>
                                @foreach($this->columnOptions as $i => $label)
                                    <option value="{{ $i }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Amount handling --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount columns</label>
                            <div class="flex flex-wrap gap-4 mb-3">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="radio" wire:model.live="amountMode" value="single" class="text-teal-600 focus:ring-teal-500">
                                    One signed amount column
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="radio" wire:model.live="amountMode" value="separate" class="text-teal-600 focus:ring-teal-500">
                                    Separate debit / credit columns
                                </label>
                            </div>

                            @if($amountMode === 'single')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <select wire:model.live="amountColumn" @class(['w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent', 'border-red-500' => $errors->has('amountColumn'), 'border-gray-300 dark:border-neutral-600' => ! $errors->has('amountColumn')])>
                                            <option value="">— Select amount column —</option>
                                            @foreach($this->columnOptions as $i => $label)
                                                <option value="{{ $i }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('amountColumn') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" wire:model.live="onlyOutflows" class="rounded text-teal-600 focus:ring-teal-500">
                                        Only import money out (negative amounts)
                                    </label>
                                </div>
                            @else
                                <div class="md:w-1/2">
                                    <select wire:model.live="debitColumn" @class(['w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent', 'border-red-500' => $errors->has('debitColumn'), 'border-gray-300 dark:border-neutral-600' => ! $errors->has('debitColumn')])>
                                        <option value="">— Select debit (spent) column —</option>
                                        @foreach($this->columnOptions as $i => $label)
                                            <option value="{{ $i }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('debitColumn') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Credits / deposits are treated as income and skipped.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Date format --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date format</label>
                            <select wire:model.live="dateFormat" class="w-full px-4 py-2.5 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                @foreach($dateFormats as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fallback category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category (optional)</label>
                            <select wire:model.live="defaultCategoryId" class="w-full px-4 py-2.5 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">Uncategorized</option>
                                @foreach($this->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @if($autoCategorize)
                                    Used for rows that can’t be auto-categorized.
                                @else
                                    Applied to every imported row.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-gray-100 dark:border-neutral-700 pt-4">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="autoCategorize" class="rounded text-teal-600 focus:ring-teal-500">
                            Auto-categorize from description
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="hasHeaderRow" class="rounded text-teal-600 focus:ring-teal-500">
                            First row is a header
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="skipDuplicates" class="rounded text-teal-600 focus:ring-teal-500">
                            Skip duplicates already in my expenses
                        </label>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Preview</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Showing first {{ min(15, $totalRows) }} of {{ $totalRows }}</span>
                    </div>

                    @if($dateColumn === null || $dateColumn === '')
                        <div class="py-8 text-center text-gray-500 dark:text-gray-400">Choose a date column to see a preview.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-neutral-700">
                                        <th class="py-2 pr-4 font-semibold">Date</th>
                                        <th class="py-2 pr-4 font-semibold">Title</th>
                                        <th class="py-2 pr-4 font-semibold">Category</th>
                                        <th class="py-2 pr-4 font-semibold text-right">Amount</th>
                                        <th class="py-2 font-semibold text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-neutral-700/60">
                                    @forelse($this->previewRows as $preview)
                                        @php $n = $preview['normalized']; @endphp
                                        <tr class="{{ $n ? '' : 'opacity-50' }}">
                                            <td class="py-2.5 pr-4 text-gray-700 dark:text-gray-200 whitespace-nowrap">{{ $n['date'] ?? $preview['rawDate'] }}</td>
                                            <td class="py-2.5 pr-4 text-gray-700 dark:text-gray-200">{{ $n['title'] ?? \Illuminate\Support\Str::limit($preview['rawDescription'], 40) }}</td>
                                            <td class="py-2.5 pr-4 whitespace-nowrap">
                                                @if($preview['categoryName'] ?? null)
                                                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-200">
                                                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $preview['categoryColor'] }};"></span>
                                                        {{ $preview['categoryName'] }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 pr-4 text-right font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $n ? '$'.number_format($n['amount'], 2) : '—' }}</td>
                                            <td class="py-2.5 text-right">
                                                @if($n)
                                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-xs font-medium">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        Will import
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500 text-xs">Skipped</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">No rows to preview.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Income/credits and unreadable rows are skipped automatically. Amounts are stored as positive expense values.</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <button type="button" wire:click="startOver"
                        class="px-5 py-3 rounded-lg font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-neutral-700 hover:bg-gray-200 dark:hover:bg-neutral-600 transition self-start">
                        Start over
                    </button>
                    <button type="button" wire:click="import" wire:loading.attr="disabled" wire:target="import"
                        class="px-8 py-3 bg-gradient-to-r from-teal-600 to-emerald-600 text-white rounded-lg font-semibold hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg wire:loading.remove wire:target="import" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V3m0 9l-3-3m3 3l3-3" />
                        </svg>
                        <svg wire:loading wire:target="import" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="import">Import expenses</span>
                        <span wire:loading wire:target="import">Importing…</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
