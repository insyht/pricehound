<?php

namespace App\Livewire\Shop;

use App\Models\Shop;
use Livewire\Component;

class Create extends Component
{
    protected $rules = [
        'name' => 'required|string|max:255',
        'xpathPrice' => 'required|string',
    ];

    public string $name = '';
    public string $xpathPrice = '';

    public function render()
    {
        return view('livewire.shop.create');
    }

    public function save()
    {
        $this->validate();

        Shop::create([
                         'name' => $this->name,
                         'xpath_price' => $this->xpathPrice,
                     ]);

        return redirect()->route('shops.index')->with('success', 'Shop created successfully.');
    }
}
