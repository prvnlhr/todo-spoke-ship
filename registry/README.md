# Local image registry (OTA)

HTTP registry on **http://localhost:5000**.

## One-time: allow insecure registry

Docker Desktop → **Settings** → **Docker Engine**:

```json
{
  "insecure-registries": ["localhost:5000"]
}
```

Apply & Restart.

## Demo

```bat
start-ota-demo.bat
```

Then change UI and:

```bat
push-ota.bat 1.2.0
```

Watchtower recreates labeled containers within ~30 seconds.
