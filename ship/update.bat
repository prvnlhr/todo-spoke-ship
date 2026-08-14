@echo off
setlocal EnableExtensions
cd /d "%~dp0"

set "INSTALL_DIR=C:\TodoSpoke"

echo ============================================
echo  Todo Spoke Updater
echo ============================================
echo.

net session >nul 2>&1
if errorlevel 1 (
  echo Please right-click update.bat and choose "Run as administrator".
  pause
  exit /b 1
)

docker info >nul 2>&1
if errorlevel 1 (
  echo Start Docker Desktop first, then run update.bat again.
  pause
  exit /b 1
)

if not exist "%INSTALL_DIR%" (
  echo %INSTALL_DIR% not found. Run install.bat first.
  pause
  exit /b 1
)

echo Loading new images...
for /f "tokens=2 delims==" %%A in ('findstr /b "APP_IMAGE=" "%INSTALL_DIR%\.env" 2^>nul') do set "APP_IMAGE=%%A"
if "%APP_IMAGE%"=="" set "APP_IMAGE=localhost:5000/todo-app:latest"

docker pull %APP_IMAGE% 2>nul
if errorlevel 1 (
  if exist "%~dp0images\app.tar" (
    docker load -i "%~dp0images\app.tar"
    docker tag todo-app:1.1 %APP_IMAGE% 2>nul
  ) else (
    echo ERROR: Could not pull %APP_IMAGE% and images\app.tar is missing.
    pause
    exit /b 1
  )
)
if exist "%~dp0images\mysql.tar" (
  docker load -i "%~dp0images\mysql.tar"
)

copy /Y "%~dp0docker-compose.yml" "%INSTALL_DIR%\docker-compose.yml" >nul
:: Do NOT overwrite .env on update (keeps SPOKE_ID / secrets)

cd /d "%INSTALL_DIR%"
docker compose up -d
if errorlevel 1 (
  echo Update failed.
  pause
  exit /b 1
)

echo.
echo Update complete.
echo Open http://localhost
echo.
pause
endlocal
