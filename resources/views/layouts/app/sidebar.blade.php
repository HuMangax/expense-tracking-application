<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                <flux:navlist.item icon="squares-2x2" :href="route('categories.index')"
                    :current="request()->routeIs('categories.index')" wire:navigate>{{ __('Categories') }}
                </flux:navlist.item>
                <flux:navlist.item icon="credit-card" :href="route('budgets.index')"
                    :current="request()->routeIs('budgets.index')" wire:navigate>{{ __('Budgets') }}
                </flux:navlist.item>
                <flux:navlist.item icon="banknotes" :href="route('expenses.index')"
                    :current="request()->routeIs('expenses.index')" wire:navigate>{{ __('Expenses') }}
                </flux:navlist.item>
                <flux:navlist.item icon="arrow-path" :href="route('recurring-expenses.index')"
                    :current="request()->routeIs('recurring-expenses.index')" wire:navigate>{{ __('Recurring Expenses') }}
                </flux:navlist.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            <button
                x-data
                type="button"
                x-on:click="$flux.appearance = document.documentElement.classList.contains('dark') ? 'light' : 'dark'"
                title="{{ __('Toggle theme') }}"
                class="group flex w-full cursor-pointer items-center gap-3 rounded-lg px-2.5 py-2 text-sm font-medium text-zinc-500 transition hover:bg-zinc-200/70 hover:text-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
            >
                <flux:icon name="moon" class="size-5 transition-transform duration-300 group-hover:-rotate-12 dark:hidden" />
                <flux:icon name="sun" class="hidden size-5 transition-transform duration-300 group-hover:rotate-45 dark:block" />
                <span class="dark:hidden">{{ __('Dark mode') }}</span>
                <span class="hidden dark:inline">{{ __('Light mode') }}</span>
            </button>

            <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Documentation') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

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
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>