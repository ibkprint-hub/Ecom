#!/usr/bin/env bash
#
# Construit une archive ZIP de BePack prête pour un hébergement mutualisé.
# - Installe les dépendances PHP optimisées (vendor/ inclus)
# - Compile les assets front (public/build inclus)
# - Exclut les fichiers de développement
#
set -e

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
OUT="${1:-bepack.zip}"

echo "==> Dépendances PHP (production)"
composer install --no-dev --optimize-autoloader

echo "==> Compilation des assets"
npm ci
npm run build

echo "==> Optimisations Laravel"
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Création de l'archive $OUT"
rm -f "$OUT"
zip -r "$OUT" . \
  -x "*.git*" \
  -x "node_modules/*" \
  -x "tests/*" \
  -x ".env" \
  -x "storage/logs/*" \
  -x "$OUT" \
  -x "scripts/*" \
  -x "docs/*"

echo "==> Terminé : $OUT"
echo "Pensez à inclure un .env basé sur .env.example chez l'hébergeur, puis lancez les migrations."
