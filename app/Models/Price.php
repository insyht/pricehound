<?php

namespace App\Models;

use App\Casts\AsMoney;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'xpath_price',
        'price',
        'currency',
        'product_id',
        'shop_id',
    ];

    protected $casts = [
        'price' => AsMoney::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeMine($query)
    {
        // One problem with this approach:
        // You might search for a product for which we know a certain shop has it,
        // but you haven't set that shop-product combination in your urls,
        // but you do have that shop for another product,
        // so that shop-product combo will be returned here anyway
        $myUrls = Url::mine()->get();
        $myProductIds = $myUrls->pluck('product_id')->unique();
        $myShopIds = $myUrls->pluck('shop_id')->unique();

        return $query->whereIn('product_id', $myProductIds)->whereIn('shop_id', $myShopIds);
    }
}
