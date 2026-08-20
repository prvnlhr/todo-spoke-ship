#!/usr/bin/env bash
set -euo pipefail

SHIP_DIR="$(cd "$(dirname "$0")" && pwd)"
INSTALL_DIR="${HOME}/TodoApp"

compose() {
  if docker compose version >/dev/null 2>&1; then
    docker compose "$@"
  elif command -v docker-compose >/dev/null 2>&1; then
    docker-compose "$@"
  else
    echo "docker compose is not available."
    exit 1
  fi
}

if ! docker info >/dev/null 2>&1; then
  echo "Start Docker, then run this updater again."
  exit 1
fi

if [[ ! -d "${INSTALL_DIR}" ]]; then
  echo "Todo App is not installed. Run install.sh first."
  exit 1
fi

if [[ ! -f "${SHIP_DIR}/images/app.tar" ]]; then
  echo "Missing images/app.tar"
  exit 1
fi

echo "Loading new images..."
docker load -i "${SHIP_DIR}/images/app.tar"
if [[ -f "${SHIP_DIR}/images/mysql.tar" ]]; then
  docker load -i "${SHIP_DIR}/images/mysql.tar"
fi

cp "${SHIP_DIR}/docker-compose.yml" "${INSTALL_DIR}/docker-compose.yml"
cd "${INSTALL_DIR}"
compose -p todoapp up -d --force-recreate

if command -v open >/dev/null 2>&1; then
  open "http://localhost:8080"
elif command -v xdg-open >/dev/null 2>&1; then
  xdg-open "http://localhost:8080" >/dev/null 2>&1 || true
fi

echo "Update complete. http://localhost:8080"
