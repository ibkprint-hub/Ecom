@extends('layouts.app')

@section('title', $family->name)

@section('content')
    <section class="max-w-6xl mx-auto px-4 py-12">
        <nav class="text-sm text-gray-400 mb-4"><a href="{{ route('home') }}" class="hover:text-kraft-600">Accueil</a> / {{ $family->name }}</nav>
        <h1 class="font-display text-3xl font-extrabold">{{ $family->name }}</h1>
        <p class="text-gray-500 mt-2 max-w-2xl">{{ $family->description }}</p>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
            @forelse($family->products as $product)
                <a href="{{ route('product', $product) }}" class="border border-gray-200 rounded-2xl p-5 hover:shadow-lg transition">
                    <div class="aspect-video rounded-xl bg-paper flex items-center justify-center mb-3 text-3xl">📦</div>
                    <h3 class="font-semibold">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $product->short_description }}</p>
                    <span class="inline-block mt-3 text-sm font-semibold" style="color:var(--bp-accent)">Personnaliser →</span>
                </a>
            @empty
                <p class="text-gray-500">Aucun produit pour le moment.</p>
            @endforelse
        </div>
    </section>
@endsection
