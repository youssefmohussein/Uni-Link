@echo off
REM Start PHP Built-in Server for Uni-Link Backend

set PHP_BIN=php
set PORT=8000

echo ========================================
echo Starting Uni-Link Backend Server
echo ========================================
echo.

REM Check if PHP is in PATH
where %PHP_BIN% >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    REM Try common XAMPP path
    if exist "C:\xampp\php\php.exe" (
        set PHP_BIN="C:\xampp\php\php.exe"
        echo Found PHP in XAMPP directory.
    ) else (
        echo ERROR: PHP not found in PATH or C:\xampp\php\
        echo.
        echo Please add PHP to your PATH or install XAMPP.
        pause
        exit /b 1
    )
)

echo Starting PHP server on http://localhost:%PORT%
echo Backend will be accessible at: http://localhost:%PORT%/
echo.
echo Press Ctrl+C to stop the server
echo.

REM Using index.php as the router script for the built-in server
%PHP_BIN% -S localhost:%PORT% index.php

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo Failed to start server on port %PORT%. 
    echo Trying port 8080...
    set PORT=8080
    %PHP_BIN% -S localhost:8080 index.php
)

pause
