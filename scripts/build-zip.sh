#!/usr/bin/env bash
#
# Construit une archive ZIP de BePack prête pour un hébergement mutualisé (cPanel).
# - Installe les dépendances PHP optimisées (vendor/ inclus)
# - Compile les assets front (public/build inclus)
# - Génère un .env de production avec une CLÉ unique (l'app démarre sans ligne de commande)
# - Exclut les fichiers de développement et la base/symlink locaux
#
set -e

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
OUT="${1:-bepack.zip}"

echo "==> Dépendances PHP (production)"
composer install --no-dev --optimize-autoloader

echo "==> Compilation des assets"
[ -d node_modules ] || npm ci
npm run build

echo "==> Optimisations Laravel"
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Préparation d'un .env de production (clé unique)"
KEY="$(php artisan key:generate --show)"
TMP_ENV="$(mktemp)"
cp .env.example "$TMP_ENV"
php -r '
$f=$argv[1];$k=$argv[2];
$c=file_get_contents($f);
$set=function($c,$key,$val){
  return preg_match("/^$key=.*$/m",$c)
    ? preg_replace("/^$key=.*$/m","$key=$val",$c)
    : $c.PHP_EOL."$key=$val";
};
$c=$set($c,"APP_NAME","BePack");
$c=$set($c,"APP_ENV","production");
$c=$set($c,"APP_DEBUG","false");
$c=$set($c,"APP_KEY",$k);
// Sessions/cache en fichiers : l_assistant /install doit fonctionner AVANT que la base existe.
$c=$set($c,"SESSION_DRIVER","file");
$c=$set($c,"CACHE_STORE","file");
file_put_contents($f,$c);
' "$TMP_ENV" "$KEY"

# Met de côté le .env de dev, place le .env de prod le temps de l'archivage
ENV_BACKUP=""
if [ -f .env ]; then ENV_BACKUP="$(mktemp)"; cp .env "$ENV_BACKUP"; fi
cp "$TMP_ENV" .env

echo "==> Création de l'archive $OUT"
rm -f "$OUT"
zip -rq "$OUT" . \
  -x "*.git*" \
  -x "node_modules/*" \
  -x "tests/*" \
  -x "docs/*" \
  -x "scripts/*" \
  -x "storage/logs/*" \
  -x "storage/installed" \
  -x "database/database.sqlite" \
  -x "public/storage" \
  -x "public/storage/*" \
  -x "$OUT"

# Restaure le .env de dev
if [ -n "$ENV_BACKUP" ]; then cp "$ENV_BACKUP" .env; rm -f "$ENV_BACKUP"; else rm -f .env; fi
rm -f "$TMP_ENV"

echo "==> Terminé : $OUT"
echo "Uploadez-le dans cPanel, faites pointer le sous-domaine sur public/, puis ouvrez /install."
