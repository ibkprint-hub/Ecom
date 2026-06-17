@php
    $siteName = \App\Support\Settings::siteName();
    $primary = \App\Support\Settings::get('primary_color', '#B5793A');
    $accent = \App\Support\Settings::get('accent_color', '#FF5A3C');
    $footerPages = \App\Models\Page::where('is_active', true)->where('show_in_footer', true)->orderBy('sort_order')->get();
@endphp
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $siteName) — {{ $siteName }}</title>
    <meta name="description" content="@yield('meta_description', 'Boîtes et sacs personnalisés pour e-commerçants. Paiement à la livraison.')">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|poppins:600,700,800" rel="stylesheet">
    <style>:root { --bp-primary: {{ $primary }}; --bp-accent: {{ $accent }}; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @include('partials.pixel')
    @stack('head')
</head>
<body class="font-sans text-ink bg-white antialiased">
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-white font-display font-extrabold" style="background: var(--bp-primary)">B</span>
                <span class="font-display text-xl font-extrabold">Be<span style="color: var(--bp-accent)">Pack</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                @foreach(\App\Models\ProductFamily::where('is_active', true)->orderBy('sort_order')->get() as $fam)
                    <a href="{{ route('family', $fam) }}" class="hover:text-kraft-600">{{ $fam->name }}</a>
                @endforeach
                <a href="{{ url('/p/comment-ca-marche') }}" class="hover:text-kraft-600">Comment ça marche</a>
            </nav>
            <a href="#" onclick="document.querySelector('#familles')?.scrollIntoView({behavior:'smooth'});return false;"
               class="text-white text-sm font-semibold px-4 py-2 rounded-lg" style="background: var(--bp-accent)">
                Commander
            </a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-ink text-white/80 mt-20">
        <div class="max-w-6xl mx-auto px-4 py-12 grid md:grid-cols-3 gap-8">
            <div>
                <div class="font-display text-2xl font-extrabold text-white">Be<span style="color: var(--bp-accent)">Pack</span></div>
                <p class="mt-3 text-sm">{{ \App\Support\Settings::get('tagline', 'Votre marque, bien emballée.') }}</p>
                <p class="mt-3 text-sm">{{ \App\Support\Settings::phone() }}</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Informations</h4>
                <ul class="space-y-2 text-sm">
                    @foreach($footerPages as $p)
                        <li><a href="{{ url('/p/'.$p->slug) }}" class="hover:text-white">{{ $p->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Paiement</h4>
                <p class="text-sm">Paiement à la livraison (COD) dans les 58 wilayas.</p>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs text-white/50">
            © {{ date('Y') }} {{ $siteName }}. Tous droits réservés.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
