#!/usr/bin/env bash
set -Eeuo pipefail
umask 077
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
RUNTIME="$BASE/runtime"
STATE="$BASE/scripts/state.php"
# Blocking lock: webhook may arrive during worker shutdown. Waiting ensures no request is lost.
exec 200>"$RUNTIME/deploy.lock"
flock -x 200
# shellcheck disable=SC1091
source "$BASE/config/deploy.env"
: "${REPO_DIR:?}" "${API_DEST:?}" "${ADMIN_DEST:?}" "${MEMBER_DEST:?}" "${API_SOURCE:?}" "${ADMIN_SOURCE:?}" "${MEMBER_SOURCE:?}"
for dest in "$API_DEST" "$ADMIN_DEST" "$MEMBER_DEST"; do
  [[ "$dest" = /* && "$dest" != / && "$dest" != *'..'* && -d "$dest" ]] || { echo "Unsafe/missing destination: $dest" >&2; exit 1; }
done
[[ -d "$REPO_DIR/.git" ]] || { echo 'Missing private git checkout' >&2; exit 1; }
for src in "$API_SOURCE" "$ADMIN_SOURCE" "$MEMBER_SOURCE"; do
  [[ "$src" != /* && "$src" != *'..'* ]] || { echo "Unsafe source: $src" >&2; exit 1; }
done
run_one() {
  local request="$1" requested latest
  requested="$(php -r '$r=json_decode($argv[1],true); echo $r["commit"] ?? "";' "$request")"
  [[ "$requested" =~ ^[0-9a-f]{40}$ ]] || return 1
  cd "$REPO_DIR"
  git fetch --no-tags "${REMOTE:-origin}" "${BRANCH:-main}"
  latest="$(git rev-parse FETCH_HEAD)"
  # Delayed webhooks never roll the deployment back.
  if ! git merge-base --is-ancestor "$requested" "$latest"; then
    echo "Webhook commit not on current remote branch; skipping: $requested"
    printf '%s
' "$latest" > "$RUNTIME/deployed-result"
    return 10
  fi
  git checkout --detach --force "$latest"
  for src in "$API_SOURCE" "$ADMIN_SOURCE" "$MEMBER_SOURCE"; do
    [[ -d "$REPO_DIR/$src" ]] || { echo "Missing source: $src" >&2; return 1; }
  done
  [[ -f "$REPO_DIR/$ADMIN_SOURCE/index.html" && -f "$REPO_DIR/$MEMBER_SOURCE/index.html" ]] || { echo 'Frontend index.html missing' >&2; return 1; }
  local flags=(-a --itemize-changes)
  [[ "${DRY_RUN:-1}" == 1 ]] && flags+=(--dry-run)
  rsync "${flags[@]}" --exclude='.git/' --exclude='.env' --exclude='.env.*' --exclude='uploads/' --exclude='storage/' --exclude='logs/' --exclude='vendor/' "$REPO_DIR/$API_SOURCE/" "$API_DEST/"
  local front=("${flags[@]}")
  [[ "${FRONTEND_DELETE:-0}" == 1 ]] && front+=(--delete)
  rsync "${front[@]}" "$REPO_DIR/$ADMIN_SOURCE/" "$ADMIN_DEST/"
  rsync "${front[@]}" "$REPO_DIR/$MEMBER_SOURCE/" "$MEMBER_DEST/"
  printf '%s
' "$latest" > "$RUNTIME/deployed-result"
  [[ "${DRY_RUN:-1}" == 1 ]] && return 11
  printf '%s
' "$latest" > "$RUNTIME/last-deployed-sha"
  return 0
}
while true; do
  request="$(php "$STATE" next)"
  [[ -n "$request" ]] || break
  php "$STATE" start "$request"
  result=0
  run_one "$request" >> "$RUNTIME/deploy.log" 2>&1 || result=$?
  deployed="$(cat "$RUNTIME/deployed-result" 2>/dev/null || true)"
  case "$result" in
    0) php "$STATE" finish "$request" success 'Deployment completed' "$deployed" ;;
    10) php "$STATE" finish "$request" skipped 'Stale webhook ignored' ;;
    11) php "$STATE" finish "$request" preview 'Dry run completed; no production changes' ;;
    *) php "$STATE" finish "$request" failed 'Deployment failed; check deploy.log' ;;
  esac
done
