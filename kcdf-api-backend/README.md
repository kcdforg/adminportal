# KCDF Parents — API Backend

Slim Framework 4 REST API for the KCDF Parents platform. Serves both the Parent App and Admin Portal.

**Base URL (dev):** `http://localhost:8080/api/v1`  
**Production:** `https://api.xyz.com/api/v1`

---

## Tech Stack

| Layer | Technology |
|---|---|
| Runtime | PHP 8.2+ |
| Framework | Slim Framework 4 |
| DI | PHP-DI |
| ORM | Illuminate Database (Eloquent) |
| Auth | JWT (firebase/php-jwt) + refresh tokens |
| Database | MySQL 8 |
| Logging | Monolog |

---

## Modules

| Module | Path prefix | Description |
|---|---|---|
| Auth | `/api/v1/auth/*` | Login, logout, refresh, profile |
| Families | `/api/v1/members`, `/families`, `/trainers`, `/admins`, `/entities` | People, families, addresses, roles |
| Academics | `/api/v1/programs`, `/batches`, `/sessions`, `/attendance`, `/enrollments` | Programs, batches, sessions, attendance |
| Payments | `/api/v1/payments` | Fees, donations, refunds |
| Community | `/api/v1/groups`, `/invitations` | Parent groups, invitations |
| Notifications | `/api/v1/notifications`, `/activity-logs` | In-app notifications, audit trail |

Full API specs: [`../docs/02-api-conventions.md`](../docs/02-api-conventions.md) and [`../docs/modules/`](../docs/modules/)

---

## Prerequisites

- PHP 8.2+ with extensions: `pdo_mysql`, `mbstring`, `json`, `openssl`
- Composer 2.x
- MySQL 8 (XAMPP MySQL works)

### macOS + XAMPP

PHP and MySQL are available under XAMPP:

```bash
/Applications/XAMPP/xamppfiles/bin/php -v
```

From the workspace root, load XAMPP into your shell:

```bash
source ../scripts/env-xampp.sh
```

Or use the wrapper script from this folder:

```bash
./scripts/use-xampp-php.sh composer install
```

---

## Installation

### Option A — Web Installer (recommended for XAMPP)

1. Install Composer dependencies (one-time, CLI):
   ```bash
   cd kcdf-api-backend
   composer install
   ```
2. Point your web server document root to `public/` (or open under XAMPP htdocs).
3. Start MySQL (XAMPP Control Panel).
4. Open the installer in your browser:
   ```
   http://localhost:8080/install/          # PHP built-in server
   http://localhost/.../public/install/    # XAMPP
   ```
5. Complete the 4-step wizard: requirements → database → admin account → done.

The installer creates the database, imports `schema.sql`, writes `.env`, and creates the first super admin.

Full details: [`../docs/deployment/backend-installer.md`](../docs/deployment/backend-installer.md)

### Option B — Manual CLI Setup

#### 1. Install dependencies

```bash
cd kcdf-api-backend
composer install
```

#### 2. Environment file

```bash
cp .env.example .env
```

Edit `.env`:

| Variable | Description |
|---|---|
| `DB_HOST` | MySQL host (default `127.0.0.1`) |
| `DB_PORT` | MySQL port (default `3306`) |
| `DB_DATABASE` | Database name (`kcdf_parents`) |
| `DB_USERNAME` | MySQL user (XAMPP default: `root`) |
| `DB_PASSWORD` | MySQL password (XAMPP default: empty) |
| `JWT_SECRET` | Long random string — **change in production** |
| `JWT_ACCESS_TTL` | Access token lifetime in seconds (default `900` = 15 min) |
| `JWT_REFRESH_TTL` | Refresh token lifetime (default `2592000` = 30 days) |
| `CORS_ALLOWED_ORIGINS` | Comma-separated frontend URLs |

**Development CORS example:**

```
CORS_ALLOWED_ORIGINS=http://localhost:4200,http://localhost:8100
```

### 3. Create database and import schema

Start MySQL (XAMPP Control Panel → Start MySQL), then:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS kcdf_parents CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p kcdf_parents < database/schema.sql
```

### 4. Start the development server

```bash
composer start
```

API runs at: **http://localhost:8080/api/v1**

---

## Project Structure

```
kcdf-api-backend/
├── public/index.php          # Web entry point
├── bootstrap/app.php         # Middleware, Eloquent bootstrap
├── config/                   # App, database, DI container
├── database/schema.sql       # Full MySQL schema (22 tables)
├── routes/api.php            # Registers all module routes
├── src/
│   ├── Core/                 # BaseController, BaseRepository
│   ├── Middleware/           # JWT, Role, CORS
│   └── Modules/
│       ├── Auth/
│       ├── Families/
│       ├── Academics/
│       ├── Payments/
│       ├── Community/
│       └── Notifications/
└── storage/logs/             # Application logs
```

Each module contains: `Controllers`, `Services`, `Repositories`, `Models`, `DTOs`, `Validators`, `Policies`, `routes.php`.

---

## Authentication

### Login

```bash
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"your.username","password":"your-password"}'
```

### Use access token

```bash
curl http://localhost:8080/api/v1/auth/me \
  -H "Authorization: Bearer <access_token>"
```

### Refresh token

```bash
curl -X POST http://localhost:8080/api/v1/auth/refresh \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"<refresh_token>"}'
```

Users must exist in `member_profiles` + `user_logins`. Create admin/parent accounts via the API or direct DB inserts during initial setup.

---

## API Response Format

All responses use a standard envelope:

```json
{ "success": true, "data": { ... }, "message": "..." }
```

```json
{ "success": false, "error": { "code": "VALIDATION_FAILED", "message": "...", "details": { ... } } }
```

---

## Production Deployment

1. Point the web server document root to `public/` (not the project root).
2. Set `APP_ENV=production`, `APP_DEBUG=false` in `.env`.
3. Use a strong `JWT_SECRET`.
4. Update `CORS_ALLOWED_ORIGINS` to production frontend URLs:
   - `https://parents.xyz.com`
   - `https://admin.xyz.com`
5. Ensure `storage/logs/` is writable.
6. Run `composer install --no-dev --optimize-autoloader`.

### Apache (XAMPP) example

Place the project under `htdocs` or configure a virtual host with:

```
DocumentRoot "/path/to/kcdf-api-backend/public"
```

Enable `mod_rewrite` and `AllowOverride All`.

---

## Troubleshooting

| Problem | Fix |
|---|---|
| `vendor/autoload.php` not found | Run `composer install` |
| Database connection error | Start MySQL; check `.env` credentials |
| CORS errors from frontend | Add frontend URL to `CORS_ALLOWED_ORIGINS` |
| 401 on protected routes | Check JWT expiry; use refresh endpoint |
| PHP not found | `source ../scripts/env-xampp.sh` (macOS XAMPP) |

---

## Documentation

| Document | Location |
|---|---|
| System overview | `../docs/00-overview.md` |
| **Web installer** | `../docs/deployment/backend-installer.md` |
| Database schema | `../docs/01-database.md` |
| API conventions | `../docs/02-api-conventions.md` |
| Module specs | `../docs/modules/` |
| Phase execution guide | `../PHASE_EXECUTION_GUIDE.md` |

---

## License

Proprietary — KCDF Parents Platform
