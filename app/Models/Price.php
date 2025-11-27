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
        'product_id',
        'hound_id',
        'price',
        'currency',
        'url',
    ];

    protected function casts(): array
    {
        return [
            'price' => AsMoney::class,
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function hound()
    {
        return $this->belongsTo(Hound::class);
    }
    public function scopeCheapest($query)
    {
        // todo Not sure if this works, got it from ChatGPT
        return $query->select('prices.*')
                     ->join('products', 'products.id', '=', 'prices.product_id')
                     ->join(DB::raw('(
                            SELECT products.id, MIN(prices.id) AS price_id
                            FROM prices
                            JOIN products ON products.id = prices.product_id
                            GROUP BY products.id
                        ) AS lowest'), 'lowest.price_id', '=', 'prices.id');
    }

    public function scopeMine($query)
    {
        $query->where('hound_id', Auth::user()->hound_id);
        $query->whereIn('product_id', Auth::user()->products->pluck('id'));

        return $query;
    }
}
