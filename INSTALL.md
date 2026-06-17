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

### 4. Installation via l'assistant web (recommandé — façon CMS)

1. Copiez `.env.example` en `.env` (un `.env` minimal suffit ; l'assistant écrit le reste).
2. Générez la clé : `php artisan key:generate` (ou laissez l'assistant le faire si la clé est vide).
3. Ouvrez **`https://votre-domaine/install`** dans le navigateur. L'assistant :
   - vérifie les **prérequis** (PHP, extensions, droits d'écriture) ;
   - demande les **identifiants de base de données** (teste la connexion) ;
   - crée le **compte administrateur** et le **nom du site** + **Pixel ID** ;
   - lance **migrations + données de démo** ;
   - crée le **lien `public/storage`** automatiquement (aucune ligne de commande requise ;
     repli silencieux si l'hébergeur désactive `symlink()`) ;
   - puis **se verrouille** automatiquement.

> Tant que l'application n'est pas installée, toutes les URL redirigent vers `/install`.
> Une fois installée (fichier `storage/installed` créé), l'assistant est désactivé.

### 4 bis. Installation manuelle (alternative CLI)

```bash
cp .env.example .env
php artisan key:generate
# Renseignez DB_* dans .env, puis :
php artisan migrate --seed --force
php artisan storage:link
php artisan config:cache
```

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
