<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Commandes';

    protected static ?string $modelLabel = 'commande';

    protected static ?string $pluralModelLabel = 'commandes';

    protected static ?string $navigationGroup = 'Ventes';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'new')->count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Statut & paiement')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('order_number')->label('N° commande')->disabled(),
                    Forms\Components\Select::make('status')->label('Statut')
                        ->options(Order::STATUSES)->required(),
                    Forms\Components\Select::make('payment_status')->label('Paiement')
                        ->options(['pending' => 'En attente', 'paid' => 'Encaissé'])->default('pending'),
                ]),
            Forms\Components\Section::make('Client')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('customer_name')->label('Nom')->required(),
                    Forms\Components\TextInput::make('customer_phone')->label('Téléphone')->tel()->required(),
                    Forms\Components\TextInput::make('customer_email')->label('Email')->email(),
                    Forms\Components\TextInput::make('wilaya_name')->label('Wilaya'),
                    Forms\Components\TextInput::make('city')->label('Ville'),
                    Forms\Components\Select::make('shipping_method')->label('Livraison')
                        ->options(['home' => 'À domicile', 'stopdesk' => 'Point relais']),
                    Forms\Components\Textarea::make('address')->label('Adresse')->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Montants')
                ->columns(4)
                ->schema([
                    Forms\Components\TextInput::make('subtotal')->label('Sous-total')->numeric()->prefix('DZD'),
                    Forms\Components\TextInput::make('design_fee')->label('Frais design')->numeric()->prefix('DZD'),
                    Forms\Components\TextInput::make('shipping_fee')->label('Livraison')->numeric()->prefix('DZD'),
                    Forms\Components\TextInput::make('total')->label('Total')->numeric()->prefix('DZD'),
                ]),
            Forms\Components\Section::make('Attribution publicitaire')
                ->columns(3)
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('utm_source')->label('Source'),
                    Forms\Components\TextInput::make('utm_medium')->label('Medium'),
                    Forms\Components\TextInput::make('utm_campaign')->label('Campagne'),
                ]),
            Forms\Components\Textarea::make('admin_notes')->label('Notes internes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('N°')->searchable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')->label('Téléphone')->searchable(),
                Tables\Columns\TextColumn::make('wilaya_name')->label('Wilaya')->searchable(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('DZD')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn (string $state) => Order::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'new' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('utm_source')->label('Source pub')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(Order::STATUSES),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
