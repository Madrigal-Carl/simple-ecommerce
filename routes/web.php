<?php

use App\Livewire\Products\Index as ProductsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('products', ProductsIndex::class)->name('products.index');
});

require __DIR__.'/settings.php';
