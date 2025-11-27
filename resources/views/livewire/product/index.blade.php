<div>
    <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Title') }}</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.EAN') }}</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.LowestPrice') }}</th>
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Actions') }}</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($products as $product)
            <tr>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $product->title }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $product->ean }}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">TODO {{-- todo --}}</td>
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    <x-button icon="arrow-right-end-on-rectangle"  color="green" outline position="left" href="{{ route('products.show', $product) }}">{{  __('pricehound.View') }}</x-button>
                    <x-button icon="x-mark" color="red" outline position="left" wire:click="delete({{ $product->id }})" wire:confirm="{{  __('pricehound.DeleteConfirmation') }}">{{  __('pricehound.Delete') }}</x-button>
                </td>
            </tr>
    @endforeach
    </tbody>
    </table>
    <br />
    <hr />
    <br />
    <x-button href="{{ route('products.add') }}">{{  __('pricehound.AddProduct') }}</x-button>
</div>
