<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

test('database seeder creates related commerce records', function () {
    $this->seed();

    expect(User::count())->toBe(10)
        ->and(Product::count())->toBe(20)
        ->and(Order::count())->toBe(20)
        ->and(OrderItem::count())->toBe(60)
        ->and(User::query()->doesntHave('orders')->count())->toBe(0)
        ->and(Order::query()->doesntHave('orderItems')->count())->toBe(0)
        ->and(OrderItem::query()->doesntHave('order')->count())->toBe(0)
        ->and(OrderItem::query()->doesntHave('product')->count())->toBe(0);
});
