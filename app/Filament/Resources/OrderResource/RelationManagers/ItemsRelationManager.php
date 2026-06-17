<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articles';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('product_name')->label('Produit')->disabled(),
            Forms\Components\TextInput::make('dimension_label')->label('Dimension')->disabled(),
            Forms\Components\TextInput::make('quantity')->label('Quantité')->disabled(),
            Forms\Components\Textarea::make('design_brief')->label('Brief design')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('product_name')->label('Produit'),
                Tables\Columns\TextColumn::make('dimension_label')->label('Dimension'),
                Tables\Columns\TextColumn::make('quantity')->label('Qté'),
                Tables\Columns\TextColumn::make('unit_price')->label('PU')->money('DZD'),
                Tables\Columns\TextColumn::make('line_total')->label('Total')->money('DZD'),
                Tables\Columns\TextColumn::make('design_mode')->label('Design')
                    ->formatStateUsing(fn ($state) => $state === 'service' ? 'Service' : 'Upload')->badge(),
                Tables\Columns\TextColumn::make('design_file_path')->label('Fichier')
                    ->formatStateUsing(fn ($state) => $state ? 'Oui' : '—')
                    ->url(fn ($record) => $record->design_file_path ? asset('storage/' . $record->design_file_path) : null, true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
