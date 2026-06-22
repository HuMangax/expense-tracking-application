<div class="-m-6 lg:-m-8 min-h-screen bg-zinc-50 dark:bg-zinc-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Hero header --}}
        <div class="brand-gradient relative overflow-hidden rounded-2xl shadow-lg animate-fade-in-up">
            <div class="absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -left-10 -bottom-20 h-56 w-56 rounded-full bg-black/10 blur-2xl"></div>
            <div class="relative px-6 py-7 sm:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-white">Dashboard</h1>
                        <p class="mt-1 text-indigo-100">Welcome back, {{ auth()->user()->name }}!</p>
                    </div>
                    <div class="flex items-center gap-2 rounded-xl bg-white/10 p-1.5 backdrop-blur">
                        <button wire:click="previousMonth" class="rounded-lg p-2 text-white transition hover:bg-white/20 active:scale-95" aria-label="Previous month">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <div class="min-w-[120px] text-center font-semibold text-white">
                            {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->format('F Y') }}
                        </div>
                        <button wire:click="nextMonth" class="rounded-lg p-2 text-white transition hover:bg-white/20 active:scale-95" aria-label="Next month">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            {{-- Total Spent --}}
            <div class="hover-lift rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Spent</h3>
                    <div class="rounded-xl bg-violet-100 p-2.5 text-violet-600 dark:bg-violet-500/15 dark:text-violet-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-zinc-800 dark:text-white">${{ number_format($totalSpent, 2) }}</div>
                @if($monthlyBudget > 0)
                    <div class="mt-2 text-sm font-medium {{ $totalSpent > $monthlyBudget ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        {{ $totalSpent > $monthlyBudget ? 'Over' : 'Under' }} budget by ${{ number_format(abs($monthlyBudget - $totalSpent), 2) }}
                    </div>
                @endif
            </div>

            {{-- Monthly Budget --}}
            <div class="hover-lift rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Monthly Budget</h3>
                    <div class="rounded-xl bg-indigo-100 p-2.5 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-zinc-800 dark:text-white">${{ number_format($monthlyBudget, 2) }}</div>
                @if($monthlyBudget > 0)
                    <div class="mt-3">
                        <div class="mb-1 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                            <span>{{ $percentageUsed }}% used</span>
                            <span>${{ number_format($monthlyBudget - $totalSpent, 2) }} left</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800">
                            <div class="h-2 rounded-full transition-all duration-500 {{ $percentageUsed > 100 ? 'bg-red-500' : ($percentageUsed > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                 style="width: {{ min($percentageUsed, 100) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Categories --}}
            <div class="hover-lift rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Categories</h3>
                    <div class="rounded-xl bg-sky-100 p-2.5 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-zinc-800 dark:text-white">{{ $expenseByCategory->count() }}</div>
                <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Active spending categories</div>
            </div>

            {{-- Recurring --}}
            <div class="hover-lift rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Recurring</h3>
                    <div class="rounded-xl bg-fuchsia-100 p-2.5 text-fuchsia-600 dark:bg-fuchsia-500/15 dark:text-fuchsia-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-zinc-800 dark:text-white">{{ $recurringExpenseCount }}</div>
                <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Active subscriptions</div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 text-lg font-semibold text-zinc-800 dark:text-white">6-Month Spending Trend</h3>
                <div class="relative h-72" wire:ignore>
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 text-lg font-semibold text-zinc-800 dark:text-white">Spending by Category</h3>
                <div class="relative h-72" wire:ignore>
                    <canvas id="categoryChart" class="{{ $expenseByCategory->isEmpty() ? 'hidden' : '' }}"></canvas>
                    <div id="categoryEmpty" class="absolute inset-0 flex items-center justify-center {{ $expenseByCategory->isEmpty() ? '' : 'hidden' }}">
                        <div class="text-center text-zinc-400 dark:text-zinc-500">
                            <svg class="mx-auto mb-2 h-10 w-10 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                            <p class="text-sm">No spending to chart yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top categories + Recent expenses --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 text-lg font-semibold text-zinc-800 dark:text-white">Top Spending Categories</h3>
                <div class="space-y-4">
                    @forelse($topCategories as $category)
                        <div class="flex items-center justify-between rounded-xl p-2 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg"
                                     style="background-color: {{ $category->color }}20;">
                                    <div class="h-3 w-3 rounded-full" style="background-color: {{ $category->color }};"></div>
                                </div>
                                <div>
                                    <div class="font-medium text-zinc-800 dark:text-white">{{ $category->name }}</div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ round(($category->total / $totalSpent) * 100, 1) }}% of total
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-zinc-800 dark:text-white">${{ number_format($category->total, 2) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 dark:text-zinc-500">
                            No expenses yet this month
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Recent Expenses</h3>
                    <a href="/expenses" wire:navigate class="text-sm font-medium text-indigo-600 transition-colors hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">View All</a>
                </div>
                <div class="space-y-1">
                    @forelse($recentExpenses as $expense)
                        <div class="flex items-center justify-between rounded-xl px-2 py-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                            <div class="flex items-center gap-3">
                                @if($expense->category)
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg"
                                         style="background-color: {{ $expense->category->color }}20;">
                                        <div class="h-3 w-3 rounded-full" style="background-color: {{ $expense->category->color }};"></div>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-zinc-800 dark:text-white">{{ $expense->title }}</div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $expense->date->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-zinc-800 dark:text-white">-${{ number_format($expense->amount, 2) }}</div>
                                @if($expense->is_auto_generated)
                                    <div class="text-xs font-medium text-fuchsia-600 dark:text-fuchsia-400">Auto</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 dark:text-zinc-500">
                            No expenses yet
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <a href="/expenses/create" wire:navigate class="hover-lift group rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 p-6 text-white shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-white/20 p-3 transition group-hover:bg-white/30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold">Add Expense</div>
                        <div class="text-sm text-indigo-100">Record new expense</div>
                    </div>
                </div>
            </a>

            <a href="/recurring-expenses" wire:navigate class="hover-lift group rounded-2xl bg-gradient-to-br from-violet-600 to-fuchsia-600 p-6 text-white shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-white/20 p-3 transition group-hover:bg-white/30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold">Recurring</div>
                        <div class="text-sm text-violet-100">Manage subscriptions</div>
                    </div>
                </div>
            </a>

            <a href="/categories" wire:navigate class="hover-lift group rounded-2xl bg-gradient-to-br from-sky-600 to-indigo-600 p-6 text-white shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-white/20 p-3 transition group-hover:bg-white/30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold">Categories</div>
                        <div class="text-sm text-sky-100">Organize expenses</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Initial chart data (re-rendered on full loads / SPA navigation) --}}
    @php
        $dashboardChartData = [
            'trendLabels' => $monthlyComparison->pluck('month'),
            'trendData' => $monthlyComparison->pluck('total'),
            'catLabels' => $expenseByCategory->pluck('name'),
            'catData' => $expenseByCategory->pluck('total'),
            'catColors' => $expenseByCategory->pluck('color'),
        ];
    @endphp
    <script type="application/json" id="dashboardChartData">{!! json_encode($dashboardChartData) !!}</script>

    <script>
        (function () {
            // Resolve once Chart.js is available, loading it on demand. This avoids a
            // race on SPA (wire:navigate) loads where the chart code can run before
            // the library has finished downloading (which left the charts blank).
            function ensureChart() {
                return new Promise(function (resolve) {
                    if (window.Chart) return resolve();
                    var s = document.getElementById('chartjs-lib');
                    if (!s) {
                        s = document.createElement('script');
                        s.id = 'chartjs-lib';
                        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                        document.head.appendChild(s);
                    }
                    var tries = 0;
                    var poll = setInterval(function () {
                        if (window.Chart) { clearInterval(poll); resolve(); }
                        else if (++tries > 250) { clearInterval(poll); } // ~10s safety cap
                    }, 40);
                });
            }

            function buildCharts(d) {
                if (typeof Chart === 'undefined') return;
                d = d || {};
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#a1a1aa' : '#71717a';
                const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
                const surface = isDark ? '#18181b' : '#ffffff';
                Chart.defaults.color = textColor;
                Chart.defaults.font.family = "'Instrument Sans', ui-sans-serif, system-ui, sans-serif";

                // 6-month trend (line)
                const trendEl = document.getElementById('monthlyTrendChart');
                if (trendEl) {
                    const prev = Chart.getChart(trendEl); if (prev) prev.destroy();
                    const ctx = trendEl.getContext('2d');
                    const fill = ctx.createLinearGradient(0, 0, 0, 280);
                    fill.addColorStop(0, 'rgba(99,102,241,0.30)');
                    fill.addColorStop(1, 'rgba(99,102,241,0.00)');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: d.trendLabels || [],
                            datasets: [{
                                label: 'Spending',
                                data: d.trendData || [],
                                borderColor: '#6366f1',
                                backgroundColor: fill,
                                borderWidth: 3, fill: true, tension: 0.4,
                                pointRadius: 4, pointHoverRadius: 6,
                                pointBackgroundColor: '#6366f1', pointBorderColor: surface, pointBorderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { color: gridColor }, border: { display: false } },
                                y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { callback: (v) => '$' + Number(v).toFixed(0) } }
                            }
                        }
                    });
                }

                // Spending by category (doughnut) — toggle empty-state
                const catEl = document.getElementById('categoryChart');
                const catEmpty = document.getElementById('categoryEmpty');
                const hasCats = (d.catData || []).length > 0;
                if (catEl) {
                    const prev = Chart.getChart(catEl); if (prev) prev.destroy();
                    catEl.classList.toggle('hidden', !hasCats);
                    if (catEmpty) catEmpty.classList.toggle('hidden', hasCats);
                    if (hasCats) {
                        new Chart(catEl.getContext('2d'), {
                            type: 'doughnut',
                            data: { labels: d.catLabels || [], datasets: [{ data: d.catData || [], backgroundColor: d.catColors || [], borderWidth: 3, borderColor: surface, hoverOffset: 6 }] },
                            options: { responsive: true, maintainAspectRatio: false, cutout: '64%', plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, padding: 14 } } } }
                        });
                    }
                }
            }

            function initialData() {
                const el = document.getElementById('dashboardChartData');
                try { return el ? JSON.parse(el.textContent) : {}; } catch (e) { return {}; }
            }

            // Wait for the library, then draw.
            function render(d) {
                var data = d || initialData();
                ensureChart().then(function () { buildCharts(data); });
            }
            window.renderDashboardCharts = render;

            // Bind the global listeners once (this script may re-run on navigation).
            if (!window.__dashboardChartsBound) {
                window.__dashboardChartsBound = true;

                // First load + every SPA navigation back to the dashboard.
                document.addEventListener('livewire:navigated', function () { render(); });

                // Re-render with fresh data when the selected month changes. Bind on
                // livewire:init, or immediately if Livewire is already running (e.g.
                // the user navigated here from another page).
                var bindMonth = function () {
                    if (window.__dashMonthBound || !window.Livewire) return;
                    window.__dashMonthBound = true;
                    window.Livewire.on('dashboard-updated', function (e) {
                        render(Array.isArray(e) ? (e[0] || {}) : e);
                    });
                };
                document.addEventListener('livewire:init', bindMonth);
                bindMonth();
            }

            // Draw now for the current page, covering the case where livewire:navigated
            // already fired before this component's script executed.
            if (document.getElementById('monthlyTrendChart')) {
                render();
            }
        })();
    </script>
</div>
