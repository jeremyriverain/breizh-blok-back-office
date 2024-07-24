#!/bin/sh

set -e

# installe les dépendances PHP de l'application Symfony
composer install

composer run fixtures

# installation des dépendances JS de l'application Symfony
npm install

# compilation du JS de l'application
npm run build