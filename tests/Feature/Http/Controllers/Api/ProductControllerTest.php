<?php

use App\Http\Controllers\Api\ProductController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->productController = new ProductController();
});

it('returns a requested product a user has, in json format', function () {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->products()->attach($product);

    $this->actingAs($user);

    $productJson = json_decode($product->load('prices')->toJson(), true);

    $this->actingAs($user)->getJson('/api/products/' . $product->id)->assertOk()->assertJson($productJson);
});

it('returns a 404 error when getting a product a user does not have, in json format', function () {
    $product = Product::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)->getJson('api/products/' . $product->id)->assertNotFound()->assertJson(['error' => 'Product not found']);
});

it('returns a 401 when getting a user\'s product while not logged in', function () {
    $product = Product::factory()->create();

    $this->getJson('/api/products/' . $product->id)->assertUnauthorized();
});

it('returns a 401 when getting all user\'s products while not logged in', function () {
    $this->getJson('/api/products')->assertUnauthorized();
});

it('returns all products a user has, in json format', function () {
    $product1 = Product::factory()->create();
    $product2 = Product::factory()->create();
    $product3 = Product::factory()->create();

    $user = User::factory()->create();
    $user->products()->attach($product1);
    $user->products()->attach($product2);
    $user->products()->attach($product3);

    $this->actingAs($user);

    $productsJson = $user->products->load('prices')->toArray();

    $this->actingAs($user)->getJson('api/products')->assertOk()->assertJson($productsJson);
});
