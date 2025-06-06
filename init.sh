#!/bin/bash

set -e

docker compose build --build-arg UID=$(id -u) --build-arg GID=$(id -g) # --no-cache --pull
docker compose up -d --remove-orphans
docker compose exec php bash -c "./init.sh"