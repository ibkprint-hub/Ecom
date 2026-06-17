@extends('install.layout')
@section('content')
    <h1 class="font-display text-2xl font-extrabold">Configuration</h1>
    <p class="text-gray-500 mt-2">Base de données, compte administrateur et réglages de base.</p>

    <form method="POST" action="{{ route('install.process') }}" class="mt-6 space-y-6" x-data="{ conn: '{{ old('db_connection','mysql') }}' }">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 text-sm">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <fieldset>
            <legend class="font-semibold mb-2">Base de données</legend>
            <select name="db_connection" x-model="conn" class="w-full border border-gray-200 rounded-lg p-2 mb-3">
                <option value="mysql">MySQL / MariaDB (recommandé en mutualisé)</option>
                <option value="sqlite">SQLite (fichier local)</option>
            </select>
            <div x-show="conn === 'mysql'" class="grid sm:grid-cols-2 gap-3">
                <input name="db_host" value="{{ old('db_host','127.0.0.1') }}" placeholder="Hôte" class="border border-gray-200 rounded-lg p-2">
                <input name="db_port" value="{{ old('db_port','3306') }}" placeholder="Port" class="border border-gray-200 rounded-lg p-2">
                <input name="db_database" value="{{ old('db_database') }}" placeholder="Nom de la base" class="border border-gray-200 rounded-lg p-2">
                <input name="db_username" value="{{ old('db_username') }}" placeholder="Utilisateur" class="border border-gray-200 rounded-lg p-2">
                <input name="db_password" type="password" placeholder="Mot de passe" class="border border-gray-200 rounded-lg p-2 sm:col-span-2">
            </div>
        </fieldset>

        <fieldset>
            <legend class="font-semibold mb-2">Site</legend>
            <input name="site_name" value="{{ old('site_name','BePack') }}" placeholder="Nom du site" class="w-full border border-gray-200 rounded-lg p-2 mb-3">
            <input name="pixel_id" value="{{ old('pixel_id') }}" placeholder="Facebook Pixel ID (optionnel)" class="w-full border border-gray-200 rounded-lg p-2">
        </fieldset>

        <fieldset>
            <legend class="font-semibold mb-2">Compte administrateur</legend>
            <div class="grid sm:grid-cols-2 gap-3">
                <input name="admin_name" value="{{ old('admin_name') }}" placeholder="Nom" class="border border-gray-200 rounded-lg p-2">
                <input name="admin_email" type="email" value="{{ old('admin_email') }}" placeholder="Email" class="border border-gray-200 rounded-lg p-2">
                <input name="admin_password" type="password" placeholder="Mot de passe (min. 6)" class="border border-gray-200 rounded-lg p-2 sm:col-span-2">
            </div>
        </fieldset>

        <button type="submit" class="px-6 py-3 rounded-lg text-white font-semibold" style="background:var(--bp-accent)">
            Installer BePack
        </button>
        <p class="text-xs text-gray-400">Cette étape crée les tables, les données de démo et votre compte admin.</p>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
