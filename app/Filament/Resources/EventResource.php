<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Registration;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Carbon\Carbon;

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
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Základní info')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Název')
                                    ->columnSpan(2)
                                    ->default('PBQ xx. kolo')
                                    ->required(),

                                TextInput::make('capacity')
                                    ->label('Kapacita (počet týmů)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(9)
                                    ->required(),

                                DateTimePicker::make('date')
                                    ->label('Datum konání')
                                    ->default(now()->addDays(21)->hours(19)->minutes(30)->seconds(0))
                                    ->required(),

                                DateTimePicker::make('register_from')
                                    ->label('Začátek registrace')
                                    ->default(now()->addDays(14)->hours(18)->minutes(0)->seconds(0)),
                            ])
                            ->columns(3),

                        Tabs\Tab::make('Fotogalerie')
                            ->hidden(fn ($record) => !$record || Carbon::now()->isBefore(Carbon::parse($record->date)))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('gallery')
                                    ->collection('gallery')
                                    ->label('Fotogalerie')
                                    ->multiple()
                                    ->image()
                                    ->panelLayout('grid')
                                    ->reorderable()
                                    ->downloadable()
                                    ->previewable()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
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
        return [];
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
