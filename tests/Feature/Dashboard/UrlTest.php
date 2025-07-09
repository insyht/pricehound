<?php

use App\Livewire\Url\Create as CreateUrl;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Url;
use App\Models\User;
use function Pest\Livewire\livewire;

it('creates a new url', function () {
    $url = fake()->url();
    $product = Product::factory()->create();
    $shop = Shop::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    livewire(CreateUrl::class)
        ->set('productId', $product->id)
        ->set('shopId', $shop->id)
        ->set('url', $url)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('urls.index'));

    $createdUrl = Url::where('url', $url)->first();
    expect($createdUrl->exists())->toBeTrue();
    expect($createdUrl->product_id)->toBe($product->id);
    expect($createdUrl->product_id)->toBe($product->id);
    expect($createdUrl->shop_id)->toBe($shop->id);
    expect($createdUrl->user_id)->toBe($user->id);
});
