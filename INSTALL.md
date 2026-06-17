# Installation de BePack

BePack est une application **Laravel 11 + Filament**. Voici comment l'installer en local (développement)
et sur un **hébergement mutualisé** (cPanel).

## Identifiants admin par défaut (après seed)

- URL : `/admin`
- Email : `admin@bepack.dz`
- Mot de passe : `password`

> ⚠️ Changez ce mot de passe immédiatement en production.

---

## A. Installation en local (développement)

Prérequis : PHP 8.2+, Composer, Node 18+, npm.

```bash
composer install
cp .env.example .env
php artisan key:generate

# Base de données : SQLite par défaut (rien à configurer)
touch database/database.sqlite
php artisan migrate:fresh --seed

php artisan storage:link
npm install && npm run build

php artisan serve
```

Site public : http://localhost:8000 — Admin : http://localhost:8000/admin

---

## B. Déploiement sur hébergement mutualisé (cPanel)

### 1. Construire l'archive ZIP

Sur une machine de dev :

```bash
./scripts/build-zip.sh bepack.zip
```

L'archive contient `vendor/` (dépendances installées) et `public/build/` (assets compilés) :
**aucun Composer ni Node n'est requis sur le serveur mutualisé.**

### 2. Base de données

Dans cPanel → *MySQL Databases* : créez une base + un utilisateur, et notez les identifiants.

### 3. Upload & extraction

- Décompressez le ZIP sur le serveur.
- **Recommandé** : faites pointer le domaine/sous-domaine sur le dossier `public/`.
- Variante `public_html` : placez le contenu de `public/` dans `public_html/` et le reste de
  l'application **au-dessus** de la racine web, puis ajustez les chemins `require` dans `public_html/index.php`.

### 4. Configuration `.env`

Copiez `.env.example` en `.env` et renseignez :

```env
APP_NAME=BePack
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Puis générez la clé et migrez :

```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
php artisan config:cache
```

> Si l'accès SSH n'est pas disponible, exécutez ces commandes via le *Terminal* cPanel, ou utilisez une
> tâche planifiée temporaire. (Un assistant d'installation web `/install` est prévu en évolution — voir
> `docs/03-architecture-technique.md`.)

### 5. Tâche planifiée (cron)

Ajoutez dans cPanel → *Cron Jobs* :

```
* * * * * php /home/USER/chemin/artisan schedule:run >> /dev/null 2>&1
```

### 6. HTTPS

Activez Let's Encrypt (cPanel → SSL/TLS Status).

---

## C. Premiers réglages dans l'admin

1. **Réglages** → nom du site, devise, téléphone, et surtout **Facebook Pixel ID**.
2. **Catalogue** → ajustez familles, produits, dimensions, paliers de prix et options.
3. **Frais de livraison** → vérifiez les tarifs par wilaya.
4. **Pages** → éditez le contenu (FAQ, CGV, etc.).
