<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Réglages';

    protected static ?string $title = 'Réglages du site';

    protected static ?string $navigationGroup = 'Configuration';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'site_name', 'tagline', 'currency', 'contact_phone', 'contact_email',
            'primary_color', 'accent_color',
            'facebook_pixel_id', 'facebook_capi_token', 'pixel_purchase_event',
        ];
        $values = [];
        foreach ($keys as $k) {
            $values[$k] = Setting::get($k);
        }
        $this->form->fill($values);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Général')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_name')->label('Nom du site')->required(),
                        TextInput::make('tagline')->label('Slogan'),
                        TextInput::make('currency')->label('Devise')->default('DZD'),
                        TextInput::make('contact_phone')->label('Téléphone'),
                        TextInput::make('contact_email')->label('Email')->email(),
                    ]),
                Section::make('Apparence')
                    ->columns(2)
                    ->schema([
                        TextInput::make('primary_color')->label('Couleur primaire')->placeholder('#B5793A'),
                        TextInput::make('accent_color')->label('Couleur accent')->placeholder('#FF5A3C'),
                    ]),
                Section::make('Marketing — Facebook')
                    ->columns(2)
                    ->schema([
                        TextInput::make('facebook_pixel_id')->label('Facebook Pixel ID')
                            ->helperText('Collez l\'ID de votre Pixel Meta. Laissez vide pour désactiver.'),
                        TextInput::make('facebook_capi_token')->label('Token API Conversions')
                            ->helperText('Optionnel — pour l\'envoi serveur (CAPI).'),
                        Select::make('pixel_purchase_event')->label('Événement de commande')
                            ->options(['Purchase' => 'Purchase', 'Lead' => 'Lead'])->default('Purchase')
                            ->helperText('Comment compter une commande COD côté Pixel.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $groups = [
            'site_name' => 'general', 'tagline' => 'general', 'currency' => 'general',
            'contact_phone' => 'general', 'contact_email' => 'general',
            'primary_color' => 'theme', 'accent_color' => 'theme',
            'facebook_pixel_id' => 'marketing', 'facebook_capi_token' => 'marketing',
            'pixel_purchase_event' => 'marketing',
        ];
        foreach ($this->form->getState() as $key => $value) {
            Setting::put($key, $value, $groups[$key] ?? 'general');
        }

        Notification::make()->title('Réglages enregistrés')->success()->send();
    }
}
