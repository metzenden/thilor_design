# Déploiement sur serveur Ubuntu (Nginx + PHP-FPM) — serveur de test

Guide pour lancer THILOR DESIGN sur un serveur Ubuntu que vous administrez
vous-même (accès root/sudo), avec Nginx et PHP-FPM. Toutes les commandes
sont à exécuter **sur le serveur Ubuntu**, en SSH.

## 1. Paquets système

```bash
sudo apt update
sudo apt install -y nginx mysql-server \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath \
  git unzip curl
```

(Si `php8.3` n'est pas disponible directement : `sudo apt install -y software-properties-common && sudo add-apt-repository ppa:ondrej/php && sudo apt update` puis relancez la commande ci-dessus.)

**Composer** (si absent) :
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**Node.js** (uniquement pour compiler les assets — pas nécessaire en
permanence, voir étape 5) :
```bash
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
```

## 2. Récupérer le projet

```bash
sudo mkdir -p /var/www/thilor-design
sudo chown $USER:$USER /var/www/thilor-design
git clone https://github.com/metzenden/thilor_design.git /var/www/thilor-design
cd /var/www/thilor-design
```

## 3. Base de données MySQL

```bash
sudo mysql -e "CREATE DATABASE thilor_design CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'thilor'@'localhost' IDENTIFIED BY 'CHANGEZ-CE-MOT-DE-PASSE';"
sudo mysql -e "GRANT ALL PRIVILEGES ON thilor_design.* TO 'thilor'@'localhost'; FLUSH PRIVILEGES;"
```

## 4. Application

```bash
composer install --no-dev --optimize-autoloader --no-interaction

cp .env.example .env
php artisan key:generate --force
```

Éditez `.env` (`nano .env`) et renseignez :
```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://51.91.58.139:5000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=thilor_design
DB_USERNAME=thilor
DB_PASSWORD=CHANGEZ-CE-MOT-DE-PASSE
```
(`APP_URL` doit correspondre exactement à l'adresse que vous taperez dans
le navigateur pour accéder au site, sinon les images ne s'afficheront pas
— voir l'incident précédent sur ce point.)

```bash
php artisan migrate --seed   # schéma + données de démonstration
php artisan storage:link
```

## 5. Assets front-end

```bash
npm install
npm run build
```

Une fois `public/build/` généré, Node n'est plus nécessaire en
fonctionnement (aucun process Node ne tourne en continu).

## 6. Permissions

```bash
sudo chown -R www-data:www-data /var/www/thilor-design
sudo chmod -R 775 storage bootstrap/cache
```

## 7. Configuration Nginx

```bash
sudo nano /etc/nginx/sites-available/thilor-design
```

Contenu :

```nginx
server {
    listen 5000;
    server_name 51.91.58.139;
    root /var/www/thilor-design/public;

    index index.php;

    add_header X-Content-Type-Options "nosniff";
    add_header X-Frame-Options "SAMEORIGIN";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;   # pour l'upload d'images produits
}
```

Activez le site :

```bash
sudo ln -s /etc/nginx/sites-available/thilor-design /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default   # évite un conflit avec le site par défaut
sudo nginx -t                                  # vérifie la config
sudo systemctl reload nginx
sudo systemctl restart php8.3-fpm
```

**Ouvrez le port 5000 dans le pare-feu** — le port 5000 n'est pas un port
web standard (80/443), il est très probablement bloqué par défaut :

```bash
sudo ufw allow 5000/tcp   # si ufw est actif (sudo ufw status pour vérifier)
```

Si le serveur est chez un hébergeur cloud (OVH, Scaleway...), vérifiez
**aussi** le pare-feu réseau/security group depuis leur espace client
(souvent appelé "Firewall réseau" ou "Security Group") — il bloque le
trafic entrant **avant** même d'atteindre `ufw`, une règle `ufw` seule ne
suffit pas toujours sur ce type d'offre.

## 8. C'est en ligne

Ouvrez **`http://51.91.58.139:5000`** dans votre navigateur.

- Site : `http://51.91.58.139:5000`
- Back-office : `http://51.91.58.139:5000/admin`
  (`admin@thilor-design.com` / `password`)

## Mettre à jour le site après un nouveau `git pull`

```bash
cd /var/www/thilor-design
git pull
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:clear && php artisan config:cache
sudo systemctl restart php8.3-fpm
```

## Problèmes courants

- **Page blanche / erreur 500** : `tail -f storage/logs/laravel.log` pour
  voir l'erreur exacte ; vérifiez aussi `sudo tail -f /var/log/nginx/error.log`.
- **Images qui ne s'affichent pas** : `APP_URL` dans `.env` ne correspond
  pas à l'adresse tapée dans le navigateur, ou `storage:link` non exécuté
  — voir étape 4.
- **Permission denied sur storage/** : relancez l'étape 6
  (`chown`/`chmod`).
- **Ce serveur n'est qu'un serveur de test** (pas encore de vrai nom de
  domaine ni de SSL) : c'est normal de rester en `http://` et
  `APP_ENV=local` / `APP_DEBUG=true` pour le moment. Voir
  `docs/DEPLOIEMENT_OVH.md` §4 et la checklist §14 le jour où ce serveur
  doit devenir la vraie production (domaine, HTTPS, `APP_DEBUG=false`,
  `SESSION_SECURE_COOKIE=true`).
