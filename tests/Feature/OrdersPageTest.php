<?php

use App\Livewire\Orders\Index;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

test('orders page is available to authenticated users', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('orders.index'))
        ->assertOk()
        ->assertSee('Orders')
        ->assertSee('Search reference number');
});

test('orders can be searched by reference number', function () {
    $user = User::factory()->create();
    $matchingOrder = Order::factory()->for($user)->create(['reference_number' => 'ORD-MATCH-001']);
    Order::factory()->for($user)->create(['reference_number' => 'ORD-OTHER-002']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('search', 'ORD-MATCH-001')
        ->assertSee($matchingOrder->reference_number)
        ->assertDontSee('ORD-OTHER-002');
});

test('orders use ten rows per page', function () {
    $user = User::factory()->create();
    Order::factory()->count(11)->for($user)->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSet('perPage', 10)
        ->assertSee('Page 1 of 2')
        ->call('nextPage', 2)
        ->assertSet('page', 2);
});

test('an order can be viewed with its products and quantities', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Canvas Weekender']);
    $order = Order::factory()->for($user)->create(['reference_number' => '2026-ORDERDETAILS']);
    OrderItem::factory()->for($order)->for($product)->create([
        'price' => '89.00',
        'quantity' => 2,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('viewOrder', $order->id)
        ->assertSet('selectedOrder.reference_number', '2026-ORDERDETAILS')
        ->assertSet('selectedOrder.items.0.name', 'Canvas Weekender')
        ->assertSet('selectedOrder.items.0.quantity', 2);
});

test('orders generate year-prefixed reference numbers', function () {
    $order = Order::factory()->create();

    expect($order->reference_number)->toMatch('/^'.now()->format('Y').'-[A-Z0-9]+$/');
});
