@extends('layouts.app')

@section('content')
    {{-- Hero --}}
    <section class="bg-paper">
        <div class="max-w-6xl mx-auto px-4 py-20 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <span class="inline-block text-xs font-semibold uppercase tracking-wider px-3 py-1 rounded-full" style="background:#fff;color:var(--bp-primary)">Pensé pour les e-commerçants</span>
                <h1 class="font-display text-4xl md:text-5xl font-extrabold mt-4 leading-tight">
                    Votre marque,<br><span style="color:var(--bp-accent)">bien emballée.</span>
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Boîtes en carton, boîtes en papier et sacs personnalisés à votre logo.
                    Configurez, importez votre design et commandez en quelques clics. Paiement à la livraison.
                </p>
                <div class="mt-6 flex gap-3">
                    <a href="#familles" class="px-6 py-3 rounded-lg text-white font-semibold" style="background:var(--bp-accent)">Commander maintenant</a>
                    <a href="{{ url('/p/comment-ca-marche') }}" class="px-6 py-3 rounded-lg border border-gray-300 font-semibold">Comment ça marche</a>
                </div>
                <div class="mt-6 flex gap-6 text-sm text-gray-500">
                    <span>✓ Devis instantané</span>
                    <span>✓ Paiement à la livraison</span>
                    <span>✓ 58 wilayas</span>
                </div>
            </div>
            <div class="aspect-square rounded-3xl bg-gradient-to-br from-kraft-100 to-paper flex items-center justify-center">
                <span class="font-display text-8xl font-extrabold" style="color:var(--bp-primary)">B</span>
            </div>
        </div>
    </section>

    {{-- Familles --}}
    <section id="familles" class="max-w-6xl mx-auto px-4 py-16">
        <h2 class="font-display text-3xl font-extrabold text-center">Choisissez votre emballage</h2>
        <p class="text-center text-gray-500 mt-2">Trois familles, des dizaines de configurations.</p>

        <div class="grid md:grid-cols-3 gap-6 mt-10">
            @foreach($families as $family)
                <a href="{{ route('family', $family) }}" class="group border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition">
                    <div class="aspect-video rounded-xl bg-paper flex items-center justify-center mb-4 text-4xl">📦</div>
                    <h3 class="font-display text-xl font-bold group-hover:text-kraft-600">{{ $family->name }}</h3>
                    <p class="text-sm text-gray-500 mt-2">{{ $family->description }}</p>
                    <span class="inline-block mt-4 text-sm font-semibold" style="color:var(--bp-accent)">Voir les produits →</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Produits vedettes --}}
    @if($featured->isNotEmpty())
        <section class="bg-paper">
            <div class="max-w-6xl mx-auto px-4 py-16">
                <h2 class="font-display text-2xl font-extrabold mb-8">Populaires</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featured as $product)
                        <a href="{{ route('product', $product) }}" class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-lg transition">
                            <div class="aspect-video rounded-xl bg-paper flex items-center justify-center mb-3 text-3xl">📦</div>
                            <h3 class="font-semibold">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $product->short_description }}</p>
                            <span class="inline-block mt-3 text-sm font-semibold" style="color:var(--bp-accent)">Personnaliser →</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Réassurance --}}
    <section class="max-w-6xl mx-auto px-4 py-16 grid md:grid-cols-3 gap-8 text-center">
        <div><div class="text-3xl">🎨</div><h3 class="font-semibold mt-2">Votre design ou le nôtre</h3><p class="text-sm text-gray-500 mt-1">Importez votre visuel ou laissez notre équipe le créer.</p></div>
        <div><div class="text-3xl">💸</div><h3 class="font-semibold mt-2">Tarif dégressif</h3><p class="text-sm text-gray-500 mt-1">Plus la quantité est élevée, plus le prix unitaire baisse.</p></div>
        <div><div class="text-3xl">🚚</div><h3 class="font-semibold mt-2">Paiement à la livraison</h3><p class="text-sm text-gray-500 mt-1">Commandez sans avance, payez à la réception.</p></div>
    </section>
@endsection
