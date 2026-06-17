@extends('install.layout')
@section('content')
    <div class="text-center">
        <div class="text-5xl mb-3">🎉</div>
        <h1 class="font-display text-2xl font-extrabold">BePack est installé !</h1>
        <p class="text-gray-500 mt-2">Votre boutique est prête. Connectez-vous au dashboard pour tout configurer.</p>
        <div class="mt-8 flex gap-3 justify-center">
            <a href="{{ url('/admin') }}" class="px-6 py-3 rounded-lg text-white font-semibold" style="background:var(--bp-accent)">Aller au dashboard</a>
            <a href="{{ url('/') }}" class="px-6 py-3 rounded-lg border border-gray-200 font-semibold">Voir le site</a>
        </div>
        <p class="text-xs text-gray-400 mt-6">Pour des raisons de sécurité, l'assistant d'installation est désormais désactivé.</p>
    </div>
@endsection
