@props([
    'title',
    'description',
    'class' => '',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl" class="{{ $class }}">{{ $title }}</flux:heading>
    <flux:subheading class="mt-2 text-brand-secondary">{{ $description }}</flux:subheading>
</div>
