<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'allow_upload' => 'boolean',
        'allow_design_service' => 'boolean',
        'allow_custom_dimensions' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'design_service_price' => 'decimal:2',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(ProductFamily::class, 'product_family_id');
    }

    public function dimensions(): HasMany
    {
        return $this->hasMany(Dimension::class)->orderBy('sort_order');
    }

    public function priceTiers(): HasMany
    {
        return $this->hasMany(PriceTier::class)->orderBy('min_quantity');
    }

    public function optionGroups(): HasMany
    {
        return $this->hasMany(OptionGroup::class)->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Prix unitaire de base pour une quantité donnée (palier applicable). */
    public function unitPriceForQuantity(int $quantity): float
    {
        $tier = $this->priceTiers
            ->filter(fn ($t) => $quantity >= $t->min_quantity)
            ->sortByDesc('min_quantity')
            ->first();

        return $tier ? (float) $tier->unit_price : 0.0;
    }
}
