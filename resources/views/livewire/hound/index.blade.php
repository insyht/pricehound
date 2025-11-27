<div>
    @if ($mine)
        <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Name') }}</th>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Description') }}</th>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Url') }}</th>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.IsOnline') }}</th>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $mine->name }}</td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $mine->description }}</td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $mine->url }}</td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        @if ($mine && $mine->online)
                            <flux:icon.check-circle class="text-green-400" />
                        @elseif ($mine && !$mine->online)
                            <flux:icon.x-circle class="text-red-500"/>
                        @endif
                    </td>
                    <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400"></td>
                </tr>
            </tbody>
        </table><br />
    @endif

        <table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Name') }}</th>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Description') }}</th>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Url') }}</th>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.IsOnline') }}</th>
                    <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{  __('pricehound.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hounds as $hound)
                    <tr>
                        <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $hound->name }}</td>
                        <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $hound->description }}</td>
                        <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $hound->url }}</td>
                        <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            @if ($hound->online)
                                <flux:icon.check-circle class="text-green-400" />
                            @else
                                <flux:icon.x-circle class="text-red-500"/>
                            @endif
                        </td>
                        <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            @if ($hound->online && (!$mine || $mine->id !== $hound->id))
                                <x-button color="blue" outline position="left" wire:click="choose({{ $hound->id }})">{{  __('pricehound.ChooseHound') }}</x-button>
                            @endif
                        </td>
                    </tr>
                @endforeach
        </tbody>
    </table>
    <br />
    <hr />
    <br />
    <x-button href="{{ route('hounds.add') }}">{{  __('pricehound.AddHound') }}</x-button>
</div>
