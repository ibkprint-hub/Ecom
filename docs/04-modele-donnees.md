# 04 — Modèle de données, configurateur & tarification

## 1. Entités principales

```
ProductFamily (Famille)          ── boîtes carton / boîtes papier / sacs plastique
  └─ Product (Modèle)            ── ex. "Caisse américaine", "Mailer e-commerce"
       ├─ ProductOption          ── cannelure, finition, couleur… (groupes d'options)
       │    └─ OptionValue       ── valeurs + éventuel surcoût
       ├─ Dimension              ── L×l×H (ou presets) + sur-mesure autorisé
       └─ PriceTier              ── paliers de quantité → prix unitaire

DesignMode                       ── "upload" | "service_design"
DesignAsset                      ── fichier client uploadé + mockup généré
DesignBrief                      ── brief quand "Faites-moi le design"

Cart / CartItem                  ── (si panier multi-produit)
Order                            ── commande COD
  └─ OrderItem                   ── snapshot produit/dimension/options/quantité/prix
  └─ OrderStatusHistory          ── suivi du workflow
Customer                         ── client (invité ou compte)
Address                          ── wilaya/ville/adresse, téléphone
ShippingZone / ShippingRate      ── frais par zone (wilaya) ou transporteur
Carrier                          ── transporteur (ex. livraison à domicile / point relais)

Page                             ── pages CMS éditables (slug, blocs, SEO)
ContentBlock                     ── blocs réutilisables (hero, FAQ, témoignages…)
Setting                          ── réglages globaux (devise, langue, Pixel ID, SMTP…)
Coupon                           ── codes promo (optionnel)
User (admin)                     ── + rôles/permissions
ActivityLog                      ── audit des actions admin
```

## 2. Le configurateur produit (le cœur)

Pour chaque **Product**, l'admin définit :
- les **dimensions** disponibles (presets) et si le **sur-mesure** est autorisé (min/max par axe) ;
- les **groupes d'options** (ex. impression : 1 couleur / quadri ; finition : mat / brillant) avec surcoûts ;
- la **grille tarifaire** (paliers de quantité) ;
- le **mode design** autorisé (upload, service, ou les deux) et le **prix du service design** ;
- les contraintes de fichier (formats, DPI mini, zone de fond perdu / marges).

### Calcul du prix (formule de référence)

```
prix_unitaire(base)      = lookup PriceTier(product, dimension, quantité)
surcout_options          = Σ surcoûts des OptionValue sélectionnées (par unité ou forfait)
prix_unitaire_final      = prix_unitaire(base) + surcout_options_par_unité
sous_total               = prix_unitaire_final × quantité
frais_design             = (mode == service_design) ? prix_service_design : 0
frais_livraison          = ShippingRate(zone, poids/volume, transporteur)
remise                   = Coupon (optionnel)
TOTAL_COD                = sous_total + frais_design + frais_livraison − remise
```

> Le calcul est **recalculé en direct** côté client et **revalidé côté serveur** à la commande
> (jamais faire confiance au prix venant du navigateur).

### Tarification dégressive — exemple de grille

| Quantité | Prix unitaire (illustratif) |
|----------|------------------------------|
| 50       | 120 |
| 100      | 95  |
| 250      | 78  |
| 500      | 62  |
| 1000     | 49  |

Chaque ligne est **éditable par produit et par dimension** dans le dashboard. Les valeurs ci-dessus sont
des exemples — les vrais prix seront saisis par l'admin.

## 3. Données « design »

- **Mode upload** : `DesignAsset` stocke le fichier original + un **mockup** généré (aperçu sur gabarit).
  Validation : format, poids, résolution mini (DPI), respect des marges/fond perdu.
- **Mode service** : `DesignBrief` capture logo, couleurs, style, références, instructions. Déclenche un
  workflow : *Brief reçu → Design en cours → BAT envoyé → BAT validé par le client → Production*.

## 4. Workflow de commande (statuts)

```
Nouvelle (COD)  →  Confirmée (appel/contact)  →  [si design service] BAT en attente → BAT validé
                →  En production  →  Expédiée  →  Livrée (encaissée)  
                                                   └─ ou →  Annulée / Retour
```

Chaque transition est journalisée (`OrderStatusHistory`) et peut déclencher une **notification**
(email/SMS/WhatsApp selon config) au client et un **event de tracking** (voir `06`).

## 5. Spécificité COD (paiement à la livraison)

- Pas de capture de paiement en ligne : la commande est créée avec `payment_method = COD` et
  `payment_status = pending` jusqu'à l'encaissement à la livraison.
- **Confirmation manuelle ou auto** : option d'appel/SMS de confirmation avant production pour réduire les
  commandes fantômes (problème classique du COD).
- **Anti-abus** : limite de commandes par téléphone/IP, blocklist de numéros, champ téléphone obligatoire
  et vérifié (format), honeypot anti-bot.
- **Frais de livraison** affichés clairement ; possibilité de « frais COD » additionnels paramétrables.

## 6. Tout est éditable (mapping CMS)

| Élément | Où c'est édité |
|---------|----------------|
| Familles, produits, options, dimensions, prix | Dashboard → Catalogue |
| Textes/images des pages, hero, FAQ, blocs | Dashboard → Pages / Contenu |
| Frais de livraison par wilaya/transporteur | Dashboard → Livraison |
| Devise, langue, logo, couleurs (thème) | Dashboard → Réglages / Apparence |
| SEO (meta, OG) par page et produit | Dashboard → SEO |
| Facebook Pixel ID, token API Conversions | Dashboard → Marketing |
| Emails transactionnels (modèles) | Dashboard → Notifications |
