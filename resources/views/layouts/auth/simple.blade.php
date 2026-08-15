<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body data-eco-auth class="min-h-screen bg-brand-neutral font-brand-sans antialiased text-brand-primary">
        <div class="flex min-h-svh flex-col items-center justify-center gap-8 p-6 md:p-10">
            <div class="flex w-full max-w-lg flex-col gap-8">
                <div class="flex justify-center">
                    <x-app-logo href="{{ route('home') }}" wire:navigate class="text-brand-primary" />
                </div>
                <div class="rounded-brand-lg border border-zinc-200 bg-brand-surface p-6 text-brand-primary shadow-[0_12px_35px_rgba(13,13,13,0.08)] sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
