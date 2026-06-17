@extends('install.layout')
@section('content')
    <h1 class="font-display text-2xl font-extrabold">Bienvenue 👋</h1>
    <p class="text-gray-500 mt-2">Vérifions que votre serveur est prêt pour BePack.</p>

    <ul class="mt-6 space-y-2">
        @foreach($requirements as [$label, $ok, $hint])
            <li class="flex items-start gap-3 text-sm">
                <span class="mt-0.5">{{ $ok ? '✅' : '❌' }}</span>
                <span>
                    <span class="font-medium">{{ $label }}</span>
                    @unless($ok)<span class="block text-gray-400">{{ $hint }}</span>@endunless
                </span>
            </li>
        @endforeach
    </ul>

    <div class="mt-8">
        @if($ready)
            <a href="{{ route('install.configure') }}" class="inline-block px-6 py-3 rounded-lg text-white font-semibold" style="background:var(--bp-accent)">Continuer</a>
        @else
            <p class="text-coral-600 text-sm">Corrigez les points en rouge, puis rechargez la page.</p>
            <a href="{{ route('install.index') }}" class="inline-block mt-4 px-6 py-3 rounded-lg border border-gray-200 font-semibold">Recharger</a>
        @endif
    </div>
@endsection
