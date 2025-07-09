{{-- todo deze gebruiken in alle index templates --}}
<table class="w-full border-separate border border-gray-400 bg-white text-sm dark:border-gray-500 dark:bg-gray-800">
<thead class="bg-gray-50 dark:bg-gray-700">
    <tr>
        @foreach ($columns as $column)
            <th class="border border-gray-300 p-4 text-left font-semibold text-gray-900 dark:border-gray-600 dark:text-gray-200">{{ $column }}</th>
        @endforeach
    </tr>
</thead>
<tbody>
@foreach ($rows as $row)
        <tr>
            @foreach ($row['cells'] as $cell)
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $cell }}</td>
            @endforeach
            @if (isset($row['actions']))
                <td class="border border-gray-300 p-4 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    @foreach ($row['actions'] as $action)
                        <flux:button icon="{{ $action['icon'] }}" wire:click="{{ $action['wireClick'] }}"></flux:button>
                    @endforeach
                </td>
            @endif
        </tr>
@endforeach
</tbody>
</table>
