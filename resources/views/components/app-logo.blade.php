@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="S-Eco" class="font-brand-display uppercase tracking-tight text-white" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center text-brand-tertiary">
            <x-app-logo-icon class="size-6" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="S-Eco" class="font-brand-display uppercase tracking-tight" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-12 items-center justify-center text-brand-tertiary">
            <x-app-logo-icon class="size-8" />
        </x-slot>
    </flux:brand>
@endif
