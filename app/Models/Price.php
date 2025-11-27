<?php

namespace App\Models;

use App\Casts\AsMoney;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_shop_id',
        'hound_id',
        'price',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'price' => AsMoney::class,
        ];
    }

    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            ProductShop::class,
            'id', // product_shop.id
            'id', // products.id
            'product_shop_id', // prices.product_shop_id
            'product_id' // product_shop.product_id
        );
    }

    public function productShop()
    {
        return $this->belongsTo(ProductShop::class);
    }

    public function shop()
    {
        return $this->hasOneThrough(
            Shop::class,
            ProductShop::class,
            'id', // product_shop.id
            'id', // shops.id
            'product_shop_id', // prices.product_shop_id
            'shop_id' // product_shop.shop_id
        );
    }

    public function hound()
    {
        return $this->belongsTo(Hound::class);
    }
    public function scopeCheapest($query)
    {
        // todo Not sure if this works, got it from ChatGPT
        return $query->select('prices.*')
                     ->join('product_shop', 'product_shop.id', '=', 'prices.product_shop_id')
                     ->join(DB::raw('(
                            SELECT product_shop.product_id, MIN(prices.id) AS price_id
                            FROM prices
                            JOIN product_shop ON product_shop.id = prices.product_shop_id
                            GROUP BY product_shop.product_id
                        ) AS lowest'), 'lowest.price_id', '=', 'prices.id');
    }

    public function scopeMine($query)
    {
        $query->where('hound_id', Auth::user()->hound_id);
        $query->whereHas('productShop', function ($query) {
            $query->whereIn('product_id', Auth::user()->products->pluck('id'));
        });

        return $query;
    }
}
