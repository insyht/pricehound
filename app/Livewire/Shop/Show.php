<?php

namespace App\Livewire\Shop;

use App\Models\Shop;
use Livewire\Component;

class Show extends Component
{
    private Shop $shop;

    public function mount($shop)
    {
        $this->shop = Shop::findOrFail($shop);
    }

    public function render()
    {
        return view('livewire.shop.show', ['shop' => $this->shop]);
    }
}
