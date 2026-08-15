<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('commerce tables contain the required columns', function () {
    expect(Schema::hasColumns('products', ['name', 'description', 'quantity', 'price', 'status']))->toBeTrue()
        ->and(Schema::hasColumns('orders', ['reference_number', 'user_id', 'price']))->toBeTrue()
        ->and(Schema::hasColumns('order_items', ['order_id', 'product_id', 'price', 'quantity']))->toBeTrue();
});

test('commerce models have the requested relationships', function () {
    $user = User::factory()->create();
    $order = Order::factory()->for($user)->create();
    $product = Product::factory()->create();
    $orderItem = OrderItem::factory()->for($order)->for($product)->create();

    expect($user->orders->pluck('id'))->toContain($order->id)
        ->and($order->orderItems->pluck('id'))->toContain($orderItem->id)
        ->and($product->orderItems->pluck('id'))->toContain($orderItem->id)
        ->and($orderItem->order->is($order))->toBeTrue()
        ->and($orderItem->product->is($product))->toBeTrue();
});
