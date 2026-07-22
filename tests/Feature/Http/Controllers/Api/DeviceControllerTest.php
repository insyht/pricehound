<?php

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

pest()->use(RefreshDatabase::class);

it('returns a 401 when registering a device while not logged in', function () {
    $this->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'android'])
        ->assertUnauthorized();

    $this->assertDatabaseCount('devices', 0);
});

it('registers a device for the logged in user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'android'])
        ->assertOk()
        ->assertJson(['success' => __('pricehound.DeviceRegistered')]);

    $this->assertDatabaseHas('devices', [
        'user_id' => $user->id,
        'token' => 'fcm-token',
        'platform' => 'android',
    ]);
});

it('accepts ios as a platform', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'ios'])
        ->assertOk();

    $this->assertDatabaseHas('devices', ['token' => 'fcm-token', 'platform' => 'ios']);
});

it('is a no-op when the same device registers again', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'android'])
        ->assertOk();
    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'android'])
        ->assertOk();

    $this->assertDatabaseCount('devices', 1);
});

it('reassigns a token to the new user instead of duplicating it', function () {
    $previousOwner = User::factory()->create();
    $newOwner = User::factory()->create();
    Device::create(['user_id' => $previousOwner->id, 'token' => 'fcm-token', 'platform' => 'android']);

    $this->actingAs($newOwner)
        ->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'android'])
        ->assertOk();

    $this->assertDatabaseCount('devices', 1);
    $this->assertDatabaseHas('devices', ['token' => 'fcm-token', 'user_id' => $newOwner->id]);
});

it('updates the platform of an already registered token', function () {
    $user = User::factory()->create();
    Device::create(['user_id' => $user->id, 'token' => 'fcm-token', 'platform' => 'android']);

    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'ios'])
        ->assertOk();

    $this->assertDatabaseCount('devices', 1);
    $this->assertDatabaseHas('devices', ['token' => 'fcm-token', 'platform' => 'ios']);
});

it('keeps devices of other users untouched when registering a new one', function () {
    $otherUser = User::factory()->create();
    $user = User::factory()->create();
    Device::create(['user_id' => $otherUser->id, 'token' => 'other-token', 'platform' => 'android']);

    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'android'])
        ->assertOk();

    $this->assertDatabaseCount('devices', 2);
    $this->assertDatabaseHas('devices', ['token' => 'other-token', 'user_id' => $otherUser->id]);
});

it('rejects a registration without a token', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/devices', ['platform' => 'android'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('token');
});

it('rejects a registration without a platform', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => 'fcm-token'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('platform');
});

it('rejects an unsupported platform', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => 'fcm-token', 'platform' => 'windows'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('platform');

    $this->assertDatabaseCount('devices', 0);
});

it('rejects a token longer than the column allows', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/devices', ['token' => Str::random(256), 'platform' => 'android'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('token');
});
