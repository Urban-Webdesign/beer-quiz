<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

	public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->name('Název'),
				DatePicker::make('date')->name('Datum konání'),
				Checkbox::make('shootout')->name('Rozstřelová otázka')->default(false),

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
				            ->numeric(),

			            Select::make('team_id')
				            ->label('Tým')
				            ->relationship('team', 'name')
				            ->searchable()
				            ->preload()
				            ->required(),

			            TextInput::make('score')
				            ->label('Skóre')
				            ->numeric()
				            ->required(),
		            ])
		            ->columnSpan(2)
		            ->columns(3)
	                ->orderColumn('order'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Název')->searchable()->sortable(),
				Tables\Columns\TextColumn::make('date')->date('d. m. Y')->label('Datum konání')->sortable(),
	            Tables\Columns\TextColumn::make('results_count')->counts('results')->badge()->color('gray')->label('Počet týmů')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
