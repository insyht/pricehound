<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $products;

    public function mount()
    {
        $this->products = Auth::user()->products ?? collect();
    }

    public function render()
    {
        return view('livewire.product.index');
    }

    public function delete(Product $product)
    {
        if ($product->id) {
            Auth::user()->products()->detach($product->id);
        }
        $this->products = Auth::user()->products ?? collect();
    }
}
