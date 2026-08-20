#!/usr/bin/env bash
set -euo pipefail

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

echo "This stops the app. Todos stay in Docker unless you choose to delete them."
echo

if [[ ! -f "${INSTALL_DIR}/docker-compose.yml" ]]; then
  echo "Nothing to uninstall at ${INSTALL_DIR}"
  exit 0
fi

cd "${INSTALL_DIR}"
compose -p todoapp down

read -r -p "Delete saved todos too? [y/N]: " WIPE
if [[ "${WIPE}" == "y" || "${WIPE}" == "Y" ]]; then
  compose -p todoapp down -v
  echo "Volumes removed."
fi

echo "Uninstall complete."
