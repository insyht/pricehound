<div>
    <form wire:submit="save">
        <div class="form-group">
            <label for="title" style="color: white">Product title</label>
            <input type="text" id="title" wire:model="title" class="form-control" style="color: white; border: 1px solid white"><br />
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <label for="ean" style="color: white">EAN-13</label>
            <input type="text" id="ean" wire:model="ean" class="form-control" style="color: white; border: 1px solid white" required><br />
            @error('ean')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary" style="color: white">Create Product</button>
        </div>
    </form>
</div>
