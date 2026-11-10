#!/usr/bin/env bash
# KCDF Deployment Manager — deploy selected applications from Git.
#
# This script runs in the background, launched by public/deploy.php.
# It never builds Angular/Ionic; their compiled output must be in Git.
set -Eeuo pipefail
umask 077

APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
RUNTIME_DIR="$APP_ROOT/runtime"
CONFIG_FILE="$APP_ROOT/config/deploy.env"
PHP_BIN="$(command -v php)"
COMMIT=""

# Prevent simultaneous deployments. The OS releases this lock on exit.
exec 9>"$RUNTIME_DIR/deploy.lock"
if ! flock -n 9; then
    echo 'Another deployment is already running.'
    exit 0
fi

# Report a human-readable stage to the login-protected dashboard.
update_status() {
    local state="$1"
    local message="$2"
    "$PHP_BIN" "$APP_ROOT/scripts/update-status.php" "$state" "$message" "$COMMIT"
}

# The error trap keeps status.json useful even if rsync or git fails.
on_error() {
    local exit_code=$?
    trap - ERR
    update_status failed "Deployment failed (exit $exit_code); see deploy.log" || true
    exit "$exit_code"
}
trap on_error ERR

# Load trusted server configuration (never supplied by a browser request).
if [[ ! -f "$CONFIG_FILE" ]]; then
    echo "Missing deployment configuration: $CONFIG_FILE"
    exit 1
fi
# shellcheck disable=SC1090
source "$CONFIG_FILE"

# Fail early when a required path or setting is absent.
required_settings=(
    REPO_URL BRANCH REPO_DIR
    API_SOURCE ADMIN_SOURCE MEMBER_SOURCE
    API_DEST ADMIN_DEST MEMBER_DEST DRY_RUN
)
for setting in "${required_settings[@]}"; do
    [[ -n "${!setting:-}" ]] || { echo "Missing setting: $setting"; exit 1; }
done

[[ "$DRY_RUN" == 0 || "$DRY_RUN" == 1 ]] || {
    echo 'DRY_RUN must be 0 or 1.'
    exit 1
}

# The cron worker passes the claimed deployment request.
REQUEST_FILE="${1:-}"

if [[ -z "$REQUEST_FILE" || "$REQUEST_FILE" != "$RUNTIME_DIR/processing.json" ]]; then
    echo "Invalid deployment request path." >&2
    exit 1
fi

validated_apps="$("$PHP_BIN" "$APP_ROOT/scripts/read-request.php" "$REQUEST_FILE")"

mapfile -t SELECTED_APPS <<< "$validated_apps"

((${#SELECTED_APPS[@]} > 0)) || {
    echo 'No valid applications were selected.'
    exit 1
}

# A private Git checkout is used; no Git metadata is copied to web roots.
update_status running 'Fetching Git repository'

if [[ ! -d "$REPO_DIR/.git" ]]; then
    mkdir -p "$(dirname "$REPO_DIR")"
    git clone --no-checkout "$REPO_URL" "$REPO_DIR"
fi

# Guard against deploying from a repository different from the configured one.
actual_origin="$(git -C "$REPO_DIR" remote get-url origin)"
[[ "$actual_origin" == "$REPO_URL" ]] || {
    echo "Unexpected Git origin: $actual_origin"
    exit 1
}

git -C "$REPO_DIR" fetch --prune origin \
    "+refs/heads/$BRANCH:refs/remotes/origin/$BRANCH"

COMMIT="$(git -C "$REPO_DIR" rev-parse "refs/remotes/origin/$BRANCH^{commit}")"
git -C "$REPO_DIR" reset --hard "$COMMIT"
git -C "$REPO_DIR" clean -fd

# Resolve each application to its configured source and destination.
application_paths() {
    local application="$1"

    case "$application" in
        api)
            SOURCE_DIR="$REPO_DIR/$API_SOURCE"
            DEST_DIR="$API_DEST"
            ;;
        admin)
            SOURCE_DIR="$REPO_DIR/$ADMIN_SOURCE"
            DEST_DIR="$ADMIN_DEST"
            ;;
        member)
            SOURCE_DIR="$REPO_DIR/$MEMBER_SOURCE"
            DEST_DIR="$MEMBER_DEST"
            ;;
        *)
            echo "Unknown application: $application" >&2
            return 1
            ;;
    esac
}

# Only deploy to an existing, non-symlink HestiaCP public_html directory.
# Confirm these paths match your actual VPS layout before production use.
validate_destination() {
    local destination="$1"
    local resolved

    [[ -d "$destination" && ! -L "$destination" ]] || {
        echo "Destination missing or symlinked: $destination" >&2
        return 1
    }

    resolved="$(realpath "$destination")"
    [[ "$resolved" == /home/*/web/*/public_html ]] || {
        echo "Unexpected deployment destination: $resolved" >&2
        return 1
    }
}

# Validate ALL selected applications before copying ANY production files.
for application in "${SELECTED_APPS[@]}"; do
    application_paths "$application"

    [[ -d "$SOURCE_DIR" && ! -L "$SOURCE_DIR" ]] || {
        echo "Deployment source missing: $SOURCE_DIR"
        exit 1
    }

    validate_destination "$DEST_DIR"

    if [[ "$application" != api && ! -f "$SOURCE_DIR/index.html" ]]; then
        echo "Compiled frontend index.html missing: $SOURCE_DIR"
        exit 1
    fi
done

# Deploy only the applications selected on the dashboard.
for application in "${SELECTED_APPS[@]}"; do
    application_paths "$application"
    update_status running "Deploying $application"

    rsync_options=(-a --itemize-changes)
    if [[ "$DRY_RUN" == 1 ]]; then
        rsync_options+=(--dry-run)
    fi

    if [[ "$application" == api ]]; then
        # Preserve production environment files and runtime storage.
        # No --delete: server-only files are left untouched.
        rsync "${rsync_options[@]}" \
            --exclude='.env' \
            --exclude='.env-*' \
            --exclude='storage/' \
            --exclude='.git/' \
            "$SOURCE_DIR/" "$DEST_DIR/"
    else
        # Angular and Ionic are prebuilt locally and committed to Git.
        rsync "${rsync_options[@]}" "$SOURCE_DIR/" "$DEST_DIR/"
    fi
done

update_status success "Completed (DRY_RUN=$DRY_RUN)"
echo "Deployed: ${SELECTED_APPS[*]} at commit $COMMIT (DRY_RUN=$DRY_RUN)"
