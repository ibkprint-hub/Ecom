<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Produits';

    protected static ?string $modelLabel = 'produit';

    protected static ?string $pluralModelLabel = 'produits';

    protected static ?string $navigationGroup = 'Catalogue';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('product_family_id')
                            ->label('Famille')
                            ->relationship('family', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\FileUpload::make('image')->label('Image')->image()->directory('products'),
                        Forms\Components\Textarea::make('short_description')->label('Description courte')->columnSpanFull(),
                        Forms\Components\Textarea::make('description')->label('Description')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Tarification & vente')
                    ->description('Comment ce produit se vend : à l\'unité, en pack, ou au mètre. Les prix par paliers de quantité se gèrent dans l\'onglet « Paliers de prix ».')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('pricing_mode')
                            ->label('Mode de vente')
                            ->options(\App\Models\Product::PRICING_MODES)
                            ->default('unit')
                            ->required()
                            ->live()
                            ->helperText('À l\'unité (pièce), en pack, ou au mètre.'),
                        Forms\Components\TextInput::make('unit_label')
                            ->label('Libellé de l\'unité')
                            ->placeholder(fn (Forms\Get $get) => match ($get('pricing_mode')) {
                                'pack' => 'pack', 'meter' => 'mètre', default => 'pièce',
                            })
                            ->helperText('Laisser vide pour utiliser le libellé par défaut du mode.'),
                        Forms\Components\TextInput::make('min_quantity')
                            ->label('Quantité minimum')
                            ->numeric()->minValue(1)->default(1)->required()
                            ->helperText('Quantité minimale commandable.'),
                        Forms\Components\TextInput::make('quantity_step')
                            ->label('Pas de quantité')
                            ->numeric()->minValue(1)->default(1)->required()
                            ->helperText('Incrément (ex. 10 = commandes par multiples de 10).'),
                        Forms\Components\TextInput::make('pack_size')
                            ->label('Unités par pack')
                            ->numeric()->minValue(1)
                            ->visible(fn (Forms\Get $get) => $get('pricing_mode') === 'pack')
                            ->helperText('Nombre de pièces contenues dans un pack (informatif).'),
                    ]),
                Forms\Components\Section::make('Personnalisation & design')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('is_customizable')
                            ->label('Produit personnalisable')
                            ->helperText('Activé : le client passe par une étape de design (upload ou service). Désactivé : produit vendu tel quel.')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('allow_upload')->label('Autoriser l\'upload de design')->default(true)
                            ->visible(fn (Forms\Get $get) => $get('is_customizable')),
                        Forms\Components\Toggle::make('allow_design_service')->label('Proposer « Faites-moi le design »')->default(true)
                            ->visible(fn (Forms\Get $get) => $get('is_customizable')),
                        Forms\Components\TextInput::make('design_service_price')->label('Prix du service design')->numeric()->default(0)
                            ->visible(fn (Forms\Get $get) => $get('is_customizable')),
                        Forms\Components\Toggle::make('allow_custom_dimensions')->label('Dimensions sur-mesure'),
                    ]),
                Forms\Components\Section::make('Affichage & SEO')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
                        Forms\Components\Toggle::make('is_featured')->label('Mis en avant'),
                        Forms\Components\TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
                        Forms\Components\TextInput::make('meta_title')->label('Meta titre'),
                        Forms\Components\Textarea::make('meta_description')->label('Meta description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label(''),
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('family.name')->label('Famille')->sortable(),
                Tables\Columns\TextColumn::make('pricing_mode')->label('Vente')->badge()
                    ->formatStateUsing(fn ($state) => \App\Models\Product::PRICING_MODES[$state] ?? $state),
                Tables\Columns\IconColumn::make('is_featured')->label('Vedette')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Actif')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Ordre')->numeric()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_family_id')
                    ->label('Famille')
                    ->relationship('family', 'name'),
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
            RelationManagers\DimensionsRelationManager::class,
            RelationManagers\PriceTiersRelationManager::class,
            RelationManagers\OptionGroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
