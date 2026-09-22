<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function save(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->update(
            [
                'name' => $request->has('name') ? $request->input('name') : $user->name,
                'email' => $request->has('email') ? $request->input('email') : $user->email,
                'hound_id' => $request->has('hound_id') ? $request->input('hound_id') : $user->hound_id,
                'hound_api_key' => $request->has('hound_api_key') ? $request->input('hound_api_key') : $user->hound_api_key,
                'join_the_pack' => $request->has('join_the_pack') ? $request->input('join_the_pack') : $user->join_the_pack,
            ]
        );

        return response()->json(['success' => __('pricehound.UserUpdated')], 200);
    }
}
