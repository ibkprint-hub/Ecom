<?php

namespace App\Livewire;

use App\Models\Dimension;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Services\PricingService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Configurator extends Component
{
    use WithFileUploads;

    public Product $product;

    // Étape courante (1..4)
    public int $step = 1;

    // Sélections
    public ?int $dimensionId = null;
    public int $quantity = 100;
    public array $selectedOptions = [];   // [groupId => optionId]
    public string $designMode = 'upload'; // upload | service
    public $designFile = null;            // upload Livewire
    public string $brief = '';

    // Coordonnées
    public string $customerName = '';
    public string $customerPhone = '';
    public string $customerEmail = '';
    public string $wilayaCode = '';
    public string $city = '';
    public string $address = '';
    public string $shippingMethod = 'home';

    // Attribution
    public array $utm = [];

    public function mount(Product $product): void
    {
        $this->product = $product->load(['dimensions', 'priceTiers', 'optionGroups.options']);

        $firstDim = $this->product->dimensions->where('is_active', true)->first();
        $this->dimensionId = $firstDim?->id;

        // Quantité de départ : plus grand entre le minimum du produit et le plus petit palier
        $smallestTier = (int) ($this->product->priceTiers->min('min_quantity') ?? 0);
        $this->quantity = max($this->product->minQuantity(), $smallestTier);

        // Options par défaut (1re option de chaque groupe)
        foreach ($this->product->optionGroups as $group) {
            $this->selectedOptions[$group->id] = $group->options->first()?->id;
        }

        if (! $this->product->is_customizable) {
            $this->designMode = 'none';
        } else {
            $this->designMode = $this->product->allow_upload ? 'upload' : 'service';
        }

        // Pré-remplissage via query string (deep-link pub)
        if ($dim = request('dim')) {
            $match = $this->product->dimensions->firstWhere('label', $dim) ?? $this->product->dimensions->find((int) $dim);
            if ($match) {
                $this->dimensionId = $match->id;
            }
        }
        if ($qty = (int) request('qty')) {
            $this->quantity = $this->product->normalizeQuantity($qty);
        }
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'] as $key) {
            if (request()->has($key)) {
                $this->utm[$key] = (string) request($key);
            }
        }

        // Démarrer sur la première étape réellement active.
        $this->step = $this->activeSteps[0];
    }

    protected function pricing(): PricingService
    {
        return app(PricingService::class);
    }

    public function getQuoteProperty(): array
    {
        $dimension = $this->dimensionId ? Dimension::find($this->dimensionId) : null;

        return $this->pricing()->quote(
            $this->product,
            $dimension,
            (int) $this->quantity,
            array_values(array_filter($this->selectedOptions)),
            $this->designMode,
            $this->wilayaCode ?: null,
            $this->shippingMethod,
        );
    }

    public function getWilayasProperty()
    {
        return ShippingRate::where('is_active', true)->orderBy('wilaya_code')->get();
    }

    /** Libellés des étapes (numéro absolu => libellé). */
    public function stepLabels(): array
    {
        return [1 => 'Dimension', 2 => 'Design', 3 => 'Quantité', 4 => 'Coordonnées'];
    }

    public function hasDimensions(): bool
    {
        return $this->product->dimensions->where('is_active', true)->isNotEmpty();
    }

    /**
     * Étapes actives. L'étape « dimension » (1) est masquée si le produit n'a pas de dimensions ;
     * l'étape « design » (2) est masquée pour un produit non personnalisable.
     */
    public function getActiveStepsProperty(): array
    {
        $steps = [];
        if ($this->hasDimensions()) {
            $steps[] = 1;
        }
        if ($this->product->is_customizable) {
            $steps[] = 2;
        }
        $steps[] = 3;
        $steps[] = 4;

        return $steps;
    }

    public function nextStep(): void
    {
        if ($this->step === 1 && ! $this->dimensionId) {
            $this->addError('dimensionId', 'Choisissez une dimension.');
            return;
        }
        if ($this->step === 2 && $this->designMode === 'service' && strlen(trim($this->brief)) < 5) {
            $this->addError('brief', 'Décrivez brièvement le design souhaité.');
            return;
        }
        $this->resetErrorBag();

        $steps = $this->activeSteps;
        $i = array_search($this->step, $steps, true);
        if ($i !== false && $i < count($steps) - 1) {
            $this->step = $steps[$i + 1];
        }
    }

    public function prevStep(): void
    {
        $steps = $this->activeSteps;
        $i = array_search($this->step, $steps, true);
        if ($i !== false && $i > 0) {
            $this->step = $steps[$i - 1];
        }
    }

    /** Aligne la quantité saisie sur le minimum et le pas du produit. */
    public function updatedQuantity($value): void
    {
        $this->quantity = $this->product->normalizeQuantity((int) $value);
    }

    public function submit(PricingService $pricing)
    {
        $rules = [
            'customerName' => 'required|string|max:120',
            'customerPhone' => 'required|string|max:30',
            'customerEmail' => 'nullable|email|max:120',
            'wilayaCode' => 'required|string',
            'address' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'shippingMethod' => 'required|in:home,stopdesk',
        ];
        if ($this->designMode === 'upload' && $this->product->allow_upload) {
            $rules['designFile'] = 'nullable|file|mimes:png,jpg,jpeg,pdf,ai,svg|max:20480';
        }
        $this->validate($rules, [], [
            'customerName' => 'nom', 'customerPhone' => 'téléphone', 'wilayaCode' => 'wilaya', 'address' => 'adresse',
        ]);

        $dimension = $this->dimensionId ? Dimension::find($this->dimensionId) : null;
        $wilaya = ShippingRate::where('wilaya_code', $this->wilayaCode)->first();

        // Recalcul serveur — source de vérité
        $quote = $pricing->quote(
            $this->product,
            $dimension,
            (int) $this->quantity,
            array_values(array_filter($this->selectedOptions)),
            $this->designMode,
            $this->wilayaCode ?: null,
            $this->shippingMethod,
        );

        $filePath = null;
        if ($this->designFile && $this->designMode === 'upload') {
            $filePath = $this->designFile->store('designs', 'public');
        }

        $order = DB::transaction(function () use ($quote, $dimension, $wilaya, $filePath) {
            $order = Order::create([
                'order_number' => Order::generateNumber(),
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_email' => $this->customerEmail ?: null,
                'wilaya_code' => $this->wilayaCode,
                'wilaya_name' => $wilaya?->wilaya_name,
                'city' => $this->city ?: null,
                'address' => $this->address,
                'shipping_method' => $this->shippingMethod,
                'subtotal' => $quote['subtotal'],
                'design_fee' => $quote['design_fee'],
                'shipping_fee' => $quote['shipping_fee'],
                'total' => $quote['total'],
                'currency' => $quote['currency'],
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'status' => 'new',
                'utm_source' => $this->utm['utm_source'] ?? null,
                'utm_medium' => $this->utm['utm_medium'] ?? null,
                'utm_campaign' => $this->utm['utm_campaign'] ?? null,
                'utm_content' => $this->utm['utm_content'] ?? null,
                'utm_term' => $this->utm['utm_term'] ?? null,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'dimension_label' => $dimension?->label,
                'options_snapshot' => $quote['selected_options'],
                'quantity' => $quote['quantity'],
                'unit_price' => $quote['unit_price'],
                'line_total' => $quote['subtotal'],
                'design_mode' => $this->designMode,
                'design_file_path' => $filePath,
                'design_brief' => $this->designMode === 'service' ? $this->brief : null,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'new',
                'note' => 'Commande créée via le site (COD).',
            ]);

            return $order;
        });

        session()->flash('order_number', $order->order_number);

        return redirect()->route('thankyou', ['order' => $order->order_number]);
    }

    public function render()
    {
        return view('livewire.configurator');
    }
}
