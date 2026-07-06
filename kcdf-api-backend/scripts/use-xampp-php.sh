#!/usr/bin/env bash
# Run a command with XAMPP PHP on PATH (macOS)
# Examples:
#   ./scripts/use-xampp-php.sh composer install
#   ./scripts/use-xampp-php.sh php -S localhost:8080 -t public

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
source "$ROOT/../scripts/env-xampp.sh"

cd "$ROOT"
exec "$@"
