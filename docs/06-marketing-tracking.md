# 06 — Marketing : Facebook Pixel, Conversions API & liens d'achat pour les pubs

C'est un objectif explicite : pouvoir **connecter Facebook Pixel** et **faire des publicités avec des liens
d'achat** pointant vers le site. Voici comment c'est prévu.

## 1. Facebook Pixel (côté navigateur)

- **Pixel ID éditable** dans Dashboard → Marketing (pas de modification de code requise).
- Chargement conditionné au **consentement cookies** (RGPD).
- Événements standard envoyés automatiquement le long du funnel :

| Étape du funnel | Événement Pixel |
|-----------------|-----------------|
| Visite page produit | `ViewContent` |
| Lancement du configurateur | `CustomizeProduct` (custom) |
| Ajout au panier / passage au checkout | `AddToCart` / `InitiateCheckout` |
| Saisie des coordonnées | `AddPaymentInfo` (adapté COD) |
| Commande confirmée (COD) | `Purchase` **ou** `Lead` selon stratégie |

> En COD, beaucoup d'annonceurs comptent la **commande passée** comme `Purchase` (avec `value` = montant et
> `currency` = DZD). On rendra ce mapping **configurable** (`Purchase` vs `Lead`) selon ta préférence
> d'optimisation des campagnes.

## 2. API de Conversions (côté serveur) — recommandé

En complément du Pixel navigateur, l'**API Conversions (CAPI)** envoie les événements depuis le serveur
Laravel. Avantages décisifs pour le COD :
- Fiabilité (non bloquée par adblock / iOS / refus cookies).
- On peut envoyer un `Purchase` **réel** au moment de la **livraison encaissée**, pas seulement à la
  commande → meilleure qualité de signal pour l'optimisation des pubs.
- **Déduplication** Pixel ↔ CAPI via `event_id`.

Réglages dans le dashboard : token d'accès, dataset/pixel, hash des données client (téléphone/email) pour
le matching, activation par événement.

## 3. Liens d'achat pour les publicités (deep-links traçables)

Pour lancer des pubs « avec lien d'achat », le site fournira des **URLs de campagne** qui amènent
directement au bon endroit du funnel et qui sont **traçables**.

- **Deep-link configurateur** : un lien peut **pré-sélectionner** produit + dimension (+ option design),
  ex. `bepack.dz/c/mailer-ecommerce?dim=25x20x10&qty=250&utm_source=facebook&utm_campaign=mailer_promo`.
  → l'utilisateur arrive directement sur le configurateur pré-rempli, prêt à commander.
- **Landing pages dédiées** par campagne (éditables dans le CMS) avec un CTA unique.
- **Paramètres UTM** capturés et **stockés sur la commande** → on sait quelle pub a généré quelle vente COD
  (reporting par source dans le dashboard).
- **Générateur de liens** dans Dashboard → Marketing : on choisit produit/dimension/quantité + UTM, et le
  back-office produit le lien prêt à coller dans le gestionnaire de pubs Meta.

## 4. Catalogue Meta (pour les pubs dynamiques / boutique)

- Export du **flux catalogue** (CSV/XML) compatible Meta : id, titre, description, prix, image, lien,
  disponibilité. Mis à jour automatiquement.
- Permet les **publicités dynamiques** (DPA) et le tag des produits, avec les liens pointant vers le
  configurateur.

## 5. Autres canaux (prévus, non bloquants)

- **TikTok Pixel / Google Tag** : même mécanique (IDs éditables) — extensible.
- **WhatsApp** : bouton de contact + notifications de commande (très utile en COD local).
- **Reciblage** via audiences personnalisées basées sur les événements ci-dessus.

## 6. Conformité
- Bandeau de consentement cookies (le Pixel ne se charge qu'après acceptation).
- Page Politique de confidentialité éditable mentionnant le tracking.
- Hash (SHA-256) des données personnelles avant envoi à la CAPI.
