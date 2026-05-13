<div>
    <form wire:submit="save">
        <div class="form-group">
            <label for="title" style="color: white">{{  __('pricehound.Title') }}</label>
            <div style="position: relative;">
                <input type="text" id="title" wire:model.live.debounce.300ms="title" class="form-control" style="color: white; border: 1px solid white" autocomplete="off">
                @if (!empty($results))
                    <ul class="list-group" style="position: absolute; z-index: 10; width: 100%; max-height: 240px; overflow-y: auto;">
                        <li class="list-group-item" wire:click="selectResult(null)" style="cursor: pointer;">
                            {{ __('pricehound.AddNewProduct') }}
                        </li>
                        @foreach ($results as $index => $result)
                            <li class="list-group-item" wire:click="selectResult({{ $index }})" style="cursor: pointer;">
                                {{ $result['title'] ?? '' }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div><br />
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
