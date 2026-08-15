<div
    x-data="{ deleteModalOpen: false }"
    x-on:open-delete-modal.window="deleteModalOpen = true"
    x-on:close-delete-modal.window="deleteModalOpen = false"
>
    <div class="flex w-full flex-1 flex-col gap-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <flux:heading size="xl" level="1">{{ __('Products') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Manage your product catalog and inventory.') }}</flux:subheading>
            </div>

            <flux:button variant="primary" icon="plus" class="h-10 !rounded-brand-md !bg-brand-tertiary !text-white hover:!bg-brand-tertiary/90 focus-visible:!ring-brand-tertiary" wire:click="openCreateModal">
                {{ __('Add product') }}
            </flux:button>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                :placeholder="__('Search products or descriptions...')"
                class="h-10 w-full sm:max-w-sm"
            />

            <flux:select wire:model.live="statusFilter" class="h-10 w-full sm:w-40">
                <flux:select.option value="all">{{ __('All statuses') }}</flux:select.option>
                <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
                <flux:select.option value="inactive">{{ __('Inactive') }}</flux:select.option>
            </flux:select>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div
                wire:loading.flex
                wire:target="search,statusFilter,previousPage,nextPage"
                class="absolute inset-0 z-20 hidden items-start justify-center bg-white/65 pt-28 backdrop-blur-[1px] dark:bg-zinc-900/65"
            >
                <div class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-4 py-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="size-4 animate-pulse rounded bg-zinc-300 dark:bg-zinc-600"></div>
                    <flux:text>{{ __('Updating products...') }}</flux:text>
                </div>
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-[44rem] px-4 sm:px-6">
                    <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="sticky start-0 z-10 bg-white dark:bg-zinc-900">{{ __('Product') }}</flux:table.column>
                        <flux:table.column class="hidden sm:table-cell">{{ __('Inventory') }}</flux:table.column>
                        <flux:table.column class="hidden sm:table-cell">{{ __('Price') }}</flux:table.column>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($visibleProducts as $product)
                            <flux:table.row wire:key="product-{{ $product->id }}" class="group border-b border-zinc-100 transition-colors hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/60">
                                <flux:table.cell class="sticky start-0 z-10 min-w-64 bg-white py-4 group-hover:bg-zinc-50 dark:bg-zinc-900 dark:group-hover:bg-zinc-800/60">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                            <flux:icon name="cube" variant="mini" />
                                        </div>
                                        <div class="min-w-0">
                                            <flux:heading class="truncate text-lg">{{ ucwords($product->name) }}</flux:heading>
                                            <flux:text class="block max-w-56 truncate text-xs">{{ $product->description }}</flux:text>
                                        </div>
                                        <div class="ml-auto flex shrink-0 items-center gap-2 sm:hidden">
                                            <span class="text-sm tabular-nums text-zinc-700 dark:text-zinc-300">{{ number_format($product->quantity) }}</span>
                                            <span class="text-sm font-medium tabular-nums text-zinc-900 dark:text-white">₱{{ number_format((float) $product->price, 2) }}</span>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="hidden py-4 sm:table-cell">
                                    <span class="tabular-nums">{{ number_format($product->quantity) }}</span>
                                    <flux:text class="text-xs">{{ __('in stock') }}</flux:text>
                                </flux:table.cell>
                                <flux:table.cell class="hidden py-4 font-medium tabular-nums sm:table-cell">
                                    ₱{{ number_format((float) $product->price, 2) }}
                                </flux:table.cell>
                                <flux:table.cell class="py-4">
                                    <button
                                        type="button"
                                        role="switch"
                                        aria-checked="{{ $product->status ? 'true' : 'false' }}"
                                        aria-label="{{ $product->status ? __('Deactivate :name', ['name' => $product->name]) : __('Activate :name', ['name' => $product->name]) }}"
                                        wire:click="toggleStatus({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $product->id }})"
                                        class="group/toggle inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full p-0.5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 focus-visible:ring-offset-white disabled:cursor-wait disabled:opacity-60 dark:focus-visible:ring-offset-zinc-900 {{ $product->status ? 'bg-emerald-600' : 'bg-zinc-300 dark:bg-zinc-700' }}"
                                    >
                                        <span class="sr-only">{{ $product->status ? __('Active') : __('Inactive') }}</span>
                                        <span class="size-5 rounded-full bg-white shadow-sm transition-transform {{ $product->status ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </flux:table.cell>
                                <flux:table.cell align="end" class="py-4">
                                    <div class="flex items-center justify-end gap-2">
                                    <flux:button
                                        variant="subtle"
                                        size="sm"
                                        icon="pencil-square"
                                        class="size-8 !p-0"
                                        wire:click="editProduct({{ $product->id }})"
                                        aria-label="{{ __('Edit :name', ['name' => $product->name]) }}"
                                    >
                                    </flux:button>
                                    <flux:button
                                        variant="subtle"
                                        size="sm"
                                        icon="trash"
                                        class="size-8 !p-0 text-zinc-500 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400"
                                        wire:click="confirmDelete({{ $product->id }})"
                                        aria-label="{{ __('Delete :name', ['name' => $product->name]) }}"
                                    >
                                    </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5">
                                    <div class="flex flex-col items-center justify-center gap-2 py-12 text-center">
                                        <flux:icon name="cube-transparent" class="size-8 text-zinc-400" />
                                        <flux:heading size="sm">{{ __('No products found') }}</flux:heading>
                                        <flux:text>{{ __('Try adjusting your search or status filter.') }}</flux:text>
                                        <flux:button variant="subtle" size="sm" class="mt-2" wire:click="clearFilters">
                                            {{ __('Clear filters') }}
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                    </flux:table>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-zinc-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-zinc-700">
                <flux:text class="text-sm">
                    {{ __('Showing :count of :total products', ['count' => $visibleProducts->count(), 'total' => $totalProducts]) }}
                </flux:text>

                <div class="flex items-center gap-2">
                    <flux:button
                        variant="subtle"
                        size="sm"
                        icon="chevron-left"
                        wire:click="previousPage"
                        :disabled="$page === 1"
                    >
                        <span class="sr-only">{{ __('Previous page') }}</span>
                    </flux:button>
                    <flux:text class="min-w-20 text-center text-sm">
                        {{ __('Page :page of :lastPage', ['page' => $page, 'lastPage' => $lastPage]) }}
                    </flux:text>
                    <flux:button
                        variant="subtle"
                        size="sm"
                        icon="chevron-right"
                        wire:click="nextPage({{ $lastPage }})"
                        :disabled="$page === $lastPage"
                    >
                        <span class="sr-only">{{ __('Next page') }}</span>
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div
        x-cloak
        x-show="deleteModalOpen"
        x-on:keydown.escape.window="deleteModalOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/50 p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-product-title"
    >
        <button class="absolute inset-0 cursor-default" type="button" aria-label="{{ __('Close dialog') }}" x-on:click="deleteModalOpen = false"></button>
        <div class="relative w-full max-w-md rounded-xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-700 dark:bg-zinc-900" x-on:click.stop>
            <div class="flex gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                    <flux:icon name="trash" variant="mini" />
                </div>
                <div>
                    <flux:heading id="delete-product-title" size="lg">{{ __('Delete product?') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('This will remove :name from the prototype catalog.', ['name' => $deletingProductName]) }}</flux:text>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <flux:button variant="subtle" type="button" x-on:click="deleteModalOpen = false">{{ __('Cancel') }}</flux:button>
                <flux:button
                    variant="danger"
                    type="button"
                    wire:click="deleteProduct({{ $deletingProductId ?? 0 }})"
                    wire:loading.attr="disabled"
                >
                    {{ __('Delete product') }}
                </flux:button>
            </div>
        </div>
    </div>

    <flux:modal name="product-form" class="md:w-[42rem]">
        <form wire:submit="saveProduct" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingProductId === null ? __('Add product') : __('Edit product') }}
                </flux:heading>
                <flux:subheading>{{ __('Keep your catalog details up to date.') }}</flux:subheading>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:input wire:model="productName" :label="__('Name')" maxlength="255" required autofocus />
                    <flux:error name="productName" />
                </flux:field>
                <flux:field>
                    <flux:input wire:model="productPrice" :label="__('Price')" type="number" min="0" step="0.01" prefix="₱" required />
                    <flux:error name="productPrice" />
                </flux:field>
                <flux:field>
                    <flux:input wire:model="productQuantity" :label="__('Quantity')" type="number" min="0" required />
                    <flux:error name="productQuantity" />
                </flux:field>
                <flux:field>
                    <flux:select wire:model="productStatus" :label="__('Status')" required>
                        <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
                        <flux:select.option value="inactive">{{ __('Inactive') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="productStatus" />
                </flux:field>
            </div>

            <flux:field>
                <flux:textarea wire:model="productDescription" :label="__('Description')" rows="4" required />
                <flux:error name="productDescription" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="subtle" type="button">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" type="submit" class="!rounded-brand-md !bg-brand-tertiary !text-white hover:!bg-brand-tertiary/90 focus-visible:!ring-brand-tertiary">{{ __('Save product') }}</flux:button>
            </div>
        </form>
    </flux:modal>
    </div>
</div>
