@echo off
setlocal EnableExtensions EnableDelayedExpansion
cd /d "%~dp0"

echo ============================================
echo  Todo Spoke Installer
echo ============================================
echo.

net session >nul 2>&1
if errorlevel 1 (
  echo Please right-click install.bat and choose "Run as administrator".
  pause
  exit /b 1
)

set "INSTALL_DIR=C:\TodoSpoke"

:: --- 1) Docker Desktop ---
where docker >nul 2>&1
if errorlevel 1 (
  echo Docker not found.
  if exist "%~dp0Docker Desktop Installer.exe" (
    echo Installing Docker Desktop...
    "%~dp0Docker Desktop Installer.exe" install --quiet --accept-license
    echo.
    echo Docker Desktop was installed.
    echo 1^) Start Docker Desktop from the Start Menu
    echo 2^) Wait until it says Docker is running
    echo 3^) Run this install.bat again
    pause
    exit /b 0
  ) else (
    echo Docker Desktop is missing.
    echo Install Docker Desktop, start it, then run install.bat again.
    echo https://www.docker.com/products/docker-desktop/
    pause
    exit /b 1
  )
)

:: --- 2) Wait for Docker engine ---
echo Waiting for Docker to be ready...
:wait_docker
docker info >nul 2>&1
if errorlevel 1 (
  timeout /t 3 /nobreak >nul
  goto wait_docker
)
echo Docker is ready.
echo.

:: --- 3) Install files ---
echo Copying files to %INSTALL_DIR% ...
if not exist "%INSTALL_DIR%" mkdir "%INSTALL_DIR%"
copy /Y "%~dp0docker-compose.yml" "%INSTALL_DIR%\docker-compose.yml" >nul
if exist "%~dp0.env" (
  if not exist "%INSTALL_DIR%\.env" (
    copy /Y "%~dp0.env" "%INSTALL_DIR%\.env" >nul
  ) else (
    echo Keeping existing %INSTALL_DIR%\.env
  )
) else (
  echo ERROR: .env is missing from this package.
  echo Fill ship\.env before shipping, or run build-ship.bat on the build PC.
  pause
  exit /b 1
)
if not exist "%INSTALL_DIR%\images" mkdir "%INSTALL_DIR%\images"
if exist "%~dp0images\*.tar" copy /Y "%~dp0images\*.tar" "%INSTALL_DIR%\images\" >nul

:: Normalize .env line endings for Linux (CRLF breaks APP_KEY)
powershell -NoProfile -Command ^
  "$p='%INSTALL_DIR%\.env'; $c=[IO.File]::ReadAllText($p) -replace \"`r`n\",\"`n\"; [IO.File]::WriteAllText($p,$c,(New-Object Text.UTF8Encoding $false))"

:: --- 4) Images: pull from registry, else load tar ---
echo Loading Docker images...
for /f "tokens=2 delims==" %%A in ('findstr /b "APP_IMAGE=" "%INSTALL_DIR%\.env"') do set "APP_IMAGE=%%A"
if "%APP_IMAGE%"=="" set "APP_IMAGE=localhost:5000/todo-app:latest"

docker pull %APP_IMAGE% 2>nul
if errorlevel 1 (
  if exist "%INSTALL_DIR%\images\app.tar" (
    echo Registry pull failed — loading images\app.tar
    docker load -i "%INSTALL_DIR%\images\app.tar"
    docker tag todo-app:1.1 %APP_IMAGE% 2>nul
  ) else (
    echo ERROR: Could not pull %APP_IMAGE% and images\app.tar is missing.
    echo Start the registry, run push-ota.bat, or include app.tar.
    pause
    exit /b 1
  )
)
if exist "%INSTALL_DIR%\images\mysql.tar" (
  docker load -i "%INSTALL_DIR%\images\mysql.tar"
) else (
  docker pull mysql:8.0
)
echo.

:: --- 5) Start stack ---
echo Starting Todo Spoke...
cd /d "%INSTALL_DIR%"
docker compose down 2>nul
docker compose up -d
if errorlevel 1 (
  echo Failed to start. Is Docker Desktop running?
  pause
  exit /b 1
)

:: --- 6) Wait for app ---
echo Waiting for the app to respond at http://localhost ...
set /a tries=0
:wait_app
timeout /t 3 /nobreak >nul
powershell -NoProfile -Command "try { (Invoke-WebRequest -Uri 'http://localhost/' -UseBasicParsing -TimeoutSec 5).StatusCode | Out-Null; exit 0 } catch { exit 1 }"
if errorlevel 1 (
  set /a tries+=1
  if !tries! lss 40 goto wait_app
  echo.
  echo WARNING: App did not respond yet. Check: docker compose -f "%INSTALL_DIR%\docker-compose.yml" logs app
  goto after_wait
)
echo App is responding.
:after_wait

:: --- 7) Desktop shortcuts (Public + logged-in user) ---
echo Creating shortcuts...
call :create_shortcut "%PUBLIC%\Desktop\Todo App.url"
for /f "delims=" %%D in ('powershell -NoProfile -Command "$u=(Get-CimInstance Win32_ComputerSystem).UserName; if($u -and $u -like '*\\*'){ Join-Path ('C:\Users\' + ($u.Split('\\')[-1])) 'Desktop\Todo App.url' }"') do (
  if exist "%%~dpD" call :create_shortcut "%%D"
)

echo.
echo ============================================
echo  DONE
echo  Open "Todo App" on the Desktop
echo  URL: http://localhost
echo ============================================
echo.
echo Tip: In Docker Desktop Settings, enable
echo "Start Docker Desktop when you sign in"
echo.
pause
exit /b 0

:create_shortcut
(
  echo [InternetShortcut]
  echo URL=http://localhost/
) > "%~1"
exit /b 0
