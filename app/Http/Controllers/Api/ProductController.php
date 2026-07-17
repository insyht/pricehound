<?php

namespace App\Http\Controllers\Api;

use App\Enums\HoundEndpoints;
use App\Http\Controllers\Controller;
use App\Jobs\PingHound;
use App\Models\Product;
use HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{
    public const PRODUCT_PLACEHOLDER_TITLE =  'Placeholder title';

    public function add(string $identifier, ?string $title = null): JsonResponse
    {
        $user = auth()?->user() ?? null;
        if ($user === null) {
            Log::warning('Someone tried to add a product to their watchlist without being logged in');

            return response()->json(['error' => __('pricehound.NotLoggedIn')], 401);
        }

        $productModel = Product::where('identifier', $identifier)->first();
        if (!$productModel || $productModel->title === '' || $productModel->title === static::PRODUCT_PLACEHOLDER_TITLE) {
            // We don't have this product yet, Get the data from the Hound
            if (!$user->hound?->online ?? false) {
                Log::info(
                    sprintf('Can\'t get product info from Hound because it\'s offline'),
                    ['hound' => $user->hound->id, 'user' => $user->id]
                );
                PingHound::dispatchSync($user->hound);

                // todo Maybe use a fallback Hound like 'Pricehound Official'?
                return response()->json(['error' => __('pricehound.HoundOfflineOrNoHoundChosenYet')], 502);
            }

            try {
                $fetchUrl = rtrim($user->hound->url, '/') . '/';
                $fetchUrl .= sprintf(
                    HoundEndpoints::GetProductInfoByIdentifier->value,
                    $identifier
                );
                $fetchResponse = Http::withToken($user->hound_api_key)->acceptJson()->get($fetchUrl);
                if ($fetchResponse->unauthorized()) {
                    Log::warning(
                        'Invalid API token',
                        ['hound' => $user->hound->id, 'user' => $user]
                    );

                    return response()->json(['error' => __('pricehound.InvalidApiToken')], 502);
                }
                if ($fetchResponse->notFound()) {
                    // Hound does not have this product yet, create it
                    $createUrl = rtrim($user->hound->url, '/') . '/';
                    $createUrl .= HoundEndpoints::CreateProduct->value;
                    $createResponse = Http::withToken($user->hound_api_key)->acceptJson()->post(
                        $createUrl,
                        ['identifier' => $identifier, 'title' => $title ?? static::PRODUCT_PLACEHOLDER_TITLE]
                    );
                    if ($createResponse->unauthorized()) {
                        Log::warning(
                            'Invalid API token',
                            ['hound' => $user->hound->id, 'user' => $user]
                        );

                        return response()->json(['error' => __('pricehound.InvalidApiToken')], 502);
                    }
                    if ($createResponse->failed()) {
                        Log::notice(
                            'Tried to create product at a hound but it failed',
                            [
                                'hound' => $user->hound->id,
                                'product' => $identifier,
                                'title' => (string)$title,
                                'message' => $createResponse->json('message', '(None)')
                            ]
                        );

                        return response()->json(['error' => __('pricehound.CouldNotCreateProductAtHound')], 502);
                    }
                    $fetchResponse = Http::withToken($user->hound_api_key)->acceptJson()->get($fetchUrl);
                }
                if ($fetchResponse->failed()) {
                    Log::notice(
                        'Tried to get product info from a hound but we received a 404 (response failed)',
                        ['hound' => $user->hound->id, 'product' => $identifier, 'message' => $fetchResponse->json('message', '(None)')]
                    );

                    return response()->json(
                        [
                            'error' => __(
                                'pricehound.ProductNotFoundAtHound',
                                ['error_message' => $fetchResponse->json('message', '(None)')]
                            )
                        ],
                        404
                    );
                } elseif ($fetchResponse->successful() && is_array($fetchResponse->json('data'))) {
                    if ($productModel !== null) {
                        $productModel->update(['title' => $fetchResponse->json('data.title', static::PRODUCT_PLACEHOLDER_TITLE)]);
                    } else {
                        // We don't have this product yet, create it
                        Log::info(
                            'Creating new product so that it can be added to a wishlist',
                            [
                                'user' => $user,
                                'hound' => $user->hound->id,
                                'product' => $fetchResponse->json('data.identifier', $identifier)
                            ]
                        );
                        $productModel = Product::create(
                            [
                                'title' => $fetchResponse->json('data.title', static::PRODUCT_PLACEHOLDER_TITLE),
                                'created_by_user_id' => $user->id,
                                'identifier' => $fetchResponse->json('data.identifier', $identifier),
                            ]
                        );
                    }
                } else {
                    Log::warning(
                        'Invalid data received from hound (missing key "data" in json or "data" is not an array)',
                        ['hound' => $user->hound->id, 'product' => $identifier, 'message' => $fetchResponse->json('message', '(None)')]
                    );

                    return response()->json(['error' => __('pricehound.FailedGettingProductInfoFromHound')], 500);
                }
            } catch (Throwable $t) {
                Log::warning(
                    'Could not get product info from hound',
                    ['hound' => $user->hound->id, 'product' => $identifier, 'error' => $t->getMessage()]
                );

                return response()->json(['error' => __('pricehound.FailedGettingProductInfoFromHound')], 500);
            }
        }
        // Idempotent attach: avoids a duplicate-key error when the product is already
        // on the user's wishlist.
        $user->products()->syncWithoutDetaching([$productModel->id]);

        return response()->json(['success' => __('pricehound.ProductAddedToWishlist')], 200);
    }

    public function show(Product $product): JsonResponse
    {
        if (auth()?->user()?->products->contains($product)) {
            return response()->json($product->load('prices'));
        }

        return response()->json(['error' => 'Product not found'], 404);
    }

    public function index(): JsonResponse
    {
        return response()->json(auth()?->user()?->products->load('prices') ?? []);
    }

    public function search(string $identifier): JsonResponse
    {
        $user = auth()?->user() ?? null;
        if ($user === null) {
            Log::warning('Someone tried to search for a product without being logged in');

            return response()->json(['error' => __('pricehound.NotLoggedIn')], 401);
        }

        if (!$user->hound?->online ?? false) {
            Log::info(
                sprintf('Can\'t get product info from Hound because it\'s offline'),
                ['hound' => $user->hound->id, 'user' => $user->id]
            );
            PingHound::dispatchSync($user->hound);

            // todo Maybe use a fallback Hound like 'Pricehound Official'?
            return response()->json(['error' => __('pricehound.HoundOfflineOrNoHoundChosenYet')], 502);
        }

        try {
            $url = rtrim($user->hound->url, '/') . '/';
            $url .= sprintf(
                HoundEndpoints::SearchProductByIdentifier->value,
                base64_encode($identifier)
            );
            $response = Http::withToken($user->hound_api_key)->acceptJson()->get($url);
            if ($response->unauthorized()) {
                Log::warning(
                    'Invalid API token',
                    ['hound' => $user->hound->id, 'user' => $user]
                );
            }
            if ($response->failed()) {
                Log::notice(
                    'Tried to search for a product through a hound but we received a 404 (response failed)',
                    ['hound' => $user->hound->id, 'product' => $identifier, 'message' => $response->json('message', '(None)')]
                );
                return response()->json(
                    [
                        'error' => __(
                            'pricehound.ProductNotFoundAtHound',
                            ['error_message' => $response->json('message', '(None)')]
                        )
                    ],
                    404
                );
            } elseif ($response->successful() && is_array($response->json('results'))) {
                $results = [];
                foreach ($response->json('results') as $result) {
                    $results[] = [
                        'id' => $result['id'],
                        'identifier' => $result['identifier'],
                        'title' => $result['title'],
                    ];
                }
                return response()->json($results, 200);
            } else {
                throw new HttpResponseException(
                    'Invalid data received from hound (missing key "data" in json or "data" is not an array)'
                );
            }
        } catch (Throwable $t) {
            Log::warning(
                'Could not search for a product through the hound',
                ['hound' => $user->hound->id, 'product' => $identifier, 'error' => $t->getMessage()]
            );
        }

        return response()->json(['error' => __('pricehound.FailedSearchingForProductThroughHound')], 500);
    }
}
