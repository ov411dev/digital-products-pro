# Digital Products Pro Full

A custom WordPress theme for selling digital downloads, templates, courses, AI prompts, and automation packs.

## Features

- Responsive one-page sales layout
- Original English demo content
- Dark/light mode
- WordPress Customizer controls
- WooCommerce support
- Gutenberg `theme.json`
- Reusable block pattern
- n8n integration guidance

## Local installation

1. Clone or download this repository.
2. Copy the repository folder into `wp-content/themes/`.
3. Rename the folder to `digital-products-pro-full` if needed.
4. Activate the theme in **Appearance → Themes**.
5. Configure content in **Appearance → Customize → Landing Page Content**.

## Build an installable ZIP

From the repository root:

```bash
bash tools/build-release.sh
```

The package will be created in `release/`.

## Recommended plugins

- WooCommerce
- WooCommerce Stripe Payment Gateway
- WooCommerce PayPal Payments
- WP Mail SMTP
- MailPoet or FluentCRM

## Repository structure

```text
assets/                 CSS and JavaScript
inc/                    Theme setup and integrations
patterns/               Gutenberg patterns
template-parts/sections Landing-page sections
woocommerce/            WooCommerce template overrides
docs/                   Installation and integration notes
tools/                  Release scripts
```

## License

GPL-2.0-or-later.
