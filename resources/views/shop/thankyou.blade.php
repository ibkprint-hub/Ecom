@extends('layouts.app')

@section('title', 'Merci pour votre commande')

@push('pixel-events')
    fbq('track', @json(\App\Support\Settings::purchaseEventName()), {
        value: {{ (float) $order->total }},
        currency: @json($order->currency),
        content_type: 'product'
    });
@endpush

@section('content')
    <section class="max-w-2xl mx-auto px-4 py-20 text-center">
        <div class="text-5xl mb-4">✅</div>
        <h1 class="font-display text-3xl font-extrabold">Commande confirmée !</h1>
        <p class="text-gray-600 mt-3">
            Merci {{ $order->customer_name }}. Votre commande <strong>{{ $order->order_number }}</strong> a bien été enregistrée.
            Nous vous appellerons au <strong>{{ $order->customer_phone }}</strong> pour confirmer.
        </p>
        <div class="mt-8 border border-gray-200 rounded-2xl p-6 text-left bg-paper">
            <div class="flex justify-between py-1"><span class="text-gray-500">Total à payer à la livraison</span>
                <strong>{{ number_format($order->total,2,',',' ') }} {{ $order->currency }}</strong></div>
            <div class="flex justify-between py-1"><span class="text-gray-500">Wilaya</span><span>{{ $order->wilaya_name }}</span></div>
            <div class="flex justify-between py-1"><span class="text-gray-500">Paiement</span><span>À la livraison (COD)</span></div>
        </div>
        <a href="{{ route('home') }}" class="inline-block mt-8 px-6 py-3 rounded-lg text-white font-semibold" style="background:var(--bp-accent)">Retour à l'accueil</a>
    </section>
@endsection
