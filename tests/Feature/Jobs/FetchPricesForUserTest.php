<?php

use App\Jobs\FetchPricesForUser;
use App\Models\Hound;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

pest()->use(RefreshDatabase::class);

it('stores the hound checked_at and created_at as the correct UTC instant, honoring their offset', function () {
    $hound = Hound::factory()->create(['online' => true, 'url' => 'http://hound.test']);
    $user = User::factory()->create(['hound_id' => $hound->id]);
    $product = Product::factory()->create(['identifier' => '1234567890123']);
    $user->products()->attach($product);

    // The hound reports 14:00 at a +02:00 offset, i.e. 12:00 UTC. Storage is UTC, so the
    // stored values must be 12:00 — proving the offset is honored, not dropped.
    Http::fake([
        'hound.test/*' => Http::response([
            [
                'identifier' => '1234567890123',
                'price' => 1999,
                'currency' => 'EUR',
                'url' => 'http://shop.test/product',
                'created_at' => '2026-08-05T14:00:00+02:00',
                'checked_at' => '2026-08-05T14:00:00+02:00',
            ],
        ], 200),
    ]);

    FetchPricesForUser::dispatch($user);

    $this->assertDatabaseHas('product_user', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'checked_at' => '2026-08-05 12:00:00',
    ]);

    $this->assertDatabaseHas('prices', [
        'product_id' => $product->id,
        'user_id' => $user->id,
        'created_at' => '2026-08-05 12:00:00',
    ]);
});
