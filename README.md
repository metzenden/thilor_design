# THILOR DESIGN — Site e-commerce

Application e-commerce complète pour la vente de tenues africaines,
développée en Laravel 12. Front-office fidèle à la maquette fournie
(élégance africaine, accents dorés), back-office Filament pour gérer
catalogue, commandes, marketing et contenu.

## Stack technique

| Composant | Choix |
|---|---|
| Framework | Laravel 12 (PHP 8.3+, développé avec 8.4) |
| Base de données | MySQL/MariaDB en production, SQLite en développement local |
| Rendu | Blade + Tailwind CSS + Vite |
| Authentification | Laravel Breeze (sessions), Spatie Permission (rôles admin/gestionnaire/client) |
| Back-office | Filament v3 |
| Images | Intervention Image (redimensionnement + conversion WebP automatique) |
| Tests | PHPUnit (Laravel Test) |

Voir [`docs/PLAN.md`](docs/PLAN.md) pour le détail des décisions
d'architecture et des choix faits sur les points d'ambiguïté du cahier des
charges.

## Prérequis

- PHP 8.3+ avec extensions `pdo_mysql` (ou `pdo_sqlite` en local), `gd`,
  `intl`, `mbstring`, `curl`, `zip`, `fileinfo`, `xml`, `tokenizer`,
  `bcmath`.
- Composer 2.x
- Node.js 20+ et npm (uniquement pour compiler les assets — jamais requis
  en production, voir le guide de déploiement)
- MySQL/MariaDB (production) — SQLite suffit en développement local

## Installation locale

```bash
composer install
cp .env.example .env
php artisan key:generate

# En local, SQLite est la façon la plus rapide de démarrer (aucun serveur
# MySQL à configurer) : dans .env, remplacez temporairement
#   DB_CONNECTION=mysql
# par
#   DB_CONNECTION=sqlite
# et supprimez/commentez les lignes DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD
touch database/database.sqlite

php artisan migrate --seed   # crée le schéma + données de démonstration
php artisan storage:link     # expose storage/app/public sur /storage

npm install
npm run build                # ou `npm run dev` pendant le développement

php artisan serve
```

Le site est alors accessible sur `http://localhost:8000`, le back-office
sur `http://localhost:8000/admin`.

### Comptes de démonstration (après `--seed`)

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `admin@thilor-design.com` | `password` |
| Client | `fatou.diop@example.com` | `password` |

**Ne jamais conserver ces comptes de démonstration en production** — voir
la checklist de [`docs/DEPLOIEMENT_OVH.md`](docs/DEPLOIEMENT_OVH.md).

## Commandes de développement

```bash
php artisan serve              # serveur de développement
npm run dev                    # compilation Vite avec rechargement à chaud

php artisan test                              # suite de tests complète
php artisan test tests/Feature/Shop           # un dossier de tests
php artisan test --filter=CheckoutFlowTest    # un fichier précis

php artisan migrate:fresh --seed              # réinitialise la base avec les données de démo

php artisan tinker             # console interactive
```

## Commandes de production

```bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build

php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Voir le guide complet et la checklist de mise en production :
[`docs/DEPLOIEMENT_OVH.md`](docs/DEPLOIEMENT_OVH.md).

## Variables d'environnement principales

Toutes documentées et commentées dans [`.env.example`](.env.example).
Points d'attention :

- `APP_DEBUG` **doit** être à `false` en production.
- `APP_URL` doit correspondre au domaine réel, en `https://` une fois le
  SSL actif (nécessaire pour que `SESSION_SECURE_COOKIE=true` fonctionne).
- `DB_*` : identifiants de la base MySQL fournie par OVH.
- `MAIL_*` : SMTP pour les emails transactionnels (confirmation de compte,
  réinitialisation de mot de passe).

Aucun secret n'est stocké ailleurs que dans `.env` (jamais versionné).

## Architecture du code

