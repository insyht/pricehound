<?php

namespace App\Livewire\Shop;

use Livewire\Component;

class Index extends Component
{
    public $shops;

    public function mount()
    {
        $this->shops = \App\Models\Shop::mine()->get();
    }

    public function render()
    {
        return view('livewire.shop.index');
    }
}
