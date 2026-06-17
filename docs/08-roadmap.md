# 08 — Roadmap, phases & décisions à valider

## 1. Découpage en phases

### Phase 0 — Cadrage & design (en cours)
- ✅ Plan fonctionnel & technique (ce dossier).
- ⬜ Validation des décisions ouvertes (section 3).
- ⬜ Branding : génération du logo + kit visuel + maquette de la home.

### Phase 1 — MVP commandable (le plus important)
- Squelette Laravel + Filament + thème BePack.
- Catalogue : 3 familles, quelques produits, dimensions, options, grilles de prix.
- **Configurateur 4 étapes** avec devis temps réel + upload de design + aperçu.
- Mode « Faites-moi le design » (brief + supplément, workflow BAT simplifié).
- Checkout **COD** + coordonnées + frais de livraison par wilaya.
- Dashboard : commandes, catalogue, livraison, réglages, pages de base.
- **Facebook Pixel** (events du funnel) + capture UTM sur les commandes.
- Pages CMS essentielles (home, produits, FAQ, livraison, CGV, contact).
- Emails transactionnels.
- Assistant d'installation + **livrable ZIP** + guide.

### Phase 2 — Croissance & marketing avancé
- **API Conversions** (CAPI) + déduplication + Purchase à la livraison.
- Générateur de liens d'achat + landing pages de campagne.
- Export catalogue Meta (DPA).
- Notifications SMS/WhatsApp.
- Codes promo, comptes clients enrichis, recommander à l'identique.
- Anti-abus COD avancé (vérif téléphone, blocklist).

### Phase 3 — Différenciation
- Intégration API transporteur COD (bordereaux + suivi + réconciliation).
- Éditeur de design en ligne (drag & drop) plus poussé.
- Multi-langue complet AR/EN + RTL.
- PWA, optimisations perf/CDN.
- Multi-vendeurs / revendeurs (si pertinent).

## 2. Estimation indicative
- **MVP (Phase 1)** : le gros du travail ; livrable utilisable et vendable.
- Phases 2 et 3 : itérations additionnelles selon priorités commerciales.

*(Estimation en jours à affiner une fois les décisions ci-dessous tranchées.)*

## 3. Décisions ouvertes à valider ✅/❌

1. **Marché & langue par défaut** : Algérie / FR par défaut (AR & EN en option) ? — *hypothèse retenue.*
2. **Devise** : DZD ? Format d'affichage ?
3. **Direction du branding** : palette **Kraft/artisan** (recommandée) ou **Tech/bleu** ?
4. **Mapping conversion** : compter la commande COD comme `Purchase` ou `Lead` côté Pixel ?
5. **Transporteur(s)** : grille manuelle par wilaya au MVP, intégration API en phase 2 — lequel
   (Yalidine / ZR Express / Maystro / autre) ?
6. **Panier multi-produit dès le MVP** ou **achat express mono-produit** d'abord ?
7. **Sur-mesure des dimensions** activé dès le départ, ou seulement des presets ?
8. **Confirmation de commande** : appel/SMS manuel, ou auto, pour limiter les commandes fantômes ?
9. **Nom de domaine** (bepack.dz ? .com ?) et hébergeur mutualisé cible (pour valider PHP/extensions).

## 4. Prochaines étapes proposées
1. Tu valides/ajustes les décisions ci-dessus.
2. Je **génère le logo + le branding visuel** et une **maquette HTML de la page d'accueil** (sans backend)
   pour valider l'aspect avant de coder.
3. On démarre la **Phase 1 (MVP)** en Laravel.
