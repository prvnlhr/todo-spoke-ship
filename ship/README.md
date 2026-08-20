# Todo App — install from this folder

1. Install Docker Desktop and start it.
2. Check `images/` (both must be **~200 MB each** — not 134 bytes):
   - `images/app.tar` (~206 MB)
   - `images/mysql.tar` (~237 MB)

   If they are tiny, this package is incomplete. See [Got the folder from Git?](#got-the-folder-from-git) below.

3. Run the installer for your OS.
4. Browser opens http://localhost:8080

## Install

| OS | Command |
| --- | --- |
| Windows | Double-click `install.bat` |
| Mac | Open Terminal in this folder: `bash install.sh` |
| Linux | `bash install.sh` |

On Mac, if `install.command` fails with “access privileges”, use `bash install.sh`.

If Docker was just installed, start Docker Desktop, wait until it is running, then run the installer again.

## Where it installs

| OS | Path |
| --- | --- |
| Windows | `%LOCALAPPDATA%\TodoApp` |
| Mac / Linux | `~/TodoApp` |

Todos are saved in Docker on this machine. You can remove the USB after install.

## Update / uninstall

- **Update:** `update.bat` (Windows) or `bash update.sh` (Mac/Linux) from a new package
- **Uninstall:** `uninstall.bat` or `bash uninstall.sh`  
  Todos stay in Docker until you choose to delete volumes.

## TablePlus (optional — view the database)

| | |
| --- | --- |
| Host | `127.0.0.1` |
| Port | `3307` |
| User | `todo` |
| Password | `secret` |
| Database | `todo` |

Open the `todos` table. TablePlus is not opened by the installer.

## Got the folder from Git?

**`git pull` alone does NOT download the Docker images.**

From the **repo root** (the folder that contains `ship/`, not inside `ship/`):

```bash
brew install git-lfs          # Mac, one time
git lfs install
git lfs pull
ls -lh ship/images/           # must show ~200M, not 134B
```

Then:

```bash
cd ship && bash install.sh
```

For border / offline clients, do **not** use git. Copy this whole `ship/` folder from a USB or zip built with `build-ship.bat` on the build PC.
