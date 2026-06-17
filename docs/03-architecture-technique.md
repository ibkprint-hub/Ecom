# 03 — Architecture technique

## 1. Stack cible

- **Framework** : **Laravel 11** (PHP 8.2+).
- **Base de données** : **MySQL / MariaDB** (standard sur hébergement mutualisé cPanel).
- **Front public** : Blade + **Tailwind CSS** + **Alpine.js** (léger, pas de build lourd côté serveur).
  - Le configurateur (calcul de prix temps réel, upload, aperçu) en Alpine.js / Livewire.
- **Admin / Dashboard** : **Filament** (PHP, basé Livewire) — back-office complet, rapide à construire,
  parfait pour un CMS « tout éditable » sans réinventer l'UI. *(Alternative : Laravel Nova — payant.)*
- **Authentification** : Laravel Breeze (clients) + garde séparé pour l'admin (Filament).
- **Assets** : Vite pour le build local ; les assets compilés sont inclus dans le ZIP (pas de build requis
  sur le mutualisé).

> Choix structurant : **Filament** couvre à la fois le back-office, la gestion produits/commandes, et
> l'édition de contenu — c'est le pilier du « CMS avec dashboard comme le site d'imprimerie ».

## 2. Contraintes hébergement mutualisé (et réponses)

| Contrainte mutualisé | Réponse dans l'architecture |
|----------------------|------------------------------|
| Pas d'accès SSH garanti / pas de Composer en prod | On livre `vendor/` déjà installé dans le ZIP + assets compilés |
| Pas de workers/queues persistants | Queue en `database` déclenchée par **cron** (`schedule:run` + `queue:work --once`) |
| Pas de Redis | Cache & sessions en `file` ou `database` |
| Document root = `public_html` | Doc fixé sur `public/` (ou variante « racine partagée » documentée) |
| Pas de Supervisor | Tâches planifiées via le **Cron Job** de cPanel (1 ligne) |
| Envoi d'emails limité | SMTP configurable (Mailtrap/SMTP de l'hébergeur/SendGrid) via l'assistant d'install |
| PHP version variable | Vérification de version + extensions dans l'assistant d'installation |

### Adaptation « racine partagée »
Beaucoup de mutualisés servent depuis `public_html`. Le livrable proposera **deux modes** :
1. **Sous-domaine pointé sur `public/`** (recommandé, propre).
2. **Déploiement en `public_html`** avec `index.php` ajusté et fichiers `app/` placés hors-web — procédure
   documentée dans le guide d'installation.

## 3. Assistant d'installation (web installer)

Comme un CMS classique, un **installateur web** (`/install`) guide l'utilisateur :
1. Vérification des prérequis (version PHP, extensions : pdo_mysql, gd/imagick, fileinfo, openssl…).
2. Saisie des accès base de données → test de connexion.
3. Migration + seed des données de démo (produits, dimensions, pages).
4. Création du compte administrateur.
5. Réglages de base (nom du site, devise, langue, SMTP, Facebook Pixel ID).
6. Génération du `.env` et de l'`APP_KEY`, puis verrouillage de l'installateur.

## 4. Gestion des fichiers (designs clients & mockups)

- Stockage local `storage/app/public` (lien symbolique ou copie pour mutualisé) ; option S3 laissée
  configurable.
- Formats acceptés : **PNG, JPG, PDF, AI/SVG** (paramétrable). Limite de poids configurable.
- Génération d'**aperçu/mockup** : superposition de l'image sur un gabarit produit (GD/Imagick).
- Antivirus/validation : vérification MIME réelle, taille, dimensions.

## 5. Internationalisation & localisation

- **Multilingue** prévu (FR par défaut ; AR et EN activables) via fichiers de langue + champs traduisibles.
- **RTL** supporté pour l'arabe (classe `dir=rtl`).
- **Devise & format** paramétrables (DZD par défaut, séparateurs, position du symbole).

## 6. Packages Laravel pressentis

| Besoin | Package |
|--------|---------|
| Back-office / CMS | `filament/filament` |
| Médias & uploads | `spatie/laravel-medialibrary` |
| Rôles & permissions | `spatie/laravel-permission` |
| Réglages éditables | `spatie/laravel-settings` |
| SEO / sitemap | `spatie/laravel-sitemap` + balises meta éditables |
| Slugs & traductions | `spatie/laravel-translatable` (ou colonnes JSON) |
| Sauvegardes | `spatie/laravel-backup` (optionnel) |
| Génération PDF (devis/bon de commande) | `barryvdh/laravel-dompdf` |

## 7. Performance & SEO sur mutualisé

- Cache de config/routes/vues généré et inclus.
- Images responsives + lazy-loading + conversion WebP à l'upload.
- Pages de contenu en cache HTTP/edge si CDN (Cloudflare gratuit recommandé devant le mutualisé).
- Balises meta, Open Graph, données structurées `Product`/`Offer` éditables par page/produit.
- `robots.txt` + sitemap auto-générés.

## 8. Sécurité

- HTTPS forcé (Let's Encrypt via cPanel), HSTS.
- CSRF (natif Laravel), validation stricte des uploads, rate-limiting sur formulaires/commande.
- Protection anti-spam sur le checkout COD (honeypot + limitation par IP/téléphone, optionnel reCAPTCHA).
- Sauvegardes BDD + médias planifiées.
- Journalisation des actions admin (audit log via Filament).
- RGPD/consentement cookies (nécessaire pour charger le Pixel — voir `06`).
