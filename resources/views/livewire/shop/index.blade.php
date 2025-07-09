<div>
    <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Name</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">XPath to price element</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($shops as $shop)
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $shop->name }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $shop->xpath_price }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    <x-button icon="arrow-right-end-on-rectangle"  color="green" outline position="left" href="{{ route('shops.show', $shop) }}">View</x-button>
                    <x-button icon="pencil-square" color="orange" outline position="left" wire:click="edit({{$shop->id}})">Edit</x-button>
                    <x-button icon="x-mark" color="red" outline position="left" wire:click="delete({{$shop->id}})">Delete</x-button>
                </td>
            </tr>
    @endforeach
    </tbody>
    </table>
    <br />
    <hr />
    <br />
    <x-button href="{{ route('shops.create') }}">Create new shop</x-button>
</div>
