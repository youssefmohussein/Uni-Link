# Backend Server Setup Guide

## Problem

The backend is returning 404 errors because the PHP server isn't running or isn't accessible at `http://localhost/backend/`.

## Solutions

You have **three options** to run the backend:

---

### Option 1: Use XAMPP (Recommended for Windows)

**If you have XAMPP installed:**

1. **Move the backend folder** to XAMPP's htdocs:
   ```bash
   # Copy the backend folder to:
   C:\xampp\htdocs\backend
   ```

2. **Start XAMPP**:
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

3. **Access backend**:
   - Backend will be at: `http://localhost/backend/`
   - Frontend can access it without changes

**If you don't have XAMPP:**
- Download from: https://www.apachefriends.org/
- Install and follow steps above

---

### Option 2: Use PHP Built-in Server (Quick Test)

**For quick testing without XAMPP:**

1. **Find your PHP installation**:
   ```powershell
   # Common locations:
   C:\xampp\php\php.exe
   C:\wamp64\bin\php\php8.x.x\php.exe
   C:\php\php.exe
   ```

2. **Start the server**:
   ```powershell
   cd "C:\Develope Tools\Github\Uni-Link\backend"
   
   # Using XAMPP PHP:
   C:\xampp\php\php.exe -S localhost:80 -t .
   
   # Or use port 8000 (no admin rights needed):
   C:\xampp\php\php.exe -S localhost:8000 -t .
   ```

3. **Update frontend API URL** (if using port 8000):
   
   Edit `unilink/src/handlers/authHandler.js`:
   ```javascript
   // Change line 1 from:
   const API_BASE_URL = 'http://localhost/backend';
   
   // To:
   const API_BASE_URL = 'http://localhost:8000';
   ```

---

### Option 3: Use the Start Script

**I've created a start script for you:**

1. **Edit `start-server.bat`** to point to your PHP:
   ```batch
   REM Change this line:
   php -S localhost:80 -t .
   
   REM To (using your PHP path):
   C:\xampp\php\php.exe -S localhost:80 -t .
   ```

2. **Run the script**:
   ```powershell
   cd "C:\Develope Tools\Github\Uni-Link\backend"
   .\start-server.bat
   ```

---

## Quick Fix: Update Frontend API URL

**If you want to use PHP built-in server on port 8000:**

1. **Start PHP server**:
   ```powershell
   cd "C:\Develope Tools\Github\Uni-Link\backend"
   C:\xampp\php\php.exe -S localhost:8000 -t .
   ```

2. **Update frontend** to use port 8000:

I can update the frontend API URL for you. Just let me know if you want to:
- **A**: Use XAMPP on port 80 (recommended)
- **B**: Use PHP built-in server on port 8000 (I'll update the frontend)

---

## Verification

Once the server is running, test it:

```bash
# Test health endpoint:
curl http://localhost/backend/health
# OR (if using port 8000):
curl http://localhost:8000/health

# Expected response:
{
  "status": "success",
  "data": {
    "database": "connected",
    "timestamp": "...",
    "php_version": "..."
  }
}
```

---

## Current Issue

The error `POST http://localhost/backend/login 404 (Not Found)` means:
- ❌ No web server is running on port 80
- ❌ OR the backend folder isn't accessible at `/backend/`

**You need to start a PHP server first!**
