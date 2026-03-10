<div class="min-h-screen bg-gray-50 dark:bg-neutral-900 transition-colors duration-300 rounded">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Budgets</h1>
                    <p class="text-indigo-100 mt-1">Plan and track your spending limits</p>
                </div>

                <div class="flex items-center gap-4">
                    <button wire:click="previousMonth" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg text-white transition backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <button wire:click="setCurrentMonth" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-white font-semibold transition backdrop-blur-sm">
                        {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->format('F Y') }}
                    </button>

                    <button wire:click="nextMonth" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg text-white transition backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- main content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session()->has('message'))
            <div
                class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg flex items-center justify-between transition-colors duration-300">
                <span>{{ session('message') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 dark:hover:text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 mb-8 transition-colors duration-300">
            <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400 mb-4">Overall Budget Summary</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Budget</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalBudget, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Spent</p>
                    <p class="text-2xl font-bold {{ $totalSpent > $totalBudget ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                        ${{ number_format($totalSpent, 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Remaining</p>
                    <p class="text-2xl font-bold {{ $totalRemaining < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                        ${{ number_format(abs($totalRemaining), 2) }}
                        @if($totalRemaining < 0) <span class="text-sm">over</span> @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Usage</p>
                    <p
                        class="text-2xl font-bold {{ $overallPercentage > 100 ? 'text-red-600 dark:text-red-400' : ($overallPercentage > 80 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-white') }}">
                        {{ number_format($overallPercentage, 1) }}%
                    </p>
                </div>
            </div>

            <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-4">
                <div class="h-4 rounded-full transition-all duration-300 {{ $overallPercentage > 100 ? 'bg-red-500' : ($overallPercentage > 90 ? 'bg-orange-500' : ($overallPercentage > 80 ? 'bg-yellow-500' : 'bg-green-500')) }}"
                    style="width: {{ min($overallPercentage, 100) }}%"></div>
            </div>
        </div>

         <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Category Budgets</h3>
            <a href="/budgets/create"
                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden sm:inline">Create Budget</span>
                <span class="sm:hidden">Create</span>
            </a>
        </div>

         @if($budgets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($budgets as $budget)
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300"
                        wire:key="budget-{{ $budget->id }}">
                        <div class="p-6 {{ $budget->category ? '' : 'bg-gradient-to-r from-gray-500 to-gray-600' }}"
                            @if($budget->category)
                                style="background: linear-gradient(135deg, {{ $budget->category->color }} 0%, {{ $budget->category->color }}dd 100%);"
                            @endif>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-white mb-1">
                                        {{ $budget->category ? $budget->category->name : 'Overall Budget' }}
                                    </h3>
                                    <p class="text-white/80 text-sm">Monthly Limit</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="/budgets/{{ $budget->id }}/edit"
                                        class="p-2 bg-white/20 hover:bg-white/30 rounded-lg text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button wire:click="deleteBudget({{ $budget->id }})"
                                        wire:confirm="Are you sure you want to delete this budget?"
                                        class="p-2 bg-white/20 hover:bg-white/30 rounded-lg text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Budget</span>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($budget->amount, 2) }}</span>
                            </div>

                            <div>
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="text-gray-500 dark:text-gray-400">Progress</span>
                                    <span
                                        class="font-semibold {{ $budget->percentage > 100 ? 'text-red-600 dark:text-red-400' : ($budget->percentage > 90 ? 'text-orange-600 dark:text-orange-400' : ($budget->percentage > 80 ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400')) }}">
                                        {{ number_format($budget->percentage, 1) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-300 {{ $budget->percentage > 100 ? 'bg-red-500' : ($budget->percentage > 90 ? 'bg-orange-500' : ($budget->percentage > 80 ? 'bg-yellow-500' : 'bg-green-500')) }}"
                                        style="width: {{ min($budget->percentage, 100) }}%"></div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-100 dark:border-neutral-700 space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Spent</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($budget->spent, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Remaining</span>
                                    <span
                                        class="font-semibold {{ $budget->remaining < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                        @if($budget->remaining >= 0)
                                            ${{ number_format($budget->remaining, 2) }}
                                        @else
                                            -${{ number_format(abs($budget->remaining), 2) }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2">
                                @if($budget->is_over)
                                    <div class="flex items-center gap-2 px-3 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-lg">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-red-800 dark:text-red-400">Over Budget</span>
                                    </div>
                                @elseif($budget->percentage >= 90)
                                    <div
                                        class="flex items-center gap-2 px-3 py-2 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/50 rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-orange-800 dark:text-orange-400">Critical</span>
                                    </div>
                                @elseif($budget->percentage >= 80)
                                    <div
                                        class="flex items-center gap-2 px-3 py-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/50 rounded-lg">
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-yellow-800 dark:text-yellow-400">Warning</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-green-800 dark:text-green-400">On Track</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-12 text-center transition-colors duration-300">
                <div class="flex justify-center mb-4">
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-full">
                        <svg class="w-16 h-16 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Budgets for
                    {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->format('F Y') }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Create budgets to track your spending and stay on target
                    with your financial goals.</p>
                <a href="/budgets/create"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Your First Budget
                </a>
            </div>
        @endif

        @if($budgets->count() > 0)
            <div class="mt-8 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 rounded-xl p-6 transition-colors duration-300">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">💡 Budget Tips</h4>
                        <ul class="text-sm text-blue-800 dark:text-blue-400 space-y-1">
                            <li>• Set realistic budgets based on your historical spending patterns</li>
                            <li>• Create category-specific budgets for better control</li>
                            <li>• Review and adjust your budgets monthly</li>
                            <li>• Enable email alerts to get notified when approaching limits</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        
    </div>
        


</div>