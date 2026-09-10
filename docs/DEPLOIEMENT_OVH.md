# Guide de déploiement — Hébergement Web OVH mutualisé

Ce guide décrit le déploiement de THILOR DESIGN sur un **Hébergement Web OVH
mutualisé** (offre Perso/Pro/Performance). Aucune étape ne nécessite d'accès
root, de Docker ni de processus Node.js permanent : le front-end est compilé
**avant** le transfert, et seul PHP-FPM + MySQL tournent côté serveur.

## 0. Prérequis côté OVH

- Un hébergement Web OVH avec **PHP 8.3 ou supérieur** (l'application est
  développée avec PHP 8.4 ; vérifiez la version la plus récente proposée par
  OVH dans l'espace client, section *Hébergements > [votre site] > PHP* — à
  défaut de 8.4, 8.3 fonctionne à l'identique, aucune fonctionnalité de ce
  projet ne dépend de 8.4 spécifiquement).
- Une base **MySQL/MariaDB** OVH (créée à l'étape 2).
- Un accès **SSH** (offert sur la plupart des offres OVH) pour lancer
  Composer/Artisan — à défaut, les commandes équivalentes peuvent être
  passées par le gestionnaire de fichiers + un script `deploy.php` temporaire
  (voir note en fin de document).
- Un nom de domaine pointé sur l'hébergement (étape 4).

## 1. Version PHP

Dans l'espace client OVH → *Hébergements* → votre site → onglet **PHP** :
sélectionnez la version **8.3** ou supérieure disponible, avec le mode
**PHP-FPM** (pas de CGI). Activez au minimum les extensions : `pdo_mysql`,
`mbstring`, `gd` (ou `imagick`), `curl`, `zip`, `intl`, `fileinfo`,
`xml`, `tokenizer`, `bcmath`. Ces extensions sont **activées par défaut**
sur les hébergements OVH récents ; vérifiez simplement `gd` et `intl`
(utilisées pour le traitement d'images produits).

## 2. Création et configuration de la base MySQL OVH

1. Espace client OVH → *Bases de données* → **Créer une base SQL** (type
   MySQL, région proche de vos clients).
2. Notez les identifiants générés : **serveur**, **port**, **nom de base**,
   **utilisateur**, **mot de passe**.
3. Reportez-les dans `.env` (voir étape 3) :
   `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
4. Le jeu de caractères doit être `utf8mb4` (défaut OVH) pour supporter les
   emojis/caractères spéciaux dans les descriptions produits.

## 3. Fichier `.env`

1. Copiez `.env.example` en `.env` sur le serveur (jamais versionné, jamais
   transféré tel quel depuis votre poste si vous y avez mis des secrets de
   dev).
2. Générez une clé applicative **une seule fois**, en local ou en SSH sur
   OVH : `php artisan key:generate --force`. Ne jamais réutiliser une clé
   d'un autre environnement.
3. Renseignez au minimum :
   - `APP_ENV=production`
   - `APP_DEBUG=false` (**obligatoire** — ne jamais activer en production,
     une page d'erreur détaillée exposerait votre code et vos secrets)
   - `APP_URL=https://www.votredomaine.com`
   - `DB_*` (voir étape 2)
   - `SESSION_SECURE_COOKIE=true` (dès que le HTTPS est actif, étape 4)
   - `MAIL_*` (voir étape 11)
4. Tous les secrets (clé d'application, mots de passe DB/mail, futures clés
   API de paiement) vivent **uniquement** dans ce `.env`, jamais dans le
   code versionné.

## 4. Domaine et SSL

1. Espace client OVH → *Domaines* → associez votre nom de domaine à
   l'hébergement (ou utilisez le sous-domaine `xxx.cluster0XX.hosting.ovh.net`
   fourni par défaut pour les tests).
2. Espace client OVH → *Hébergements* → onglet **SSL** → activez le
   certificat **Let's Encrypt gratuit** (automatique sur les hébergements
   OVH mutualisés récents). Attendez la propagation (quelques minutes à
   quelques heures).
3. Une fois le HTTPS actif, vérifiez que `APP_URL` utilise bien `https://`
   et que `SESSION_SECURE_COOKIE=true` — sinon les cookies de session ne
   seront pas envoyés correctement et les utilisateurs seront déconnectés
   en boucle.

## 5. Racine web vers `public/`

