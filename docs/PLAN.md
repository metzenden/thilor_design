# THILOR DESIGN — Plan technique & backlog

## 1. Références
- Cahier des charges : `PROMPT_CLAUDE_CODE_THILOR_DESIGN.pdf` (fourni par le client).
- Maquette visuelle : écrans 01 Accueil, 02 Catalogue, 03 Fiche produit, 04 Panier,
  05 Checkout (infos/livraison), 06 Checkout (paiement), 07 Confirmation, 08 Mon compte.
  Identité d'origine (approximée depuis la maquette, avant réception de la charte) : fond
  blanc cassé, accents dorés (#C89B3C-ish), bordeaux, logo "TD" circulaire.
  **Mise à jour** : la charte de marque officielle THILOR DESIGN a depuis été fournie par
  le client et appliquée à la place de cette approximation — voir section 10.

## 2. Stack retenue
- Laravel 12 (dernière stable), PHP 8.4 (disponible ; documenté comme "8.3+ recommandé,
  8.4 utilisé ici" pour rester conforme à la contrainte OVH 8.3+).
- MySQL en production (OVH). **SQLite en développement local** dans cet environnement
  (pas de serveur MySQL disponible ici) — le schéma reste 100% portable (migrations
  Eloquent standards, pas de fonctions spécifiques SQLite). Documenté dans le README.
- Blade + Laravel Breeze (stack Blade, sessions) pour l'authentification front (client).
- Tailwind CSS + Vite pour les assets, compilés avant transfert (pas de Node en prod).
- Filament v3/v4 pour le back-office admin (panel séparé `/admin`).
- Pas de Docker, pas de queue worker permanent obligatoire (jobs synchrones ou queue
  "database" traitée via une tâche ponctuelle si besoin — pas de process permanent).

## 3. Rôles
- `admin` : accès total back-office Filament.
- `manager` (optionnel, via permissions) : gestion catalogue/commandes sans paramètres
  sensibles — implémenté via Spatie permission si le temps le permet, sinon rôle simple
  `is_admin` + policies (choix documenté si simplifié).
- `client` : compte front-office (inscription/connexion), accès à ses propres données
  uniquement (policies).

## 4. Entités principales (voir migrations)
users, addresses, categories, collections, products, product_images, product_variants
(taille/couleur + stock par variante), orders, order_items (prix historisé), payments,
shipping_methods, coupons, wishlists, reviews, banners, pages, newsletter_subscribers,
contact_messages, activity logs (admin).

Règles :
- Stock géré au niveau `product_variants.stock` (pas seulement au produit).
- `order_items.unit_price` et `product_name`/`variant_label` historisés au moment de la
  commande (jamais recalculés depuis le produit courant).
- Suppression protégée : `products`/`orders` en soft delete ; un produit référencé par une
  commande n'est jamais supprimé physiquement.
- Index sur slugs, foreign keys, `orders.status`, `products.category_id`, recherche.

## 5. Catalogue / navigation
La maquette (filtres catalogue, bloc "Catégories populaires") montre Femme, Homme, Enfant,
Haute couture et Accessoires comme des **catégories** au même niveau (c'est le filtre
"Catégories" du panneau de filtres). Décision retenue : 5 catégories plates —
Femme, Homme, Enfant, Haute couture, Accessoires. "Prêt-à-porter" (cité au §5 du cahier des
charges mais absent de la maquette) est modélisé comme **collection** transverse au même
titre que d'autres mises en avant marketing (ex. "Nouveautés", "Édition Mariage") — un
produit peut appartenir à une catégorie ET à une ou plusieurs collections. Navigation
principale : Accueil, Femme, Homme, Enfant, Collections, Promotions, Blog, Contact —
"Promotions" = vue filtrée `on_sale=true`, "Blog" = `pages` de type article (liste + détail),
sans moteur de blog complexe (hors périmètre du cahier des charges).

## 6. Paiement
Interface `PaymentGatewayContract` avec implémentation `CashOnDeliveryGateway`
(fonctionnelle) et stubs `StripeGateway`/`WaveGateway`/`OrangeMoneyGateway` non branchés
(nécessitent des identifiants marchands non fournis) — clairement marqués TODO, jamais
un paiement simulé comme "payé". Statuts `payments.status` :
pending/paid/failed/cancelled/refunded.

## 7. Décisions par défaut (ambiguïtés non bloquantes)
- Devise : FCFA (XOF), pas de decimales affichées, stocké en entier (plus petite unité).
- Pays de livraison par défaut : Sénégal, adresse avec ville/quartier/téléphone.
- Emails transactionnels : Laravel Mail avec driver `log` par défaut en dev, SMTP en prod
  (documenté dans le guide OVH).
- Panier : stocké en session pour invités, fusionné en base (`carts`/`cart_items`) à la
  connexion pour les clients identifiés.

## 8. Phases de livraison
Suit les phases 1 à 9 du cahier des charges. Chaque phase = commit(s) dédié(s).

## 9. Compatibilité OVH mutualisé
- Pas de VPS/Docker/K8s/Node permanent : validé, Laravel + PHP-FPM classique + build Vite
  livré en assets statiques compilés (`npm run build` avant transfert, jamais en prod).
- Filament fonctionne en PHP pur côté serveur (Livewire), compatible mutualisé.
- Cron uniquement si le scheduler Laravel est utilisé (à activer seulement si nécessaire
  côté back-office : ex. désactivation auto de promotions expirées) — documenté comme
  optionnel dans le guide de déploiement.

## 10. Charte de marque officielle (appliquée en remplacement de la maquette)
- Source : « Charte de marque THILOR DESIGN » (PDF fourni par le client) + 5 fichiers
  logo PNG réels (wordmark complet, « THILOR » seul, monogramme « THR » — variantes
  bleu nuit et chocolat), déposés dans `public/images/brand/`.
- Couleurs officielles : bleu nuit `#101C2C`, chocolat `#3A2A22`, beige sable `#D8C7AD`,
  crème `#F4F0E8`. Appliquées dans `tailwind.config.js` (tokens `ink`, `gold`, `sand`,
  `cream` — noms de tokens conservés pour limiter le diff, mais valeurs 100% officielles).
- Typographies officielles : Montserrat (titres) et Merriweather Sans (texte courant),
  chargées via Google Fonts dans `resources/css/app.css`.
- La charte ne définit pas de couleur d'alerte/promo. Le token `bordeaux` (utilisé pour
  les badges de réduction et les actions de suppression) a été recoloré en un terracotta
  sourd (`#8C3B2E`) choisi pour rester dans l'esprit chaud de la palette officielle,
  plutôt que d'introduire une couleur hors charte — décision assumée, à ajuster si le
  client préfère une autre teinte.
- Logo : le header et le footer utilisent désormais l'image réelle du wordmark
  (`thilor-design-navy.png`) à la place de l'icône SVG recréée précédemment (supprimée).
- Point ouvert non résolu (à trancher avec le client) : la charte mentionne le domaine
  `www.thilor.com` et une adresse email non finalisée (`XXXX@thilor.com`), alors que les
  seeders/docs utilisent encore `thilor-design.com`. Aucune valeur n'a été inventée pour
  l'email de contact public — à confirmer avant mise en production.
