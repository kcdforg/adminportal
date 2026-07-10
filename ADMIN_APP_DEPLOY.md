# Admin App Deployment Checklist & Instructions

## Quick Deploy Summary

After fixing the NG0908 initialization error, here's how to deploy the admin app:

### Pre-Deployment (Local Machine)

- [x] Build completed: `npm run build` ✅
- [x] Output location: `/Users/apple/development/php/kcdf-parents/kcdf-admin-app/dist/kcdf-admin-app/browser/`
- [x] `.htaccess` file created for routing
- [x] API endpoint configured: `https://api.kcdfindia.com/api/v1`
- [x] localStorage access fixed (safe lazy initialization)
- [x] Dependency injection fixed in error interceptor

### Files Ready to Deploy

The following files should be in your local build output:
```
dist/kcdf-admin-app/browser/
├── index.html
├── main-*.js
├── chunk-*.js
├── styles-*.css
├── .htaccess                    ← NEW: Routing configuration
├── favicon.ico
└── 3rdpartylicenses.txt
```

### HestiaCP Deployment Steps

#### Step 1: Prepare on HestiaCP (One-time setup)

1. Log into HestiaCP: `https://your-server-ip:8083`
2. Go to **Web** → **Add Web Domain**
3. Enter:
   - Domain: `admin.kcdfindia.com`
   - Enable SSL Support ✓
4. Click **Add**
5. In HestiaCP, click **SSL** → **Let's Encrypt** → **Issue**

#### Step 2: Upload Files (Repeat for updates)

**Option A: Using SFTP**

```bash
# On your local machine
cd /Users/apple/development/php/kcdf-parents/kcdf-admin-app/dist/kcdf-admin-app/browser

# Connect to server
sftp your-username@your-server-ip

# Navigate to admin folder
cd /home/your-username/public_html/admin

# Remove old files
rm -r *

# Upload new files
put -r ./* .

# Exit
exit
```

**Option B: Using SCP (Simpler)**

```bash
# From local machine, in the project directory
scp -r kcdf-admin-app/dist/kcdf-admin-app/browser/* your-username@your-server-ip:/home/your-username/public_html/admin/
```

#### Step 3: Verify Permissions on Server

```bash
# SSH to server
ssh your-username@your-server-ip

# Check files are there
ls -la /home/your-username/public_html/admin/

# You should see:
# -rw-r--r--   ...   index.html
# -rw-r--r--   ...   main-*.js
# -rw-r--r--   ...   .htaccess
# etc.

# Set correct permissions (if needed)
chmod 755 /home/your-username/public_html/admin
chmod 644 /home/your-username/public_html/admin/*
chmod 644 /home/your-username/public_html/admin/.htaccess
```

#### Step 4: Test in Browser

1. Visit: `https://admin.kcdfindia.com`
2. Open DevTools: **F12** → **Console** tab
3. Should NOT see NG0908 error anymore
4. Should see app loading
5. Try logging in to verify API connectivity

### Verification Checklist

After deploying, verify:

- [ ] Page loads at `https://admin.kcdfindia.com`
- [ ] No NG0908 error in console (F12)
- [ ] SSL shows green lock
- [ ] All network requests show 200 status (F12 → Network)
- [ ] App UI displays correctly
- [ ] Can navigate pages
- [ ] Can attempt login (test API connectivity)
- [ ] No CORS errors in console

### What Was Fixed

1. **NG0908 Error**: Fixed by:
   - Improving error interceptor dependency injection
   - Lazy-loading localStorage access instead of module-level access
   - Adding safe localStorage access with try-catch blocks
   - Better error handling in main.ts

2. **Routing**: Added `.htaccess` for:
   - Proper Angular SPA routing
   - Gzip compression
   - Cache control for assets
   - Security headers

3. **API Configuration**: Updated to use:
   - Production: `https://api.kcdfindia.com/api/v1`
   - Development: `http://localhost:8080/api/v1`

### Troubleshooting

If you still see errors after deployment:

1. **Clear browser cache**
   - Ctrl+Shift+Delete → Clear all
   - Try incognito/private window

2. **Verify `.htaccess` is present**
   ```bash
   ls -la /home/your-username/public_html/admin/.htaccess
   # Should show the file exists
   ```

3. **Check HestiaCP logs**
   - HestiaCP Dashboard → Web → Domain → Logs

4. **Verify all files uploaded**
   ```bash
   ls /home/your-username/public_html/admin/ | wc -l
   # Should show ~80+ files
   ```

5. **Check API connectivity**
   - Open console (F12)
   - Go to Network tab
   - Try to login
   - Should see requests to `https://api.kcdfindia.com/api/v1/auth/login`

### Next Steps

After admin app is deployed:

1. Deploy parents app to `parents.kcdfindia.com`
2. Deploy API backend to `api.kcdfindia.com`
3. Test both apps together
4. Monitor logs for issues

### Build File Info

Built: July 10, 2026, 13:33 UTC
- Main bundle: `main-ZR3QYX7H.js` (8.89 KB)
- Initial total: 493.92 KB (126.42 KB compressed)
- Lazy chunks: 47+ files for code splitting
- Build time: 18.3 seconds

### Important Notes

- `.htaccess` is critical for routing - must be in the web root
- All files must be readable by the web server
- SSL certificate must be valid (Let's Encrypt recommended)
- API backend must be running and accessible
- localStorage is required for authentication tokens
