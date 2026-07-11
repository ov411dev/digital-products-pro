#!/usr/bin/env bash
set -euo pipefail
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEME_NAME="digital-products-pro"
THEME_DIR="$ROOT_DIR/theme/$THEME_NAME"
RELEASE_DIR="$ROOT_DIR/release"
STAGING_DIR="$RELEASE_DIR/$THEME_NAME"
ZIP_FILE="$RELEASE_DIR/$THEME_NAME.zip"
cd "$ROOT_DIR"
if [[ -f package-lock.json ]]; then npm ci; else npm install; fi
npm run build
rm -rf "$RELEASE_DIR"
mkdir -p "$STAGING_DIR"
rsync -a --exclude='.DS_Store' --exclude='*.map' "$THEME_DIR/" "$STAGING_DIR/"
cd "$RELEASE_DIR"
zip -qr "$ZIP_FILE" "$THEME_NAME"
echo "Created: $ZIP_FILE"
