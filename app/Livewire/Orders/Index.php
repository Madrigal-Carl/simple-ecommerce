<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Orders')]
class Index extends Component
{
    public string $search = '';

    public int $page = 1;

    public int $perPage = 10;

    /**
     * Temporary read-only order details for the view modal.
     *
     * @var array{reference_number?: string, customer?: string, email?: string, price?: string, placed_at?: string, items?: array<int, array{name: string, price: string, quantity: int}>}
     */
    public array $selectedOrder = [];

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->page = 1;
    }

    public function viewOrder(int $orderId): void
    {
        $order = Order::with(['user', 'orderItems.product'])->find($orderId);

        if ($order === null) {
            return;
        }

        $this->selectedOrder = [
            'reference_number' => $order->reference_number,
            'customer' => $order->user->first_name.' '.$order->user->last_name,
            'email' => $order->user->email,
            'price' => (string) $order->price,
            'placed_at' => $order->created_at->format('M d, Y · g:i A'),
            'items' => $order->orderItems->map(fn (OrderItem $item): array => [
                'name' => $item->product->name,
                'price' => (string) $item->price,
                'quantity' => $item->quantity,
            ])->all(),
        ];

        $this->dispatch('open-order-details');
    }

    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);
    }

    public function nextPage(int $lastPage): void
    {
        $this->page = min($lastPage, $this->page + 1);
    }

    public function render(): View
    {
        $query = Order::query()
            ->with('user')
            ->withCount('orderItems')
            ->when($this->search !== '', function (Builder $query): void {
                $query->where('reference_number', 'like', '%'.$this->search.'%');
            })
            ->latest();

        $totalOrders = (clone $query)->count();
        $lastPage = max(1, (int) ceil($totalOrders / $this->perPage));
        $this->page = min($this->page, $lastPage);
        $visibleOrders = $query->paginate($this->perPage, ['*'], 'orders', $this->page);

        return view('livewire.orders.index', [
            'visibleOrders' => $visibleOrders,
            'totalOrders' => $totalOrders,
            'lastPage' => $lastPage,
        ]);
    }
}
