# KCDF Backend Deployment on HestiaCP

This guide walks you through deploying the KCDF API backend on a HestiaCP VPS.

**Prerequisites:**
- HestiaCP installed and configured
- SSH access to your VPS
- Domain/subdomain ready (e.g., `api.yourdomain.com`)
- Git repository access configured

---

## Step 1: Add Domain in HestiaCP

1. Log in to HestiaCP control panel
2. Navigate to **Web → Add Web Domain**
3. Fill in:
   - **Domain:** `api.yourdomain.com`
   - **IP:** Select your VPS IP
   - **PHP version:** 8.2 (ensure it's available)
   - **SSL:** Enable Let's Encrypt
   - **SSL auto-renew:** Yes
4. Click **Add** (this creates the web folder and SSL certificate)

---

## Step 2: SSH into Your VPS

```bash
ssh admin@your-vps-ip
```

Or if using a specific username:

```bash
ssh username@your-vps-ip
```

---

## Step 3: Navigate to the Web Folder

HestiaCP creates domains under `/home/{username}/public_html/{domain_name}/`.

```bash
cd /home/your-username/public_html/api.yourdomain.com
```

Or find the exact path:

```bash
ls -la /home/*/public_html/ | grep api
```

---

## Step 4: Clone the Repository

```bash
# Navigate to parent directory (one level up from api.yourdomain.com)
cd /home/your-username/public_html

# Clone the entire project
git clone git@github.com:kcdforg/adminportal.git .

# OR clone only into the api folder (if you prefer)
git clone git@github.com:kcdforg/adminportal.git api-temp
mv api-temp/kcdf-api-backend/* api.yourdomain.com/
rm -rf api-temp
```

---

## Step 5: Install Composer Dependencies

```bash
cd /home/your-username/public_html/api.yourdomain.com

# Install PHP 8.2 specifically (HestiaCP may have multiple PHP versions)
/usr/bin/php8.2 -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
/usr/bin/php8.2 composer-setup.php
/usr/bin/php8.2 composer.phar install --no-dev --optimize-autoloader

# Or if composer is globally installed
composer install --no-dev --optimize-autoloader
```

Check PHP version:

```bash
php -v
# If wrong version, use explicit path:
/usr/bin/php8.2 --version
```

---

## Step 6: Configure Web Server Document Root

HestiaCP uses Apache by default. We need to point the document root to `public/`.

### Via HestiaCP UI (Easier):

1. Go to **Web → Edit Web Domain** (your api domain)
2. Under "Advanced Options," you may find a **Document Root** or **Public HTML Folder** setting
3. Change it to `public/` (relative to domain folder) or absolute path:
   ```
   /home/your-username/public_html/api.yourdomain.com/public
   ```
4. Save and restart Apache

### Via SSH (Manual):

Edit the Apache VirtualHost config:

```bash
sudo nano /etc/apache2/sites-available/api.yourdomain.com.conf
```

Find the `DocumentRoot` line and update it:

```apache
DocumentRoot /home/your-username/public_html/api.yourdomain.com/public
```

Then restart Apache:

```bash
sudo systemctl restart apache2
```

### HestiaCP Alternative — Using `.htaccess` Redirect

If you cannot change the DocumentRoot through HestiaCP:

1. Create `/home/your-username/public_html/api.yourdomain.com/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

2. Verify `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## Step 7: Set File Permissions

```bash
cd /home/your-username/public_html/api.yourdomain.com

# Make storage writable
chmod -R 775 storage
chmod -R 775 public

# Set proper ownership (HestiaCP user)
sudo chown -R your-username:www-data storage
sudo chown -R your-username:www-data public
sudo chown -R your-username:www-data bootstrap
```

---

## Step 8: MySQL Database Setup

### Option A: Via HestiaCP UI

1. Log in to HestiaCP
2. Go to **DB → Add Database**
3. Fill in:
   - **Database name:** `kcdf_parents`
   - **User:** Create new (e.g., `kcdf_user`)
   - **Password:** Generate strong password
4. Click **Add**
5. Save the credentials

### Option B: Via SSH/CLI

```bash
# If MySQL is running as your user
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS kcdf_parents CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Create a dedicated user
mysql -u root -p -e "CREATE USER 'kcdf_user'@'localhost' IDENTIFIED BY 'strong_password_here';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON kcdf_parents.* TO 'kcdf_user'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"

# Test connection
mysql -u kcdf_user -p kcdf_parents -e "SELECT 1;"
```

---

## Step 9: Import Database Schema

```bash
cd /home/your-username/public_html/api.yourdomain.com

# Navigate to where schema is
mysql -u kcdf_user -p kcdf_parents < database/schema.sql
```

Or if you're in the parent directory:

```bash
mysql -u kcdf_user -p kcdf_parents < kcdf-api-backend/database/schema.sql
```

Verify import:

```bash
mysql -u kcdf_user -p kcdf_parents -e "SHOW TABLES;"
```

---

## Step 10: Create `.env` File

```bash
cd /home/your-username/public_html/api.yourdomain.com

# Copy example to actual
cp .env.example .env

# Edit with your credentials
nano .env
```

**Key `.env` settings for production:**

```env
# App
APP_ENV=production
APP_DEBUG=false

# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kcdf_parents
DB_USERNAME=kcdf_user
DB_PASSWORD=your_strong_password_here

# JWT (generate a strong random string!)
JWT_SECRET=your_very_long_random_string_64_chars_minimum
JWT_ACCESS_TTL=900
JWT_REFRESH_TTL=2592000

# CORS (update with your frontend domains)
CORS_ALLOWED_ORIGINS=https://parents.yourdomain.com,https://admin.yourdomain.com

# Logging
APP_LOG_LEVEL=error
```

Generate a secure JWT secret:

```bash
openssl rand -hex 32
```

Copy the output (32 hex characters) into `JWT_SECRET`.

---

## Step 11: Enable Apache Modules

The backend requires URL rewriting for the Slim framework router.

```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

Verify `.htaccess` in `public/` directory. It should already exist in the repo:

```bash
cat /home/your-username/public_html/api.yourdomain.com/public/.htaccess
```

Expected content:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^ index.php [QSA,L]
</IfModule>
```

---

## Step 12: Verify Installation

### Test API connectivity:

```bash
curl https://api.yourdomain.com/api/v1/health

# Or via SSH on the server
curl http://localhost/api/v1/health
```

Expected response (if endpoint exists):

```json
{"success": true, "data": {"status": "ok"}}
```

### If not working, check logs:

```bash
tail -f /home/your-username/public_html/api.yourdomain.com/storage/logs/app.log
```

### Check Apache error log:

```bash
sudo tail -f /var/log/apache2/error.log
```

---

## Step 13: Run Web Installer (Recommended)

The backend includes a browser-based installer for security and ease. Access it:

```
https://api.yourdomain.com/install/
```

**Installer will:**
1. Check PHP version, extensions, and file permissions
2. Test database connection and create tables
3. Create a super admin account
4. Generate and lock the installer

**After installer completes:**
- The installer is automatically locked (`storage/installed.lock` created)
- You cannot run the installer again (delete `installed.lock` to re-run)
- Do NOT make this accessible in production

---

## Step 14: Create First Admin Account (If Not Using Installer)

If you skipped the installer, create the admin manually:

```bash
cd /home/your-username/public_html/api.yourdomain.com

# Use PHP CLI
php -r "
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\\Modules\\Families\\Models\\MemberProfile;
use App\\Modules\\Auth\\Models\\UserLogin;
use App\\Modules\\Families\\Models\\Admin;

// Create member profile
\$profile = MemberProfile::create([
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@yourdomain.com',
    'phone_number' => '1234567890',
    'status' => 'active'
]);

// Create user login
\$user = UserLogin::create([
    'profile_id' => \$profile->id,
    'username' => 'admin',
    'password' => password_hash('secure_password_here', PASSWORD_BCRYPT),
    'user_type' => 'admin',
    'status' => 'active'
]);

// Create admin role
Admin::create([
    'profile_id' => \$profile->id,
    'role' => 'super_admin',
    'status' => 'active'
]);

echo 'Admin account created!';
"
```

---

## Step 15: Set Up HTTPS and SSL

HestiaCP's Let's Encrypt integration typically handles this automatically, but verify:

```bash
# Check if SSL is installed
ls -la /etc/ssl/certs/ | grep yourdomain
```

If using Let's Encrypt (recommended):

1. In HestiaCP Web Domain settings, ensure **SSL** is enabled
2. Check auto-renewal is set to **Yes**

For manual renewal (if needed):

```bash
sudo certbot renew --dry-run  # Test
sudo certbot renew             # Renew all certificates
sudo systemctl restart apache2
```

---

## Step 16: Configure Firewall (If Applicable)

Ensure HTTP/HTTPS traffic is allowed:

```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## Post-Deployment Checklist

- [ ] Domain resolves to your VPS IP
- [ ] HTTPS certificate is valid (green padlock in browser)
- [ ] API endpoint responds: `https://api.yourdomain.com/api/v1/health`
- [ ] Database tables created (`SHOW TABLES;` returns 22 tables)
- [ ] Super admin account created
- [ ] `.env` file is not publicly accessible (should be above `public/`)
- [ ] `storage/logs/` directory is writable
- [ ] `CORS_ALLOWED_ORIGINS` points to your frontend URLs
- [ ] SSL auto-renewal is configured

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| 404 on API endpoints | Check `.htaccess` in `public/`; verify `DocumentRoot` points to `public/` |
| Database connection error | Verify `.env` credentials; check MySQL is running |
| Permission denied on storage/ | `chmod -R 775 storage/` and `chown -R user:www-data storage/` |
| CORS errors from frontend | Update `CORS_ALLOWED_ORIGINS` in `.env` |
| 500 Internal Server Error | Check `storage/logs/app.log` for PHP errors |
| Composer install fails | Ensure PHP 8.2+ is used; check `php -v` |
| SSL not working | Verify certificates exist in `/etc/ssl/`; restart Apache |

---

## Production Security Hardening

1. **Disable the installer in production:**
   ```bash
   sudo rm -rf /home/your-username/public_html/api.yourdomain.com/public/install/
   ```

2. **Restrict `.env` access:**
   ```bash
   chmod 600 /home/your-username/public_html/api.yourdomain.com/.env
   ```

3. **Ensure logs are not publicly accessible:**
   ```bash
   # Add to public/.htaccess
   <FilesMatch "\.log$">
       Deny from all
   </FilesMatch>
   ```

4. **Update JWT secret** — generate a new one in production

5. **Enable HTTP security headers** in Apache:
   ```apache
   Header set X-Content-Type-Options "nosniff"
   Header set X-Frame-Options "DENY"
   Header set X-XSS-Protection "1; mode=block"
   Header set Referrer-Policy "strict-origin-when-cross-origin"
   ```

6. **Backup database regularly:**
   ```bash
   mysqldump -u kcdf_user -p kcdf_parents > backup-$(date +%Y%m%d).sql
   ```

---

## Related Documentation

- [Backend README](../../kcdf-api-backend/README.md)
- [Database Schema](../01-database.md)
- [API Conventions](../02-api-conventions.md)
- [Web Installer Guide](./backend-installer.md)
