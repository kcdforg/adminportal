#!/usr/bin/env bash
# KCDF Parents — XAMPP environment (macOS)
# Usage:
#   source scripts/env-xampp.sh
# Or from repo root:
#   . ./scripts/env-xampp.sh

XAMPP_ROOT="/Applications/XAMPP/xamppfiles"

if [[ ! -d "$XAMPP_ROOT/bin" ]]; then
  echo "XAMPP not found at $XAMPP_ROOT" >&2
  return 1 2>/dev/null || exit 1
fi

export XAMPP_ROOT
export PATH="$XAMPP_ROOT/bin:$PATH"

export PHP_INI_SCAN_DIR="$XAMPP_ROOT/etc"
export MYSQL_HOME="$XAMPP_ROOT"

echo "XAMPP environment loaded:"
echo "  PHP:   $(command -v php) — $(php -r 'echo PHP_VERSION;')"
echo "  MySQL: $(command -v mysql 2>/dev/null || echo 'not in PATH')"
