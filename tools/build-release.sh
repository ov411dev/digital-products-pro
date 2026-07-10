#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEME_SLUG="digital-products-pro-full"
RELEASE_DIR="$ROOT/release"
STAGE_DIR="$RELEASE_DIR/$THEME_SLUG"
ZIP_PATH="$RELEASE_DIR/$THEME_SLUG.zip"

rm -rf "$STAGE_DIR" "$ZIP_PATH"
mkdir -p "$STAGE_DIR"

rsync -a --delete \
  --exclude '.git' \
  --exclude '.github' \
  --exclude '.gitignore' \
  --exclude 'release' \
  --exclude 'tools' \
  --exclude 'CHANGELOG.md' \
  --exclude 'CONTRIBUTING.md' \
  --exclude 'ROADMAP.md' \
  --exclude 'README.md' \
  "$ROOT/" "$STAGE_DIR/"

cd "$RELEASE_DIR"
zip -qr "$ZIP_PATH" "$THEME_SLUG"
rm -rf "$STAGE_DIR"
echo "Created $ZIP_PATH"
