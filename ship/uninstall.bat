@echo off
setlocal EnableExtensions
cd /d "%~dp0"

set "INSTALL_DIR=%LOCALAPPDATA%\TodoApp"

echo ============================================
echo  Todo App uninstall
echo ============================================
echo.
echo This stops the app. Your todos stay in Docker unless you choose to delete them.
echo.

if not exist "%INSTALL_DIR%\docker-compose.yml" (
  echo Nothing to uninstall at %INSTALL_DIR%
  pause
  exit /b 0
)

where docker >nul 2>&1
if not errorlevel 1 (
  cd /d "%INSTALL_DIR%"
  docker compose -p todoapp down
)

echo.
set /p WIPE=Delete saved todos too? [y/N]: 
if /I "%WIPE%"=="y" (
  cd /d "%INSTALL_DIR%"
  docker compose -p todoapp down -v
  echo Volumes removed.
)

echo Uninstall complete.
pause
endlocal
