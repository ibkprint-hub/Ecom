# 01 — Vision, périmètre & parcours client

## 1. Vision

BePack est la « boutique d'emballage à la demande » pour les e-commerçants : ils arrivent avec leur logo
ou leur idée, configurent leur emballage en quelques clics, et reçoivent un devis instantané. Le ton est
celui d'un SaaS moderne (rapide, visuel, rassurant), mais le paiement reste **COD** pour coller au marché
local (confiance, pas de carte requise).

### Proposition de valeur
- **Personnalisation simple** : pas besoin de logiciel pro, on importe une image ou on délègue le design.
- **Devis instantané** selon dimension + quantité.
- **Sans risque** : on ne paie qu'à la réception (COD).
- **Pensé e-commerçant** : packaging branding, unboxing soigné, commandes répétables.

## 2. Cibles

1. **E-commerçants / vendeurs en ligne** (cible principale) : veulent des boîtes/sacs à leur marque pour
   l'expédition et l'unboxing.
2. **Petites marques & artisans** : cosmétiques, food, mode, qui veulent un emballage premium.
3. **Revendeurs / grossistes** : commandes en gros, paliers de quantité élevés.

## 3. Catalogue produit

Trois familles, chacune déclinée en modèles puis en dimensions :

| Famille | Exemples de modèles | Attributs configurables |
|---------|--------------------|--------------------------|
| **Boîtes en carton** | Caisse américaine, boîte pliante, boîte à fenêtre, mailer e-commerce | Dimensions (L×l×H), cannelure/épaisseur, impression 1 ou quadri |
| **Boîtes en papier** | Boîte cadeau, étui, boîte à rabat | Dimensions, type de papier, finition (mat/brillant) |
| **Sacs en plastique** | Sac à soufflet, sac zip, pochette d'expédition | Dimensions, micronnage/épaisseur, couleur de base |

> Les familles, modèles, dimensions et options sont **gérés depuis le dashboard** (voir `04-modele-donnees.md`).

## 4. Parcours client (le funnel print-on-demand)

```
Accueil / Landing
      │
      ▼
[Étape 1] Choix du produit  ──►  Choix d'une dimension (ou dimension sur-mesure)
      │
      ▼
[Étape 2] Design
      ├─ « J'ai mon design »      → upload fichier (PNG/PDF/AI), aperçu sur gabarit
      └─ « Faites-moi le design » → brief (logo, couleurs, références) + supplément design
      │
      ▼
[Étape 3] Quantité  ──►  prix unitaire dégressif + total affichés en direct
      │
      ▼
[Étape 4] Coordonnées + Livraison
      ├─ Nom, téléphone, wilaya/ville, adresse
      └─ Choix transporteur / point relais (selon config)
      │
      ▼
Récapitulatif  ──►  Confirmation commande (COD)
      │
      ▼
Page de remerciement + suivi  (déclenche l'event Pixel "Purchase"/"Lead")
```

### Détails clés du parcours
- **Devis temps réel** : le prix se recalcule à chaque changement de dimension/quantité/option design.
- **Upload de design** : validation du format/poids, génération d'un aperçu (mockup) sur le gabarit produit.
- **Mode « on fait le design »** : ajoute un coût fixe paramétrable et déclenche un workflow de validation
  (l'admin envoie un BAT — bon à tirer — que le client approuve avant production).
- **Panier** : possibilité de configurer plusieurs produits avant de commander (option ; le MVP peut
  démarrer en mono-produit « achat express »).
- **Compte client optionnel** : commande possible en invité ; un compte permet de retrouver l'historique
  et de recommander à l'identique.

## 5. Pages publiques (toutes éditables via CMS)

- Accueil (hero, sections de réassurance, produits mis en avant, témoignages, CTA).
- Listing produits par famille + fiche produit avec configurateur.
- Landing pages dédiées (pour les campagnes pub — voir `06-marketing-tracking.md`).
- Pages de contenu : À propos, FAQ, Comment ça marche, Livraison & retours, CGV, Mentions légales,
  Politique de confidentialité, Contact.
- Blog / articles (optionnel, utile pour le SEO).

## 6. Hors périmètre (MVP)

- Paiement en ligne (CB / carte) — **non**, COD uniquement. (Architecture laissée extensible.)
- Marketplace multi-vendeurs.
- Éditeur de design en ligne avancé type Canva (on commence par l'upload + aperçu ; un éditeur drag&drop
  pourra être une phase ultérieure).
- Application mobile native (le site sera responsive / PWA-ready).
