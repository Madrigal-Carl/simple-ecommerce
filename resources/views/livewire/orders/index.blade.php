<div
    x-data="{ orderDetailsOpen: false }"
    x-effect="document.body.classList.toggle('overflow-hidden', orderDetailsOpen)"
    x-on:open-order-details.window="orderDetailsOpen = true"
>
    <div class="flex w-full flex-1 flex-col gap-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <flux:heading size="xl" level="1">{{ __('Orders') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Review customer orders and their current totals.') }}
                </flux:subheading>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                :placeholder="__('Search reference number...')" class="h-10 w-full sm:max-w-sm" />
        </div>

        <div
            class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div wire:loading.flex wire:target="search,previousPage,nextPage"
                class="absolute inset-0 z-20 hidden items-start justify-center bg-white/65 pt-28 backdrop-blur-[1px] dark:bg-zinc-900/65">
                <div
                    class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-4 py-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="size-4 animate-pulse rounded bg-zinc-300 dark:bg-zinc-600"></div>
                    <flux:text>{{ __('Updating orders...') }}</flux:text>
                </div>
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-[44rem] px-4 sm:px-6">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column class="sticky start-0 z-10 bg-white dark:bg-zinc-900">{{ __('Reference
                                number') }}</flux:table.column>
                            <flux:table.column>{{ __('Customer') }}</flux:table.column>
                            <flux:table.column>{{ __('Items') }}</flux:table.column>
                            <flux:table.column>{{ __('Total') }}</flux:table.column>
                            <flux:table.column>{{ __('Placed') }}</flux:table.column>
                            <flux:table.column></flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($visibleOrders as $order)
                            <flux:table.row wire:key="order-{{ $order->id }}"
                                class="group border-b border-zinc-100 transition-colors hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/60">
                                <flux:table.cell
                                    class="sticky start-0 z-10 min-w-48 bg-white py-4 group-hover:bg-zinc-50 dark:bg-zinc-900 dark:group-hover:bg-zinc-800/60">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                            <flux:icon name="receipt-percent" variant="mini" />
                                        </div>
                                        <flux:heading class="block max-w-48 truncate text-sm"
                                            title="{{ $order->reference_number }}">
                                            {{ $order->reference_number }}
                                        </flux:heading>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="py-4">
                                    <div class="flex items-center gap-2">
                                        <flux:avatar :name="$order->user->first_name.' '.$order->user->last_name"
                                            :initials="$order->user->initials()" size="sm" />
                                        <div class="min-w-0">
                                            <flux:text class="truncate font-medium text-zinc-900 dark:text-white">
                                                {{ $order->user->first_name.' '.$order->user->last_name }}
                                            </flux:text>
                                            <flux:text class="truncate text-xs">{{ $order->user->email }}</flux:text>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="py-4 tabular-nums">
                                    {{ number_format($order->order_items_count) }}
                                </flux:table.cell>
                                <flux:table.cell class="py-4 font-medium tabular-nums">
                                    ₱{{ number_format((float) $order->price, 2) }}
                                </flux:table.cell>
                                    <flux:table.cell class="py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </flux:table.cell>
                                    <flux:table.cell align="end" class="py-4">
                                        <flux:button
                                            variant="subtle"
                                            size="sm"
                                            icon="eye"
                                            class="size-8 !p-0"
                                            wire:click="viewOrder({{ $order->id }})"
                                            aria-label="{{ __('View order :reference', ['reference' => $order->reference_number]) }}"
                                        >
                                        </flux:button>
                                    </flux:table.cell>
                            </flux:table.row>
                            @empty
                            <flux:table.row>
                                    <flux:table.cell colspan="6">
                                    <div class="flex flex-col items-center justify-center gap-2 py-12 text-center">
                                        <flux:icon name="receipt-percent" class="size-8 text-zinc-400" />
                                        <flux:heading size="sm">{{ __('No orders found') }}</flux:heading>
                                        <flux:text>{{ __('Try a different reference number.') }}</flux:text>
                                        <flux:button variant="subtle" size="sm" class="mt-2" wire:click="clearSearch">
                                            {{ __('Clear search') }}
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
            </div>

            <div
                class="flex flex-col gap-3 border-t border-zinc-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-zinc-700">
                <flux:text class="text-sm">
                    {{ __('Showing :count of :total orders', ['count' => $visibleOrders->count(), 'total' =>
                    $totalOrders]) }}
                </flux:text>

                <div class="flex items-center gap-2">
                    <flux:button variant="subtle" size="sm" icon="chevron-left" wire:click="previousPage"
                        :disabled="$page === 1">
                        <span class="sr-only">{{ __('Previous page') }}</span>
                    </flux:button>
                    <flux:text class="min-w-20 text-center text-sm">
                        {{ __('Page :page of :lastPage', ['page' => $page, 'lastPage' => $lastPage]) }}
                    </flux:text>
                    <flux:button variant="subtle" size="sm" icon="chevron-right" wire:click="nextPage({{ $lastPage }})"
                        :disabled="$page === $lastPage">
                        <span class="sr-only">{{ __('Next page') }}</span>
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div
        x-cloak
        x-show="orderDetailsOpen"
        x-on:keydown.escape.window="orderDetailsOpen = false"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="order-details-title"
    >
        <button
            type="button"
            class="absolute inset-0 cursor-default"
            aria-label="{{ __('Close order details') }}"
            x-on:click="orderDetailsOpen = false"
        ></button>

        <div
            class="relative w-full max-w-md overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 text-zinc-900 shadow-2xl dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-100"
            x-on:click.stop
        >
            @if ($selectedOrder !== [])
                <div class="space-y-8">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h2 id="order-details-title" class="text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">
                                {{ __('Order details') }}
                            </h2>
                            <p class="mt-1 truncate font-mono text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $selectedOrder['reference_number'] }}
                            </p>
                        </div>
                        <button
                            type="button"
                            class="-mr-2 -mt-2 rounded-full p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white dark:focus-visible:ring-white/60"
                            aria-label="{{ __('Close order details') }}"
                            x-on:click="orderDetailsOpen = false"
                        >
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </div>

                    <div class="grid gap-5 rounded-xl bg-zinc-50 p-4 sm:grid-cols-2 dark:bg-white/5">
                        <div class="min-w-0">
                            <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-zinc-500 dark:text-zinc-400">{{ __('Customer') }}</p>
                            <p class="mt-2 truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $selectedOrder['customer'] }}</p>
                            <p class="mt-1 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedOrder['email'] }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-zinc-500 dark:text-zinc-400">{{ __('Placed') }}</p>
                            <p class="mt-2 text-sm font-semibold text-zinc-900 dark:text-white">{{ $selectedOrder['placed_at'] }}</p>
                        </div>
                        <div class="border-t border-zinc-200 pt-4 sm:col-span-2 dark:border-white/10">
                            <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-zinc-500 dark:text-zinc-400">{{ __('Order total') }}</p>
                            <p class="mt-1 text-xl font-semibold tabular-nums text-emerald-400">₱{{ number_format((float) $selectedOrder['price'], 2) }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[11px] font-medium uppercase tracking-[0.16em] text-zinc-500 dark:text-zinc-400">{{ __('Products ordered') }}</p>
                        <div class="mt-3 divide-y divide-zinc-200 dark:divide-white/10">
                            @foreach ($selectedOrder['items'] as $item)
                                <div class="flex items-center justify-between gap-4 py-4 first:pt-2 last:pb-2">
                                    <div class="min-w-0 leading-tight">
                                        <p class="truncate text-sm font-normal text-zinc-800 dark:text-zinc-100">{{ $item['name'] }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Quantity: :quantity', ['quantity' => $item['quantity']]) }}</p>
                                    </div>
                                    <p class="shrink-0 text-sm tabular-nums text-zinc-600 dark:text-zinc-300">₱{{ number_format((float) $item['price'], 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
