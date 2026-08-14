@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo ============================================
echo  Start local OTA demo (registry + hub + spoke)
echo ============================================
echo.
echo Hub:   http://localhost:8080
echo Spoke: http://localhost
echo.
echo Uses ship/ (image only). Stops the bind-mount spoke on port 80.
echo.

where docker >nul 2>&1
if errorlevel 1 (
  echo Docker is not available.
  exit /b 1
)

echo Stopping dev spoke (root compose) if running...
docker compose down 2>nul

echo Stopping bind-mount hub if running...
docker compose -f hub\docker-compose.yml down 2>nul

call push-ota.bat 1.1.0
if errorlevel 1 exit /b 1

echo.
echo Starting OTA hub...
docker compose -f hub\docker-compose.ota.yml up -d
if errorlevel 1 (
  echo Hub failed to start.
  exit /b 1
)

echo Starting OTA spoke (ship)...
docker compose -f ship\docker-compose.yml --env-file ship\.env up -d
if errorlevel 1 (
  echo Spoke failed to start. Is port 80 free?
  exit /b 1
)

echo.
echo ============================================
echo  Ready
echo    Hub    http://localhost:8080   (version in sidebar)
echo    Spoke  http://localhost        (version in footer)
echo.
echo  To push an update:
echo    1. Change UI in app\
echo    2. push-ota.bat 1.2.0
echo    3. Wait ~30s, refresh both browsers
echo ============================================
endlocal
