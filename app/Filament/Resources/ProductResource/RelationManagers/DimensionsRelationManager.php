<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DimensionsRelationManager extends RelationManager
{
    protected static string $relationship = 'dimensions';

    protected static ?string $title = 'Dimensions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')->label('Libellé')->required()->maxLength(255),
            Forms\Components\TextInput::make('length_mm')->label('Longueur (mm)')->numeric(),
            Forms\Components\TextInput::make('width_mm')->label('Largeur (mm)')->numeric(),
            Forms\Components\TextInput::make('height_mm')->label('Hauteur (mm)')->numeric(),
            Forms\Components\TextInput::make('price_multiplier')->label('Multiplicateur de prix')
                ->numeric()->default(1)->required()->helperText('1 = prix de base, 1.25 = +25%'),
            Forms\Components\TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('label')->label('Libellé'),
                Tables\Columns\TextColumn::make('price_multiplier')->label('×Prix'),
                Tables\Columns\IconColumn::make('is_active')->label('Actif')->boolean(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
