# Todo Hub (office PC)

Fully offline. Same Docker image as the spoke (`todo-app:1.0`), role `APP_ROLE=hub`.

## Start (dev)

```powershell
cd hub
docker compose up -d --build
```

Open **http://localhost:8090**

| | |
| --- | --- |
| Hub UI | http://localhost:8090 |
| Hub MySQL (TablePlus) | `127.0.0.1:3308` · user `hub` · password `secret` · db `todo_hub` |

## What you can do

1. **Spokes** — register border posts (`post-north-01`, …). IDs must match each spoke’s `SPOKE_ID`.
2. **Import** — upload a JSON file exported from a spoke (USB).
3. **Spoke detail** — view that post’s imported todos.

Demo spokes are seeded: `post-north-01`, `post-south-01`.

## USB sync flow

1. On spoke: click **Export for hub (USB)** → save the `.json` file.
2. Copy file to office on USB.
3. On hub: **Import** → select spoke → upload file.

No internet. No LAN. Hub never calls the spoke.
