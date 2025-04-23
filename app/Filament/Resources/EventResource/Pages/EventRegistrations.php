<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
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
			ActionGroup::make([
				Tables\Actions\Action::make('cancel')
					->label('Odhlásit')
					->icon('heroicon-o-x-mark')
					->color('danger')
					->action(function (Registration $record) {
						$record->delete();
					})
					->modalHeading('Odhlašení týmu')
					->requiresConfirmation('Opravdu chcete odhlásit tento tým?')
					->modalSubmitActionLabel('Odhlásit'),

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
					})
					->requiresConfirmation()
					->modalHeading('Vytvořit tým')
					->modalDescription('Opravdu chcete vytvořit tým z této registrace?')
					->modalSubmitActionLabel('Vytvořit'),
			])
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
		return fn (Registration $record) => $record->team_exists ? null : 'bg-red-50';
	}
}