<div>
    <div class="mx-auto max-w-5xl px-4 pt-8 sm:px-6 lg:px-8">
        <div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Add Multiple Expenses</h1>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Enter several one-time expenses and save them all at once</p>
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
        <form wire:submit="save">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 transition-colors duration-300">

                {{-- Column headers (desktop) --}}
                <div class="hidden md:grid md:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1.6fr)_minmax(0,1.3fr)_auto] md:gap-3 px-1 pb-3 mb-2 border-b border-gray-100 dark:border-neutral-700 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <div>Title</div>
                    <div>Amount</div>
                    <div>Category</div>
                    <div>Date</div>
                    <div class="w-9"></div>
                </div>

                {{-- Rows --}}
                <div class="space-y-4 md:space-y-3">
                    @foreach($rows as $i => $row)
                        <div wire:key="row-{{ $row['id'] }}"
                            class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1.6fr)_minmax(0,1.3fr)_auto] md:items-start rounded-lg border border-gray-100 dark:border-neutral-700/60 md:border-0 p-3 md:p-0">
                            {{-- Title --}}
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400 md:hidden">Title</label>
                                <input type="text" wire:model="rows.{{ $i }}.title" placeholder="e.g., Groceries"
                                    class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('rows.'.$i.'.title') border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror">
                                @error('rows.'.$i.'.title')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Amount --}}
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400 md:hidden">Amount</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">$</span>
                                    <input type="number" step="0.01" min="0.01" wire:model.live.debounce.400ms="rows.{{ $i }}.amount" placeholder="0.00"
                                        class="w-full pl-7 pr-3 py-2 border rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('rows.'.$i.'.amount') border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror">
                                </div>
                                @error('rows.'.$i.'.amount')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400 md:hidden">Category</label>
                                <select wire:model="rows.{{ $i }}.category_id"
                                    class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('rows.'.$i.'.category_id') border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror">
                                    <option value="">Uncategorized</option>
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('rows.'.$i.'.category_id')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date --}}
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400 md:hidden">Date</label>
                                <input type="date" wire:model="rows.{{ $i }}.date"
                                    class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('rows.'.$i.'.date') border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror">
                                @error('rows.'.$i.'.date')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Remove --}}
                            <div class="flex md:justify-center md:pt-1">
                                <button type="button" wire:click="removeRow('{{ $row['id'] }}')"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 md:h-10 md:w-9 md:px-0"
                                    aria-label="Remove row">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="md:hidden">Remove</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Add row --}}
                <div class="mt-4">
                    <button type="button" wire:click="addRow"
                        class="inline-flex items-center gap-2 rounded-lg border border-dashed border-teal-300 dark:border-teal-700 px-4 py-2 text-sm font-medium text-teal-600 dark:text-teal-400 transition hover:bg-teal-50 dark:hover:bg-teal-900/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add another row
                    </button>
                </div>
            </div>

            {{-- Footer / actions --}}
            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ count($rows) }}</span>
                    {{ Str::plural('row', count($rows)) }}
                    &middot;
                    Total <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($this->total, 2) }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('expenses.index') }}" wire:navigate
                        class="px-5 py-3 rounded-lg font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-neutral-700 hover:bg-gray-200 dark:hover:bg-neutral-600 transition">
                        Cancel
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="px-8 py-3 bg-gradient-to-r from-teal-600 to-emerald-600 text-white rounded-lg font-semibold hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg wire:loading.remove wire:target="save" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg wire:loading wire:target="save" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Save all expenses</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
