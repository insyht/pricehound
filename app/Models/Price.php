<?php

namespace App\Models;

use App\Casts\AsMoney;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @property \Money\Money $price;
 */
class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'hound_id',
        'price',
        'currency',
        'url',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => AsMoney::class,
            'fetched_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // The user is allowed to view his own fetched prices only
        static::addGlobalScope('mine', function (Builder $builder) {
            $builder->where('user_id', Auth::user()->id);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function hound()
    {
        return $this->belongsTo(Hound::class);
    }

    public function rules()
    {
        return PriceRule::query()->join('product_user', 'price_rules.product_user_id', '=', 'product_user.id')
                                 ->where('product_user.product_id', $this->product_id)
                                 ->where('product_user.user_id', $this->user_id)
                                 ->select('price_rules.*')
                                 ->get();
    }

    public function scopeCheapest($query)
    {
        // todo Er is nu voortaan een user_id in deze tabel, laat onderstaande query hier rekening mee houden
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
        $query->whereIn('product_id', Auth::user()->products->pluck('id'));

        return $query;
    }
}
