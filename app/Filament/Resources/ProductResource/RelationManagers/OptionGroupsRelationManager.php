<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OptionGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'optionGroups';

    protected static ?string $title = 'Groupes d\'options';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nom du groupe')->required()
                ->helperText('Ex : Impression, Finition'),
            Forms\Components\Toggle::make('is_required')->label('Obligatoire')->default(true),
            Forms\Components\TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
            Forms\Components\Repeater::make('options')
                ->relationship()
                ->label('Options')
                ->schema([
                    Forms\Components\TextInput::make('label')->label('Libellé')->required(),
                    Forms\Components\TextInput::make('price_delta')->label('Surcoût / unité')->numeric()->default(0),
                    Forms\Components\TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
                    Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
                ])
                ->orderColumn('sort_order')
                ->defaultItems(1)
                ->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Groupe'),
                Tables\Columns\TextColumn::make('options_count')->counts('options')->label('Options'),
                Tables\Columns\IconColumn::make('is_required')->label('Obligatoire')->boolean(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
