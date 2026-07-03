<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100"
    x-data="{ mobileNav: false }">

    {{-- ───────────────────────── Top navigation ───────────────────────── --}}
    <header
        class="sticky top-0 z-40 border-b border-zinc-200/80 bg-white/85 backdrop-blur-md dark:border-zinc-800/80 dark:bg-zinc-900/80">
        <div class="mx-auto flex h-16 max-w-[1400px] items-center gap-3 px-4 sm:px-6 lg:px-8">

            {{-- Brand --}}
            <a href="{{ route('dashboard') }}" wire:navigate class="flex shrink-0 items-center gap-2.5">
                <span class="brand-gradient flex size-9 items-center justify-center rounded-xl text-white shadow-sm">
                    <x-app-logo-icon class="size-5 fill-current" />
                </span>
                <span class="font-display text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
            </a>

            {{-- Desktop nav --}}
            @php
                $nav = [
                    ['label' => __('Dashboard'), 'icon' => 'home', 'route' => 'dashboard', 'active' => ['dashboard']],
                    ['label' => __('Expenses'), 'icon' => 'banknotes', 'route' => 'expenses.index', 'active' => ['expenses.*']],
                    ['label' => __('Budgets'), 'icon' => 'credit-card', 'route' => 'budgets.index', 'active' => ['budgets.*', 'budget.*']],
                    ['label' => __('Categories'), 'icon' => 'squares-2x2', 'route' => 'categories.index', 'active' => ['categories.*']],
                    ['label' => __('Recurring'), 'icon' => 'arrow-path', 'route' => 'recurring-expenses.index', 'active' => ['recurring-expenses.*']],
                ];
            @endphp
            <nav class="ms-4 hidden items-center gap-1 lg:flex">
                @foreach ($nav as $item)
                    @php $isCurrent = request()->routeIs($item['active']); @endphp
                    <a href="{{ route($item['route']) }}" wire:navigate @class([
                        'inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-300' => $isCurrent,
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! $isCurrent,
                    ])>
                        <flux:icon :name="$item['icon']" class="size-4" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="flex-1"></div>

            {{-- Theme toggle --}}
            <button x-data type="button"
                x-on:click="$flux.appearance = document.documentElement.classList.contains('dark') ? 'light' : 'dark'"
                title="{{ __('Toggle theme') }}"
                class="group inline-flex size-9 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-teal-600 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-teal-300">
                <flux:icon name="moon" class="size-5 transition-transform duration-300 group-hover:-rotate-12 dark:hidden" />
                <flux:icon name="sun" class="hidden size-5 transition-transform duration-300 group-hover:rotate-45 dark:block" />
            </button>

            {{-- Profile --}}
            <flux:dropdown position="bottom" align="end">
                <button type="button"
                    class="flex items-center gap-2 rounded-lg p-1 transition hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    <flux:avatar size="sm" :name="auth()->user()->name" :initials="auth()->user()->initials()"
                        class="ring-2 ring-teal-500/30" />
                    <flux:icon name="chevron-down" class="me-1 size-4 text-zinc-400 max-sm:hidden" />
                </button>

                <flux:menu>
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                        </div>
                    </div>
                    <flux:menu.separator />
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer" data-test="logout-button">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>

            {{-- Mobile hamburger --}}
            <button type="button" x-on:click="mobileNav = ! mobileNav"
                class="inline-flex size-9 items-center justify-center rounded-lg text-zinc-600 transition hover:bg-zinc-100 lg:hidden dark:text-zinc-300 dark:hover:bg-zinc-800"
                aria-label="{{ __('Toggle menu') }}">
                <flux:icon name="bars-3" class="size-5" x-show="! mobileNav" />
                <flux:icon name="x-mark" class="size-5" x-show="mobileNav" x-cloak />
            </button>
        </div>

        {{-- Mobile nav drawer --}}
        <nav x-show="mobileNav" x-collapse x-cloak
            class="border-t border-zinc-200/80 px-4 py-3 lg:hidden dark:border-zinc-800/80">
            <div class="space-y-1">
                @foreach ($nav as $item)
                    @php $isCurrent = request()->routeIs($item['active']); @endphp
                    <a href="{{ route($item['route']) }}" wire:navigate x-on:click="mobileNav = false" @class([
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                        'bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-300' => $isCurrent,
                        'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800' => ! $isCurrent,
                    ])>
                        <flux:icon :name="$item['icon']" class="size-5" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    {{-- ───────────────────────── Page content ─────────────────────────
         Bare wrapper — each page provides its own centered container so the
         content width lines up with the max-w-[1400px] nav bar above. --}}
    <main class="min-h-[calc(100vh-4rem)]">
        {{ $slot }}
    </main>

    @fluxScripts
</body>

</html>
