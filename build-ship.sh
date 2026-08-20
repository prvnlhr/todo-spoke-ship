#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"

echo "============================================"
echo " Build Todo ship package"
echo "============================================"
echo

if ! command -v docker >/dev/null 2>&1; then
  echo "Docker is not available. Start Docker and retry."
  exit 1
fi

if ! docker info >/dev/null 2>&1; then
  echo "Docker engine is not running. Start Docker and retry."
  exit 1
fi

if [[ ! -f .env ]]; then
  cp .env.example .env
fi
mkdir -p ship/images
if [[ ! -f ship/.env ]]; then
  cp ship/.env.example ship/.env
fi

chmod +x ship/install.sh ship/update.sh ship/uninstall.sh ship/install.command 2>/dev/null || true

echo "[1/4] Building app image todo-app:1.0 ..."
docker compose build app

echo
echo "[2/4] Ensuring mysql:8.0 is available..."
if ! docker image inspect mysql:8.0 >/dev/null 2>&1; then
  docker pull mysql:8.0
fi

echo
echo "[3/4] Saving images to ship/images/ ..."
docker save todo-app:1.0 -o ship/images/app.tar
docker save mysql:8.0 -o ship/images/mysql.tar

echo
echo "[4/4] Ship folder ready."
echo
echo "============================================"
echo " Give the client the ship/ folder as a zip or USB."
echo "============================================"
