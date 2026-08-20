Todo App

Windows: double-click install.bat
Mac:     double-click install.command
Linux:   bash install.sh

Then open http://localhost:8080

Needs Docker Desktop (or Docker Engine) running.

This folder must include images/app.tar and images/mysql.tar
(from Git LFS after clone, or from build-ship on the build PC).
The first run loads images; later runs start in a few seconds.

Update later: run update.bat / update.sh from a new USB package.
Uninstall: uninstall.bat / uninstall.sh (todos stay in Docker until you remove volumes).
