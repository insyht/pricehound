<div class="bg-dark-700">
    <form wire:submit="save">
    <x-input wire:model="url" label="Enter a url *" required />
        @error('url')
            <x-alert title="Error" text="{{ $message }}" color="red" />
        @enderror
{{--{{ dd($products) }}--}}
    <x-select.styled
        label="Choose a product"
        :options="$products"
        searchable
        wire:model="productId"
        required
        select="label:title|value:id"
        placeholder="Choose a product" />
        @error('productId')
            <x-alert title="Error" text="{{ $message }}" color="red" />
        @enderror

    <x-select.styled
        label="Choose a shop"
        :options="$shops"
        searchable
        wire:model="shopId"
        required
        select="label:name|value:id"
       placeholder="Choose a shop" />
        @error('shopId')
            <x-alert title="Error" text="{{ $message }}" color="red" />
        @enderror

    <x-button submit icon="check-circle" position="left">Save</x-button>
    </form>
</div>
