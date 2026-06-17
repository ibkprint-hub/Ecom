# 05 — Dashboard / CMS (back-office)

Objectif : un back-office « comme le site d'imprimerie » — l'administrateur gère **tout** sans toucher au
code. Construit avec **Filament** (rapide, professionnel, extensible).

## 1. Modules du dashboard

### Tableau de bord (accueil admin)
- KPIs : commandes du jour/semaine, chiffre d'affaires COD, taux de confirmation, panier moyen.
- Graphiques : ventes par produit, par wilaya, sources de trafic (UTM).
- File d'attente : commandes à confirmer, BAT à envoyer/valider, commandes à expédier.

### Catalogue
- Familles, produits, options & valeurs, dimensions (presets + sur-mesure), médias.
- Grilles de prix par paliers de quantité (éditeur de tableau).
- Réglages design par produit (modes autorisés, prix du service design, contraintes fichiers).
- Activation/désactivation, mise en avant, ordre d'affichage.

### Commandes
- Liste filtrable (statut, wilaya, transporteur, date, source pub).
- Fiche commande : détail, design uploadé / brief, mockup, coordonnées, historique de statut.
- Actions : confirmer, changer de statut, envoyer BAT, générer **bon de commande / facture PDF**,
  imprimer bordereau de livraison, exporter CSV.
- Notifications client (email/SMS/WhatsApp) selon transitions.

### Clients
- Comptes, historique, adresses, blocklist (anti-abus COD).

### Pages & Contenu (CMS)
- Éditeur de pages par blocs (hero, sections, FAQ, témoignages, CTA, galerie).
- Menus (header/footer), bannières, pop-ups.
- Blog/articles (optionnel, SEO).

### Livraison
- Zones (wilayas/régions), tarifs par zone et/ou par transporteur, délais, frais COD.
- Transporteurs et modes (domicile / point relais).

### Marketing
- Facebook **Pixel ID**, token **API Conversions**, événements activés.
- Générateur de **liens d'achat** UTM (voir `06`).
- Codes promo / coupons (optionnel).
- Export du **catalogue produits** (CSV/XML) pour le Catalogue Meta.

### Apparence / Thème
- Logo, favicon, couleurs (variables CSS), typographies, images par défaut.
- Choix de la palette (Kraft vs Tech) et personnalisation fine.

### Réglages
- Identité du site, devise (DZD…), langues actives (FR/AR/EN), fuseau horaire.
- SMTP / e-mails, modèles de notifications.
- RGPD / bandeau cookies / consentement (prérequis au chargement du Pixel).
- Maintenance, cache, sauvegardes.

### Utilisateurs & rôles
- Rôles (Admin, Gestionnaire commandes, Designer, Éditeur de contenu) via `spatie/laravel-permission`.
- Journal d'audit des actions.

## 2. Principes UX du back-office
- **Données de démo** au premier lancement pour comprendre le fonctionnement immédiatement.
- **Édition en place** et aperçu avant publication pour les pages.
- **Recherche globale** et filtres puissants sur les commandes.
- **Multilingue** dans les formulaires (onglets de langue par champ traduisible).
