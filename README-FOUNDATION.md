# Foundation v1

Adds Composer/WPCS, npm/Vite/ESLint/Prettier, GitHub Actions, theme packaging, and a local Docker WordPress environment.

## Apply

From the repository root:

```bash
unzip -o ~/Downloads/digital-products-pro-foundation-v1.zip -d .
chmod +x scripts/*.sh
./scripts/bootstrap.sh
```

Then test:

```bash
npm run lint:js
npm run build
composer lint:php
npm run package:theme
```

Start local WordPress:

```bash
cp .env.example docker/.env
docker compose --env-file docker/.env -f docker/compose.yml up -d
```

Open WordPress at http://localhost:8080, phpMyAdmin at http://localhost:8081, and Mailpit at http://localhost:8025.

The first PHPCS run may report violations in existing theme files. Review them before running an automatic fixer across the whole project.
