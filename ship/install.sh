#!/usr/bin/env bash
set -euo pipefail

SHIP_DIR="$(cd "$(dirname "$0")" && pwd)"
INSTALL_DIR="${HOME}/TodoApp"
APP_URL="http://localhost:8080"

echo "============================================"
echo " Todo App installer"
echo "============================================"
echo

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

open_url() {
  if command -v open >/dev/null 2>&1; then
    open "$1"
  elif command -v xdg-open >/dev/null 2>&1; then
    xdg-open "$1" >/dev/null 2>&1 || true
  fi
}

if ! command -v docker >/dev/null 2>&1; then
  echo "Docker is not installed."
  echo "Install Docker Desktop (or Docker Engine), start it, then run this installer again."
  open_url "https://www.docker.com/products/docker-desktop/"
  exit 1
fi

if [[ ! -f "${SHIP_DIR}/images/app.tar" || ! -f "${SHIP_DIR}/images/mysql.tar" ]]; then
  echo "This package is missing images/app.tar or images/mysql.tar."
  echo "On the build PC run ./build-ship.sh, then copy the ship folder."
  exit 1
fi

if ! docker info >/dev/null 2>&1; then
  echo "Starting Docker..."
  if [[ "$(uname -s)" == "Darwin" ]]; then
    open -a Docker >/dev/null 2>&1 || true
  fi
  ready=0
  for _ in $(seq 1 60); do
    if docker info >/dev/null 2>&1; then
      ready=1
      break
    fi
    sleep 3
  done
  if [[ "$ready" -ne 1 ]]; then
    echo "Docker did not start. Open Docker Desktop, wait until it is running, then run this installer again."
    exit 1
  fi
fi
echo "Docker is ready."
echo

echo "Loading images..."
docker load -i "${SHIP_DIR}/images/app.tar"
docker load -i "${SHIP_DIR}/images/mysql.tar"

mkdir -p "${INSTALL_DIR}"
cp "${SHIP_DIR}/docker-compose.yml" "${INSTALL_DIR}/docker-compose.yml"
if [[ ! -f "${INSTALL_DIR}/.env" ]]; then
  if [[ -f "${SHIP_DIR}/.env" ]]; then
    cp "${SHIP_DIR}/.env" "${INSTALL_DIR}/.env"
  else
    cp "${SHIP_DIR}/.env.example" "${INSTALL_DIR}/.env"
  fi
fi

echo "Starting Todo App..."
cd "${INSTALL_DIR}"
compose -p todoapp up -d

echo "Waiting for ${APP_URL} ..."
ready=0
for _ in $(seq 1 40); do
  if command -v curl >/dev/null 2>&1 && curl -fsS --max-time 5 "${APP_URL}/up" >/dev/null 2>&1; then
    ready=1
    break
  fi
  sleep 3
done

if [[ "$ready" -eq 1 ]]; then
  echo "App is ready."
else
  echo "App is still starting. Open ${APP_URL} in a moment."
fi

open_url "${APP_URL}"
echo
echo "============================================"
echo " DONE"
echo " ${APP_URL}"
echo "============================================"
