<?php

namespace App\Livewire\Url;

use App\Models\Url;
use Livewire\Component;

class Show extends Component
{
    private Url $url;

    public function mount($url)
    {
        $this->url = Url::findOrFail($url);
    }

    public function render()
    {
        return view('livewire.url.show', ['url' => $this->url]);
    }
}
