@echo off
setlocal EnableExtensions
cd /d "%~dp0"

set "VERSION=%~1"
if "%VERSION%"=="" set "VERSION=latest"
set "IMAGE=ghcr.io/prvnlhr/todo-app:latest"

echo ============================================
echo  Push %IMAGE%  APP_VERSION=%VERSION%
echo ============================================
echo.
echo Login: GitHub username + a PAT with write:packages
echo   docker login ghcr.io
echo.

docker info >nul 2>&1
if errorlevel 1 (
  echo Docker is not running.
  exit /b 1
)

docker build -t "%IMAGE%" --build-arg APP_VERSION=%VERSION% .
if errorlevel 1 exit /b 1

docker push "%IMAGE%"
if errorlevel 1 (
  echo Push failed. Run: docker login ghcr.io
  exit /b 1
)

echo.
echo Pushed %IMAGE%
echo After the first push, set the package Public:
echo   GitHub - Packages - todo-app - Package settings - Change visibility
echo Friend Watchtower will pull within ~30s if APP_IMAGE=%IMAGE%
endlocal
