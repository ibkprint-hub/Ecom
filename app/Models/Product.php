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
        'is_customizable' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'design_service_price' => 'decimal:2',
        'min_quantity' => 'integer',
        'quantity_step' => 'integer',
        'pack_size' => 'integer',
    ];

    public const PRICING_MODES = [
        'unit' => 'À l\'unité',
        'pack' => 'En pack',
        'meter' => 'Au mètre',
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

    /** Libellé de l'unité de vente (pièce, pack, mètre…). */
    public function unitLabel(): string
    {
        if (! empty($this->unit_label)) {
            return $this->unit_label;
        }

        return match ($this->pricing_mode) {
            'pack' => 'pack',
            'meter' => 'mètre',
            default => 'pièce',
        };
    }

    public function minQuantity(): int
    {
        return max(1, (int) ($this->min_quantity ?: 1));
    }

    public function quantityStep(): int
    {
        return max(1, (int) ($this->quantity_step ?: 1));
    }

    /** Ramène une quantité saisie à une valeur valide (>= min, alignée sur le pas). */
    public function normalizeQuantity(int $quantity): int
    {
        $min = $this->minQuantity();
        $step = $this->quantityStep();

        if ($quantity < $min) {
            return $min;
        }

        // Aligner sur le pas à partir du minimum.
        $offset = $quantity - $min;
        $aligned = $min + (int) (round($offset / $step) * $step);

        return max($min, $aligned);
    }
}
