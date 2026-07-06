# KCDF Parents

Educational program registration system with two **self-contained** applications:

| Application | Stack | Port | Purpose |
|-------------|-------|------|---------|
| **admin/** | Slim 4 + Bootstrap 5 + MySQL | 8001 | Admin console (programs, users, reports) |
| **mainapp/** | Slim 4 API + Ionic/Angular + MySQL | 8002 / 8100 | Parent mobile/web app |

No shared code between applications. Each has its own database.

## Quick Start

### 1. Admin Console

```bash
cd admin
composer install
cp .env.example .env
composer migrate
composer seed
composer start
```

Open http://localhost:8001 — `admin@kcdf.org` / `Admin@12345`

### 2. Parent API

```bash
cd mainapp/api
composer install
cp .env.example .env
# Set SYNC_API_KEY same as admin MAINAPP_SYNC_KEY
composer migrate
composer seed
composer start
```

### 3. Parent App (Ionic)

```bash
cd mainapp/app
npm install
npm start
```

Open http://localhost:8100

## Program Publishing

1. Create a program in **Admin** → Programs
2. Click **Publish** — syncs to mainapp via `POST /api/v1/sync/programs`
3. Parents see it in the Ionic app under Programs

Configure matching keys in:
- `admin/.env` → `MAINAPP_SYNC_KEY`
- `mainapp/api/.env` → `SYNC_API_KEY`

## Project Structure

```
kcdf-parents/
├── admin/                 # Admin web console (Slim + Bootstrap)
│   ├── public/
│   ├── src/
│   ├── views/
│   └── database/
├── mainapp/
│   ├── api/               # Parent REST API
│   └── app/               # Ionic frontend
└── PROJECT_REQUIREMENTS.md
```

## Deploy on XAMPP (drop-in)

1. Copy the whole project to XAMPP `htdocs` (e.g. `htdocs/kcdf-parents/`)
2. Run `composer install` in `admin` and `mainapp/api`
3. Open **http://localhost/kcdf-parents/setup.php** in your browser
4. Build the parent app: `cd mainapp/app && npm install && npm run build`
5. Use **http://localhost/kcdf-parents/admin/** and **http://localhost/kcdf-parents/mainapp/app/www/**

No virtual hosts or hosts file changes required. See **[XAMPP_DEPLOYMENT.md](./XAMPP_DEPLOYMENT.md)**.

## Requirements

See [PROJECT_REQUIREMENTS.md](./PROJECT_REQUIREMENTS.md) for full product specification.
