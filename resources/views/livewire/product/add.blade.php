<div>
    <form wire:submit="save">
        <div class="form-group">
            <label for="title" style="color: white">{{  __('pricehound.Title') }}</label>
            <input type="text" id="title" wire:model="title" class="form-control" style="color: white; border: 1px solid white"><br />
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <label for="identifier" style="color: white">{{  __('pricehound.Identifier') }}</label>
            <input type="text" id="identifier" wire:model="identifier" class="form-control" style="color: white; border: 1px solid white" required><br />
            @error('identifier')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary" style="color: white">{{  __('pricehound.AddProduct') }}</button>
        </div>
    </form>
</div>