Un hébergement OVH mutualisé sert le contenu du dossier configuré comme
racine du site (souvent `www/`). Deux approches possibles :

- **Recommandé** : dans l'espace client OVH → *Hébergements* → *Multisite*,
  configurez le sous-domaine/domaine pour pointer directement vers le
  sous-dossier `www/public` de votre arborescence (là où vous aurez
  transféré le contenu du dossier `public/` du projet).
- **Alternative** (si le multisite ne permet pas de choisir un sous-dossier
  arbitraire) : placez tout le contenu de `public/` à la racine `www/`, et
  le reste du projet (`app/`, `bootstrap/`, `config/`, etc.) **hors** de
  `www/`, dans un dossier parent non accessible publiquement (ex.
  `thilor-design-app/`). Adaptez alors les chemins `require` dans
  `www/index.php` (`__DIR__.'/../thilor-design-app/vendor/autoload.php'`
  et `__DIR__.'/../thilor-design-app/bootstrap/app.php'`).

Dans les deux cas, **seul le contenu de `public/`** doit être exposé
publiquement — jamais `app/`, `.env`, `storage/`, `vendor/`.

## 6. Dépendances Composer

En SSH, dans le dossier du projet sur le serveur :

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

- `--no-dev` exclut les paquets de développement (PHPUnit, Pint...).
- `--optimize-autoloader` génère un autoloader optimisé (meilleure
  performance).
- Si Composer n'est pas disponible en SSH sur votre offre, exécutez cette
  commande **en local**, puis transférez le dossier `vendor/` complet par
  FTP/SFTP (plus lent mais fonctionne partout).

## 7. Compilation Vite avant transfert

**Toujours en local** (jamais sur OVH, qui ne doit pas faire tourner de
process Node.js) :

```bash
npm ci
npm run build
```

Cela génère `public/build/` (assets CSS/JS minifiés et versionnés). C'est
**ce dossier compilé** qu'il faut transférer vers OVH avec le reste de
`public/` — le dossier `node_modules/` et les sources non compilées
(`resources/js`, `resources/css`) n'ont pas besoin d'être présents en
production (mais peuvent l'être sans risque, ils ne sont jamais exécutés
côté serveur).

## 8. Permissions `storage/` et `bootstrap/cache`

Laravel doit pouvoir écrire dans ces dossiers (logs, cache compilé, fichiers
uploadés) :

```bash
chmod -R 775 storage bootstrap/cache
```

Sur OVH mutualisé, le PHP tourne sous votre utilisateur FTP : `775` (ou
`755` si `775` est refusé par votre configuration) suffit largement, **`777`
n'est jamais nécessaire** et affaiblit la sécurité inutilement.

## 9. Lien symbolique `storage`

Les images uploadées (produits, catégories, bannières...) sont stockées
dans `storage/app/public` et exposées via `public/storage`. Créez le lien :

```bash
php artisan storage:link
```

Si la commande échoue (certains hébergements mutualisés bloquent les liens
symboliques), créez manuellement un dossier `public/storage` et copiez-y le
contenu de `storage/app/public` après chaque déploiement, ou contactez le
support OVH pour activer le support des liens symboliques sur votre offre.

## 10. Cache Laravel

Une fois le `.env` en place et la base migrée (voir plus bas), optimisez :

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

**Important** : après **toute** modification du `.env` ou des routes en
production, relancez `php artisan config:cache` (et `route:cache`) sinon
Laravel continuera de servir l'ancienne configuration mise en cache. Pour
tout annuler temporairement (débogage) : `php artisan optimize:clear`.

## 11. Configuration mail

Dans `.env` :

```
MAIL_MAILER=smtp
MAIL_HOST=<serveur SMTP fourni par OVH ou votre prestataire>
MAIL_PORT=587
MAIL_USERNAME=<adresse complète>
MAIL_PASSWORD=<mot de passe de la boîte>
MAIL_SCHEME=tls
MAIL_FROM_ADDRESS=contact@votredomaine.com
MAIL_FROM_NAME="THILOR DESIGN"
```

Les hébergements mail OVH utilisent typiquement `ssl0.ovh.net` (port 587,
TLS) — vérifiez la valeur exacte dans l'espace client OVH, section
*Emails*. Testez l'envoi via `php artisan tinker` :
`Mail::raw('test', fn($m) => $m->to('vous@exemple.com')->subject('Test'));`

