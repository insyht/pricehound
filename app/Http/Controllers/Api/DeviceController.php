<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    /**
     * Registers the calling app install for push notifications.
     *
     * The app re-sends its token on every start and whenever FCM rotates it, so this upserts on
     * the token: a repeat call is a no-op, and a token that moved to another account (reinstall,
     * different login on the same phone) is reassigned instead of duplicated.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|max:255',
            'platform' => 'required|string|in:android,ios',
        ]);

        $user = auth()->user();
        if ($user === null) {
            Log::warning('Someone tried to register a device without being logged in');

            return response()->json(['error' => __('pricehound.NotLoggedIn')], 401);
        }

        Device::updateOrCreate(
            ['token' => $validated['token']],
            ['user_id' => $user->id, 'platform' => $validated['platform']]
        );

        return response()->json(['success' => __('pricehound.DeviceRegistered')]);
    }
}
