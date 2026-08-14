@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo ============================================
echo  Expose hub (port 8080) with ngrok
echo ============================================
echo.
echo 1. Leave this window open while you demo.
echo 2. Copy the https URL ngrok prints.
echo 3. Put it in hub\.env as APP_URL=
echo 4. Recreate hub:  docker compose -f hub\docker-compose.ota.yml up -d
echo 5. Friend ship\.env:  HUB_URL=that-same-https-url
echo.

where ngrok >nul 2>&1
if errorlevel 1 (
  echo ngrok is not on PATH.
  echo Install from https://ngrok.com then run: ngrok http 8080
  exit /b 1
)

ngrok http 8080
endlocal
