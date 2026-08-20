@echo off
setlocal EnableExtensions
cd /d "%~dp0"

set "INSTALL_DIR=%LOCALAPPDATA%\TodoApp"

echo ============================================
echo  Todo App updater
echo ============================================
echo.

where docker >nul 2>&1
if errorlevel 1 (
  echo Docker is not installed. Run install.bat first.
  pause
  exit /b 1
)

docker info >nul 2>&1
if errorlevel 1 (
  echo Start Docker Desktop, then run this updater again.
  pause
  exit /b 1
)

if not exist "%INSTALL_DIR%" (
  echo Todo App is not installed. Run install.bat first.
  pause
  exit /b 1
)

if not exist "%~dp0images\app.tar" (
  echo Missing images\app.tar
  pause
  exit /b 1
)

echo Loading new images...
docker load -i "%~dp0images\app.tar"
if errorlevel 1 (
  echo Failed to load the app image.
  pause
  exit /b 1
)
if exist "%~dp0images\mysql.tar" docker load -i "%~dp0images\mysql.tar"

copy /Y "%~dp0docker-compose.yml" "%INSTALL_DIR%\docker-compose.yml" >nul

cd /d "%INSTALL_DIR%"
docker compose -p todoapp up -d --force-recreate
if errorlevel 1 (
  echo Update failed.
  pause
  exit /b 1
)

start "" "http://localhost:8080"
echo.
echo Update complete. http://localhost:8080
pause
endlocal
