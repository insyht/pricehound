<?php

namespace App\Livewire\Product;

use Livewire\Component;

class Index extends Component
{
    public $products;

    public function mount()
    {
        $this->products = \App\Models\Product::mine()->get();
    }

    public function render()
    {
        return view('livewire.product.index');
    }
}
