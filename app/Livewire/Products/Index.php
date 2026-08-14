<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Products')]
class Index extends Component
{
    public string $search = '';

    public string $statusFilter = 'all';

    public int $page = 1;

    public int $perPage = 10;

    public ?int $editingProductId = null;

    public ?int $deletingProductId = null;

    public string $deletingProductName = '';

    public string $productName = '';

    public string $productDescription = '';

    public int $productQuantity = 0;

    public string $productPrice = '';

    public string $productStatus = 'active';

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedStatusFilter(): void
    {
        $this->page = 1;
    }

    public function openCreateModal(): void
    {
        $this->resetProductForm();
        Flux::modal('product-form')->show();
    }

    public function editProduct(int $productId): void
    {
        $product = Product::find($productId);

        if ($product === null) {
            return;
        }

        $this->editingProductId = $productId;
        $this->productName = $product->name;
        $this->productDescription = $product->description;
        $this->productQuantity = $product->quantity;
        $this->productPrice = (string) $product->price;
        $this->productStatus = $product->status ? 'active' : 'inactive';

        Flux::modal('product-form')->show();
    }

    public function saveProduct(): void
    {
        $validated = $this->validate([
            'productName' => ['required', 'string', 'max:255'],
            'productDescription' => ['required', 'string'],
            'productQuantity' => ['required', 'integer', 'min:0'],
            'productPrice' => ['required', 'numeric', 'min:0'],
            'productStatus' => ['required', 'in:active,inactive'],
        ]);

        $attributes = [
            'name' => $validated['productName'],
            'description' => $validated['productDescription'],
            'quantity' => $validated['productQuantity'],
            'price' => $validated['productPrice'],
            'status' => $validated['productStatus'] === 'active',
        ];

        if ($this->editingProductId === null) {
            Product::create($attributes);
        } else {
            Product::findOrFail($this->editingProductId)->update($attributes);
        }

        Flux::modal('product-form')->close();
        Flux::toast(variant: 'success', text: __($this->editingProductId === null ? 'Product added.' : 'Product updated.'));
        $this->resetProductForm();
    }

    public function confirmDelete(int $productId): void
    {
        $product = Product::find($productId);

        if ($product === null) {
            return;
        }

        $this->deletingProductId = $productId;
        $this->deletingProductName = $product->name;
        $this->dispatch('open-delete-modal');
    }

    public function deleteProduct(int $productId): void
    {
        if ($this->deletingProductId !== $productId) {
            return;
        }

        Product::findOrFail($productId)->delete();

        $this->deletingProductId = null;
        $this->deletingProductName = '';
        $this->dispatch('close-delete-modal');
        Flux::toast(variant: 'success', text: __('Product deleted.'));
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->page = 1;
    }

    public function toggleStatus(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $product->update(['status' => ! $product->status]);
    }

    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);
    }

    public function nextPage(int $lastPage): void
    {
        $this->page = min($lastPage, $this->page + 1);
    }

    private function resetProductForm(): void
    {
        $this->resetValidation();
        $this->editingProductId = null;
        $this->productName = '';
        $this->productDescription = '';
        $this->productQuantity = 0;
        $this->productPrice = '';
        $this->productStatus = 'active';
    }

    public function render(): View
    {
        $query = Product::query()
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', fn (Builder $query): Builder => $query->where('status', $this->statusFilter === 'active'))
            ->latest();

        $totalProducts = (clone $query)->count();
        $lastPage = max(1, (int) ceil($totalProducts / $this->perPage));
        $this->page = min($this->page, $lastPage);
        $visibleProducts = $query->paginate($this->perPage, ['*'], 'products', $this->page);

        return view('livewire.products.index', [
            'visibleProducts' => $visibleProducts,
            'totalProducts' => $totalProducts,
            'lastPage' => $lastPage,
        ]);
    }
}
