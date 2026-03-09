<div class="min-h-screen bg-gray-50 dark:bg-neutral-900 transition-colors duration-300">
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Recurring Expenses</h1>
                    <p class="text-blue-100 mt-1">Manage your subscriptions and recurring bills</p>
                </div>
                <a href="/expenses/create" class="bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-lg font-semibold transition backdrop-blur-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Recurring
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>{{ session('message') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 dark:hover:text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 border-l-4 border-blue-500 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1 font-medium">Active Recurring</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $recurringExpenses->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 border-l-4 border-purple-500 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1 font-medium">Monthly Total</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            ${{ number_format($recurringExpenses->where('recurring_frequency', 'monthly')->sum('amount'), 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 border-l-4 border-emerald-500 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1 font-medium">Generated This Month</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $recurringExpenses->sum(fn($e) => $e->childExpenses()->whereMonth('date', now()->month)->whereYear('date', now()->year)->count()) }}
                        </p>
                    </div>
                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        @if($recurringExpenses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recurringExpenses as $expense)
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl dark:hover:shadow-2xl dark:hover:shadow-black/40 transition-all duration-300">
                        <div class="p-6 {{ $expense->category ? '' : 'bg-gradient-to-r from-gray-600 to-gray-700' }}" 
                             @if($expense->category)
                                style="background: linear-gradient(135deg, {{ $expense->category->color }} 0%, {{ $expense->category->color }}dd 100%);"
                             @endif>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-white mb-1">{{ $expense->title }}</h3>
                                    @if($expense->category)
                                        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold text-white">
                                            {{ $expense->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="/expenses/{{ $expense->id }}/edit" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button wire:click="confirmDelete({{ $expense->id }})" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400 text-sm">Amount</span>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($expense->amount, 2) }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400 text-sm">Frequency</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300">
                                    {{ ucfirst($expense->recurring_frequency) }}
                                </span>
                            </div>

                            <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-neutral-700">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Starts</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-200">{{ $expense->recurring_start_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Ends</span>
                                    <span class="font-medium {{ $expense->recurring_end_date ? 'text-gray-900 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500 italic' }}">
                                        {{ $expense->recurring_end_date ? $expense->recurring_end_date->format('M d, Y') : 'Never' }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-100 dark:border-neutral-700">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Total Generated</span>
                                    <span class="font-bold text-purple-600 dark:text-purple-400">
                                        {{ $expense->childExpenses->count() }} expenses
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-12 text-center transition-all">
                <div class="flex justify-center mb-4">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-full">
                        <svg class="w-16 h-16 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Recurring Expenses Yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Set up recurring expenses like subscriptions or rent to track them automatically.</p>
                <a href="/expenses/create" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition">
                    New Recurring Expense
                </a>
            </div>
        @endif
    </div>
</div>