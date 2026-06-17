@php($cur = $this->quote['currency'])
<div class="grid lg:grid-cols-3 gap-8">
    {{-- Colonne configuration --}}
    <div class="lg:col-span-2">
        {{-- Stepper (dynamique : l'étape Design disparaît pour un produit non personnalisable) --}}
        @php($labels = $this->stepLabels())
        <ol class="flex items-center gap-2 mb-8 text-sm">
            @foreach($this->activeSteps as $i => $n)
                <li class="flex items-center gap-2">
                    <span class="h-7 w-7 rounded-full flex items-center justify-center font-semibold {{ $step >= $n ? 'text-white' : 'bg-gray-100 text-gray-500' }}"
                          @style(['background: var(--bp-accent)' => $step >= $n])>{{ $i + 1 }}</span>
                    <span class="{{ $step === $n ? 'font-semibold' : 'text-gray-500' }} hidden sm:inline">{{ $labels[$n] }}</span>
                    @if(! $loop->last)<span class="w-6 h-px bg-gray-200"></span>@endif
                </li>
            @endforeach
        </ol>

        {{-- Étape 1 : Dimension --}}
        @if($step === 1)
            <h2 class="font-display text-xl font-bold mb-4">Choisissez une dimension</h2>
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach($product->dimensions->where('is_active', true) as $dim)
                    <label class="border rounded-xl p-4 cursor-pointer flex items-start gap-3 {{ $dimensionId === $dim->id ? 'border-kraft-600 ring-2 ring-kraft-100' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="dimensionId" value="{{ $dim->id }}" class="mt-1 accent-kraft-600">
                        <span>
                            <span class="font-medium block">{{ $dim->label }}</span>
                            <span class="text-sm text-gray-500">Multiplicateur ×{{ rtrim(rtrim(number_format($dim->price_multiplier,2),'0'),'.') }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('dimensionId')<p class="text-coral-600 text-sm mt-2">{{ $message }}</p>@enderror
        @endif

        {{-- Étape 2 : Design --}}
        @if($step === 2)
            <h2 class="font-display text-xl font-bold mb-4">Votre design</h2>
            <div class="grid sm:grid-cols-2 gap-3 mb-5">
                @if($product->allow_upload)
                    <label class="border rounded-xl p-4 cursor-pointer {{ $designMode === 'upload' ? 'border-kraft-600 ring-2 ring-kraft-100' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="designMode" value="upload" class="accent-kraft-600">
                        <span class="font-medium ml-2">J'ai mon design</span>
                        <p class="text-sm text-gray-500 mt-1">Importez votre fichier (PNG, JPG, PDF, AI, SVG).</p>
                    </label>
                @endif
                @if($product->allow_design_service)
                    <label class="border rounded-xl p-4 cursor-pointer {{ $designMode === 'service' ? 'border-kraft-600 ring-2 ring-kraft-100' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="designMode" value="service" class="accent-kraft-600">
                        <span class="font-medium ml-2">Faites-moi le design</span>
                        <p class="text-sm text-gray-500 mt-1">+{{ number_format($product->design_service_price,0,',',' ') }} {{ $cur }} — notre équipe crée votre visuel.</p>
                    </label>
                @endif
            </div>

            @if($designMode === 'upload')
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                    <input type="file" wire:model="designFile" class="w-full text-sm">
                    <div wire:loading wire:target="designFile" class="text-sm text-gray-500 mt-2">Téléversement…</div>
                    @error('designFile')<p class="text-coral-600 text-sm mt-2">{{ $message }}</p>@enderror
                    @if($designFile)<p class="text-green-600 text-sm mt-2">Fichier prêt ✓</p>@endif
                    <p class="text-xs text-gray-400 mt-2">Max 20 Mo. Vous pourrez valider un BAT avant production.</p>
                </div>
            @else
                <textarea wire:model="brief" rows="4" placeholder="Décrivez votre marque, vos couleurs, le style souhaité, et joignez vos références par la suite…"
                          class="w-full border border-gray-200 rounded-xl p-3 text-sm"></textarea>
                @error('brief')<p class="text-coral-600 text-sm mt-1">{{ $message }}</p>@enderror
            @endif

            {{-- Options --}}
            @include('livewire.partials.options')
        @endif

        {{-- Étape 3 : Quantité --}}
        @if($step === 3)
            @php($unit = $this->quote['unit_label'])
            {{-- Options affichées ici pour les produits non personnalisables (pas d'étape design) --}}
            @unless($product->is_customizable)
                @include('livewire.partials.options')
                <div class="mb-6"></div>
            @endunless
            <h2 class="font-display text-xl font-bold mb-4">Quantité <span class="text-gray-400 font-normal text-base">(en {{ $unit }}s)</span></h2>
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($product->priceTiers as $tier)
                    <button type="button" wire:click="$set('quantity', {{ $tier->min_quantity }})"
                            class="border rounded-lg px-4 py-2 text-sm {{ (int)$quantity === (int)$tier->min_quantity ? 'border-kraft-600 bg-kraft-50' : 'border-gray-200' }}">
                        {{ $tier->min_quantity }} {{ $unit }}{{ $tier->min_quantity > 1 ? 's' : '' }}
                    </button>
                @endforeach
            </div>
            <label class="text-sm text-gray-600">Quantité personnalisée (en {{ $unit }}s)</label>
            <input type="number" min="{{ $product->minQuantity() }}" step="{{ $product->quantityStep() }}"
                   wire:model.blur="quantity" class="block w-40 border border-gray-200 rounded-lg p-2 mt-1">
            <p class="text-xs text-gray-400 mt-1">
                Minimum {{ $product->minQuantity() }} {{ $unit }}{{ $product->minQuantity() > 1 ? 's' : '' }}
                @if($product->quantityStep() > 1) · par multiples de {{ $product->quantityStep() }} @endif
                @if($product->pricing_mode === 'pack' && $product->pack_size) · 1 pack = {{ $product->pack_size }} pièces @endif
            </p>
            <p class="text-sm text-gray-500 mt-3">Prix par {{ $unit }} : <strong>{{ number_format($this->quote['unit_price'],2,',',' ') }} {{ $cur }}</strong></p>
        @endif

        {{-- Étape 4 : Coordonnées --}}
        @if($step === 4)
            <h2 class="font-display text-xl font-bold mb-4">Vos coordonnées (paiement à la livraison)</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Nom complet *</label>
                    <input wire:model="customerName" class="w-full border border-gray-200 rounded-lg p-2 mt-1">
                    @error('customerName')<p class="text-coral-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Téléphone *</label>
                    <input wire:model="customerPhone" class="w-full border border-gray-200 rounded-lg p-2 mt-1">
                    @error('customerPhone')<p class="text-coral-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Email (optionnel)</label>
                    <input wire:model="customerEmail" class="w-full border border-gray-200 rounded-lg p-2 mt-1">
                    @error('customerEmail')<p class="text-coral-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Wilaya *</label>
                    <select wire:model.live="wilayaCode" class="w-full border border-gray-200 rounded-lg p-2 mt-1">
                        <option value="">— Choisir —</option>
                        @foreach($this->wilayas as $w)
                            <option value="{{ $w->wilaya_code }}">{{ $w->wilaya_code }} — {{ $w->wilaya_name }}</option>
                        @endforeach
                    </select>
                    @error('wilayaCode')<p class="text-coral-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Ville / Commune</label>
                    <input wire:model="city" class="w-full border border-gray-200 rounded-lg p-2 mt-1">
                </div>
                <div>
                    <label class="text-sm text-gray-600">Mode de livraison</label>
                    <select wire:model.live="shippingMethod" class="w-full border border-gray-200 rounded-lg p-2 mt-1">
                        <option value="home">À domicile</option>
                        <option value="stopdesk">Point relais / bureau</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm text-gray-600">Adresse *</label>
                    <textarea wire:model="address" rows="2" class="w-full border border-gray-200 rounded-lg p-2 mt-1"></textarea>
                    @error('address')<p class="text-coral-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        @endif

        {{-- Navigation --}}
        <div class="flex justify-between mt-8">
            <button type="button" wire:click="prevStep" @disabled($step === 1)
                    class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-medium disabled:opacity-40">
                Retour
            </button>
            @if($step < 4)
                <button type="button" wire:click="nextStep"
                        class="px-6 py-2.5 rounded-lg text-white text-sm font-semibold" style="background: var(--bp-accent)">
                    Continuer
                </button>
            @else
                <button type="button" wire:click="submit" wire:loading.attr="disabled"
                        class="px-6 py-2.5 rounded-lg text-white text-sm font-semibold" style="background: var(--bp-accent)">
                    <span wire:loading.remove wire:target="submit">Confirmer la commande (COD)</span>
                    <span wire:loading wire:target="submit">Envoi…</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Colonne récapitulatif --}}
    <aside class="lg:col-span-1">
        <div class="sticky top-24 border border-gray-200 rounded-2xl p-6 bg-paper">
            <h3 class="font-display font-bold text-lg mb-4">Votre devis</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Produit</dt><dd class="font-medium text-right">{{ $product->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Quantité</dt><dd class="font-medium">{{ number_format($this->quote['quantity'],0,',',' ') }} {{ $this->quote['unit_label'] }}{{ $this->quote['quantity'] > 1 ? 's' : '' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Prix / {{ $this->quote['unit_label'] }}</dt><dd class="font-medium">{{ number_format($this->quote['unit_price'],2,',',' ') }} {{ $cur }}</dd></div>
                <div class="flex justify-between border-t border-gray-200 pt-2"><dt class="text-gray-500">Sous-total</dt><dd class="font-medium">{{ number_format($this->quote['subtotal'],2,',',' ') }} {{ $cur }}</dd></div>
                @if($this->quote['design_fee'] > 0)
                    <div class="flex justify-between"><dt class="text-gray-500">Service design</dt><dd class="font-medium">{{ number_format($this->quote['design_fee'],2,',',' ') }} {{ $cur }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-gray-500">Livraison</dt><dd class="font-medium">{{ $this->quote['shipping_fee'] > 0 ? number_format($this->quote['shipping_fee'],2,',',' ').' '.$cur : '—' }}</dd></div>
            </dl>
            <div class="flex justify-between items-center border-t border-gray-300 mt-4 pt-4">
                <span class="font-semibold">Total</span>
                <span class="font-display text-2xl font-extrabold" style="color: var(--bp-primary)">{{ number_format($this->quote['total'],2,',',' ') }} {{ $cur }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-3">Paiement à la réception. Aucune avance requise.</p>
        </div>
    </aside>
</div>
