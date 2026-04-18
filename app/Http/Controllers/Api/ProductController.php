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

    public function add(string $identifier): JsonResponse
    {
        $user = auth()?->user() ?? null;
        if ($user === null) {
            Log::warning('Someone tried to add a product to their watchlist without being logged in');

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
                HoundEndpoints::GetProductInfoByIdentifier->value,
                $identifier
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
                    'Tried to get product info from a hound but we received a 404 (response failed)',
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
            } elseif ($response->successful() && is_array($response->json('data'))) {
                $productModel = Product::where('identifier', $identifier)->first();
                if ($productModel === null) {
                    // We don't have this product yet, create it
                    Log::info(
                        'Creating new product so that it can be added to a wishlist',
                        [
                            'user' => $user,
                            'hound' => $user->hound->id,
                            'product' => $response->json('data.identifier', $identifier)
                        ]
                    );
                    $productModel = Product::create(
                        [
                            'title' => $response->json('data.title', static::PRODUCT_PLACEHOLDER_TITLE),
                            'created_by_user_id' => $user->id,
                            'identifier' => $response->json('data.identifier', $identifier),
                        ]
                    );
                } elseif ($productModel->title === '' || $productModel->title === static::PRODUCT_PLACEHOLDER_TITLE) {
                    $productModel->update(['title' => $response->json('data.title', static::PRODUCT_PLACEHOLDER_TITLE)]);
                }
                $user->products()->save($productModel);

                return response()->json(['success' => __('pricehound.ProductAddedToWishlist')], 200);
            } else {
                throw new HttpResponseException(
                    'Invalid data received from hound (missing key "data" in json or "data" is not an array)'
                );
            }
        } catch (Throwable $t) {
            Log::warning(
                'Could not get product info from hound',
                ['hound' => $user->hound->id, 'product' => $identifier, 'error' => $t->getMessage()]
            );
        }

        return response()->json(['error' => __('pricehound.FailedGettingProductInfoFromHound')], 500);
    }

    public function show(Product $product)
    {
        if (auth()?->user()?->products->contains($product)) {
            return response()->json($product->load('prices'));
        }

        return response()->json(['error' => 'Product not found'], 404);
    }

    public function index()
    {
        return response()->json(auth()?->user()?->products->load('prices') ?? []);
    }
}
