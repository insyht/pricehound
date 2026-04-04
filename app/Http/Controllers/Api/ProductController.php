<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function add(string $identifier)
    {
        // todo Add a product to my watchlist (and also to pricehound API I guess?)
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
