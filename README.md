# Todo App — Phase 1 (offline spoke)

One Laravel app. One Docker image (`todo-app:1.0`) plus MySQL. Runs entirely locally. No AWS, no image registry, no internet after the images are on the USB/zip.

This is **Phase 1** of hub-and-spoke: a spoke you can hand to anyone as a folder.

| Who | What | URL |
| --- | --- | --- |
| You (dev spoke) | `docker compose up -d --build` | http://localhost:8080 |
| You (dev hub) | `cd hub` → `docker compose up -d --build` | http://localhost:8090 |
| Client spoke | Double-click `ship/install.bat` / `bash install.sh` | http://localhost:8080 |

---

## Architecture (offline)

```
app/  →  docker build  →  todo-app:1.0
                              │
                    docker save → ship/images/*.tar
                              │
                    USB / zip of ship/
                              │
                    client: one-click install
                    APP + MySQL in Docker
                    http://localhost:8080
```

Later phases (not in this build):

| Phase | What |
| --- | --- |
| **1** | Spoke todo CRUD. MySQL. Zip/USB one-click install. |
| **2 (now)** | Hub on office PC. USB JSON import. Select which spoke to pull. |
| **3** | Per-client ship packages with unique `SPOKE_ID`. |
| **4** | USB/zip updates for new UI versions (`update.bat` / `update.sh`). |

No registry. No Watchtower. App updates travel the same way as install: a new `app.tar` on USB/zip.

---

## Give this to a user (one click)

On your PC (Docker running; internet only needed here to build / pull MySQL once):

**Windows:** `build-ship.bat`  
**Mac / Linux:** `./build-ship.sh`

Then zip the `ship/` folder or copy it to a USB stick. `ship/images/*.tar` is stored in Git LFS, so a full clone also includes the images (`git lfs install` once, then `git clone` / `git lfs pull`).

The client:

1. Unzip / open the USB folder.
2. Double-click **install** (see table below).
3. Browser opens http://localhost:8080

| OS | Installer |
| --- | --- |
| Windows | `install.bat` |
| Mac | `install.command` |
| Linux | `bash install.sh` |

If Docker is missing, the installer opens the Docker Desktop download page. After Docker is installed and running, they click install once more.

That is the only extra step. There is no registry login, no compose typing, no `.env` editing.

Install location: `%LOCALAPPDATA%\TodoApp` (Windows) or `~/TodoApp` (Mac/Linux). USB can be removed after install. Todos live in a Docker volume.

---

## Hub (office, offline)

```powershell
cd hub
docker compose up -d --build
```

Open [http://localhost:8090](http://localhost:8090). See `hub/README.md`.

Sync: spoke **Export for hub (USB)** → copy JSON → hub **Import** (pick spoke). No internet / LAN.

---

## Run locally (spoke)

```powershell
copy .env.example .env   # first time only; already present if you just cloned this tree
docker compose up -d --build
```

Open [http://localhost:8080](http://localhost:8080).

Stop: `docker compose down`  
Wipe todos: `docker compose down -v`

---

## What the user gets

```
ship/
├── install.bat / install.command / install.sh
├── update.bat / update.sh
├── uninstall.bat / uninstall.sh
├── docker-compose.yml
├── .env
├── README.md
└── images/
    ├── app.tar      # todo-app:1.0
    └── mysql.tar    # mysql:8.0
```

`install` loads those tars (`docker load`) and starts the stack. It never runs `docker pull`.

---

## Repo layout

```
spoke-demo/
├── app/                 # Laravel (hub + spoke via APP_ROLE)
├── docker/entrypoint.sh
├── Dockerfile
├── docker-compose.yml   # spoke (dev)
├── hub/                 # hub deploy (office)
├── ship/                # spoke client package
├── build-ship.bat
├── build-ship.sh
└── README.md
```

---

## Phase checklist

- [x] Single-page todo CRUD (create, edit, complete, delete)
- [x] MySQL 8 in Docker
- [x] Shared image `todo-app:1.0`
- [x] Offline ship package (`app.tar` + `mysql.tar`)
- [x] One-click Windows / Mac / Linux install
- [x] Hub UI (dashboard, spokes, USB import)
- [x] Spoke export JSON for hub
- [ ] Per-client spoke identity in ship packages
- [ ] One-click hub installer package