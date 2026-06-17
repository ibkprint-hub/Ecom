# BePack — boutique d'emballage personnalisé (print-on-demand)

Plateforme e-commerce **Laravel 11 + Filament** pour la vente de **boîtes en carton, boîtes en papier et
sacs personnalisés** aux e-commerçants. Configurateur en 4 étapes, devis instantané, **paiement à la
livraison (COD)**, CMS/dashboard auto-administrable, intégration **Facebook Pixel** et liens d'achat
traçables pour les publicités.

## Démarrage rapide

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
npm install && npm run build
php artisan serve
```

- **Site public** : http://localhost:8000
- **Admin** : http://localhost:8000/admin — `admin@bepack.dz` / `password`

Voir [`INSTALL.md`](INSTALL.md) pour le déploiement sur hébergement mutualisé (ZIP + cPanel).

## Fonctionnalités (MVP)

- **Configurateur** (Livewire) : produit → dimension → design (upload ou « faites-moi le design ») →
  quantité → coordonnées, avec **devis recalculé en direct** et **revalidé côté serveur**.
- **COD** : commande sans paiement en ligne, statuts de suivi, attribution UTM stockée sur la commande.
- **Dashboard Filament** : catalogue (familles, produits, dimensions, paliers de prix, options),
  commandes, frais de livraison par wilaya (58), pages CMS, réglages, statistiques.
- **Facebook Pixel** : ID éditable dans l'admin, événements `ViewContent` et `Purchase`/`Lead`.
- **Deep-links pub** : `/c/{produit}?dim=...&qty=...&utm_source=...` pré-remplit le configurateur.

## Architecture

| Couche | Détail |
|--------|--------|
| Domaine | `app/Models` — catalogue, commandes, livraison, pages, réglages |
| Tarification | `app/Services/PricingService.php` (source de vérité serveur) |
| Configurateur | `app/Livewire/Configurator.php` + `resources/views/livewire/configurator.blade.php` |
| Public | `app/Http/Controllers/ShopController.php`, `resources/views/shop/*`, `layouts/app.blade.php` |
| Admin | `app/Filament/Resources/*`, `app/Filament/Pages/ManageSettings.php`, `app/Filament/Widgets/*` |
| Données démo | `database/seeders/DatabaseSeeder.php` |

## Tests

```bash
php artisan test --filter=ConfiguratorTest
```

## Documentation de conception

Le plan complet du projet (vision, branding, architecture, roadmap) est dans [`docs/`](docs/) — voir
[`docs/00-plan-README.md`](docs/00-plan-README.md).
