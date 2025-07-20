<div>
    @foreach ($prices as $pricePerProduct)
    <h1>{{ $pricePerProduct->first()->product->title }}</h1>
        <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Shop</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Price</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Last checked</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($pricePerProduct as $price)
                <tr>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $price->shop->name }}</td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $price->price->getAmount() / 100 }} {{ $price->currency }}</td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $price->created_at->format('d-m-Y H:i:s') }}</td>
                </tr>
        @endforeach
        </tbody>
        </table>
        <br />
    @endforeach
    <br />
    <hr />
    <br />
</div>
