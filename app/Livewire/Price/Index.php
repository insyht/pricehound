<?php

namespace App\Livewire\Price;

use App\Models\Price;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $prices;

    public function mount()
    {
        $this->prices = Price::select('prices.*')
            ->joinSub(
                Price::select('product_id', 'shop_id', DB::raw('MAX(created_at) as max_created_at'))
                    ->groupBy('product_id', 'shop_id'),
                'latest_prices',
                function ($join) {
                    $join->on('prices.product_id', '=', 'latest_prices.product_id')
                        ->on('prices.shop_id', '=', 'latest_prices.shop_id')
                        ->on('prices.created_at', '=', 'latest_prices.max_created_at');
                }
            )
            ->get()
            ->groupBy('product_id')->map(function ($group) {
                return $group->sortBy('price');
            });
    }

    public function render()
    {
        return view('livewire.price.index');
    }
}
