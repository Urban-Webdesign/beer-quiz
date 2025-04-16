<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Registration;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

	protected static ?string $pluralLabel = 'Události';

	protected static ?string $label = 'Událost';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->name('Název')->columnSpan(2)->required(),
	            TextInput::make('capacity')->label('Kapacita')->numeric()->minValue(1)->default(8)->required(),
                DateTimePicker::make('date')->name('Datum konání')->required(),
	            DateTimePicker::make('register_from')->name('Začátek registrace'),

                Repeater::make('results')
                    ->label('Výsledky')
                    ->relationship('results')
                    ->schema([
                        TextInput::make('order')
                            ->label('Pořadí')
                            ->numeric()
                            ->hidden(),

                        TextInput::make('position')
                            ->label('Umístění')
                            ->numeric()
	                        ->minValue(1)
	                        ->required(),

                        Select::make('team_id')
                            ->label('Tým')
                            ->relationship('team', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('score')
                            ->label('Skóre')
                            ->numeric()
	                        ->minValue(0)
                            ->required(),
                    ])
                    ->columnSpan(3)
                    ->columns(3)
                    ->orderColumn('order')
	                ->nullable()
	                ->hidden(fn ($record) => !$record || $record->date > now()),

	            Checkbox::make('shootout')->name('Rozstřelová otázka')->default(false)
		            ->hidden(fn ($record) => !$record),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Název')->searchable()->sortable(),
	            Tables\Columns\TextColumn::make('date')->date('d. m. Y')->label('Datum konání')->sortable(),
	            Tables\Columns\TextColumn::make('registrations_count')
		            ->counts('registrations')
		            ->label('Přihlášených týmů')
		            ->badge()
		            ->color('gray')
		            ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
	            Action::make('registrations')
		            ->label('Přihlášené týmy')
		            ->color('gray')
		            ->icon('heroicon-o-user-group')
		            ->url(fn (Event $record) => EventResource::getUrl('registrations', [$record->id]))
		            ->hidden(fn (Event $record) => $record->registrations_count < 1),

	            Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
	        'index' => Pages\ListEvents::route('/'),
	        'registrations' => Pages\EventRegistrations::route('/{record}/registrations'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
