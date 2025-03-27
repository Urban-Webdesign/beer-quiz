<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class EventRegistrations extends Page implements HasTable
{
	use InteractsWithTable;

	protected static ?string $title = 'Přihlášené týmy';
	protected static string $resource = EventResource::class;
	protected static ?string $navigationParentItem = 'Událost';
	protected static ?string $breadcrumb = 'Přihlášené týmy';

	protected static string $view = 'filament.resources.event-resource.pages.event-registrations';

	public Event $record;

	protected function getTableQuery(): Builder
	{
		return Registration::where('event_id', $this->record->id);
	}

	protected function getTableColumns(): array
	{
		return [
			Tables\Columns\TextColumn::make('name')
				->label('Název týmu'),
			Tables\Columns\TextColumn::make('leader')
				->label('Vedoucí'),
			Tables\Columns\TextColumn::make('phone')
				->label('Telefon'),
			Tables\Columns\TextColumn::make('created_at')
				->label('Datum registrace')
				->dateTime('d.m.Y H:i'),
			Tables\Columns\TextColumn::make('team_exists')
				->label('Stav týmu')
				->formatStateUsing(fn ($state) => $state ? 'Existuje' : 'Chybí')
				->color(fn ($state) => $state ? 'success' : 'danger'),
		];
	}

	protected function getTableActions(): array
	{
		return [
			Tables\Actions\Action::make('create_team')
				->label('Vytvořit tým')
				->icon('heroicon-o-plus')
				->color('success')
				->hidden(fn (Registration $record) => $record->team()->exists())
				->action(function (Registration $record) {
					// Create team from registration
					Team::create([
						'name' => $record->name,
						// Add any other fields you need
					]);

					// Optional: Refresh the table
					$this->refreshTable();
				})
				->requiresConfirmation()
				->modalHeading('Vytvořit tým')
				->modalDescription('Opravdu chcete vytvořit tým z této registrace?')
				->modalSubmitActionLabel('Vytvořit'),
		];
	}

	protected function getHeaderActions(): array
	{
		return [
			Action::make('back')
				->label('Zpět na událost')
				->url(fn () => EventResource::getUrl('edit', ['record' => $this->record])),
		];
	}

	// Add this method to your table configuration
	protected function getTableRecordClassesUsing(): ?\Closure
	{
		return fn (Registration $record) => $record->team()->exists() ? null : 'bg-red-50';
	}
}