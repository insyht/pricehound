<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Livewire\Component;

class Show extends Component
{
    private ?Product $product;

    public function mount($product)
    {
        // Might be a product for which the user has no url set.
        // In that case, set $this->product to null and show a 'no urls found' notification
        $this->product = Product::where('id', $product)->first() ?? null;
    }

    public function render()
    {
        $lineChartModel = LivewireCharts::lineChartModel()
                                        ->withOnPointClickEvent('onPointClick')
                                        ->setSmoothCurve()
                                        ->setXAxisVisible(true)
                                        ->setDataLabelsEnabled(true);
        foreach ($this->product->prices->sortBy('fetched_at') as $price) {
            $lineChartModel->addPoint($price->fetched_at->format('d-m-Y H:i'), $price->price->getAmount() / 100);
        }

        return view('livewire.product.show', ['product' => $this->product, 'lineChartModel' => $lineChartModel]);
    }
}
