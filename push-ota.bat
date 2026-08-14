@echo off
setlocal EnableExtensions EnableDelayedExpansion
cd /d "%~dp0"

set "VERSION=%~1"
if "%VERSION%"=="" set "VERSION=1.1.0"
set "REGISTRY=localhost:5000"
set "IMAGE=%REGISTRY%/todo-app:latest"

echo ============================================
echo  Push OTA image  APP_VERSION=%VERSION%
echo  %IMAGE%
echo ============================================
echo.

where docker >nul 2>&1
if errorlevel 1 (
  echo Docker is not available.
  exit /b 1
)

docker info >nul 2>&1
if errorlevel 1 (
  echo Docker engine is not running.
  exit /b 1
)

echo Starting local registry...
docker compose -f registry\docker-compose.yml up -d
if errorlevel 1 (
  echo Failed to start registry.
  exit /b 1
)

echo Waiting for registry on :5000 ...
set /a tries=0
:wait_reg
powershell -NoProfile -Command "try { Invoke-WebRequest -Uri 'http://localhost:5000/v2/' -UseBasicParsing -TimeoutSec 3 | Out-Null; exit 0 } catch { exit 1 }" >nul 2>&1
if errorlevel 1 (
  set /a tries+=1
  if !tries! geq 20 (
    echo Registry did not become ready.
    exit /b 1
  )
  timeout /t 1 /nobreak >nul
  goto wait_reg
)

echo.
echo Building image...
docker build -t todo-app:1.1 -t "%IMAGE%" --build-arg APP_VERSION=%VERSION% .
if errorlevel 1 (
  echo Build failed.
  exit /b 1
)

echo.
echo Pushing %IMAGE% ...
docker push "%IMAGE%"
if errorlevel 1 (
  echo.
  echo Push failed. Add an insecure registry in Docker Desktop:
  echo   Settings - Docker Engine - add:
  echo     "insecure-registries": ["localhost:5000"]
  echo   then Apply and Restart, and run this script again.
  exit /b 1
)

echo.
echo ============================================
echo  Pushed %IMAGE%  (APP_VERSION=%VERSION%^)
echo  Watchtower on hub/spoke will pull within ~30s.
echo ============================================
endlocal
