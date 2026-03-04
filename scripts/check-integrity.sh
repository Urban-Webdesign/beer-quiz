#!/usr/bin/env bash
# ============================================================
# Beer Quiz — Server integrity check
# Run this after every deployment and via cron (e.g. hourly).
# Usage: bash scripts/check-integrity.sh
# ============================================================
set -euo pipefail

APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ALERT=0

red()   { echo -e "\033[0;31m[ALERT] $*\033[0m"; }
green() { echo -e "\033[0;32m[OK]    $*\033[0m"; }
info()  { echo -e "\033[0;34m[INFO]  $*\033[0m"; }

info "Checking integrity of $APP_ROOT"
echo ""

# ---------------------------------------------------------------
# 1. Check index.php has not been tampered with
# ---------------------------------------------------------------
EXPECTED_HASH="$(sha256sum "$APP_ROOT/public/index.php.baseline" 2>/dev/null | awk '{print $1}')"
ACTUAL_HASH="$(sha256sum "$APP_ROOT/public/index.php" | awk '{print $1}')"

if [ -f "$APP_ROOT/public/index.php.baseline" ]; then
    if [ "$EXPECTED_HASH" = "$ACTUAL_HASH" ]; then
        green "public/index.php — hash matches baseline"
    else
        red "public/index.php — HASH MISMATCH! File may be compromised."
        red "  Expected: $EXPECTED_HASH"
        red "  Actual:   $ACTUAL_HASH"
        ALERT=1
    fi
else
    info "No baseline hash found. Creating baseline now..."
    cp "$APP_ROOT/public/index.php" "$APP_ROOT/public/index.php.baseline"
    sha256sum "$APP_ROOT/public/index.php" > "$APP_ROOT/public/index.php.sha256"
    green "Baseline created: public/index.php.baseline"
fi

# ---------------------------------------------------------------
# 2. Scan for PHP files in public/ subdirectories (should NEVER exist except index.php)
#    This catches the exact attack vector used: public/assets/images/accesson.php
# ---------------------------------------------------------------
PUBLIC_PHP=$(find "$APP_ROOT/public" \
    -name "*.php" -o -name "*.phtml" -o -name "*.phar" 2>/dev/null \
    | grep -v "^$APP_ROOT/public/index\.php$" \
    | head -20)
if [ -n "$PUBLIC_PHP" ]; then
    red "PHP files found in public/ (outside index.php) — WEBSHELL LIKELY!"
    echo "$PUBLIC_PHP"
    red "Deleting them now..."
    find "$APP_ROOT/public" \
        -name "*.php" -o -name "*.phtml" -o -name "*.phar" 2>/dev/null \
        | grep -v "^$APP_ROOT/public/index\.php$" \
        | xargs rm -f
    red "Deleted. Investigate how they got there."
    ALERT=1
else
    green "public/ — no rogue PHP files found"
fi

# ---------------------------------------------------------------
# 2b. Scan specifically for known webshell fingerprints
# ---------------------------------------------------------------
WEBSHELL_HIT=$(grep -rEl \
    'eval\(base64_decode\(\$_|eval\(\$_REQUEST|eval\(\$_POST|eval\(\$_GET|@copy\(\$_FILES|\$_COOKIE\[.{1,30}\].*md5|echo.*\*.*[0-9]{5}' \
    "$APP_ROOT/public" \
    --include="*.php" 2>/dev/null | head -10)
if [ -n "$WEBSHELL_HIT" ]; then
    red "Known webshell pattern matched in public/:"
    echo "$WEBSHELL_HIT"
    ALERT=1
else
    green "public/ — no known webshell fingerprints"
fi

# ---------------------------------------------------------------
# 3. Scan for suspicious patterns in PHP source files (outside vendor)
# ---------------------------------------------------------------
info "Scanning for obfuscated/malicious PHP patterns (excluding vendor)..."

SUSPICIOUS=$(grep -rEl \
    'goto [a-zA-Z0-9_]+;|eval\(base64_decode|eval\(\$_|shell_exec\(|passthru\(|system\(\$_|proc_open\(|\$GLOBALS\[.{1,20}\]\[.{1,20}\]' \
    "$APP_ROOT" \
    --include="*.php" \
    --exclude-dir=vendor \
    --exclude-dir=.git \
    2>/dev/null | head -20)

if [ -n "$SUSPICIOUS" ]; then
    red "Suspicious PHP patterns found in:"
    echo "$SUSPICIOUS"
    ALERT=1
else
    green "No obvious obfuscated/malicious PHP found in source"
fi

# ---------------------------------------------------------------
# 4. Check .env is not world-readable
# ---------------------------------------------------------------
ENV_PERMS=$(stat -c "%a" "$APP_ROOT/.env" 2>/dev/null || stat -f "%Lp" "$APP_ROOT/.env" 2>/dev/null)
if [[ "$ENV_PERMS" == "640" || "$ENV_PERMS" == "600" ]]; then
    green ".env permissions: $ENV_PERMS"
else
    red ".env permissions are $ENV_PERMS — should be 640 or 600"
    ALERT=1
fi

# ---------------------------------------------------------------
# 5. Check APP_DEBUG is not true in production
# ---------------------------------------------------------------
if grep -q "APP_DEBUG=true" "$APP_ROOT/.env" 2>/dev/null; then
    red "APP_DEBUG=true found in .env — must be false in production!"
    ALERT=1
else
    green "APP_DEBUG is not true"
fi

# ---------------------------------------------------------------
# 6. Check storage & bootstrap/cache are writable but NOT web-accessible PHP
# ---------------------------------------------------------------
STORAGE_PHP_APP=$(find "$APP_ROOT/storage" -name "*.php" \
    -not -path "*/framework/*" \
    -not -path "*/logs/*" \
    2>/dev/null | head -10)
if [ -n "$STORAGE_PHP_APP" ]; then
    red "Unexpected PHP files in storage (outside framework):"
    echo "$STORAGE_PHP_APP"
    ALERT=1
else
    green "storage — no unexpected PHP files"
fi

# ---------------------------------------------------------------
# Summary
# ---------------------------------------------------------------
echo ""
if [ "$ALERT" -eq 1 ]; then
    red "============================================"
    red " INTEGRITY CHECK FAILED — investigate NOW"
    red "============================================"
    exit 1
else
    green "============================================"
    green " All checks passed"
    green "============================================"
fi


