#!/usr/bin/env bash
# Run on the VPS to pull the latest code and rebuild the containers.
set -euo pipefail
cd "$(dirname "$0")"

git pull --ff-only
# Pull newer builds of the base images (PHP, Caddy) so OS and security patches land on every deploy
docker compose -f docker-compose.prod.yml pull --ignore-buildable
docker compose -f docker-compose.prod.yml build --pull
docker compose -f docker-compose.prod.yml up -d
docker image prune -f
