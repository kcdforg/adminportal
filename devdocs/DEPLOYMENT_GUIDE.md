# KCDF Deployment Guide - HestiaCP

This guide covers deploying the KCDF application suite to production using HestiaCP:
- **Parents App**: `parents.kcdfindia.com`
- **Admin App**: `admin.kcdfindia.com`
- **API Backend**: `api.kcdfindia.com`

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Building Applications](#building-applications)
3. [HestiaCP Setup](#hestiacp-setup)
4. [Deploying Applications](#deploying-applications)
5. [SSL/HTTPS Setup](#sslhttps-setup)
6. [Environment Configuration](#environment-configuration)
7. [Post-Deployment](#post-deployment)

---

## Prerequisites

### Local Setup (Your Development Machine)
- Node.js v20+ installed
- npm v11+ installed
- Git installed

### HestiaCP Server
- HestiaCP installed and configured
- SSH access to the server
- Admin access to HestiaCP control panel

### Domains
- `parents.kcdfindia.com` - Pointing to your server IP
- `admin.kcdfindia.com` - Pointing to your server IP
- `api.kcdfindia.com` - Pointing to your server IP

---

## Building Applications

### Build Parents App (Locally)

```bash
cd kcdf-parents-app

# Install dependencies
npm install

# Build for production
npm run build

# Output will be in: dist/kcdf-parents-app/browser/
```

### Build Admin App (Locally)

```bash
cd kcdf-admin-app

# Install dependencies
npm install

# Build for production
npm run build

# Output will be in: dist/kcdf-admin-app/browser/
```

**Note**: Keep these `dist` folders - you'll upload them to the server.

---

## HestiaCP Setup

### Step 1: Create New Domains in HestiaCP

#### For Parents App:

1. Log in to HestiaCP control panel (https://your-ip:8083)
2. Navigate to **Web** → **Add Web Domain**
3. Fill in the details:
   - **Domain**: `parents.kcdfindia.com`
   - **Username**: Select appropriate user (or create new)
   - **Document Root**: Leave as default or customize (e.g., `public_html/parents`)
   - **Proxy Template**: Select `nginx` (or appropriate option)
   - Enable **SSL Support** ✓
   - **Wildcard SSL**: Optional
4. Click **Add**

#### For Admin App:

1. Navigate to **Web** → **Add Web Domain**
2. Fill in the details:
   - **Domain**: `admin.kcdfindia.com`
   - **Username**: Select appropriate user
   - **Document Root**: Leave as default or customize (e.g., `public_html/admin`)
   - Enable **SSL Support** ✓
3. Click **Add**

#### For API Backend:

1. Navigate to **Web** → **Add Web Domain**
2. Fill in the details:
   - **Domain**: `api.kcdfindia.com`
   - **Username**: Select appropriate user
   - **Document Root**: Leave as default (e.g., `public_html/api`)
   - Enable **SSL Support** ✓
3. Click **Add**

---

## Deploying Applications

### Option A: Upload via SFTP/SSH

#### 1. Connect to Server via SFTP

```bash
sftp your-username@your-server-ip
# Enter password when prompted
```

#### 2. Navigate to Web Directory

```bash
cd /home/your-username/public_html/parents
```

#### 3. Upload Parents App Build

```bash
# From your local machine terminal
sftp your-username@your-server-ip

# Navigate to the parents directory
cd /home/your-username/public_html/parents

# Upload all files from your local dist folder
put -r dist/kcdf-parents-app/browser/* .

# Or if it's cleaner, delete existing and upload fresh
rm -r *
put -r dist/kcdf-parents-app/browser/* .

exit
```

#### 4. Repeat for Admin App

```bash
sftp your-username@your-server-ip
cd /home/your-username/public_html/admin
put -r dist/kcdf-admin-app/browser/* .
exit
```

#### 5. Upload API Backend

```bash
sftp your-username@your-server-ip
cd /home/your-username/public_html/api
put -r kcdf-api-backend/* .
exit
```

### Option B: Deploy via Git (Recommended)

On your HestiaCP server:

```bash
ssh your-username@your-server-ip

# Navigate to document root
cd /home/your-username/public_html/parents

# Clone the repository
git clone https://github.com/your-repo/kcdf-parents.git .

# Build Parents App
cd kcdf-parents-app
npm install
npm run build

# Move dist contents to public_html
cp -r dist/kcdf-parents-app/browser/* ../

# Repeat for Admin App
cd ../kcdf-admin-app
npm install
npm run build
cp -r dist/kcdf-admin-app/browser/* ../../admin/

# Repeat for API
cd ../kcdf-api-backend
# Setup PHP dependencies if needed
composer install
cp .env.example .env
# Edit .env with production settings
# Set proper permissions
chmod -R 755 storage logs
```

---

## SSL/HTTPS Setup

### Automatic SSL with Let's Encrypt (HestiaCP)

1. Log in to HestiaCP Control Panel
2. Navigate to **Web** → Select your domain
3. Click the **SSL** button or look for SSL settings
4. Select **Let's Encrypt**
5. Click **Issue**

HestiaCP will automatically:
- Generate SSL certificates for all three domains
- Configure auto-renewal
- Redirect HTTP to HTTPS

**Repeat for all three domains**: parents.kcdfindia.com, admin.kcdfindia.com, api.kcdfindia.com

---

## Environment Configuration

### Configure Parents App

After uploading, check if environment files need to be set:

```bash
# SSH into server
ssh your-username@your-server-ip

# Navigate to parents app
cd /home/your-username/public_html/parents

# If there's an environment file
cat > .env << EOF
# Configure based on your setup
VITE_API_URL=https://api.kcdfindia.com
EOF
```

### Configure Admin App

```bash
cd /home/your-username/public_html/admin

# Check for environment configuration
cat > .env << EOF
VITE_API_URL=https://api.kcdfindia.com
EOF
```

### Configure API Backend

```bash
cd /home/your-username/public_html/api/kcdf-api-backend

# Copy environment template
cp .env.example .env

# Edit with production values
nano .env
```

Edit `.env` with:
- Database credentials
- API base URL
- Mail settings
- Other production configurations

---

## Post-Deployment Verification

### Checklist

1. **Test HTTPS Access**
   - Visit https://parents.kcdfindia.com
   - Visit https://admin.kcdfindia.com
   - Visit https://api.kcdfindia.com
   - All should show secure (green lock)

2. **Verify Applications Load**
   - Parents app should display correctly
   - Admin app should display correctly
   - Check browser console for errors (F12)

3. **Test API Connectivity**
   - Try logging in or making API calls
   - Check network tab in browser DevTools
   - Verify API responses are correct

4. **Check File Permissions**
   ```bash
   # SSH into server
   ssh your-username@your-server-ip
   
   # Verify permissions
   ls -la /home/your-username/public_html/
   ```

5. **Monitor Logs**
   - HestiaCP: Check domain logs via control panel
   - Or via SSH: `tail -f /var/log/hestia/web.log`

---

## Troubleshooting

### Issue: "Angular app not loading / Blank page"

**Solution:**
1. Check browser console (F12 → Console tab) for errors
2. Verify build was successful (`npm run build`)
3. Check that all files were uploaded to public_html
4. Clear browser cache (Ctrl+Shift+Delete)
5. Try `cd` to app directory and rebuild:
   ```bash
   npm run build
   ```

### Issue: "Cannot find module / 404 errors"

**Possible Causes:**
- Files not fully uploaded
- Wrong directory path

**Solution:**
1. SSH to server and verify files exist:
   ```bash
   ls -la /home/your-username/public_html/parents/
   ```
2. Re-upload if necessary:
   ```bash
   sftp your-username@your-server-ip
   cd /home/your-username/public_html/parents
   put -r dist/kcdf-parents-app/browser/* .
   ```

### Issue: "API not responding / CORS errors"

**Solution:**
1. Verify API domain is accessible: `https://api.kcdfindia.com`
2. Check API backend permissions:
   ```bash
   chmod -R 755 /home/your-username/public_html/api/storage
   chmod -R 755 /home/your-username/public_html/api/logs
   ```
3. Check `.env` configuration on API
4. Review HestiaCP logs for PHP errors

### Issue: "SSL certificate not working"

**Solution:**
1. In HestiaCP, go to Web → Domain → SSL
2. Click "Issue" or "Renew"
3. Wait a few minutes for Let's Encrypt to process
4. Test in browser again

### Issue: "Cannot connect via SFTP"

**Solution:**
1. Ensure SSH access is enabled in HestiaCP
2. Check credentials are correct
3. Try using port 22 explicitly:
   ```bash
   sftp -P 22 your-username@your-server-ip
   ```
4. Use SCP as alternative:
   ```bash
   scp -r dist/kcdf-parents-app/browser/* your-username@your-server-ip:/home/your-username/public_html/parents/
   ```

---

## Updating Applications

### To Deploy Updates:

```bash
# Locally: rebuild
cd kcdf-parents-app
npm run build

# Upload the new dist folder
sftp your-username@your-server-ip
cd /home/your-username/public_html/parents
rm -r *
put -r dist/kcdf-parents-app/browser/* .
exit

# If using Git on server:
ssh your-username@your-server-ip
cd /home/your-username/public_html/parents/kcdf-parents-app
git pull origin main
npm run build
cp -r dist/kcdf-parents-app/browser/* ../
```

---

## Security Best Practices

1. **Environment Variables**: Keep `.env` files secure, never commit to git
2. **File Permissions**: Use appropriate permissions (755 for directories, 644 for files)
3. **Database**: Use strong passwords, limit access
4. **Backups**: Regular backups through HestiaCP
5. **Updates**: Keep software updated through HestiaCP
6. **HTTPS**: Always use HTTPS (should be automatic with Let's Encrypt)

---

## Quick Reference Commands

```bash
# SSH to server
ssh your-username@your-server-ip

# SFTP to server
sftp your-username@your-server-ip

# Build locally
npm run build

# Copy files to server (from local)
scp -r dist/folder/* user@server:/path/to/public_html/

# Set permissions on server
chmod -R 755 /path/to/public_html/parents
chmod -R 644 /path/to/public_html/parents/*

# View server logs
ssh user@server tail -f /var/log/hestia/web.log
```

---

## Support Resources

- HestiaCP Docs: https://docs.hestiacp.com/
- Angular Deployment: https://angular.io/guide/deployment
- Let's Encrypt: https://letsencrypt.org/
- Ionic Docs: https://ionicframework.com/docs/angular/deployment
