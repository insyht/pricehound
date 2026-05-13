<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class Add extends Component
{
    protected $rules = [
        'title' => 'required|string',
        'identifier' => 'required|string',
    ];

    public string $title = '';
    public string $identifier = '';
    public array $results = [];

    public function render()
    {
        return view('livewire.product.add', ['products' => Product::all()]);
    }

    public function updatedTitle(string $value): void
    {
        if (trim($value) === '') {
            $this->results = [];
            return;
        }

        $products = Product::where(function ($query) use ($value) {
                            $query->whereLike('title', Str::wrap($value, '%'))
                                  ->orWhereLike('identifier', Str::wrap($value, '%'));
                           })
                           ->whereNotIn('id', Auth::user()->products->pluck('id'))
                           ->select('title', 'identifier')
                           ->get();
        $this->results = $products->toArray();
    }

    public function selectResult(?int $index = null): void
    {
        if ($index === null) {
            $this->results = [];
            return;
        }

        if (!isset($this->results[$index])) {
            return;
        }

        $this->title = $this->results[$index]['title'] ?? '';
        $this->identifier = $this->results[$index]['identifier'] ?? '';
        $this->results = [];
    }

    public function save()
    {
        $this->validate();

        if (!Product::where('identifier', $this->identifier)->exists()) {
            Product::create([
                'title' => $this->title,
                'identifier' => $this->identifier,
            ]);
        }

        Auth::user()->products()->syncWithoutDetaching(Product::where('identifier', $this->identifier)->first());

        return redirect()->route('products.index')->with('success', __('pricehound.ProductAddedToWatchlist'));
    }
}
