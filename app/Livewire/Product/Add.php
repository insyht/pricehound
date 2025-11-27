<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Add extends Component
{
    protected $rules = [
        'title' => 'required|string',
        'ean' => 'required|string|digits:13',
    ];

    public string $title = '';
    public string $ean = '';

    public function render()
    {
        return view('livewire.product.add', ['products' => Product::all()]);
    }

    public function save()
    {
        $this->validate();

        Product::create([
                         'title' => $this->title,
                         'ean' => $this->ean,
                     ]);
        // Todo Dit moet anders, je moet hier een product kunnen zoeken in de database en deze koppelen aan de gebruiker
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }
}
