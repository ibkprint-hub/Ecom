# BePack — Plan du projet

**BePack** est une plateforme e-commerce de type *print-on-demand* dédiée à l'emballage personnalisé :
**boîtes en carton, boîtes en papier et sacs en plastique personnalisés**, à destination des **e-commerçants**.

Le client choisit un produit → une dimension → importe son design (ou commande la création du design) →
choisit une quantité → renseigne ses coordonnées → **paie à la livraison (COD)**.

Le site est un **CMS auto-administrable** (dashboard d'administration), construit en **Laravel**,
livrable en **archive ZIP** installable sur **hébergement mutualisé**, avec **tout le contenu éditable**
et l'intégration **Facebook Pixel + liens d'achat pour les publicités**.

> ⚠️ Cette phase est **uniquement de la planification**. Aucun code applicatif n'est encore écrit.

## Sommaire des documents

| Document | Contenu |
|----------|---------|
| [`docs/01-vision-perimetre.md`](docs/01-vision-perimetre.md) | Vision, cibles, périmètre fonctionnel, parcours client |
| [`docs/02-branding.md`](docs/02-branding.md) | Identité BePack : logo, couleurs, typographies, ton |
| [`docs/03-architecture-technique.md`](docs/03-architecture-technique.md) | Stack Laravel, contraintes mutualisé, packages |
| [`docs/04-modele-donnees.md`](docs/04-modele-donnees.md) | Entités, relations, configurateur, tarification |
| [`docs/05-dashboard-cms.md`](docs/05-dashboard-cms.md) | Back-office, édition de contenu, gestion commandes |
| [`docs/06-marketing-tracking.md`](docs/06-marketing-tracking.md) | Facebook Pixel, Conversions API, catalogue, liens d'achat |
| [`docs/07-livraison-installation.md`](docs/07-livraison-installation.md) | Livraison COD, transporteurs, livrable ZIP, installation |
| [`docs/08-roadmap.md`](docs/08-roadmap.md) | Phases, MVP, estimation, décisions à valider |

## Résumé en une page

- **Modèle de paiement** : COD (paiement à la livraison) exclusivement.
- **Cœur du produit** : un configurateur en 4 étapes (produit → dimension → design → quantité).
- **Deux modes de design** : « J'ai mon design » (upload de fichier) ou « Faites-moi le design » (brief + supplément).
- **Tarification dégressive** par paliers de quantité, paramétrable par produit/dimension.
- **Tout éditable** : pages, textes, images, produits, dimensions, prix, frais de livraison, SEO, depuis le dashboard.
- **Marketing** : Facebook Pixel + API de Conversions, deep-links d'achat traçables pour les publicités.
- **Déploiement** : compatible hébergement mutualisé (cPanel), livré en ZIP avec assistant d'installation.
