<?php

use App\Livewire\Orders\Index as OrdersIndex;
use App\Livewire\Products\Index as ProductsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', 'products')->name('dashboard');
    Route::livewire('products', ProductsIndex::class)->name('products.index');
    Route::livewire('orders', OrdersIndex::class)->name('orders.index');
});

require __DIR__.'/settings.php';
