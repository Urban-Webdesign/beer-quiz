<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium">Registrace týmů</h3>
            <span class="text-2xl font-bold">{{ $this->teamsCount }}</span>
        </div>

        <div class="mt-4">
            <a
                    href="{{ \App\Filament\Resources\EventResource::getUrl('registrations', ['record' => $record]) }}"
                    class="text-primary-500 hover:text-primary-600 hover:underline"
            >
                Zobrazit všechny registrace →
            </a>
        </div>
    </x-filament::card>
</x-filament::widget>