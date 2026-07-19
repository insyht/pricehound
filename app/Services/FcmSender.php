<?php

namespace App\Services;

use App\Models\Device;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sends a push through the Firebase Cloud Messaging HTTP v1 API.
 *
 * Only the send call is needed here, so this talks to the endpoint directly rather than pulling
 * in the full Firebase SDK (which carries grpc, protobuf and cloud-storage for no benefit).
 */
class FcmSender
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    private const ENDPOINT = 'https://fcm.googleapis.com/v1/projects/%s/messages:send';

    /** Access tokens live an hour; refreshing a little early avoids racing the expiry. */
    private const TOKEN_TTL_SECONDS = 3300;

    public function isConfigured(): bool
    {
        $credentials = config('services.firebase.credentials');

        return !empty(config('services.firebase.project_id'))
            && !empty($credentials)
            && is_readable($credentials);
    }

    /**
     * Pushes to a single device. The app expects data-only messages; a `notification` block
     * would be drawn by the system while the app is backgrounded and skip its own formatting.
     *
     * @param  array<string, string|int|null>  $data  Extra payload keys, cast to strings for FCM.
     */
    public function send(Device $device, string $title, string $body, array $data = []): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('Skipping push: Firebase is not configured.', ['device' => $device->id]);

            return false;
        }

        $payload = ['title' => $title, 'body' => $body];
        foreach ($data as $key => $value) {
            if ($value !== null) {
                // FCM rejects data payloads whose values are not strings.
                $payload[$key] = (string) $value;
            }
        }

        try {
            $response = Http::withToken($this->accessToken())
                            ->post(
                                sprintf(self::ENDPOINT, config('services.firebase.project_id')),
                                [
                                    'message' => [
                                        'token' => $device->token,
                                        'data' => $payload,
                                        // Wakes the app for delivery rather than letting Doze
                                        // hold a price alert until the next maintenance window.
                                        'android' => ['priority' => 'high'],
                                    ],
                                ]
                            );
        } catch (Throwable $t) {
            Log::error('Push failed', ['device' => $device->id, 'error' => $t->getMessage()]);

            return false;
        }

        if ($response->successful()) {
            return true;
        }

        $this->handleFailure($device, $response->status(), $response->json('error.status'));

        return false;
    }

    /**
     * A token dies when the app is uninstalled or FCM rotates it, and stays dead. Dropping the
     * row keeps us from pushing to it forever; anything else is likely transient, so it is only
     * logged and the device is left in place for the next attempt.
     */
    private function handleFailure(Device $device, int $status, ?string $errorStatus): void
    {
        $tokenIsDead = $status === 404
            || ($status === 400 && $errorStatus === 'INVALID_ARGUMENT')
            || $errorStatus === 'UNREGISTERED';

        if ($tokenIsDead) {
            Log::info('Removing dead device token', ['device' => $device->id, 'status' => $status]);
            $device->delete();

            return;
        }

        Log::error('Push rejected', [
            'device' => $device->id,
            'status' => $status,
            'error' => $errorStatus,
        ]);
    }

    /** OAuth tokens are reusable for their lifetime, so they are cached rather than re-minted. */
    private function accessToken(): string
    {
        return Cache::remember('fcm.access_token', self::TOKEN_TTL_SECONDS, function () {
            $credentials = new ServiceAccountCredentials(
                self::SCOPE,
                config('services.firebase.credentials')
            );

            return $credentials->fetchAuthToken()['access_token'];
        });
    }
}
