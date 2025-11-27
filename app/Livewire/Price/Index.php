<?php

namespace App\Livewire\Price;

use App\Models\Price;
use Livewire\Component;

class Index extends Component
{
    public $prices;

    public function mount()
    {
        $allMyPrices = Price::mine()->with('productShop')->get();
        $allMyPrices = $allMyPrices->groupBy(function (Price $price, int $key) {
            return 'product-' . $price->productShop->product_id;
        });
        $before = $allMyPrices->toArray();

        $lowestPrices = collect();
        $allMyPrices->each(function ($pricesForProduct) use ($lowestPrices) {
            $lowestPrices->add($pricesForProduct->sortBy('price')->first());
        });

        $this->prices = $lowestPrices;
    }

    public function render()
    {
        return view('livewire.price.index');
    }
}
