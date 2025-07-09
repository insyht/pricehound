<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Show extends Component
{
    private ?Product $product;

    public function mount($product)
    {
        // Might be a product for which then user has no url set.
        // In that case, set $this->product to null and show a 'no urls found' notification
        $this->product = Product::mine()->where('id', $product)->first() ?? null;
    }

    public function render()
    {
        return view('livewire.product.show', ['product' => $this->product]);
    }
}
