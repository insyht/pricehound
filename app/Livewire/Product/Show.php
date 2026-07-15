<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Show extends Component
{
    private ?Product $product;

    public function mount($product)
    {
        // Might be a product for which the user has no url set.
        // In that case, set $this->product to null and show a 'no urls found' notification
        $this->product = Product::where('id', $product)->first() ?? null;
    }

    public function render()
    {
        $priceLabels = [];
        $priceData = [];

        if ($this->product !== null) {
            foreach ($this->product->prices->sortBy('fetched_at') as $price) {
                $priceLabels[] = $price->fetched_at->format('d-m-Y H:i');
                $priceData[] = $price->price->getAmount() / 100;
            }
        }

        return view('livewire.product.show', [
            'product' => $this->product,
            'priceLabels' => $priceLabels,
            'priceData' => $priceData,
        ]);
    }
}
