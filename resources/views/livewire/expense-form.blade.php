<div class="min-h-screen bg-gray-50 dark:bg-neutral-900 transition-colors duration-300">
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 shadow-lg transition-colors duration-300">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        {{ $isEdit ? 'Edit Expense' : 'Add New Expense' }}
                    </h1>
                    <p class="text-blue-100 mt-1">{{ $isEdit ? 'Update expense details' : 'Record a new expense' }}</p>
                </div>
                <a href="/expenses" class="p-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <form wire:submit="save" class="space-y-6">

            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 transition-colors duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 text-lg">$</span>
                            </div>
                            <input type="number" id="amount" wire:model="amount" step="0.01" min="0" placeholder="0.00"
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 dark:placeholder-gray-400 @error('amount') border-red-500 @enderror">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date" wire:model="date"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent [color-scheme:light] dark:[color-scheme:dark] @error('date') border-red-500 @enderror">
                        @error('date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" wire:model="title" placeholder="e.g., Grocery Shopping"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 dark:placeholder-gray-400 @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Category
                        </label>
                        <select wire:model="category_id" id="category_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Don't see your category? <a href="/categories"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition">Create one</a>
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea wire:model="description" id="description" rows="3"
                            placeholder="Add any additional notes..."
                            class="w-full px-4 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none placeholder-gray-400 dark:placeholder-gray-400"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 transition-colors duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Expense Type</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <label
                        class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition {{ $type === 'one-time' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-neutral-700 hover:border-gray-300 dark:hover:border-neutral-600' }}">
                        <input type="radio" wire:model.live="type" value="one-time" class="sr-only">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg {{ $type === 'one-time' ? 'bg-blue-600' : 'bg-gray-300 dark:bg-neutral-600' }} flex items-center justify-center transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">One-time</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Single expense</div>
                                </div>
                            </div>
                        </div>
                        @if($type === 'one-time')
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 absolute right-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </label>

                    <label
                        class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition {{ $type === 'recurring' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-neutral-700 hover:border-gray-300 dark:hover:border-neutral-600' }}">
                        <input type="radio" wire:model.live="type" value="recurring" class="sr-only">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg {{ $type === 'recurring' ? 'bg-blue-600' : 'bg-gray-300 dark:bg-neutral-600' }} flex items-center justify-center transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">Recurring</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Repeating expense</div>
                                </div>
                            </div>
                        </div>
                        @if($type === 'recurring')
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 absolute right-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </label>
                </div>

                @if($type === 'recurring')
                    <div class="space-y-4 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border border-blue-100 dark:border-blue-800/30">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="recurring_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Frequency <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="recurring_frequency" id="recurring_frequency"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('recurring_frequency') border-red-500 @enderror">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                @error('recurring_frequency')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="recurring_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="recurring_start_date" wire:model="recurring_start_date"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent [color-scheme:light] dark:[color-scheme:dark] @error('recurring_start_date') border-red-500 @enderror">
                                @error('recurring_start_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="recurring_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    End Date <span class="text-gray-500 dark:text-gray-500">(Optional)</span>
                                </label>
                                <input type="date" id="recurring_end_date" wire:model="recurring_end_date"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent [color-scheme:light] dark:[color-scheme:dark] @error('recurring_end_date') border-red-500 @enderror">
                                @error('recurring_end_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-start gap-2 text-sm text-blue-800 dark:text-blue-300">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <strong>Note:</strong> This expense will automatically generate new entries based on the
                                frequency.
                                The scheduler runs daily at midnight to create new occurrences.
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-between">
                <a href="/expenses"
                    class="px-6 py-3 border border-gray-300 dark:border-neutral-600 rounded-lg text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $isEdit ? 'Update Expense' : 'Save Expense' }}
                </button>
            </div>

        </form>

    </div>
</div>