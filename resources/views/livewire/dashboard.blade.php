<div class="mx-auto max-w-[1400px] space-y-6 px-4 py-8 sm:px-6 lg:px-8">

    {{-- Header + month switcher --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Welcome back, {{ auth()->user()->name }} — your
                {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->format('F Y') }} overview.
            </p>
        </div>
        <div
            class="flex items-center gap-1 self-start rounded-xl border border-zinc-200 bg-white p-1 shadow-sm sm:self-auto dark:border-zinc-800 dark:bg-zinc-900">
            <button wire:click="previousMonth"
                class="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-teal-600 active:scale-95 dark:hover:bg-zinc-800 dark:hover:text-teal-300"
                aria-label="Previous month">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="min-w-[130px] text-center text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->format('F Y') }}
            </div>
            <button wire:click="nextMonth"
                class="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-teal-600 active:scale-95 dark:hover:bg-zinc-800 dark:hover:text-teal-300"
                aria-label="Next month">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Bento: headline stats --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Featured — spent this month (anchors the grid in place of the old hero) --}}
        <div class="hover-lift brand-gradient relative overflow-hidden rounded-2xl p-6 text-white shadow-lg sm:col-span-2">
            <div class="absolute -right-8 -top-10 size-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-16 -left-8 size-44 rounded-full bg-black/10 blur-2xl"></div>
            <div class="relative flex h-full flex-col">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-white/80">Spent this month</span>
                    <span class="rounded-lg bg-white/15 p-2">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 text-4xl font-bold tracking-tight">${{ number_format($totalSpent, 2) }}</div>
                @if ($monthlyBudget > 0)
                    <div class="mt-auto pt-5">
                        <div class="mb-1.5 flex items-center justify-between text-xs text-white/80">
                            <span>{{ $percentageUsed }}% of ${{ number_format($monthlyBudget, 0) }} budget</span>
                            <span>{{ $totalSpent > $monthlyBudget ? 'Over by' : 'Left' }}
                                ${{ number_format(abs($monthlyBudget - $totalSpent), 2) }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/20">
                            <div class="h-2 rounded-full bg-white transition-all duration-500"
                                style="width: {{ min($percentageUsed, 100) }}%"></div>
                        </div>
                    </div>
                @else
                    <div class="mt-auto pt-5 text-sm text-white/80">No budget set for this month yet.</div>
                @endif
            </div>
        </div>

        {{-- Monthly budget --}}
        <div class="hover-lift rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Monthly Budget</span>
                <span class="rounded-lg bg-teal-100 p-2 text-teal-600 dark:bg-teal-500/15 dark:text-teal-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 text-2xl font-bold text-zinc-900 dark:text-white">${{ number_format($monthlyBudget, 2) }}</div>
            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                {{ $monthlyBudget > 0 ? $percentageUsed . '% used' : 'Not set for this month' }}
            </div>
        </div>

        {{-- Categories --}}
        <div class="hover-lift rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Categories</span>
                <span class="rounded-lg bg-sky-100 p-2 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 text-2xl font-bold text-zinc-900 dark:text-white">{{ $expenseByCategory->count() }}</div>
            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Active this month</div>
        </div>

        {{-- Recurring --}}
        <div class="hover-lift rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Recurring</span>
                <span class="rounded-lg bg-fuchsia-100 p-2 text-fuchsia-600 dark:bg-fuchsia-500/15 dark:text-fuchsia-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 text-2xl font-bold text-zinc-900 dark:text-white">{{ $recurringExpenseCount }}</div>
            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Active subscriptions</div>
        </div>

        {{-- Quick actions --}}
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm lg:col-span-3 dark:border-zinc-800 dark:bg-zinc-900">
            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Quick actions</span>
            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <a href="/expenses/create" wire:navigate
                    class="group flex items-center gap-3 rounded-xl border border-zinc-200 p-3 transition hover:border-teal-300 hover:bg-teal-50 dark:border-zinc-800 dark:hover:border-teal-500/40 dark:hover:bg-teal-500/10">
                    <span class="brand-gradient flex size-9 shrink-0 items-center justify-center rounded-lg text-white">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>
                    <div>
                        <div class="text-sm font-semibold text-zinc-800 dark:text-white">Add expense</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">One-time</div>
                    </div>
                </a>
                <a href="/expenses/import" wire:navigate
                    class="group flex items-center gap-3 rounded-xl border border-zinc-200 p-3 transition hover:border-teal-300 hover:bg-teal-50 dark:border-zinc-800 dark:hover:border-teal-500/40 dark:hover:bg-teal-500/10">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V3m0 9l-3-3m3 3l3-3" />
                        </svg>
                    </span>
                    <div>
                        <div class="text-sm font-semibold text-zinc-800 dark:text-white">Import CSV</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">From your bank</div>
                    </div>
                </a>
                <a href="/expenses/bulk" wire:navigate
                    class="group flex items-center gap-3 rounded-xl border border-zinc-200 p-3 transition hover:border-teal-300 hover:bg-teal-50 dark:border-zinc-800 dark:hover:border-teal-500/40 dark:hover:bg-teal-500/10">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-fuchsia-100 text-fuchsia-600 dark:bg-fuchsia-500/15 dark:text-fuchsia-400">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </span>
                    <div>
                        <div class="text-sm font-semibold text-zinc-800 dark:text-white">Add multiple</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Batch entry</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 text-base font-semibold text-zinc-800 dark:text-white">6-Month Spending Trend</h3>
            <div class="relative h-72" wire:ignore>
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 text-base font-semibold text-zinc-800 dark:text-white">Spending by Category</h3>
            <div class="relative h-72" wire:ignore>
                <canvas id="categoryChart" class="{{ $expenseByCategory->isEmpty() ? 'hidden' : '' }}"></canvas>
                <div id="categoryEmpty"
                    class="absolute inset-0 flex items-center justify-center {{ $expenseByCategory->isEmpty() ? '' : 'hidden' }}">
                    <div class="text-center text-zinc-400 dark:text-zinc-500">
                        <svg class="mx-auto mb-2 h-10 w-10 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        <p class="text-sm">No spending to chart yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent expenses + top categories --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm lg:col-span-3 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Recent Expenses</h3>
                <a href="/expenses" wire:navigate
                    class="text-sm font-medium text-teal-600 transition-colors hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">View
                    All</a>
            </div>
            <div class="space-y-1">
                @forelse($recentExpenses as $expense)
                    <div class="flex items-center justify-between rounded-xl px-2 py-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                        <div class="flex items-center gap-3">
                            @if ($expense->category)
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
                            @if ($expense->is_auto_generated)
                                <div class="text-xs font-medium text-fuchsia-600 dark:text-fuchsia-400">Auto</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-zinc-400 dark:text-zinc-500">No expenses yet</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 text-base font-semibold text-zinc-800 dark:text-white">Top Spending Categories</h3>
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
                                    {{ $totalSpent > 0 ? round(($category->total / $totalSpent) * 100, 1) : 0 }}% of total
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-zinc-800 dark:text-white">${{ number_format($category->total, 2) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-zinc-400 dark:text-zinc-500">No expenses yet this month</div>
                @endforelse
            </div>
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
                const textColor = isDark ? '#94a3b8' : '#64748b';
                const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
                const surface = isDark ? '#0f172a' : '#ffffff';
                Chart.defaults.color = textColor;
                Chart.defaults.font.family = "'Manrope', ui-sans-serif, system-ui, sans-serif";

                // 6-month trend (line)
                const trendEl = document.getElementById('monthlyTrendChart');
                if (trendEl) {
                    const prev = Chart.getChart(trendEl); if (prev) prev.destroy();
                    const ctx = trendEl.getContext('2d');
                    const fill = ctx.createLinearGradient(0, 0, 0, 280);
                    fill.addColorStop(0, 'rgba(20,184,166,0.30)');
                    fill.addColorStop(1, 'rgba(20,184,166,0.00)');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: d.trendLabels || [],
                            datasets: [{
                                label: 'Spending',
                                data: d.trendData || [],
                                borderColor: '#14b8a6',
                                backgroundColor: fill,
                                borderWidth: 3, fill: true, tension: 0.4,
                                pointRadius: 4, pointHoverRadius: 6,
                                pointBackgroundColor: '#14b8a6', pointBorderColor: surface, pointBorderWidth: 2,
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

                // Re-render with fresh data when the selected month changes.
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
