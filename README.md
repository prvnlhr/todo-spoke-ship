# Todo App — Phase 1 (offline spoke)

One Laravel app. One Docker image (`todo-app:1.0`) plus MySQL. Runs entirely locally. No AWS, no image registry, no internet after the images are on the USB/zip.

This is **Phase 1** of hub-and-spoke: a spoke you can hand to anyone as a folder.

| Who | What | URL |
| --- | --- | --- |
| You (dev) | `docker compose up -d --build` | http://localhost:8080 |
| Client | Double-click `ship/install.bat` (Windows) / `install.command` (Mac) / `install.sh` (Linux) | http://localhost:8080 |

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
| **1 (now)** | Spoke todo CRUD. MySQL. Zip/USB one-click install. |
| **2** | Hub on a company PC (`APP_ROLE=hub`). Same image family. |
| **3** | Data sync over LAN (`HUB_URL=http://<hub-ip>:8080`). No cloud. |
| **4** | USB/zip updates for new UI versions (`update.bat` / `update.sh`). |

No registry. No Watchtower. App updates travel the same way as install: a new `app.tar` on USB/zip.

---

## Give this to a user (one click)

On your PC (Docker running; internet only needed here to build / pull MySQL once):

**Windows:** `build-ship.bat`  
**Mac / Linux:** `./build-ship.sh`

Then zip the `ship/` folder or copy it to a USB stick.

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

## Run locally (you)

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
├── README.txt
└── images/
    ├── app.tar      # todo-app:1.0
    └── mysql.tar    # mysql:8.0
```

`install` loads those tars (`docker load`) and starts the stack. It never runs `docker pull`.

---

## Repo layout

```
spoke-demo/
├── app/                 # Laravel todo CRUD
├── docker/entrypoint.sh
├── Dockerfile
├── docker-compose.yml   # local / build
├── ship/                # client package
├── build-ship.bat
├── build-ship.sh
└── README.md
```

---

## Phase 1 checklist

- [x] Single-page todo CRUD (create, edit, complete, delete)
- [x] MySQL 8 in Docker
- [x] Shared image `todo-app:1.0`
- [x] Offline ship package (`app.tar` + `mysql.tar`)
- [x] One-click Windows / Mac / Linux install
- [ ] Hub (company PC)
- [ ] LAN sync
- [ ] Per-client spoke identity
