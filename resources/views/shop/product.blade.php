@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', $product->meta_description ?? $product->short_description)

@push('pixel-events')
    fbq('track', 'ViewContent', { content_name: @json($product->name), content_type: 'product', currency: @json(\App\Support\Settings::currency()) });
@endpush

@section('content')
    <section class="max-w-6xl mx-auto px-4 py-10">
        <nav class="text-sm text-gray-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-kraft-600">Accueil</a> /
            <a href="{{ route('family', $product->family) }}" class="hover:text-kraft-600">{{ $product->family->name }}</a> /
            {{ $product->name }}
        </nav>
        <h1 class="font-display text-3xl font-extrabold">{{ $product->name }}</h1>
        <p class="text-gray-500 mt-2 max-w-2xl">{{ $product->description }}</p>

        <div class="mt-10">
            @livewire('configurator', ['product' => $product])
        </div>
    </section>
@endsection
