# Digital Products Pro Full

## Install
1. WordPress Admin → Appearance → Themes → Add New → Upload Theme.
2. Upload `digital-products-pro-full.zip`.
3. Activate.
4. Go to Appearance → Customize → Landing Page Content.
5. Replace demo text, price, Telegram URL, email, and CTA links.

## Recommended plugins
- WooCommerce
- Stripe for WooCommerce
- PayPal Payments
- WP Mail SMTP
- MailPoet or FluentCRM

## n8n automation idea
WooCommerce → Settings → Advanced → Webhooks:
- Topic: Order created
- Delivery URL: your n8n webhook URL

In n8n:
Webhook → Verify order → Telegram notification → Email delivery → Google Sheets/CRM.
