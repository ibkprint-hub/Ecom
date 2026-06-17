<?php

namespace App\Support;

use App\Models\Setting;

/** Accès pratique aux réglages éditables du site. */
class Settings
{
    public static function get(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }

    public static function siteName(): string
    {
        return (string) Setting::get('site_name', 'BePack');
    }

    public static function currency(): string
    {
        return (string) Setting::get('currency', 'DZD');
    }

    public static function pixelId(): ?string
    {
        $v = Setting::get('facebook_pixel_id');
        return $v !== null && $v !== '' ? (string) $v : null;
    }

    public static function phone(): ?string
    {
        return Setting::get('contact_phone');
    }

    public static function purchaseEventName(): string
    {
        return (string) Setting::get('pixel_purchase_event', 'Purchase');
    }
}
