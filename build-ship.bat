@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo ============================================
echo  Build Todo Spoke ship package
echo ============================================
echo.

where docker >nul 2>&1
if errorlevel 1 (
  echo Docker is not available. Start Docker Desktop and retry.
  exit /b 1
)

docker info >nul 2>&1
if errorlevel 1 (
  echo Docker engine is not running. Start Docker Desktop and retry.
  exit /b 1
)

if not exist "ship\images" mkdir "ship\images"

echo [1/4] Building app image todo-app:1.1 ...
docker compose build app
if errorlevel 1 (
  echo Build failed.
  exit /b 1
)

echo.
echo [2/4] Ensuring mysql:8.0 is available...
docker image inspect mysql:8.0 >nul 2>&1
if errorlevel 1 (
  docker pull mysql:8.0
  if errorlevel 1 (
    echo Failed to pull mysql:8.0
    exit /b 1
  )
)

echo.
echo [3/4] Saving images to ship\images\ ...
docker save todo-app:1.1 -o ship\images\app.tar
if errorlevel 1 (
  echo Failed to save app image.
  exit /b 1
)
docker save mysql:8.0 -o ship\images\mysql.tar
if errorlevel 1 (
  echo Failed to save mysql image.
  exit /b 1
)

echo.
echo [4/4] Preparing ship\.env ...
if exist "ship\.env" (
  echo ship\.env already exists — leaving it unchanged.
) else (
  copy /Y "ship\.env.example" "ship\.env" >nul
  echo Created ship\.env from .env.example
  echo IMPORTANT: Set APP_KEY, SPOKE_ID, and strong DB passwords before shipping.
  echo Generate APP_KEY with:
  echo   docker compose exec app php artisan key:generate --show
)

echo.
echo ============================================
echo  Ship package ready: ship\
echo.
echo  Contents to give the client:
echo    ship\install.bat
echo    ship\update.bat
echo    ship\docker-compose.yml
echo    ship\.env
echo    ship\README.txt
echo    ship\images\app.tar
echo    ship\images\mysql.tar
echo    (optional) Docker Desktop Installer.exe
echo.
echo  Zip the ship folder or copy it to a USB stick.
echo ============================================
endlocal
