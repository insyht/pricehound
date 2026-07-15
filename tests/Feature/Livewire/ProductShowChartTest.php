<?php

use App\Models\Price;
use App\Models\Product;
use App\Models\User;
use App\Livewire\Product\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

pest()->use(RefreshDatabase::class);

it('renders the product show page with price-history chart data', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $user->products()->attach($product);
    Price::factory()->count(3)->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Show::class, ['product' => $product->id])
        ->assertOk()
        ->assertViewHas('priceLabels')
        ->assertViewHas('priceData')
        ->assertSee('new ApexCharts', false);
});

it('renders the product show page when the product is missing', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Show::class, ['product' => 999999])
        ->assertOk();
});
