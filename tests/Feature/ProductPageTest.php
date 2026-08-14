<?php

use App\Livewire\Products\Index;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

test('products page is available to authenticated users', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Products')
        ->assertSee('Add product');
});

test('products page filters products and creates a product', function () {
    $this->actingAs(User::factory()->create());
    Product::factory()->create(['name' => 'Canvas Weekender', 'status' => true]);
    Product::factory()->create(['name' => 'Desk Lamp', 'status' => false]);

    Livewire::test(Index::class)
        ->set('search', 'Canvas')
        ->assertSet('page', 1)
        ->assertSee('Canvas Weekender')
        ->set('statusFilter', 'inactive')
        ->assertSet('page', 1)
        ->call('openCreateModal')
        ->set('productName', 'New Product')
        ->set('productDescription', 'A product added from the UI.')
        ->set('productQuantity', 12)
        ->set('productPrice', '29.99')
        ->set('productStatus', 'active')
        ->call('saveProduct')
        ->assertHasNoErrors()
        ->assertSet('productName', '');

    $this->assertDatabaseHas('products', [
        'name' => 'New Product',
        'price' => '29.99',
        'status' => true,
    ]);
});

test('product can be updated', function () {
    $this->actingAs(User::factory()->create());
    $product = Product::factory()->create();

    Livewire::test(Index::class)
        ->call('editProduct', $product->id)
        ->set('productName', 'Updated Product')
        ->set('productDescription', 'Updated description.')
        ->set('productQuantity', 7)
        ->set('productPrice', '49.99')
        ->set('productStatus', 'inactive')
        ->call('saveProduct')
        ->assertHasNoErrors();

    expect($product->refresh()->name)->toBe('Updated Product')
        ->and($product->description)->toBe('Updated description.')
        ->and($product->quantity)->toBe(7)
        ->and((string) $product->price)->toBe('49.99')
        ->and($product->status)->toBeFalse();
});

test('product deletion requires confirmation state', function () {
    $this->actingAs(User::factory()->create());
    $product = Product::factory()->create(['name' => 'Canvas Weekender']);

    Livewire::test(Index::class)
        ->call('confirmDelete', $product->id)
        ->assertSet('deletingProductId', $product->id)
        ->assertSet('deletingProductName', 'Canvas Weekender')
        ->call('deleteProduct', $product->id)
        ->assertSet('deletingProductId', null);

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('product status can be toggled', function () {
    $product = Product::factory()->create(['status' => true]);

    Livewire::test(Index::class)
        ->call('toggleStatus', $product->id);

    expect($product->refresh()->status)->toBeFalse();
});

test('editing a product uses the same validation rules', function () {
    $product = Product::factory()->create();

    Livewire::test(Index::class)
        ->call('editProduct', $product->id)
        ->set('productName', '')
        ->call('saveProduct')
        ->assertHasErrors(['productName' => 'required']);
});