## 12. Tâche planifiée (cron) — uniquement si nécessaire

Ce projet **ne dépend d'aucune tâche cron pour fonctionner** (pas de file
d'attente asynchrone obligatoire, pas de notifications différées). Un cron
n'est utile que pour des automatisations futures optionnelles, par exemple
désactiver automatiquement les coupons/bannières expirés au lieu de
s'appuyer sur le filtrage `is_active`/`ends_at` déjà en place côté requêtes.

Si vous activez un jour le planificateur Laravel, ajoutez dans l'espace
client OVH → *Tâches CRON* :

```
* * * * * php /chemin/vers/le/projet/artisan schedule:run >> /dev/null 2>&1
```

(Vérifiez que votre offre OVH autorise une fréquence d'1 minute ; sinon,
utilisez la fréquence minimale autorisée.)

## 13. Rollback

En cas de problème après un déploiement :

1. **Base de données** : restaurez la dernière sauvegarde (voir § 14) via
   phpMyAdmin (espace client OVH → *Bases de données* → *phpMyAdmin*) ou
   `mysql -h HOST -u USER -p BASE < sauvegarde.sql`.
2. **Code** : conservez toujours l'archive du déploiement précédent
   (`tar czf backup-YYYYMMDD.tar.gz public/ app/ ...` avant chaque mise à
   jour) pour pouvoir la retransférer en quelques minutes.
3. **Cache** : après tout rollback de code, relancez
   `php artisan optimize:clear` puis `php artisan config:cache` pour éviter
   de servir un cache de configuration incohérent avec l'ancien code.
4. Recommandé : versionner le projet avec Git et taguer chaque déploiement
   (`git tag v1.2.0`) pour pouvoir `git checkout` une version précédente
   avant de retransférer.

## 14. Checklist de production

Avant de considérer le site en ligne :

- [ ] `APP_ENV=production` et `APP_DEBUG=false`
- [ ] `APP_KEY` généré et unique à cet environnement
- [ ] HTTPS actif, `APP_URL` en `https://`, `SESSION_SECURE_COOKIE=true`
- [ ] Base MySQL migrée : `php artisan migrate --force`
- [ ] Données de démo **remplacées** par le vrai catalogue (ne pas garder
      les produits/clients de test en production — ne PAS lancer
      `db:seed` en production, sauf `RolesAndPermissionsSeeder` pour créer
      le premier compte admin, puis changez immédiatement son mot de passe)
- [ ] `storage:link` exécuté, upload d'une image test validé
- [ ] `composer install --no-dev --optimize-autoloader` exécuté
- [ ] Assets Vite compilés (`npm run build`) et transférés
- [ ] `config:cache`, `route:cache`, `view:cache` exécutés
- [ ] Permissions `storage/` et `bootstrap/cache` en écriture (775)
- [ ] Email transactionnel testé (mot de passe oublié, confirmation compte)
- [ ] Sauvegarde automatique de la base activée (OVH propose des sauvegardes
      automatiques sur certaines offres ; à défaut, planifiez un export
      `mysqldump` régulier via un script téléchargé manuellement ou un cron
      si disponible sur votre offre)
- [ ] Certificat SSL valide (cadenas navigateur, pas d'avertissement)
- [ ] `robots.txt` et `/sitemap.xml` accessibles et corrects
- [ ] Test de bout en bout : parcourir le catalogue, ajouter au panier,
      passer une commande réelle en paiement à la livraison, vérifier sa
      réception dans le back-office `/admin`
- [ ] Compte administrateur de démonstration (`admin@thilor-design.com` /
      `password`) **supprimé ou mot de passe changé** avant mise en ligne

---

### Note — déploiement sans accès SSH

Si votre offre OVH ne propose pas SSH : exécutez `composer install`,
`npm run build`, `php artisan migrate`, etc. **en local** (ou dans cet
environnement de développement), puis transférez l'intégralité du dossier
du projet (avec `vendor/` et `public/build/` déjà générés) par FTP/SFTP.
Les seules commandes qui doivent alors tourner sur OVH sont celles
nécessitant l'accès à la base de production réelle (`migrate --force`,
`storage:link`) — la plupart des hébergements OVH proposent un accès SSH
même sur les offres mutualisées de base ; vérifiez dans l'espace client
avant d'exclure cette option.
