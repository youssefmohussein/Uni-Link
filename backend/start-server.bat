@echo off
REM Start PHP Built-in Server for Uni-Link Backend
REM This script starts the PHP development server on port 80

echo ========================================
echo Starting Uni-Link Backend Server
echo ========================================
echo.

REM Check if PHP is in PATH
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: PHP not found in PATH
    echo.
    echo Please either:
    echo 1. Add PHP to your PATH environment variable
    echo 2. Use XAMPP/WAMP and access via http://localhost/backend/
    echo 3. Edit this script to point to your PHP installation
    echo.
    echo Example for XAMPP:
    echo   C:\xampp\php\php.exe -S localhost:80 -t .
    echo.
    pause
    exit /b 1
)

echo Starting PHP server on http://localhost:80
echo Backend will be accessible at: http://localhost/backend/
echo.
echo Press Ctrl+C to stop the server
echo.

REM Start PHP built-in server
REM Note: Running on port 80 requires administrator privileges
REM If you get a permission error, try port 8000 instead
php -S localhost:80 -t .

pause
