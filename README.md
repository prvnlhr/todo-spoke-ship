# Todo Hub / Spoke Demo

One Laravel app (`app/`) deployed as hub or spoke via `APP_ROLE`.


| Role           | Start                                     | URL                                            |
| -------------- | ----------------------------------------- | ---------------------------------------------- |
| Spoke (dev)    | `docker compose up -d --build`            | [http://localhost](http://localhost)           |
| Hub            | `cd hub` → `docker compose up -d --build` | [http://localhost:8080](http://localhost:8080) |
| Spoke (client) | `ship/install.bat`                        | [http://localhost](http://localhost)           |

Remote demo: hub on this PC + `.\expose-hub.bat` (ngrok) → set `APP_URL` / friend’s `HUB_URL` to the https URL. See `hub/README.md`.


See `hub/README.md` and `ship/README.txt` for sync and OTA details.

Copy/paste this as your architecture doc (e.g. into `docs/architecture.md` or expand `README.md`). Ask mode can’t write the file for you — switch to Agent mode if you want it saved in the repo.

---



# Hub–Spoke Architecture



## One-line summary

**One Laravel app** (`app/`), built as **one Docker image**, runs as **hub** or **spoke** via `APP_ROLE`. Clients get a spoke install package; hub is central admin + sync API.

---



## Roles


|                | **Hub**                                            | **Spoke**                               |
| -------------- | -------------------------------------------------- | --------------------------------------- |
| **Who**        | You / company                                      | Client PC                               |
| **Role**       | `APP_ROLE=hub`                                     | `APP_ROLE=spoke`                        |
| **Job**        | Admin UI, spokes list, menus, sync API, central DB | Offline todos, local MySQL, sync client |
| **Where**      | Local Docker now; AWS later                        | Client machine via `ship/`              |
| **URL (demo)** | [http://localhost:8080](http://localhost:8080)     | [http://localhost](http://localhost)    |


Same codebase. Same image family (`todo-app:x.y`). Different env + compose.

```
app/  →  build  →  todo-app:1.1
                      │
          ┌───────────┴───────────┐
          ▼                       ▼
   Hub (APP_ROLE=hub)      Spoke (APP_ROLE=spoke)
   hub/docker-compose.yml  root compose (dev)
                           ship/ (client)
```

---



## How the spoke reaches the client

1. Build image on your PC (`build-ship.bat` → `ship/images/app.tar` + compose + `.env`).
2. Give client the `ship/` folder (USB / zip / share).
3. Client runs `install.bat` (admin) → installs to `C:\TodoSpoke`, loads Docker images, starts stack.
4. Client opens **Todo App** shortcut → browser → `http://localhost`.

Hub does **not** push the app onto the client. Delivery is the installer package.

---



## Data sync (when spoke is online)

Spoke initiates contact with hub (`HUB_URL` + bearer token). Hub does not open inbound connections to the client.


| Direction       | Data                                       |
| --------------- | ------------------------------------------ |
| **Spoke → Hub** | Push todos (create / update / soft-delete) |
| **Hub → Spoke** | Pull menu / sidebar links                  |


- Offline: spoke keeps working on local MySQL.
- Online: `php artisan sync:hub` (scheduled every minute on spoke) catches up.
- Demo token: `SPOKE_ID=spoke-demo-01`, `HUB_API_TOKEN=token-demo-01`.

---



## UI / feature updates (new Docker image)

Deploying hub **does not** patch spokes by itself. You publish a **new shared image**; hub and spoke **pull** it.

```
Change code in app/
        │
        ▼
  push-ota.bat 1.2.0
  (build + push localhost:5000/todo-app:latest)
        │
        ├─► Hub Watchtower pulls (~30s) → http://localhost:8080
        └─► Spoke Watchtower pulls (~30s) → http://localhost
```

**Local registry OTA (implemented):** `start-ota-demo.bat`, then `push-ota.bat 1.2.0`.  
Requires Docker Engine `"insecure-registries": ["localhost:5000"]`.

**USB fallback:** `build-ship.bat` → client `update.bat`.

**Dev bind-mount spoke** (root `docker-compose.yml`) will **not** show image OTA — it mounts `./app`. Use `ship/` for OTA tests.

---



## Repo layout

```
spoke-demo/
├── app/                 # Unified Laravel app (hub + spoke code)
├── docker/ + Dockerfile # Shared image build
├── docker-compose.yml   # Spoke (dev)
├── hub/                 # Hub deploy only (compose + .env)
├── ship/                # Client installer / updater
├── build-ship.bat
└── README.md
```

---



## What’s done

- [x] Unified Laravel app with `APP_ROLE=hub|spoke`
- [x] Spoke todo CRUD (offline-first, local MySQL)
- [x] Hub admin: dashboard, spokes, menus
- [x] Sync API on hub (`/api/sync/push`, `/api/sync/pull`) + spoke auth token
- [x] Spoke `sync:hub` command + schedule (every minute)
- [x] Shared Docker image `todo-app:1.1`
- [x] Hub local stack (`hub/docker-compose.yml`, port 8080)
- [x] Spoke local/dev stack (root compose, port 80)
- [x] Client ship package: `install.bat`, `update.bat`, compose, image tar workflow
- [x] Desktop shortcut install path (`C:\TodoSpoke`)
- [x] Seeded demo spoke + menus on hub
- [x] Spoke sidebar from synced menus + version footer
- [x] Watchtower on ship + hub OTA stacks (poll 30s)
- [x] Local registry OTA (`registry/`, `push-ota.bat`, `start-ota-demo.bat`)

---



## What’s remaining

- [ ] **AWS hub** — deploy same image to AWS; set real `HUB_URL` on spokes
- [ ] **Remote registry (ECR / Docker Hub)** — replace `localhost:5000` with a real URL + credentials
- [ ] **Per-client ship config** — unique `SPOKE_ID` + `HUB_API_TOKEN` per client (hub admin create spoke)
- [ ] **Hub spoke provisioning UI** — create spoke + generate token (today: seeder / DB)
- [ ] **Richer sync** — conflict rules, full bidirectional if needed, sync status in hub UI
- [ ] **Auth on hub UI** — login / roles (currently open admin for demo)
- [ ] **HTTPS / production secrets** — real DB passwords, APP_KEY management, TLS
- [ ] **Health / monitoring** — last sync time alerts, failed sync visibility for ops
- [ ] **Automated CI** — build image, tag version, publish registry on merge

---



## Demo checklist (local)

1. Start hub: `cd hub` → `docker compose up -d --build` → [http://localhost:8080](http://localhost:8080)
2. Start spoke: from root → `docker compose up -d --build` → [http://localhost](http://localhost)
3. Sync: `docker compose exec app php artisan sync:hub`
4. Add menu on hub → sync again → sidebar appears on spoke
5. Client story: `build-ship.bat` → run `ship/install.bat` on a machine with Docker

---



## Answers in one place

1. **Spoke on client via Docker?** Yes — `ship/install.bat` installs and starts it.
2. **Hub on AWS or local?** Yes — local now; AWS later with same image.
3. **Sync so hub has spoke data?** Yes — todos push to hub; menus pull to spoke.
4. **UI update via registry?** Yes locally: `push-ota.bat 1.2.0` → Watchtower updates hub and spoke from `localhost:5000/todo-app:latest`. Hub deploy alone still does not push code into the client.

---

If you want this written into the repo as `docs/architecture.md` (and linked from `README.md`), switch to **Agent mode** and ask to add it.




Hub ::

`cd "d:\Web Development\Projects\spoke-demo\hub"`
`docker compose up -d --build`

Spoke ::

`cd "d:\Web Development\Projects\spoke-demo"`
`docker compose up -d --build`