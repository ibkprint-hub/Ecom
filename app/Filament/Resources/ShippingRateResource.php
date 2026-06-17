<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingRateResource\Pages;
use App\Filament\Resources\ShippingRateResource\RelationManagers;
use App\Models\ShippingRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShippingRateResource extends Resource
{
    protected static ?string $model = ShippingRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Frais de livraison';

    protected static ?string $modelLabel = 'tarif de livraison';

    protected static ?string $pluralModelLabel = 'frais de livraison';

    protected static ?string $navigationGroup = 'Configuration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('wilaya_code')
                    ->required(),
                Forms\Components\TextInput::make('wilaya_name')
                    ->required(),
                Forms\Components\TextInput::make('home_price')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('stopdesk_price')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('delay_days')
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wilaya_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wilaya_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('home_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stopdesk_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delay_days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ]);
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
            'index' => Pages\ListShippingRates::route('/'),
            'create' => Pages\CreateShippingRate::route('/create'),
            'edit' => Pages\EditShippingRate::route('/{record}/edit'),
        ];
    }
}
