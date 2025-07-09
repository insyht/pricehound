<?php

namespace App\Livewire\Url;

use Livewire\Component;

class Index extends Component
{
    public $urls;

    public function mount()
    {
        $this->urls = \App\Models\Url::with(['product', 'shop'])->get();
    }

    public function render()
    {
        return view('livewire.url.index');
    }
}
