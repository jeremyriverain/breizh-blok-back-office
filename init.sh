#!/bin/bash

set -e

# Construit les images Docker listées dans le fichier ./docker compose.yml
docker compose build --build-arg UID=$(id -u) --build-arg GID=$(id -g) # --no-cache --pull

# Lance les conteneurs de l'application
docker compose up -d

# Démarre l'API permettant de sauvegarder et charger la base de données à la volée durant les tests E2E.
docker compose exec db bash -c "npm run start"

# Exécute le script d'initialisation du conteneur php (./symfony/init.sh)
docker compose exec php bash -c "./init.sh"