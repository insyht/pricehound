<?php

namespace App\Http\Controllers\Api;

use App\Dto\GetSource;
use App\Http\Controllers\Controller;
use App\Jobs\DispatchGetSourceToPack;
use App\Models\Hound;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PackController extends Controller
{
    private const RETRIEVE_SOURCE_STATUS_TOO_SLOW = 'too_slow';
    private const RETRIEVE_SOURCE_STATUS_SUCCESS = 'success';

    public function getSource(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'id' => 'required|string',
                'url' => 'required|url',
                'headers' => 'required|array',
                'callback_url' => 'required|string',
            ]
        );
        $hound = Hound::first(); // todo How will I determine the Hound this request came from? I can't trust what the request claims

        DispatchGetSourceToPack::dispatch(
            new GetSource(
                $hound,
                $validated['id'],
                $validated['url'],
                $validated['headers'],
                $validated['callback_url']
            )
        );

        return response()->json(['success' => sprintf('Job dispatched: %s', $validated['id'])], 200);
    }

    public function retrieveSource(Request $request): JsonResponse
    {
        \Illuminate\Log\log('Recieved source');
        $validated = $request->validate(
            [
                'id' => 'required|string',
                'source' => 'required|string',
            ]
        );
        \Illuminate\Log\log('Source:', ['id' => $validated['id'], 'source' => base64_encode($validated['source'])]);
        $requestModel = \App\Models\Request::where('request_id', $validated['id'])->first();
        if (!$requestModel) {
            return response()->json(
                [
                    'status' => static::RETRIEVE_SOURCE_STATUS_TOO_SLOW,
                    'message' => __('pricehound.RetrieveSourceTooSlow')
                ],
                200
            );
        }

        try {
            $url = rtrim($requestModel->hound->url, '/') . '/' . $requestModel->callback_url;
            Http::acceptJson()->post($url, $validated);
            // todo Give this user points to increase their position on the leaderboard?
        } catch (Throwable $t) {
            Log::warning(
                'Could not send source code of a product page to hound',
                ['hound' => $requestModel->hound->id, 'requestId' => $validated['id'], 'error' => $t->getMessage()]
            );
        }

        $requestModel->delete();

        return response()->json(
            [
                'status' => static::RETRIEVE_SOURCE_STATUS_SUCCESS,
                'message' => __('pricehound.RetrieveSourceSuccess')
            ],
            200
        );
    }
}
