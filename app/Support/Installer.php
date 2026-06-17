<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class Installer
{
    public static function lockPath(): string
    {
        return storage_path('installed');
    }

    public static function isInstalled(): bool
    {
        return File::exists(self::lockPath());
    }

    public static function lock(): void
    {
        File::put(self::lockPath(), 'Installé le ' . now()->toDateTimeString() . PHP_EOL);
    }

    /** Vérifie les prérequis serveur. Retourne [ [label, ok, hint], ... ]. */
    public static function requirements(): array
    {
        $reqExt = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'fileinfo', 'ctype', 'json'];
        $checks = [
            ['PHP ≥ 8.2', version_compare(PHP_VERSION, '8.2.0', '>='), 'Version actuelle : ' . PHP_VERSION],
        ];
        foreach ($reqExt as $ext) {
            $checks[] = ["Extension PHP : {$ext}", extension_loaded($ext), 'Activez l\'extension chez votre hébergeur.'];
        }
        $checks[] = ['Extension : pdo_mysql ou pdo_sqlite', extension_loaded('pdo_mysql') || extension_loaded('pdo_sqlite'), 'Au moins un pilote de base.'];
        $checks[] = ['Extension : gd', extension_loaded('gd'), 'Recommandée pour les images.'];
        $checks[] = ['storage/ accessible en écriture', is_writable(storage_path()), 'chmod 755 sur storage/.'];
        $checks[] = ['.env accessible en écriture', is_writable(base_path('.env')) || is_writable(base_path()), 'Créez .env depuis .env.example.'];

        return $checks;
    }

    public static function allRequirementsMet(): bool
    {
        foreach (self::requirements() as [$label, $ok]) {
            if (! $ok) {
                return false;
            }
        }

        return true;
    }

    /** Met à jour (ou ajoute) des clés dans le fichier .env. */
    public static function setEnv(array $values): void
    {
        $path = base_path('.env');
        if (! File::exists($path)) {
            File::copy(base_path('.env.example'), $path);
        }
        $content = File::get($path);

        foreach ($values as $key => $value) {
            $escaped = self::escapeEnvValue($value);
            $line = "{$key}={$escaped}";
            if (preg_match("/^{$key}=.*$/m", $content)) {
                $content = preg_replace("/^{$key}=.*$/m", $line, $content);
            } else {
                $content .= PHP_EOL . $line;
            }
        }

        File::put($path, $content);
    }

    private static function escapeEnvValue(?string $value): string
    {
        $value = (string) $value;
        if ($value === '' || preg_match('/\s|#|"|\'/', $value)) {
            return '"' . str_replace('"', '\"', $value) . '"';
        }

        return $value;
    }
}