```
app/
  Contracts/PaymentGatewayContract.php   # interface commune à tous les moyens de paiement
  Filament/                              # back-office (Resources, Widgets, Pages)
  Http/Controllers/                      # contrôleurs fins (front-office)
  Http/Requests/                         # validation (Form Requests)
  Models/                                # domaine Eloquent
  Policies/                              # autorisation (adresses, commandes)
  Services/                              # logique métier : CartService, OrderService,
                                          #   Payments\* (paiement à la livraison fonctionnel,
                                          #   carte/Wave/Orange Money en architecture prête,
                                          #   non branchés tant que les identifiants marchands
                                          #   ne sont pas fournis)
  Support/                               # ImageUploader (resize/WebP), PlaceholderImage (démo)
database/
  migrations/  factories/  seeders/
resources/views/
  components/layouts/shop.blade.php      # layout principal du front-office
  home.blade.php, catalog/, products/, cart/, checkout/, account/, ...
docs/
  PLAN.md                    # plan technique, backlog, décisions
  DEPLOIEMENT_OVH.md         # guide de déploiement pas à pas
  GUIDE_ADMINISTRATEUR.md    # guide d'utilisation du back-office
```

## Fonctionnalités livrées

- **Front-office** : accueil (bannières, catégories, nouveautés,
  promotions, meilleures ventes, collections, avis, newsletter), catalogue
  avec filtres (catégorie/taille/couleur/prix/disponibilité) et tri,
  recherche, fiche produit (galerie, variantes, avis, produits similaires),
  panier, checkout (informations → livraison → paiement → confirmation),
  compte client (tableau de bord, commandes, adresses, favoris, avis),
  contact, blog/pages de contenu, footer complet.
- **Back-office Filament** : dashboard (CA, commandes, stocks faibles),
  CRUD produits (images + variantes tailles/couleurs/stock), catégories,
  collections, commandes (statuts distincts commande/paiement), coupons,
  modes de livraison, avis (modération), bannières, pages/blog, messages
  de contact, newsletter, paramètres boutique/SEO, administrateurs et
  rôles, journal d'activité.
- **Paiement** : paiement à la livraison fonctionnel de bout en bout ;
  architecture extensible (`PaymentGatewayContract`) pour carte bancaire,
  Wave, Orange Money — non simulés tant que les accès marchands ne sont
  pas fournis par le client.
- **SEO** : slugs uniques, meta title/description dynamiques, canonical,
  sitemap.xml, robots.txt, Open Graph, Twitter Cards, Schema.org/Product,
  breadcrumbs, 404 personnalisée.
- **Sécurité** : CSRF, Form Requests, policies (isolation des données
  client), hashage des mots de passe, rate limiting sur les actions
  sensibles (connexion, inscription, contact, newsletter, coupon,
  paiement), upload d'images restreint et retraité (jamais le fichier
  d'origine stocké tel quel), journal d'activité administrateur.
- **Performance** : eager loading systématique, pagination serveur, index
  SQL sur les colonnes de recherche/relation, cache des paramètres
  boutique/navigation/sitemap, images compressées et converties en WebP.
- **Tests** : 56 tests automatisés couvrant inscription/connexion,
  catalogue/recherche, panier, calcul des prix et promotions, création de
  commande et gestion des stocks, permissions admin/gestionnaire/client,
  isolation des données client, validations et cas d'erreur.

## Limites connues / à prévoir avec le client

- **Paiement carte bancaire / Wave / Orange Money** : l'architecture est
  prête (`app/Services/Payments/`) mais nécessite les identifiants
  marchands et contrats avec ces prestataires, non fournis à ce stade.
- **Images de démonstration** : les seeders génèrent des visuels de
  substitution colorés (pas de vraies photos produits) — à remplacer par
  le vrai catalogue avant mise en production.
- **Emails transactionnels** : fonctionnels dès qu'un SMTP est configuré
  (voir `.env`), non testés avec un prestataire mail réel dans cet
  environnement de développement.
- **Hébergement mutualisé OVH** : pas de file d'attente asynchrone
  persistante ni de WebSocket — toutes les opérations (dont la création de
  commande) sont volontairement synchrones pour rester compatible.
