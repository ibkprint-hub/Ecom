<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Support\Installer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstallController extends Controller
{
    public function index()
    {
        return view('install.index', [
            'requirements' => Installer::requirements(),
            'ready' => Installer::allRequirementsMet(),
        ]);
    }

    public function configure()
    {
        return view('install.configure');
    }

    public function process(Request $request)
    {
        $data = $request->validate([
            'db_connection' => 'required|in:mysql,sqlite',
            'db_host' => 'required_if:db_connection,mysql',
            'db_port' => 'nullable',
            'db_database' => 'required_if:db_connection,mysql',
            'db_username' => 'required_if:db_connection,mysql',
            'db_password' => 'nullable',
            'site_name' => 'required|string|max:120',
            'admin_name' => 'required|string|max:120',
            'admin_email' => 'required|email|max:160',
            'admin_password' => 'required|string|min:6',
            'pixel_id' => 'nullable|string|max:60',
        ]);

        // 1. Tester la connexion à la base
        try {
            $this->applyDbConfig($data);
            DB::connection('install_test')->getPdo();
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['db_host' => 'Connexion à la base impossible : ' . $e->getMessage()]);
        }

        // 2. Écrire le .env
        $env = [
            'APP_NAME' => $data['site_name'],
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => $request->getSchemeAndHttpHost(),
            'DB_CONNECTION' => $data['db_connection'],
        ];
        if ($data['db_connection'] === 'mysql') {
            $env += [
                'DB_HOST' => $data['db_host'],
                'DB_PORT' => $data['db_port'] ?: '3306',
                'DB_DATABASE' => $data['db_database'],
                'DB_USERNAME' => $data['db_username'],
                'DB_PASSWORD' => $data['db_password'] ?? '',
            ];
        } else {
            $sqlitePath = database_path('database.sqlite');
            if (! file_exists($sqlitePath)) {
                touch($sqlitePath);
            }
            $env['DB_DATABASE'] = $sqlitePath;
        }
        Installer::setEnv($env);

        // 3. Clé d'application si manquante
        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
        }

        // 4. Recharger la connexion par défaut sur les nouveaux paramètres puis migrer + seed
        $this->bindAsDefault($data);
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

        // 4 bis. Lien public/storage -> storage/app/public (designs uploadés, médias).
        // Certains hébergeurs mutualisés désactivent symlink() : on tente le lien, puis on
        // se rabat sur une copie du dossier, sans jamais faire échouer l'installation.
        $this->linkStorage();

        // 5. Créer / mettre à jour l'admin et les réglages clés
        User::updateOrCreate(
            ['email' => $data['admin_email']],
            ['name' => $data['admin_name'], 'password' => Hash::make($data['admin_password'])]
        );
        Setting::put('site_name', $data['site_name'], 'general');
        if (! empty($data['pixel_id'])) {
            Setting::put('facebook_pixel_id', $data['pixel_id'], 'marketing');
        }

        // 6. Verrouiller l'installateur
        Installer::lock();

        return redirect()->route('install.complete');
    }

    public function complete()
    {
        return view('install.complete');
    }

    /**
     * Crée le lien public/storage de façon tolérante aux hébergements mutualisés.
     * Ne lève jamais d'exception : l'installation ne doit pas échouer pour ça.
     */
    private function linkStorage(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (is_link($link) || is_dir($link)) {
            return;
        }

        // 1. Voie standard Laravel.
        try {
            Artisan::call('storage:link', ['--force' => true]);
        } catch (\Throwable $e) {
            // symlink() peut être désactivé : on tente les solutions de repli.
        }

        if (is_link($link) || is_dir($link)) {
            return;
        }

        // 2. Lien symbolique natif.
        try {
            @symlink($target, $link);
        } catch (\Throwable $e) {
            // ignoré
        }

        // 3. Dernier repli : un vrai dossier (l'admin pourra créer le lien via Terminal plus tard).
        if (! is_link($link) && ! is_dir($link)) {
            @mkdir($link, 0755, true);
        }
    }

    private function applyDbConfig(array $data): void
    {
        if ($data['db_connection'] === 'mysql') {
            config(['database.connections.install_test' => [
                'driver' => 'mysql',
                'host' => $data['db_host'],
                'port' => $data['db_port'] ?: '3306',
                'database' => $data['db_database'],
                'username' => $data['db_username'],
                'password' => $data['db_password'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]]);
        } else {
            $sqlitePath = database_path('database.sqlite');
            if (! file_exists($sqlitePath)) {
                touch($sqlitePath);
            }
            config(['database.connections.install_test' => [
                'driver' => 'sqlite',
                'database' => $sqlitePath,
                'prefix' => '',
            ]]);
        }
        DB::purge('install_test');
    }

    private function bindAsDefault(array $data): void
    {
        $conn = $data['db_connection'];
        config(['database.default' => $conn]);
        if ($conn === 'mysql') {
            config(['database.connections.mysql' => array_merge(config('database.connections.mysql'), [
                'host' => $data['db_host'],
                'port' => $data['db_port'] ?: '3306',
                'database' => $data['db_database'],
                'username' => $data['db_username'],
                'password' => $data['db_password'] ?? '',
            ])]);
        } else {
            config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        }
        DB::purge($conn);
    }
}
