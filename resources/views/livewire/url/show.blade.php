<div>
@if ($url == null)
            <x-alert title="Url not found" text="Sorry, I could not find this url" />
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
            <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">Product</td>
            <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $url->product->title }}</td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">Shop</td>
            <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $url->shop->name }}</td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">Url</td>
            <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400"><a href="{{ $url->url }}" target="blank">{{ $url->url }}</a><br>
        </tr>
        <tr>
            <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">XPath</td>
            <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $url->shop->xpath_price }}<br>
        </tr>
    </tbody>
    </table>
@endif
</div>
