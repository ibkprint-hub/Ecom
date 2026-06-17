# 07 — Livraison COD & livrable ZIP / installation

## 1. Livraison & COD

Le COD (paiement à la livraison) implique une **logistique de livraison** soignée.

- **Zones de livraison** par **wilaya / région**, avec tarif et délai paramétrables.
- **Modes** : livraison à **domicile** et/ou **point relais / bureau**, prix distincts possibles.
- **Frais COD** additionnels optionnels (couvrir les frais de gestion de l'encaissement).
- **Transporteurs** : champ générique configurable. Possibilité (phase 2) d'intégrer des API de
  transporteurs COD locaux (ex. Yalidine, ZR Express, Maystro… selon ton choix) pour :
  - créer le bordereau automatiquement,
  - récupérer le suivi,
  - réconcilier l'encaissement.
- **Bordereaux & bons** : génération PDF imprimable depuis la fiche commande.

> ❗ Décision à valider : pays/marché cible et transporteur(s). Le MVP fonctionne avec une **grille manuelle
> par wilaya** ; l'intégration API transporteur est une option de phase 2.

## 2. Livrable : archive ZIP installable sur mutualisé

Le projet sera livré sous forme d'un **ZIP autonome** :

```
bepack.zip
├── (code Laravel complet)
├── vendor/                 ← dépendances déjà installées (pas de Composer requis en prod)
├── public/build/           ← assets front compilés (pas de build Node requis en prod)
├── database/               ← migrations + seeders (données de démo)
├── install/                ← assistant d'installation web
├── .env.example
├── INSTALL.md              ← guide pas-à-pas (FR)
└── README.md
```

### Procédure d'installation (résumé)
1. Créer une base MySQL + un utilisateur dans cPanel.
2. Uploader/dézipper le ZIP (idéalement faire pointer le domaine/sous-domaine sur `public/`).
3. Ouvrir `https://ledomaine/install` → l'assistant :
   - vérifie PHP/extensions,
   - configure la BDD (test de connexion),
   - lance migrations + données de démo,
   - crée l'admin,
   - écrit `.env` + génère la clé,
   - configure SMTP, devise, langue, Pixel ID,
   - se verrouille.
4. Ajouter **1 cron** cPanel : `* * * * * php /chemin/artisan schedule:run` (planification, queues, sitemaps,
   sauvegardes).
5. Activer HTTPS (Let's Encrypt).

### Mises à jour
- Procédure de mise à jour documentée (remplacement de fichiers + `php artisan migrate` via l'installateur
  ou une page admin protégée), pour éviter d'avoir besoin de SSH.

## 3. Sauvegardes
- Sauvegarde planifiée BDD + médias (`spatie/laravel-backup`), téléchargeable depuis le dashboard
  ou envoyée vers un stockage distant (optionnel).
