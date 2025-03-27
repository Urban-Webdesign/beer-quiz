<x-filament::page>

    <x-filament::card>
        <h2 class="text-2xl font-bold mb-4">{{ $record->name }}</h2>

        {{ $this->table }}
    </x-filament::card>
</x-filament::page>