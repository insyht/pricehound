<div>
@if ($product === null)
            <x-alert title="Product not found" text="{{  __('pricehound.ProductNotFound') }}" />
@else
    <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Attribute') }}</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Value') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{  __('pricehound.Title') }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $product->title }}</td>
            </tr>
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{  __('pricehound.Identifier') }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $product->identifier }}</td>
            </tr>
        </tbody>
    </table>
    <br />

        <div class="shadow rounded p-4 border bg-white" style="height: 32rem;"
            wire:ignore
            x-data="{
                chart: null,
                init() {
                    this.chart = new ApexCharts($refs.chart, {
                        chart: { type: 'line', height: '100%', toolbar: { show: false } },
                        series: [{ name: '{{ __('pricehound.Price') }}', data: @js($priceData) }],
                        xaxis: { categories: @js($priceLabels) },
                        stroke: { curve: 'smooth' },
                        dataLabels: { enabled: true },
                    });
                    this.chart.render();
                },
            }">
            <div x-ref="chart" class="h-full"></div>
        </div>

    <br />

    <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.WhenPriceFetched') }}</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Url') }}</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.HoundUsed') }}</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Price') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($product->prices as $price)
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $price->created_at->timezone(config('app.display_timezone'))->format('d-m-Y H:i:s') }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $price->url }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $price->hound?->name }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $price->price->getAmount() / 100 }} {{ $price->currency }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

@endif
</div>
