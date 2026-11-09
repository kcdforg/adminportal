# KCDF Deployment Manager — Readable Edition

A small PHP 8+ deployment portal: log in, select one or more applications, click **Deploy**, and view the current status. The code is formatted and commented for maintainability, without adding a framework or database.

**No GitHub webhook, cron, frontend builds on VPS, deployment history, automatic backups, rollback, or database changes.**

## Project layout

```text
kcdf-deployment-manager/
├── public/                # Only this directory is exposed over HTTPS
│   ├── assets/            # CSS and dashboard status refresh
│   ├── index.php          # Dashboard and application selection
│   ├── login.php          # Administrator sign-in
│   ├── logout.php         # Session logout
│   ├── deploy.php         # Authenticated deployment request
│   └── status.php         # Authenticated JSON status endpoint
├── includes/
│   ├── bootstrap.php      # Session, CSRF, config, and JSON helpers
│   └── layout.php         # Shared HTML layout
├── scripts/
│   ├── deploy.sh          # Background Git fetch and rsync
│   ├── read-request.php   # Validate selected application identifiers
│   ├── update-status.php  # Atomic status.json writer
│   └── hash-password.php  # Interactive password-hash generator
├── config/
│   ├── config.example.php # Example login configuration
│   └── deploy.example.env # Example deployment configuration
└── runtime/               # Status, request, lock, and log files
```

## Setup

1. Install the folder outside the public web root, e.g. `/home/kcdf/deployment/`.
2. Copy `config/config.example.php` to `config/config.php`. Generate a password hash using `php scripts/hash-password.php` and paste the hash into the configuration.
3. Copy `config/deploy.example.env` to `config/deploy.env`. Set the Git SSH URL, branch, checkout path, frontend output directories, and **actual** HestiaCP destination directories.
4. Configure `deploy.kcdfindia.com` with HTTPS and point its document root to `deployment/public/`. If HestiaCP cannot point there, publish only `public/` and adjust the bootstrap include paths to the private installation.
5. Install `git`, `rsync`, `php-cli`, `flock` (util-linux), and `nohup`. Configure a read-only GitHub deploy key and test non-interactive Git access as the worker's operating-system user.
6. Make `runtime/` writable by the PHP-FPM account, and ensure that account can read the private Git checkout and write to the three website destinations. **Never use `chmod 777`, run the web process as root, or grant unrestricted sudo.** A controlled, restricted worker identity is preferable in production.
7. Keep `config/`, `runtime/`, the Git checkout, and SSH credentials inaccessible over HTTP. Protect the deployment portal with HTTPS, a strong password, and ideally an IP/VPN restriction.
8. Leave `DRY_RUN=1` initially. Sign in, deploy one application, and inspect `runtime/status.json` and `runtime/deploy.log`. Enable `DRY_RUN=0` only after verifying every path and permission.

## How deployment works

1. `public/deploy.php` verifies the login, CSRF token, and selected applications.
2. It writes `runtime/request.json` and marks `status.json` as queued.
3. PHP starts `scripts/deploy.sh` using `nohup` and returns to the dashboard.
4. The worker obtains an exclusive `flock`, fetches the latest configured Git branch, validates all selected paths, and copies the selected files with `rsync`.
5. The worker writes running/success/failure information to `runtime/status.json`. The dashboard polls `/status.php` every three seconds.

The API copies `api-backend-php/` except `.env`, `.env-*`, `storage/`, and `.git/`. Angular and Ionic copy their already-compiled output. **No `--delete` is used**, so destination-only files are preserved. This also means removed source files may remain on production.

## Operational limitations

- The manager stores only the current deployment status and one log. It does not maintain deployment history or perform rollback.
- The production database is not touched. Maintain independent backups of the database and production-only configuration.
- A failed or interrupted deployment can leave some application files updated; file replacement is not atomic.
- A crashed background process can leave status stuck at `queued` or `running`. Inspect `deploy.log` and the operating-system lock before manually resetting status. **Do not delete `deploy.lock` while a process may be running.**
- The PHP-FPM process must permit `exec()` and allow detached children to survive request completion. Some hosting configurations prevent this; in that case use a restricted systemd launcher.
- The dashboard login uses a basic shared rate limit (10 failed attempts per 15-minute window). Restrict access by IP/VPN if practical; consider stronger authentication for a production deployment control plane.
- The `deploy.env` file is sourced as shell code. Only trusted administrators should be able to edit it.
- The private Git checkout is reset to the remote branch on each deployment; never store manual changes or production secrets there.

## Source layout notes

The example Angular output path is not guaranteed. Verify `angular.json` and Ionic build configuration, then set `ADMIN_SOURCE` and `MEMBER_SOURCE` accordingly. The HestiaCP destinations are examples until confirmed on the server.
