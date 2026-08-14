# Spoke (client) — install from GitHub

Clone the **whole repo**. The app image is **`ghcr.io/prvnlhr/todo-app:latest`**. Watchtower pulls new versions automatically.

## Mac / Linux (friend)

1. Install [Docker Desktop](https://www.docker.com/products/docker-desktop/) and start it.
2. Clone:

```bash
git clone https://github.com/prvnlhr/todo-spoke-ship.git
cd todo-spoke-ship
cp ship/.env.example ship/.env
```

3. Edit `ship/.env`:

```env
HUB_URL=https://YOUR-NGROK-URL
HUB_API_TOKEN=token-demo-01
SPOKE_ID=spoke-demo-01
APP_IMAGE=ghcr.io/prvnlhr/todo-app:latest
APP_KEY=base64:...   # from the owner, or generate after first start
```

4. Start (pulls the image; no local `docker build` required once GHCR is public):

```bash
cd ship
docker compose up -d
```

5. Open http://localhost

Todos sync to the hub about every minute. UI updates: owner pushes a new image to ghcr.io; Watchtower restocks this spoke (~30s).

If the image is **private**, run `docker login ghcr.io` once (GitHub username + PAT with `read:packages`).

## Owner: publish a UI update

```bat
push-ghcr.bat 1.6.0
```

Or push to `main` — GitHub Actions builds and pushes `:latest`.

First time: GitHub → **Packages** → **todo-app** → Package settings → **Change visibility → Public**.
