<?php
/**
 * Premium hero section.
 *
 * @package DigitalProductsPro
 */

?>
<section class="dpp-hero" aria-labelledby="dpp-hero-title">
	<div class="dpp-hero__glow dpp-hero__glow--one" aria-hidden="true"></div>
	<div class="dpp-hero__glow dpp-hero__glow--two" aria-hidden="true"></div>

	<div class="container dpp-hero__layout">
		<div class="dpp-hero__content">
			<p class="dpp-eyebrow"><?php esc_html_e( 'The complete digital commerce platform', 'digital-products-pro-full' ); ?></p>
			<h1 id="dpp-hero-title"><?php esc_html_e( 'Build, sell, and automate your digital product business.', 'digital-products-pro-full' ); ?></h1>
			<p class="dpp-lead dpp-hero__lead">
				<?php esc_html_e( 'Create digital products, accept payments, automate delivery, and manage customers from one WordPress platform powered by WooCommerce, AI, and n8n.', 'digital-products-pro-full' ); ?>
			</p>
			<div class="dpp-hero__actions">
				<a class="dpp-button dpp-button--primary dpp-button--large" href="<?php echo esc_url( dpp_get( 'dpp_primary_url', '#pricing' ) ); ?>"><?php esc_html_e( 'Get Started', 'digital-products-pro-full' ); ?></a>
				<a class="dpp-button dpp-button--secondary dpp-button--large" href="#features"><?php esc_html_e( 'Explore the Platform', 'digital-products-pro-full' ); ?></a>
			</div>
			<div class="dpp-tech-row">
				<span>WordPress</span><span>WooCommerce</span><span>AI</span><span>n8n</span><span>Docker</span><span>Cloudflare</span>
			</div>
		</div>

		<div class="dpp-dashboard" aria-label="<?php esc_attr_e( 'Platform dashboard preview', 'digital-products-pro-full' ); ?>">
			<div class="dpp-dashboard__topbar">
				<div><p class="dpp-dashboard__eyebrow">Platform overview</p><h2>Business Command Center</h2></div>
				<span class="dpp-dashboard__status"><span aria-hidden="true"></span>Live</span>
			</div>
			<div class="dpp-dashboard__metrics">
				<article><span>Revenue</span><strong>$12,480</strong><small>+24%</small></article>
				<article><span>Orders</span><strong>128</strong><small>+18%</small></article>
				<article><span>Customers</span><strong>72</strong><small>+11%</small></article>
				<article><span>Downloads</span><strong>564</strong><small>+31%</small></article>
			</div>
			<div class="dpp-dashboard__body">
				<div class="dpp-panel">
					<h3>Automation status</h3>
					<ul><li>✓ Order delivery</li><li>✓ Telegram notification</li><li>✓ CRM synchronization</li><li>✓ Follow-up email</li></ul>
				</div>
				<div class="dpp-panel">
					<h3>Recent products</h3>
					<ul><li>AI Prompt Pack</li><li>WordPress Theme</li><li>Automation Toolkit</li></ul>
				</div>
			</div>
		</div>
	</div>
</section>
