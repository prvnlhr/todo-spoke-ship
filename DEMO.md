# Demo walkthrough — Hub on your PC, Spoke on a friend’s laptop

One Laravel app. You run **hub**. Friend runs **spoke**. Todos sync by themselves (~1 minute). UI updates when you publish a new Docker image to GitHub Container Registry.

Repo: https://github.com/prvnlhr/todo-spoke-ship

---

## What each person has

| | You (owner) | Friend (client) |
|--|-------------|-----------------|
| Role | Hub | Spoke |
| URL | Public ngrok HTTPS | http://localhost |
| Docker | Hub stack + ngrok | Spoke stack only |

Leave **ngrok and hub running** for the whole demo. If ngrok stops, the public URL dies and sync stops.

---

## Part A — You: start the hub and expose it

### A1. Docker Desktop

Start Docker Desktop on your PC. Wait until it is running.

### A2. Start hub (OTA / image stack)

From the project root:

```powershell
docker compose -f hub/docker-compose.ota.yml up -d
```

Check locally: http://localhost:8080

### A3. Public URL (ngrok)

```powershell
.\expose-hub.bat
```

Or: `ngrok http 8080`

Copy the **https** URL, for example:

`https://untranslated-overventuresome-ozie.ngrok-free.dev`

### A4. Point hub at that URL

In `hub/.env`:

```env
APP_URL=https://YOUR-NGROK-URL
APP_ROLE=hub
```

Recreate hub:

```powershell
docker compose -f hub/docker-compose.ota.yml up -d
```

On your phone (mobile data) open the ngrok URL. You should see the hub (ngrok may show a “Visit Site” button once).

---

## Part B — One-time: GitHub image (for automatic UI updates)

### B1. Wait for the image

GitHub → **Actions** → workflow **Publish spoke image** → must be **green**.

Image name: `ghcr.io/prvnlhr/todo-app:latest`

### B2. Make the package public

GitHub → your profile → **Packages** → **todo-app** → **Package settings** → **Change visibility → Public**.

If this stays private, the friend cannot pull the image.

---

## Part C — Friend: set up spoke (Mac)

### C1. Docker Desktop

Install and **start** Docker Desktop. Wait until it is running.

`docker info` must work. If you see `docker.sock: no such file`, Docker is not running.

### C2. Clone

```bash
git clone https://github.com/prvnlhr/todo-spoke-ship.git
cd todo-spoke-ship
cp ship/.env.example ship/.env
```

### C3. Edit `ship/.env`

```env
APP_ROLE=spoke
SPOKE_ID=spoke-demo-01
HUB_URL=https://YOUR-NGROK-URL
HUB_API_TOKEN=token-demo-01
SYNC_ENABLED=true
APP_IMAGE=ghcr.io/prvnlhr/todo-app:latest
APP_KEY=   # owner sends this privately
```

`HUB_URL` must match the **current** ngrok https URL (no trailing slash).  
`APP_KEY`: owner pastes from their `ship/.env` (do not put it in the public repo).

### C4. Start spoke

```bash
cd ship
docker compose up -d
```

First pull can take several minutes. Image supports **Intel and Apple Silicon (arm64)**.

### C5. Open the app

http://localhost

---

## Part D — Demo: todos (automatic sync)

No extra commands if the spoke **scheduler** is running (`docker compose up -d` starts it).

1. Friend adds a todo on http://localhost  
2. Wait up to **1 minute**  
3. You refresh the hub (ngrok URL) → Dashboard / that spoke → the new todo is there  

If nothing appears:

```bash
docker compose exec app php artisan sync:hub
```

(run in friend’s `ship/` folder)

Friend must be able to open your ngrok URL in **their** browser. If that fails, sync will fail.

**Menus:** you add a link on hub → **Menu links**. After the next sync (~1 min), friend’s sidebar updates. That is **data**, not a new image.

---

## Part E — Demo: UI change (automatic OTA)

### E1. You change the product

Edit files in `app/` (spoke UI = `todos.blade.php`, hub UI = hub views).

### E2. You publish a new image

**Option 1 — GitHub Actions:** commit and `git push origin main`. Wait for **Publish spoke image** to finish.

**Option 2 — your PC:**

```powershell
.\push-ghcr.bat 1.6.0
```

(`docker login ghcr.io` may be required for option 2.)

### E3. Friend does nothing

Watchtower on their machine pulls `ghcr.io/prvnlhr/todo-app:latest` (about **30 seconds**). They refresh http://localhost.

They need Docker running and `APP_IMAGE=ghcr.io/prvnlhr/todo-app:latest`.

---

## Stop (your PC)

```powershell
docker compose -f hub/docker-compose.ota.yml down
```

Stop the ngrok window too.

Friend:

```bash
cd ship
docker compose down
```

---

## Quick checklist

**You**

- [ ] Docker running  
- [ ] Hub up on :8080  
- [ ] Ngrok running; `APP_URL` = that https URL  
- [ ] GHCR package **public**; Actions build succeeded  

**Friend**

- [ ] Docker running  
- [ ] `HUB_URL` = your ngrok https URL  
- [ ] `APP_IMAGE=ghcr.io/prvnlhr/todo-app:latest`  
- [ ] `docker compose up -d` in `ship/`  
- [ ] http://localhost works  

**Do not mix**

- Friend must **not** use `APP_IMAGE=localhost:5000/...` (that is only your PC).  
- Friend must **not** use `HUB_URL=http://localhost:8080` (that is their own machine).  
- `install.bat` is Windows-only; Mac uses the steps in Part C.
