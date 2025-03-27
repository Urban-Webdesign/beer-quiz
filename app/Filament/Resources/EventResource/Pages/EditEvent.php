<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\Widgets\EventRegistrationsOverview;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
	        Actions\Action::make('registrations')
		        ->label('Přihlášené týmy')
		        ->url(fn () => EventResource::getUrl('registrations', ['record' => $this->record])),
            Actions\DeleteAction::make(),
        ];
    }
}
