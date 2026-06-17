<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PriceTiersRelationManager extends RelationManager
{
    protected static string $relationship = 'priceTiers';

    protected static ?string $title = 'Paliers de prix';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('min_quantity')->label('Quantité min.')->numeric()->required(),
            Forms\Components\TextInput::make('unit_price')->label('Prix unitaire')->numeric()->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('min_quantity')
            ->defaultSort('min_quantity')
            ->columns([
                Tables\Columns\TextColumn::make('min_quantity')->label('À partir de (qté)'),
                Tables\Columns\TextColumn::make('unit_price')->label('Prix unitaire'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
