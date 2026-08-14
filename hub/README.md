# Todo Hub (unified app — APP_ROLE=hub)

Same codebase as spoke (`../app/`). Hub runs on **http://localhost:8080**.

Hub uses its own env file (`hub/.env`) mounted over the shared app — do not point hub compose at `app/.env` or it will run as spoke.

## Start

```powershell
cd hub
docker compose up -d --build
```

## Spoke (dev)

From project root:

```powershell
docker compose up -d --build
```

Spoke: **http://localhost** · Hub: **http://localhost:8080**

## Expose hub on a public URL (remote demo)

Hub stays on this PC at port **8080**. A tunnel makes it reachable from another city.

1. Hub running (`docker compose -f hub/docker-compose.ota.yml up -d` or `cd hub && docker compose up -d`).
2. Install [ngrok](https://ngrok.com), then:

```powershell
ngrok http 8080
```

3. Copy the **https** forwarding URL (e.g. `https://abc123.ngrok-free.app`).
4. Set it in `hub/.env`:

```env
APP_URL=https://abc123.ngrok-free.app
```

5. Recreate hub so it picks up `APP_URL`:

```powershell
docker compose -f hub/docker-compose.ota.yml up -d
```

6. Friend’s `ship/.env`:

```env
HUB_URL=https://abc123.ngrok-free.app
HUB_API_TOKEN=token-demo-01
SPOKE_ID=spoke-demo-01
```

Free ngrok URLs change when you restart ngrok. Production: deploy the same hub image to AWS with a real HTTPS domain.

Or run `.\expose-hub.bat` from the repo root (starts ngrok if it is on PATH).


Hub seeds `spoke-demo-01` with token `token-demo-01`. Spoke `.env` / compose should match:

```env
HUB_URL=http://host.docker.internal:8080
HUB_API_TOKEN=token-demo-01
SPOKE_ID=spoke-demo-01
```

Run manually: `docker compose exec app php artisan sync:hub`

## OTA (registry + Watchtower)

Hub deploy does **not** patch spokes. Both pull the **same image** from a registry.

Local demo:

1. Docker Desktop → Settings → Docker Engine → add `"insecure-registries": ["localhost:5000"]` → Apply & Restart
2. `start-ota-demo.bat` — registry + hub (OTA) + ship spoke + Watchtower
3. Change UI in `app/`, then `push-ota.bat 1.2.0`
4. Wait ~30s, refresh hub `:8080` and spoke `:80` — version numbers should match

Dev hub (bind-mount, no OTA): `docker compose -f hub/docker-compose.yml up -d --build`

USB fallback: `build-ship.bat` + `ship/update.bat`

## Pages (hub)

| URL | Purpose |
|-----|---------|
| `/` | Dashboard |
| `/spokes` | Clients |
| `/menus` | Sidebar links (synced to spokes) |
