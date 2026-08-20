@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo ============================================
echo  Build Todo ship package
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

if not exist ".env" copy /Y ".env.example" ".env" >nul
if not exist "ship\images" mkdir "ship\images"
if not exist "ship\.env" copy /Y "ship\.env.example" "ship\.env" >nul

echo [1/4] Building app image todo-app:1.0 ...
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
docker save todo-app:1.0 -o ship\images\app.tar
if errorlevel 1 (
  echo Failed to save app image.
  exit /b 1
)
docker save mysql:8.0 -o ship\images\mysql.tar
if errorlevel 1 (
  echo Failed to save MySQL image.
  exit /b 1
)

echo.
echo [4/4] Ship folder ready.
echo.
echo ============================================
echo  Give the client the ship\ folder as a zip or USB:
echo    install.bat / install.command / install.sh
echo    update.bat / update.sh
echo    uninstall.bat / uninstall.sh
echo    docker-compose.yml
echo    .env
echo    README.txt
echo    images\app.tar
echo    images\mysql.tar
echo ============================================
endlocal
