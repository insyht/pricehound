<?php

use App\Http\Controllers\Api\ProductController;
use App\Jobs\PingHound;
use App\Models\Hound;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

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

it('returns a 401 when adding a product while not logged in', function () {
    $this->postJson('/api/products/add/1234567890123')
        ->assertUnauthorized();
});

it('returns a 502 when the hound is offline', function () {
    Bus::fake();

    $hound = Hound::factory()->create(['online' => false]);
    $user = User::factory()->create(['hound_id' => $hound->id]);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertStatus(502)
        ->assertJson(['error' => __('pricehound.HoundOfflineOrNoHoundChosenYet')]);

    Bus::assertDispatched(PingHound::class);
});

it('returns a 500 when the hound connection throws an exception', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);

    Http::fake([
        'hound.test/*' => fn () => throw new \RuntimeException('Connection refused'),
    ]);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertStatus(500)
        ->assertJson(['error' => __('pricehound.FailedGettingProductInfoFromHound')]);
});

it('creates the product at the hound when it does not have it yet, then adds it to the wishlist', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);

    // First fetch: hound does not have the product yet (404). The controller then
    // creates it (POST), after which the re-fetch (GET) returns the product info.
    $productInfo = Http::response([
        'data' => [
            'title' => 'Freshly Created Product',
            'identifier' => '1234567890123',
        ],
    ], 200);
    Http::fakeSequence('hound.test/*')
        ->push(['message' => 'Product not found'], 404)
        ->pushResponse(Http::response(['data' => ['identifier' => '1234567890123']], 201))
        ->pushResponse($productInfo);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertOk()
        ->assertJson(['success' => __('pricehound.ProductAddedToWishlist')]);

    $this->assertDatabaseHas('products', [
        'title' => 'Freshly Created Product',
        'identifier' => '1234567890123',
        'created_by_user_id' => $user->id,
    ]);

    expect($user->products)->toHaveCount(1);
});

it('creates a new product and adds it to the user wishlist when the hound returns product info', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);

    Http::fake([
        'hound.test/*' => Http::response([
            'data' => [
                'title' => 'Test Product',
                'identifier' => '1234567890123',
            ],
        ], 200),
    ]);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertOk()
        ->assertJson(['success' => __('pricehound.ProductAddedToWishlist')]);

    $this->assertDatabaseHas('products', [
        'title' => 'Test Product',
        'identifier' => '1234567890123',
        'created_by_user_id' => $user->id,
    ]);

    expect($user->products)->toHaveCount(1);
    expect($user->products->first()->identifier)->toBe('1234567890123');
});

it('attaches an existing product to the user wishlist without changing its title', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);
    $product = Product::factory()->create(['identifier' => '1234567890123', 'title' => 'Existing Title']);

    Http::fake([
        'hound.test/*' => Http::response([
            'data' => [
                'title' => 'Different Title From Hound',
                'identifier' => '1234567890123',
            ],
        ], 200),
    ]);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertOk()
        ->assertJson(['success' => __('pricehound.ProductAddedToWishlist')]);

    expect($product->fresh()->title)->toBe('Existing Title');
    expect($user->fresh()->products)->toHaveCount(1);
});

it('updates the title of an existing product that has a placeholder title', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);
    $product = Product::factory()->create(['identifier' => '1234567890123', 'title' => ProductController::PRODUCT_PLACEHOLDER_TITLE]);

    Http::fake([
        'hound.test/*' => Http::response([
            'data' => [
                'title' => 'Real Product Title',
                'identifier' => '1234567890123',
            ],
        ], 200),
    ]);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertOk()
        ->assertJson(['success' => __('pricehound.ProductAddedToWishlist')]);

    expect($product->fresh()->title)->toBe('Real Product Title');
});

it('updates the title of an existing product that has an empty title', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);
    $product = Product::factory()->create(['identifier' => '1234567890123', 'title' => '']);

    Http::fake([
        'hound.test/*' => Http::response([
            'data' => [
                'title' => 'Real Product Title',
                'identifier' => '1234567890123',
            ],
        ], 200),
    ]);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertOk();

    expect($product->fresh()->title)->toBe('Real Product Title');
});

it('returns a 500 when the hound returns invalid data', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);

    Http::fake([
        'hound.test/*' => Http::response(['data' => 'not-an-array'], 200),
    ]);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertStatus(500)
        ->assertJson(['error' => __('pricehound.FailedGettingProductInfoFromHound')]);
});

it('returns a 404 when the hound returns a server error', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);

    Http::fake([
        'hound.test/*' => Http::response(['message' => 'Internal Server Error'], 500),
    ]);

    $this->actingAs($user)
        ->postJson('/api/products/add/1234567890123')
        ->assertNotFound()
        ->assertJson([
            'error' => __('pricehound.ProductNotFoundAtHound', ['error_message' => 'Internal Server Error']),
        ]);
});
