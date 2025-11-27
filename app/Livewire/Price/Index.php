<?php

namespace App\Livewire\Price;

use App\Models\Price;
use Livewire\Component;

class Index extends Component
{
    public $prices;

    public function mount()
    {
        $allMyPrices = Price::mine()->with('product')->get();
        $allMyPrices = $allMyPrices->groupBy(function (Price $price) {
            return 'product-' . $price->product_id;
        });

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
