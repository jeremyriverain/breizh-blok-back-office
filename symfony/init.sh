#!/bin/sh

set -e

# installe les dépendances PHP de l'application Symfony
composer install

# créer certains dossiers et règle les permissions de ceux-ci
mkdir -p public/uploads && chmod -R 0777 public/uploads && echo "public/uploads folder writable"
mkdir -p public/media/cache && chmod -R 0777 public/media/cache && echo "public/media/cache folder writable"

# création de la base de données
symfony console doctrine:database:drop --force --if-exists
symfony console doctrine:database:create --if-not-exists
symfony console doctrine:migration:migrate --no-interaction

# injection des données de départ
symfony console doctrine:fixtures:load --no-interaction

# installation des dépendances JS de l'application Symfony
npm install

# compilation du JS de l'application
npm run build