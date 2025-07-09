<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Create extends Component
{
    protected $rules = [
        'title' => 'required|string',
        'ean' => 'required|string|digits:13',
    ];

    public string $title = '';
    public string $ean = '';

    public function render()
    {
        return view('livewire.product.create');
    }

    public function save()
    {
        $this->validate();

        Product::create([
                         'title' => $this->title,
                         'ean' => $this->ean,
                     ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }
}
