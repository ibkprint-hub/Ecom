<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installation — BePack</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
    <style>:root{--bp-primary:#B5793A;--bp-accent:#FF5A3C}</style>
</head>
<body class="bg-paper min-h-screen font-sans text-ink">
    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="flex items-center gap-2 mb-8">
            <x-brand-mark :size="40" />
            <span class="font-display text-2xl font-extrabold">Be<span style="color:var(--bp-accent)">Pack</span></span>
            <span class="ml-auto text-sm text-gray-400">Assistant d'installation</span>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>
