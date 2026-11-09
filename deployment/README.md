# KCDF Deployment Manager — First-run Installer

A minimal PHP deployment dashboard: login, select PHP API / Angular Admin / Ionic Member, and copy prebuilt files from Git. No database, deployment history, rollback, or automatic backups.

## Install on HestiaCP

1. Upload this entire directory outside the public document root, e.g. `/home/kcdf/deployment/`. Set the `deploy.kcdfindia.com` document root to the `public/` subdirectory. HTTPS is required.
2. Ensure `runtime/` is writable by the PHP user. During installation, `config/` must be writable by PHP to create two private configuration files. Remove directory write permission after installation.
3. **Authorize the first installation:** create a private `config/setup-secret.txt` containing a cryptographically random secret of at least 32 characters, e.g. run `openssl rand -hex 32 > config/setup-secret.txt` over SSH or your hosting file manager. Keep it outside the public web root and restrict its permissions to `0600`. This one-time step prevents anyone who visits the domain first from taking over administrator setup. Alternatively provide `KCDF_DEPLOY_SETUP_SECRET` as a server-side environment variable.
4. Visit `https://deploy.kcdfindia.com/`. The app redirects to `/install.php` when no configuration or lock exists. Enter the setup secret, administrator credentials, Git URL/branch/local checkout, three repository-relative source directories, and three existing HestiaCP `public_html` destinations.
5. The installer checks field formats, destination paths and permissions, and availability of basic commands. It writes `config/config.php`, `config/deploy.env`, and finally `runtime/installed.lock`. It attempts to delete `config/setup-secret.txt` after success. Delete it yourself if still present.
6. Log in. Deployments start with `DRY_RUN=1` (preview only). Review `runtime/deploy.log` and `runtime/status.json`. To enable actual file copying, privately change `DRY_RUN=1` to `DRY_RUN=0` in `config/deploy.env`.

**Important:** A missing lock alone does not reopen setup: existing configuration also blocks it. If setup fails partway through, fix the private files and permissions manually; do not delete credentials to reopen public installation without a deliberate recovery plan.

## Git and permissions

The server account running `scripts/deploy.sh` must have Git read access (SSH deploy key for private repositories), and permission to write the selected destination directories. PHP background execution (`exec`, `nohup`) must be allowed. Do not run the web server as root. Verify that HestiaCP and PHP-FPM permit detached jobs.

Compiled Angular `dist/.../browser` and Ionic `www/` must already be committed. The VPS does not build frontend assets. The script uses `git reset --hard` and `git clean -fd` on its **dedicated checkout**; never point `REPO_DIR` at a working directory containing uncommitted work.

API copying excludes `.env`, `.env-*`, `storage/` and `.git/`. Files are copied with rsync **without `--delete`**, so removed Git files may remain on the server. No database changes are performed. Angular and Ionic also copy without deletion.

## Configuration and security

The installer creates `config/config.php` (username and PHP password hash) and `config/deploy.env` (trusted shell assignments). Both stay outside `public/` and should have `0600` permissions. The installer locks itself with `runtime/installed.lock`.

After installation, edit the private config files to change deployment paths or credentials; there is no Settings page in this minimal release. Keep the manager behind HTTPS, preferably with additional IP restrictions. `runtime/deploy.log` contains deployment output; monitor it if a deployment fails.

**Note:** This code has not been exercised on your actual VPS. Confirm PHP and shell execution permissions and paths using dry-run before live deployment.
