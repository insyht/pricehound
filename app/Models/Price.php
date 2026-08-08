<?php

namespace App\Models;

use App\Casts\AsMoney;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'created_at',
        'notified',
    ];

    protected function casts(): array
    {
        return [
            'price' => AsMoney::class,
            'fetched_at' => 'datetime',
            'notified' => 'boolean',
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

    public function scopeMine($query)
    {
        $query->whereIn('product_id', Auth::user()->products->pluck('id'));

        return $query;
    }
}
