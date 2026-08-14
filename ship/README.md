# Spoke (client) — install from GitHub

This folder is the **client stack**. Clone the **whole repo** (not this folder alone) so you can build the Docker image.

## Mac / Linux (friend)

1. Install [Docker Desktop](https://www.docker.com/products/docker-desktop/) and start it.
2. Clone:

```bash
git clone https://github.com/prvnlhr/todo-spoke-ship.git
cd todo-spoke-ship
```

3. Spoke env:

```bash
cp ship/.env.example ship/.env
```

Edit `ship/.env`:

- `HUB_URL` — the hub public URL (ngrok/AWS), e.g. `https://xxxx.ngrok-free.dev`
- `HUB_API_TOKEN=token-demo-01`
- `SPOKE_ID=spoke-demo-01`
- `APP_IMAGE=todo-app:latest`
- Generate `APP_KEY` after the first container exists, or copy one from the owner.

4. Build the image (from **repo root**):

```bash
docker build -t todo-app:latest --build-arg APP_VERSION=1.5.0 .
```

5. Start spoke:

```bash
cd ship
docker compose up -d --pull never
```

6. Open http://localhost

Sync (if hub is reachable):

```bash
docker compose exec app php artisan sync:hub
```

## Windows

Run `install.bat` as administrator **or** the same `docker build` + `docker compose` steps as above.

## What not to commit

Never commit `ship/.env` (secrets + live hub URL). Use `.env.example`.
