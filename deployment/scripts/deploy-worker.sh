
#!/usr/bin/env bash

# KCDF Deployment Manager
# Cron-based deployment worker.
#
# This script is executed by cron, not PHP.
# It processes one pending deployment request at a time.

set -Eeuo pipefail
umask 077

# Locate the Deployment Manager directory.
APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

RUNTIME_DIR="$APP_ROOT/runtime"
REQUEST_FILE="$RUNTIME_DIR/request.json"
PROCESSING_FILE="$RUNTIME_DIR/processing.json"
LOG_FILE="$RUNTIME_DIR/deploy.log"

PHP_BIN="$(command -v php)"

# Ensure the runtime directory exists.
mkdir -p "$RUNTIME_DIR"

# Prevent overlapping cron workers.
exec 8>"$RUNTIME_DIR/worker.lock"

if ! flock -n 8; then
    exit 0
fi

# Coordinate with PHP when taking a queued request.
exec 7>"$RUNTIME_DIR/request.lock"
flock -x 7

# Nothing to process.
if [[ ! -f "$REQUEST_FILE" ]]; then
    flock -u 7
    exit 0
fi

# A previous request is still marked as processing.
if [[ -f "$PROCESSING_FILE" ]]; then
    flock -u 7
    echo "A deployment is already marked as processing." >&2
    exit 1
fi

# Atomically claim the request.
mv "$REQUEST_FILE" "$PROCESSING_FILE"

flock -u 7

# Log the worker execution.
{
    echo "========================================"
    echo "Deployment started: $(date -Is)"
    echo "========================================"

    # Run the existing deployment script.
    /bin/bash "$APP_ROOT/scripts/deploy.sh" "$PROCESSING_FILE"

    echo "Deployment worker finished: $(date -Is)"

} >> "$LOG_FILE" 2>&1

# The deployment script has completed successfully.
# Remove the processing marker.
rm -f "$PROCESSING_FILE"
