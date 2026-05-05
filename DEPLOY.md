# Uni-Link — Linux Server Deployment Guide

> **Zero-config on the server.** Everything is auto-detecting. Just follow these steps once.

---

## Server Requirements
- Apache 2.4+ with `mod_rewrite` enabled
- PHP 8.0+
- MySQL / MariaDB
- (No Node.js needed on the server — you build locally)

---

## Step 1 — Set DB credentials (do this locally, before uploading)

Edit `backend/.env`:
```env
DB_HOST=localhost
DB_NAME=unilink
DB_USER=your_mysql_user
DB_PASS=your_mysql_password
```

---

## Step 2 — Build the frontend (run this on your local machine)

```bash
cd unilink
npm install
npm run build
```

This creates a `unilink/dist/` folder — the compiled frontend.

---

## Step 3 — Upload to the server

Upload these two folders to your Apache web root (e.g. `/var/www/html/`):

```
/var/www/html/
├── index.html          ← from unilink/dist/
├── assets/             ← from unilink/dist/assets/
├── .htaccess           ← from unilink/public/.htaccess  (auto-copied by Vite build)
└── backend/            ← the entire backend/ folder
    ├── index.php
    ├── .env
    ├── .htaccess
    └── ...
```

> **SCP example:**
> ```bash
> scp -r unilink/dist/* user@your-server:/var/www/html/
> scp -r backend/       user@your-server:/var/www/html/backend/
> ```

---

## Step 4 — Import the database

```bash
mysql -u your_user -p unilink < unilink.sql
```

Or use phpMyAdmin to import `unilink.sql`.

---

## Step 5 — Enable Apache mod_rewrite (one-time server setup)

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Make sure your Apache VirtualHost has `AllowOverride All`:
```apache
<Directory /var/www/html>
    AllowOverride All
    Require all granted
</Directory>
```

---

## How the URL auto-detection works

| Environment | How backend URL is determined |
|---|---|
| `npm run dev` (local) | `.env.development.local` → `VITE_API_BASE_URL=http://localhost:8000` |
| `npm run build` → upload | `window.location.origin + '/backend'` — auto-detected at runtime in the browser |

**Examples on the server:**
- Server IP `192.168.1.10` → backend fetched at `http://192.168.1.10/backend`
- Domain `https://uni-link.edu` → backend fetched at `https://uni-link.edu/backend`

No env vars, no file editing on the server. ✅

---

## Troubleshooting

| Problem | Fix |
|---|---|
| API calls return 404 | Check Apache `AllowOverride All` + `a2enmod rewrite` |
| React routes give 404 on refresh | The frontend `.htaccess` wasn't copied — check `unilink/dist/.htaccess` exists |
| DB connection error | Check `backend/.env` credentials match your MySQL server |
| CORS errors | Both frontend and backend must be on the same domain (`/backend` subfolder) |
