<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('S-Eco') }}</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-brand-neutral font-brand-sans text-brand-primary">
        <div class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-6 py-6 sm:px-10 lg:px-16">
            <header class="flex items-center justify-between">
                <x-app-logo href="{{ route('home') }}" class="text-brand-primary" />

                @if (Route::has('login'))
                    <nav class="flex items-center gap-5 text-sm font-semibold">
                        @auth
                            <a href="{{ route('products.index') }}" class="border-b border-transparent py-2 text-brand-primary transition hover:border-brand-primary" wire:navigate>
                                {{ __('Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="border-b border-transparent py-2 text-brand-primary transition hover:border-brand-primary" wire:navigate>
                                {{ __('Log in') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-brand-md bg-brand-tertiary px-5 py-3 text-white transition hover:bg-brand-tertiary/90" wire:navigate>
                                    {{ __('Get started') }}
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="flex flex-1 items-center py-20 lg:py-28">
                <div class="grid w-full gap-16 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                    <section class="max-w-3xl">
                        <div class="mb-8 flex size-16 items-center justify-center text-brand-tertiary sm:size-20">
                            <x-app-logo-icon class="size-full" />
                        </div>
                        <p class="mb-5 text-xs font-bold uppercase tracking-[0.2em] text-brand-secondary">{{ __('Simple commerce, clearly managed') }}</p>
                        <h1 class="font-brand-display text-6xl uppercase leading-[0.92] tracking-[-0.025em] text-brand-primary sm:text-7xl lg:text-[6rem]">
                            {{ __('Move goods.') }}<br>
                            <span class="text-brand-tertiary">{{ __('Simply.') }}</span>
                        </h1>
                        <p class="mt-8 max-w-xl text-base leading-7 text-brand-secondary sm:text-lg">
                            {{ __('S-Eco keeps products, orders, and everyday operations in one focused workspace.') }}
                        </p>
                        <div class="mt-10">
                            @auth
                                <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-brand-md bg-brand-tertiary px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-tertiary/90" wire:navigate>
                                    {{ __('Open Products') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center rounded-brand-md bg-brand-tertiary px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-tertiary/90" wire:navigate>
                                    {{ __('Get Started') }}
                                </a>
                            @endauth
                        </div>
                    </section>

                    <aside class="hidden border-l border-zinc-300 pl-10 lg:block">
                        <p class="font-brand-display text-5xl uppercase leading-none tracking-tight text-brand-primary">{{ __('Built for the next order.') }}</p>
                        <p class="mt-6 max-w-xs text-sm leading-6 text-brand-secondary">{{ __('A calm, practical home for your catalog and customer activity.') }}</p>
                    </aside>
                </div>
            </main>

            <footer class="flex items-center justify-between border-t border-zinc-300 pt-5 text-xs font-semibold uppercase tracking-[0.12em] text-brand-secondary">
                <span>{{ __('S-Eco') }}</span>
                <span>{{ __('Commerce workspace') }}</span>
            </footer>
        </div>
    </body>
</html>
