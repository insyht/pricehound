<div>
    <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{ __('pricehound.Title') }}</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{ __('pricehound.Url') }}</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{ __('pricehound.Price') }}</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{ __('pricehound.LastChecked') }}d</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($prices as $lowestPricePerProduct)
                <tr>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $lowestPricePerProduct->product->title }}</td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $lowestPricePerProduct->url }}</td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        @if ($lowestPricePerProduct->url)
                            <a href="{{ $lowestPricePerProduct->url }}" target="_blank">
                        @endif
                        {{ $lowestPricePerProduct->price->getAmount() / 100 }} {{ $lowestPricePerProduct->currency }}
                        @if ($lowestPricePerProduct->url)
                            </a>
                        @endif
                    </td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $lowestPricePerProduct->created_at->timezone(config('app.display_timezone'))->format('d-m-Y H:i:s') }}</td>
                </tr>
        @endforeach
        </tbody>
    </table>
    <br />
    <br />
    <hr />
    <br />
</div>
