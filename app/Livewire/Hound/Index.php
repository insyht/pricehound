<?php

namespace App\Livewire\Hound;

use App\Models\Hound;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $hounds;
    public $mine;

    public function mount()
    {
        $this->hounds = Hound::all();
        $this->mine = Auth::user()->hound;
    }

    public function render()
    {
        return view('livewire.hound.index');
    }

    public function choose(Hound $hound)
    {
        if ($hound->id) {
            Auth::user()->hound()->associate($hound)->save();
        }
        $this->mine = Auth::user()->hound;
    }
}
