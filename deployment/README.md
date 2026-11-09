# KCDF Parents — webhook deployment (v3)

This revision uses **one shell script** (`scripts/deploy.sh`) and **one PHP state helper** (`scripts/state.php`). There is no cron job. Angular and Ionic are built locally; their compiled files are committed to GitHub.

## Contents

- `webhook.php`: validate GitHub signature/repository/branch, atomically write `runtime/pending.json`, launch background deployment.
- `scripts/deploy.sh`: blocking `flock` lock, fetch remote branch, synchronize PHP/Angular/Ionic, update status through helper.
- `scripts/state.php`: atomic state reads/writes under a separate `state.lock`.
- `runtime/status.json`, `pending.json`, `deploy.lock`, `state.lock`, `deploy.log`, `launcher.log`: **created at runtime**, not committed. Do not delete lock files to release locks.

## Setup

1. Place `deployment/` in the repository for source control, but install the working deployment directory **outside the API web root**, for example `/home/kcdf/deployment`. Do not expose config, runtime, scripts or repository to HTTP.
2. Copy `config/config.example.php` to `config/config.php` and set a long random webhook secret, actual repository, runtime and worker paths.
3. Copy `config/deploy.example.env` to `config/deploy.env` and verify the **real** paths from your Angular/Ionic build settings and HestiaCP domains. `ADMIN_SOURCE` is only an example.
4. Clone the private repository into `/home/kcdf/deployment/repo` using read-only deploy credentials. Configure noninteractive Git authentication and Git safe-directory settings as appropriate.
5. Install `git`, `rsync`, `flock` (util-linux), PHP CLI and Bash. The web PHP account must be able to write runtime state and launch the script; the shell process also needs read access to the Git checkout and write access to all three document roots. **Do not grant unrestricted sudo or make runtime world-writable.** For stronger isolation, use a restricted service account/systemd runner.
6. Route only `POST /deploy` to the private `webhook.php` via a small API entry point. This endpoint uses GitHub HMAC authentication, **not** your normal JWT middleware. Configure the GitHub repository webhook to `https://api.kcdfindia.com/deploy`, content type `application/json`, secret matching `config.php`, event `push`.
7. Keep `DRY_RUN=1` and `FRONTEND_DELETE=0` until you inspect `runtime/deploy.log` and confirm all source/destination paths. Never enable `--delete` unless the frontend destinations contain only deployable assets.
8. Build Angular and Ionic locally and **commit their output files**. Confirm `.gitignore` permits the compiled files. Push to the configured branch.

## Flow and coordination

- PHP atomically writes `pending.json` with the requested SHA and delivery ID, then starts `deploy.sh` detached.
- Each webhook launches a worker; the workers serialize on `runtime/deploy.lock`. This **blocking lock** closes the race between a worker deciding to exit and a new webhook arriving.
- The shell worker reads the newest pending request, fetches the branch tip, deploys only a revision containing the requested SHA, and updates `status.json` via `state.php`. Multiple intermediate commits can be coalesced.
- A completed request is marked by `processed_delivery_id`, avoiding infinite retries after failure. A new delivery triggers another attempt.
- Logs are in `runtime/deploy.log` and `runtime/launcher.log`; status is in `runtime/status.json`. Never expose these files publicly.

## Limitations / production precautions

- This is a small single-server deployment system, not an atomic release manager. File copies directly into live roots may briefly expose partial deployments; use release directories and symlink switching for zero-downtime/rollback.
- A detached child can still be terminated by some hosting configurations or PHP-FPM process managers. A supervisor/systemd service is more reliable if this occurs.
- A worker crash after writing `running` may require a new push/redelivery to restart; no cron or persistent service recovers it automatically.
- Webhook delivery ordering is not a guarantee of Git ancestry. The worker verifies the requested SHA is on the fetched branch before deploying, but branch force-pushes and concurrent remote updates require operational care.
- Do not let your API sync overwrite the `/deploy` entry point or production secrets. Exclude those paths or keep the route entry point managed outside the synchronized tree.
- PHP API deploy excludes `.env`, `uploads`, `storage`, `logs`, `vendor`; adjust these exclusions to your real app. API deployment does not delete removed files by default.
- Ensure the PHP CLI version, Git permissions, Apache/Nginx routing, and HestiaCP directory ownership are correct before enabling live deployment.
