# Quick Start Guide

## 🚀 Running Uni-Link

### Backend Setup

1. **Navigate to backend directory:**
   ```bash
   cd "c:\Develope Tools\Github\Uni-Link\backend"
   ```

2. **Ensure .env file exists with database credentials:**
   ```env
   DB_HOST=localhost
   DB_NAME=unilink
   DB_USER=root
   DB_PASS=
   ```

3. **Start PHP server:**
   ```bash
   php -S localhost:80
   ```

   Backend will be available at: `http://localhost/backend/index.php`

### Frontend Setup

1. **Navigate to frontend directory:**
   ```bash
   cd "c:\Develope Tools\Github\Uni-Link\unilink"
   ```

2. **Install dependencies (if not done):**
   ```bash
   npm install
   ```

3. **Start development server:**
   ```bash
   npm run dev
   ```

   Frontend will be available at: `http://localhost:5173`

## ✅ Verify Everything Works

1. Open browser to `http://localhost:5173`
2. Try logging in
3. Check browser console for any errors
4. Check backend terminal for API requests

## 🔍 Testing API Directly

### Test Login (OOP Route)
```bash
curl -X POST http://localhost/backend/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"identifier\":\"test@example.com\",\"password\":\"password\"}"
```

### Test Get Posts (Legacy Route)
```bash
curl http://localhost/backend/api/posts
```

## 📁 Important Files

- **Backend Entry**: `backend/index.php`
- **Frontend API Config**: `unilink/config/api.js`
- **Backend Routes**: `backend/config/routes.php`
- **OOP Controllers**: `backend/app/Controllers/`
- **Legacy Controllers**: `backend/controllers/`

## 🐛 Common Issues

### Issue: CORS Error
**Solution**: Ensure React runs on `http://localhost:5173`

### Issue: Database Connection Failed
**Solution**: Check `.env` file and MySQL service

### Issue: Route Not Found
**Solution**: Check if route exists in `config/routes.php` or legacy route files

### Issue: Session Not Working
**Solution**: Ensure cookies are enabled and `session_start()` is called

## 🎯 Architecture Overview

```
React Frontend (Port 5173)
    ↓ API Calls
Backend index.php (Port 80)
    ↓
Try OOP Routes (config/routes.php)
    ↓ If not found
Fall Back to Legacy Routes (routes/*.php)
    ↓
Return JSON Response
```

## 📚 More Information

- Full documentation: `README.md`
- Implementation details: `brain/walkthrough.md`
- Code examples: `EXAMPLES.php`
