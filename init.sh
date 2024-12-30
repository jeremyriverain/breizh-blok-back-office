#!/bin/bash

set -e

# Construit les images Docker listées dans le fichier ./docker compose.yml
docker compose build --build-arg UID=$(id -u) --build-arg GID=$(id -g) # --no-cache --pull

# Lance les conteneurs de l'application
docker compose up -d --remove-orphans

# Exécute le script d'initialisation du conteneur php (./symfony/init.sh)
docker compose exec php bash -c "./init.sh"