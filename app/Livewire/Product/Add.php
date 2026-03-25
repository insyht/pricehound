<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Add extends Component
{
    protected $rules = [
        'title' => 'required|string',
        'identifier' => 'required|string',
    ];

    public string $title = '';
    public string $identifier = '';

    public function render()
    {
        return view('livewire.product.add', ['products' => Product::all()]);
    }

    public function save()
    {
        $this->validate();

        Product::create([
                         'title' => $this->title,
                         'identifier' => $this->identifier,
                     ]);
        // Todo Dit moet anders, je moet hier een product kunnen zoeken in de database en deze koppelen aan de gebruiker
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }
}
