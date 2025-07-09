<div>
    <form wire:submit="save">
        <div class="form-group">
            <label for="name" style="color: white">Shop Name</label>
            <input type="text" id="name" wire:model="name" class="form-control" style="color: white; border: 1px solid white"><br />
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <label for="xpathPrice" style="color: white">XPath to price element</label>
            <input type="text" id="xpathPrice" wire:model="xpathPrice" class="form-control" style="color: white; border: 1px solid white" required><br />
            @error('xpath_price')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary" style="color: white">Create Shop</button>
        </div>
    </form>
</div>
