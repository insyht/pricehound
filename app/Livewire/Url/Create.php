<?php

namespace App\Livewire\Url;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Url;
use Livewire\Component;

class Create extends Component
{
    protected $rules = [
        'url' => 'required|string',
        'productId' => 'required|integer|exists:products,id',
        'shopId' => 'required|integer|exists:shops,id',
    ];

    public string $url = '';
    public $productId;
    public $shopId;

    public function render()
    {
        return view(
            'livewire.url.create',
            [
                'products' => Product::pluck('title', 'id')->map(fn($title, $id) => [
                    'id' => $id,
                    'title' => $title,
                ])->values()->toArray(),
                'shops' => Shop::pluck('name', 'id')->map(fn($name, $id) => [
                    'id' => $id,
                    'name' => $name,
                ])->values()->toArray(),
            ]
        );
    }

    public function save()
    {
        $this->validate();

        Url::create([
                         'url' => $this->url,
                         'product_id' => $this->productId,
                         'shop_id' => $this->shopId,
                     ]);

        return redirect()->route('urls.index')->with('success', 'Url created successfully.');
    }
}
