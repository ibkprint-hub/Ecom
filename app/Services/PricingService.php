<?php

namespace App\Services;

use App\Models\Dimension;
use App\Models\Option;
use App\Models\Product;
use App\Models\ShippingRate;

class PricingService
{
    /**
     * Calcule le devis pour une configuration produit.
     * Source de vérité côté serveur : ne jamais faire confiance au prix du navigateur.
     *
     * @param  array  $optionIds  Liste d'IDs d'options sélectionnées
     */
    public function quote(
        Product $product,
        ?Dimension $dimension,
        int $quantity,
        array $optionIds = [],
        string $designMode = 'upload',
        ?string $wilayaCode = null,
        string $shippingMethod = 'home'
    ): array {
        $quantity = $product->normalizeQuantity(max(1, $quantity));

        $baseUnit = $product->unitPriceForQuantity($quantity);

        $multiplier = $dimension ? (float) $dimension->price_multiplier : 1.0;
        $unit = $baseUnit * $multiplier;

        // Surcoûts d'options (par unité)
        $optionsDelta = 0.0;
        $selectedOptions = [];
        if (! empty($optionIds)) {
            $options = Option::whereIn('id', $optionIds)
                ->whereHas('group', fn ($q) => $q->where('product_id', $product->id))
                ->get();
            foreach ($options as $option) {
                $optionsDelta += (float) $option->price_delta;
                $selectedOptions[] = [
                    'id' => $option->id,
                    'group' => $option->group->name,
                    'label' => $option->label,
                    'price_delta' => (float) $option->price_delta,
                ];
            }
        }

        $unitFinal = round($unit + $optionsDelta, 2);
        $subtotal = round($unitFinal * $quantity, 2);

        $designFee = ($designMode === 'service' && $product->allow_design_service)
            ? (float) $product->design_service_price
            : 0.0;

        $shippingFee = $this->shippingFee($wilayaCode, $shippingMethod);

        $total = round($subtotal + $designFee + $shippingFee, 2);

        return [
            'unit_base' => round($baseUnit, 2),
            'unit_price' => $unitFinal,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'design_fee' => round($designFee, 2),
            'shipping_fee' => round($shippingFee, 2),
            'total' => $total,
            'currency' => \App\Support\Settings::currency(),
            'selected_options' => $selectedOptions,
            'unit_label' => $product->unitLabel(),
            'pricing_mode' => $product->pricing_mode,
        ];
    }

    public function shippingFee(?string $wilayaCode, string $method): float
    {
        if (! $wilayaCode) {
            return 0.0;
        }

        $rate = ShippingRate::where('wilaya_code', $wilayaCode)->where('is_active', true)->first();
        if (! $rate) {
            return 0.0;
        }

        return $method === 'stopdesk'
            ? (float) $rate->stopdesk_price
            : (float) $rate->home_price;
    }
}
