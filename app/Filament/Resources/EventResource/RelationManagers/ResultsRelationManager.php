<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'results';

    protected static ?string $title = 'Výsledky';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Carbon::now()->isAfter(Carbon::parse($ownerRecord->date));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->label('Umístění')
                    ->sortable()
                    ->numeric()
                    ->badge(),

                Tables\Columns\TextColumn::make('team.name')
                    ->label('Tým')
                    ->searchable(),

                Tables\Columns\TextColumn::make('score')
                    ->label('Skóre')
                    ->sortable(),
            ])
            ->defaultSort('position', 'asc')
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Přidat výsledek'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
