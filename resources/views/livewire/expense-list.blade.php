<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Expenses</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and track your expenses</p>
            </div>
            
            <a href="/expenses/create"
                class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Expense
            </a>
            
        </div>
    </div>
</div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session()->has('message'))
            <div
                class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>{{ session('message') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6 transition-colors duration-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Filters</h3>
                <button wire:click="$toggle('showFilters')"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                    {{ $showFilters ? 'Hide' : 'Show' }} Filters
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search expenses..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 dark:placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select wire:model.live="selectedCategory"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                    <input type="date" wire:model.live="startDate"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent [color-scheme:light] dark:[color-scheme:dark]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                    <input type="date" wire:model.live="endDate"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent [color-scheme:light] dark:[color-scheme:dark]">
                </div>
            </div>

            @if($showFilters)
                <div class="mt-4 flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Showing {{ $expenses->count() }} of {{ $expenses->total() }} expenses
                        <span class="font-semibold text-gray-900 dark:text-gray-200">• Total: ${{ number_format($total, 2) }}</span>
                    </div>
                    <button wire:click="clearFilters" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                        Clear Filters
                    </button>
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th wire:click="sortBy('date')"
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center gap-2">
                                    Date
                                    @if($sortBy === 'date')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Category
                            </th>
                            <th wire:click="sortBy('title')"
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center gap-2">
                                    Title
                                    @if($sortBy === 'title')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Description
                            </th>
                            <th wire:click="sortBy('amount')"
                                class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center justify-end gap-2">
                                    Amount
                                    @if($sortBy === 'amount')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($expenses as $expense)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" wire:key="expense-{{ $expense->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $expense->date->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $expense->date->format('l') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($expense->category)
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium"
                                            style="background-color: {{ $expense->category->color }}20; color: {{ $expense->category->color }}; border: 1px solid {{ $expense->category->color }}40;">
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $expense->category->color }};"></span>
                                            {{ $expense->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 text-sm">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $expense->title }}</div>
                                    @if($expense->is_auto_generated)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 border border-orange-200 dark:border-orange-800 mt-1">
                                            Auto-generated
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                        {{ $expense->description ?: '—' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        ${{ number_format($expense->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="/expenses/{{ $expense->id }}/edit"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button wire:click="deleteExpense({{ $expense->id }})"
                                            wire:confirm="Are you sure you want to delete this expense?"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No expenses found</h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-4">Start tracking your expenses to see them here.</p>
                                        <a href="/expenses/create"
                                            class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                                            Add Your First Expense
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>

    </div>
</div>