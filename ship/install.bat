@echo off
setlocal EnableExtensions EnableDelayedExpansion
cd /d "%~dp0"

echo ============================================
echo  Todo App installer
echo ============================================
echo.

set "INSTALL_DIR=%LOCALAPPDATA%\TodoApp"
set "APP_URL=http://localhost:8080"

where docker >nul 2>&1
if errorlevel 1 (
  echo Docker is not installed.
  echo Opening the Docker Desktop download page...
  start "" "https://www.docker.com/products/docker-desktop/"
  echo.
  echo Install Docker Desktop, start it, then double-click install.bat again.
  pause
  exit /b 1
)

if not exist "%~dp0images\app.tar" goto missing_images
if not exist "%~dp0images\mysql.tar" goto missing_images

echo Checking Docker...
docker info >nul 2>&1
if errorlevel 1 (
  echo Starting Docker Desktop...
  if exist "%ProgramFiles%\Docker\Docker\Docker Desktop.exe" (
    start "" "%ProgramFiles%\Docker\Docker\Docker Desktop.exe"
  )
)

set /a waits=0
:wait_docker
docker info >nul 2>&1
if errorlevel 1 (
  set /a waits+=1
  if !waits! gtr 60 (
    echo Docker did not start. Open Docker Desktop, wait until it is running, then run this installer again.
    pause
    exit /b 1
  )
  timeout /t 3 /nobreak >nul
  goto wait_docker
)
echo Docker is ready.
echo.

echo Loading images...
docker load -i "%~dp0images\app.tar"
if errorlevel 1 (
  echo Failed to load the app image.
  pause
  exit /b 1
)
docker load -i "%~dp0images\mysql.tar"
if errorlevel 1 (
  echo Failed to load the MySQL image.
  pause
  exit /b 1
)

if not exist "%INSTALL_DIR%" mkdir "%INSTALL_DIR%"
copy /Y "%~dp0docker-compose.yml" "%INSTALL_DIR%\docker-compose.yml" >nul
if not exist "%INSTALL_DIR%\.env" (
  if exist "%~dp0.env" (
    copy /Y "%~dp0.env" "%INSTALL_DIR%\.env" >nul
  ) else if exist "%~dp0.env.example" (
    copy /Y "%~dp0.env.example" "%INSTALL_DIR%\.env" >nul
  ) else (
    echo Missing .env in this package.
    pause
    exit /b 1
  )
)

powershell -NoProfile -Command ^
  "$p='%INSTALL_DIR%\.env'; $c=[IO.File]::ReadAllText($p) -replace \"`r`n\",\"`n\"; [IO.File]::WriteAllText($p,$c,(New-Object Text.UTF8Encoding $false))"

echo Starting Todo App...
cd /d "%INSTALL_DIR%"
docker compose -p todoapp up -d
if errorlevel 1 (
  echo Failed to start. Is Docker Desktop running?
  pause
  exit /b 1
)

echo Waiting for %APP_URL% ...
set /a tries=0
:wait_app
timeout /t 3 /nobreak >nul
powershell -NoProfile -Command "try { (Invoke-WebRequest -Uri 'http://localhost:8080/up' -UseBasicParsing -TimeoutSec 5).StatusCode | Out-Null; exit 0 } catch { exit 1 }"
if errorlevel 1 (
  set /a tries+=1
  if !tries! lss 40 goto wait_app
  echo App is still starting. Open %APP_URL% in a moment.
  goto open_browser
)
echo App is ready.

:open_browser
start "" "%APP_URL%"
echo.
echo ============================================
echo  DONE
echo  %APP_URL%
echo ============================================
pause
exit /b 0

:missing_images
echo This package is missing images\app.tar or images\mysql.tar.
echo On the build PC run build-ship.bat, then copy the ship folder.
pause
exit /b 1
