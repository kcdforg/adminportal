# Backend Web Installer

The KCDF API backend includes a browser-based installer for first-time setup. It is designed for XAMPP and similar environments where CLI access is limited.

**Installer URL:** `{api-public-url}/install/`

Examples:
- PHP built-in server: `http://localhost:8080/install/`
- XAMPP: `http://localhost/kcdf-parents/kcdf-api-backend/public/install/`

---

## Prerequisites (manual, before running installer)

These steps cannot be done through the browser:

1. **Composer dependencies** — run once on the server:
   ```bash
   cd kcdf-api-backend
   composer install
   ```
2. **Web server** — document root must point to `kcdf-api-backend/public/`
3. **MySQL** — MySQL 8 must be running (XAMPP: start MySQL in Control Panel)

---

## Installer Steps

The wizard runs four steps:

| Step | What happens |
|---|---|
| 1. Requirements | Checks PHP 8.2+, extensions, `vendor/`, schema file, writable `storage/` |
| 2. Database | Tests connection, creates database, imports `database/schema.sql`, writes `.env` |
| 3. Admin account | Creates `member_profiles`, `user_logins`, and `admins` (super_admin) |
| 4. Complete | Writes `storage/installed.lock` and locks the installer |

---

## What Gets Created

| Output | Location |
|---|---|
| Environment file | `kcdf-api-backend/.env` |
| Database tables | All tables from `database/schema.sql` (22 tables) |
| JWT secret | Auto-generated random 64-char hex string |
| Install lock | `storage/installed.lock` |
| Super admin | First admin user (username/password from form) |

Default `.env` values set by installer:
- `APP_ENV=production`
- `APP_DEBUG=false`
- Database credentials from form
- `CORS_ALLOWED_ORIGINS` from form (default: `http://localhost:4200,http://localhost:8100`)

---

## API Redirect Behaviour

`public/index.php` checks for `storage/installed.lock`:

- **Not installed** → redirects to `/install/`
- **Installed** → boots the Slim API normally

---

## After Installation

1. Open the Admin Portal and log in with the admin account created in step 3.
2. Update `CORS_ALLOWED_ORIGINS` in `.env` if frontend URLs differ.
3. For production:
   - Block or delete `public/install/` directory
   - Ensure `APP_DEBUG=false`
   - Use HTTPS

---

## Reinstalling

To run the installer again:

1. Delete `storage/installed.lock`
2. Delete `.env` (optional — installer overwrites it)
3. Drop the database or tables if you want a clean schema
4. Open `/install/` again

**Warning:** Reinstalling on a database with existing data will run `DROP TABLE` statements from `schema.sql` and destroy all data.

---

## Security Notes

- Installer uses PHP sessions and CSRF tokens on POST forms.
- Once `installed.lock` exists, the wizard shows "Already Installed" (except step 4 success view).
- Do not leave `/install/` accessible on production servers after setup.
- Database password is never displayed in the UI.

---

## Troubleshooting

| Problem | Fix |
|---|---|
| Requirements fail: vendor/ | Run `composer install` |
| Requirements fail: storage not writable | `chmod -R 775 storage/` |
| Database connection failed | Start MySQL; check host/user/password |
| Could not write .env | Ensure project root is writable or create `.env` manually |
| Redirect loop | Ensure `storage/installed.lock` exists after install |
| Schema import error | Check MySQL 8 is running; verify user has CREATE privileges |

---

## Related Documentation

- [System Overview](../00-overview.md)
- [Database Schema](../01-database.md)
- [API Conventions](../02-api-conventions.md)
- Backend README: `kcdf-api-backend/README.md`
