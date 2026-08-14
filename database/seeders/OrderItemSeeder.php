<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::query()->each(function (Order $order): void {
            $total = 0;

            foreach (range(1, 3) as $item) {
                $product = Product::query()->inRandomOrder()->firstOrFail();
                $quantity = fake()->numberBetween(1, min(10, $product->quantity));

                $orderItem = OrderItem::factory()
                    ->for($order)
                    ->for($product)
                    ->create([
                        'price' => $product->price,
                        'quantity' => $quantity,
                    ]);

                $total += (float) $orderItem->price * $orderItem->quantity;
            }

            $order->update(['price' => $total]);
        });
    }
}
