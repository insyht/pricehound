<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

pest()->use(RefreshDatabase::class);

it('logs a user in and returns a token', function () {
    $user = User::factory()->create(['email' => 'someone@example.com']);

    $response = $this->postJson('/api/login', [
        'email' => 'someone@example.com',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJson([
            'response_code' => 200,
            'status' => 'success',
            'message' => 'Login successful',
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token_type' => 'Bearer',
        ])
        ->assertJsonStructure(['token']);

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'authToken',
    ]);
});

it('returns a token that can be used to authenticate a following request', function () {
    $user = User::factory()->create(['email' => 'someone@example.com']);

    $token = $this->postJson('/api/login', [
        'email' => 'someone@example.com',
        'password' => 'password',
    ])->json('token');

    $this->withToken($token)->getJson('/api/user')
        ->assertOk()
        ->assertJson(['id' => $user->id, 'email' => 'someone@example.com']);
});

it('returns a 401 when the password is wrong', function () {
    User::factory()->create(['email' => 'someone@example.com']);

    $this->postJson('/api/login', [
        'email' => 'someone@example.com',
        'password' => 'not-the-password',
    ])->assertUnauthorized()
        ->assertJson([
            'response_code' => 401,
            'status' => 'error',
            'message' => 'Unauthorized',
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('returns a 401 when the email does not belong to a user', function () {
    $this->postJson('/api/login', [
        'email' => 'nobody@example.com',
        'password' => 'password',
    ])->assertUnauthorized()
        ->assertJson(['message' => 'Unauthorized']);
});

it('returns a 422 with the failing fields when credentials are missing', function () {
    $this->postJson('/api/login', [])
        ->assertStatus(422)
        ->assertJson([
            'response_code' => 422,
            'status' => 'error',
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure(['errors' => ['email', 'password']]);
});

it('returns a 422 when the email is not a valid address', function () {
    $this->postJson('/api/login', [
        'email' => 'not-an-email',
        'password' => 'password',
    ])->assertStatus(422)
        ->assertJsonStructure(['errors' => ['email']]);
});

it('returns a 500 when authenticating throws an unexpected exception', function () {
    Auth::shouldReceive('attempt')->once()->andThrow(new RuntimeException('Database gone'));

    $this->postJson('/api/login', [
        'email' => 'someone@example.com',
        'password' => 'password',
    ])->assertStatus(500)
        ->assertJson([
            'response_code' => 500,
            'status' => 'error',
            'message' => 'Login failed',
        ]);
});

it('returns a 401 when requesting the current user while not logged in', function () {
    $this->getJson('/api/user')->assertUnauthorized();
});

it('returns the current user when logged in', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->getJson('/api/user')
        ->assertOk()
        ->assertJson(['id' => $user->id, 'email' => $user->email]);
});
