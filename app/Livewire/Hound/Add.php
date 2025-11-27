<?php

namespace App\Livewire\Hound;

use App\Jobs\PingHound;
use App\Models\Hound;
use Livewire\Component;

class Add extends Component
{
    protected $rules = [
        'name' => 'required|string',
        'url' => 'required|url',
    ];

    public string $name = '';
    public string $url = '';

    public function render()
    {
        return view('livewire.hound.add');
    }

    public function save()
    {
        $this->validate();
        $foundHound = Hound::where('name', $this->name)->orWhere('url', $this->url)->first();
        if (!$foundHound) {
            $hound = Hound::create(
                [
                    'name' => $this->name,
                    'url' => $this->url,
                ]
            );
            PingHound::dispatch($hound);
        }

        return redirect()->route('hounds.index')
                         ->with(
                             'success',
                             $foundHound ? __('pricehound.HoundAlreadyExists') . $foundHound->name : __('pricehound.HoundAdded')
                         );
    }
}
