<?php

use App\Livewire\Product\Create as CreateProduct;
use App\Models\Product;
use App\Models\Url;
use App\Models\User;
use function Pest\Livewire\livewire;

it('creates a new product', function () {
    $productTitle = fake()->word();
    $ean = (int) fake()->ean13();

    livewire(CreateProduct::class)
        ->set('title', $productTitle)
        ->set('ean', $ean)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('products.index'));

    $createdProduct = Product::where('title', $productTitle)->first();
    expect($createdProduct->exists())->toBeTrue();
    expect($createdProduct->title)->toBe($productTitle);
    expect($createdProduct->ean)->toBe($ean);
});

it('lists all products that I am watching', function () {
    $me = User::factory()->create();
    $someoneElse = User::factory()->create();

    $this->actingAs($someoneElse);
    Url::factory()->count(5)->create();

    $this->actingAs($me);
    Url::factory()->count(3)->create();

    livewire(\App\Livewire\Product\Index::class)
        ->assertViewHas('products', function ($products) use ($me) {
            return $products->count() === 3 && $products->every(fn($product) => $product->urls->first()->user_id === $me->id);
        });
});
