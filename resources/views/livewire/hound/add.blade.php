<div>
    <form wire:submit="save">
        <div class="form-group">
            <label for="name" style="color: white">{{  __('pricehound.Name') }}</label>
            <input type="text" id="name" wire:model="name" class="form-control" style="color: white; border: 1px solid white"><br />
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <label for="url" style="color: white">{{  __('pricehound.Url') }}</label>
            <input type="url" id="url" wire:model="url" class="form-control" style="color: white; border: 1px solid white" required><br />
            @error('url')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary" style="color: white">{{  __('pricehound.AddHound') }}</button>
        </div>
    </form>
</div>
