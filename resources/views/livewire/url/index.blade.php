<div>
    <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Url</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">XPath</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Product</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Shop</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($urls as $url)
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400"><a href="{{ $url->url }}" target="_blank">{{ $url->url }}</a></td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $url->shop->xpath_price }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                <a href="{{ route('products.show', [$url->product->id]) }}">{{ $url->product->title }}</a>
                </td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                <a href="{{ route('shops.show', [$url->shop->id]) }}">{{ $url->shop->name }}</a>
                </td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    <x-button icon="pencil-square" color="blue" outline position="left" wire:click="process({{$url->id}})">Sniff</x-button>
                    <x-button icon="arrow-right-end-on-rectangle"  color="green" outline position="left" href="{{ route('urls.show', $url) }}">View</x-button>
                    <x-button icon="pencil-square" color="orange" outline position="left" wire:click="edit({{$url->id}})">Edit</x-button>
                    <x-button icon="x-mark" color="red" outline position="left" wire:click="delete({{$url->id}})">Delete</x-button>
                </td>
            </tr>
    @endforeach
    </tbody>
    </table>
    <br />
    <hr />
    <br />
    <x-button href="{{ route('urls.create') }}">Create new url</x-button>
</div>
