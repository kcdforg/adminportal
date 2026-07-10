# Admin App Troubleshooting Guide

## NG0908 Error - Angular Initialization Error

If you're seeing this error in the browser console after deploying the admin app:

```
NG0908
    at new e (chunk-LDX32CDG.js:4:17503)
    at Object.ngZoneFactory [as useFactory] (chunk-LDX32CDG.js:4:127067)
    ...
```

### Root Causes & Solutions

#### 1. **Missing or Incorrect index.html**
The app requires proper routing to serve index.html for all routes.

**Fix:**
- Ensure the `.htaccess` file is in the public folder
- The `.htaccess` file is included in the built distribution at: `dist/kcdf-admin-app/browser/.htaccess`
- Copy it to the web root when deploying

#### 2. **Application Not Initializing Due to Storage Access**
The app accesses localStorage during initialization.

**Fix:**
- The app now handles storage access safely with try-catch blocks
- localStorage is accessed lazily only when needed
- No issues should occur even if localStorage is unavailable

#### 3. **Zone.js Not Loading Properly**
Angular needs Zone.js for change detection.

**Fix:**
- Ensure all JavaScript bundles are loading correctly
- Check browser DevTools Network tab for failed script loads
- All chunks should have a 200 status code

#### 4. **CORS or API Configuration Issues**
The app might fail to initialize if API configuration is wrong.

**Fix:**
- Verify API URL is correct: `https://api.kcdfindia.com/api/v1`
- Environment file should have: 
  ```typescript
  apiUrl: 'https://api.kcdfindia.com/api/v1'
  ```

### Debugging Steps

1. **Open Browser DevTools (F12)**
   - Go to Console tab
   - Look for any JavaScript errors before the NG0908 error
   - Check Network tab to see if all files loaded successfully

2. **Check Browser Network Tab**
   - All `.js` files should have status 200
   - CSS files should have status 200
   - HTML should have status 200
   - If any 404s appear, files weren't deployed correctly

3. **Clear Browser Cache**
   ```
   Ctrl+Shift+Delete (Windows)
   Cmd+Shift+Delete (Mac)
   ```
   Then try loading the app again

4. **Check HestiaCP Logs**
   - Log into HestiaCP
   - Check Web domain logs for errors
   - Look for 404 or 500 errors

5. **Verify File Permissions**
   ```bash
   # SSH into server
   ssh user@server
   
   # Check permissions
   ls -la /home/user/public_html/admin/
   
   # Should see files like:
   # -rw-r--r--   1 user  group   8890 Jul 10 13:33 main-ZR3QYX7H.js
   # -rw-r--r--   1 user  group  17175 Jul 10 13:33 index.html
   # -rw-r--r--   1 user  group   .htaccess
   ```

### Deployment Checklist

Before deploying, verify:

- [ ] Built with: `npm run build`
- [ ] Output directory: `dist/kcdf-admin-app/browser/`
- [ ] `.htaccess` file is in the directory
- [ ] All files copied to `/home/user/public_html/admin/`
- [ ] File permissions are correct (755 for folders, 644 for files)
- [ ] SSL certificate is valid (green lock in browser)
- [ ] Environment file has correct API URL
- [ ] Network tab shows all files with 200 status
- [ ] Console shows no JavaScript errors

### Deployment Steps (HestiaCP)

1. **Build locally**
   ```bash
   cd /Users/apple/development/php/kcdf-parents/kcdf-admin-app
   npm run build
   ```

2. **Upload to server**
   ```bash
   # Option 1: SFTP
   sftp user@server
   cd /home/user/public_html/admin
   rm -r *  # Remove old files
   put -r dist/kcdf-admin-app/browser/* .
   exit
   
   # Option 2: SCP (from local machine)
   scp -r dist/kcdf-admin-app/browser/* user@server:/home/user/public_html/admin/
   ```

3. **Verify on Server**
   ```bash
   ssh user@server
   ls -la /home/user/public_html/admin/
   # Should show index.html, chunks, styles, .htaccess, etc.
   ```

4. **Test in Browser**
   - Visit `https://admin.kcdfindia.com`
   - Open DevTools (F12)
   - Check Console tab for errors
   - Check Network tab to see if all files loaded

### API Configuration Verification

The admin app should make API calls to: `https://api.kcdfindia.com/api/v1`

**To verify:**
1. Open DevTools → Network tab
2. Perform an action (like login)
3. Look for requests to `/auth/login` or similar
4. Should see requests to `https://api.kcdfindia.com/api/v1/auth/login`

If requests fail with CORS errors:
- Check API server is running
- Check API domain is accessible
- Verify API CORS settings allow the admin domain

### Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| Blank white page | Files not deployed | Redeploy all files from dist/kcdf-admin-app/browser/ |
| 404 errors in Network tab | .htaccess not deployed | Copy .htaccess from dist folder |
| CORS errors | API not accessible | Check API server and domain |
| Slow loading | Large bundles | Check Network tab for slow requests |
| Page refreshes on login | Auth interceptor issue | Check browser console for errors |

### Performance Tips

- All CSS and JS are cached for 1 year (via .htaccess)
- Gzip compression is enabled
- Bundles are split for lazy loading
- Initial bundle size: ~126 KB (compressed)

### Support

If issues persist:
1. Check all files are in the correct directory
2. Verify .htaccess is present and has correct content
3. Clear browser cache completely
4. Try a different browser
5. Check HestiaCP server logs
6. Ensure SSL certificate is valid
