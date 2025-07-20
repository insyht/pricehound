<div>
@if ($product === null)
            <x-alert title="Product not found" text="Sorry, I could not find this product" />
@else
    <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Attribute</th>
                <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">Title</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $product->title }}</td>
            </tr>
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">EAN</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $product->ean }}</td>
            </tr>
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">Urls + price</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                @foreach ($product->urls as $url)
                    <a href="{{ route('shops.show', $url->shop) }}">{{ $url->shop->name }}</a>: <a href="{{ route('urls.show', $url) }}">{{ $url->url }}</a> <span class="text-green-400">(&euro; TODO {{-- todo --}})</span><br />
                @endforeach
                </td>
            </tr>
        </tbody>
    </table>
@endif
</div>
