<?php
/**
 * Template Name: Creator Dashboard
 * Template Post Type: page
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	auth_redirect();
}

if ( ! current_user_can( 'manage_woocommerce' ) ) {
	wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
	exit;
}

get_header();

$dpp_creator_user = wp_get_current_user();
?>

<main id="primary" class="dpp-creator-dashboard">
	<div class="dpp-container">
		<header class="dpp-creator-dashboard__header">
			<div>
				<p class="dpp-eyebrow">
					<?php esc_html_e( 'Creator workspace', 'digital-products-pro-full' ); ?>
				</p>

				<h1>
					<?php esc_html_e( 'Creator Dashboard', 'digital-products-pro-full' ); ?>
				</h1>

				<p>
					<?php
					printf(
						/* translators: %s: current user display name. */
						esc_html__(
							'Welcome back, %s. Manage your digital-product business from one place.',
							'digital-products-pro-full'
						),
						esc_html( $dpp_creator_user->display_name )
					);
					?>
				</p>
			</div>

			<a
				class="dpp-button dpp-button--primary"
				href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>"
			>
				<?php esc_html_e( 'Add product', 'digital-products-pro-full' ); ?>
			</a>
		</header>

		<section class="dpp-creator-metrics" aria-label="<?php esc_attr_e( 'Business summary', 'digital-products-pro-full' ); ?>">
			<article class="dpp-creator-metric">
				<span><?php esc_html_e( 'Products', 'digital-products-pro-full' ); ?></span>
				<strong>—</strong>
				<small><?php esc_html_e( 'Live data coming next', 'digital-products-pro-full' ); ?></small>
			</article>

			<article class="dpp-creator-metric">
				<span><?php esc_html_e( 'Orders', 'digital-products-pro-full' ); ?></span>
				<strong>—</strong>
				<small><?php esc_html_e( 'Live data coming next', 'digital-products-pro-full' ); ?></small>
			</article>

			<article class="dpp-creator-metric">
				<span><?php esc_html_e( 'Revenue', 'digital-products-pro-full' ); ?></span>
				<strong>—</strong>
				<small><?php esc_html_e( 'Live data coming next', 'digital-products-pro-full' ); ?></small>
			</article>

			<article class="dpp-creator-metric">
				<span><?php esc_html_e( 'Automations', 'digital-products-pro-full' ); ?></span>
				<strong>0</strong>
				<small><?php esc_html_e( 'n8n integration planned', 'digital-products-pro-full' ); ?></small>
			</article>
		</section>

		<div class="dpp-creator-dashboard__grid">
			<section class="dpp-creator-panel">
				<header class="dpp-creator-panel__header">
					<div>
						<p class="dpp-eyebrow">
							<?php esc_html_e( 'Catalog', 'digital-products-pro-full' ); ?>
						</p>

						<h2><?php esc_html_e( 'Recent products', 'digital-products-pro-full' ); ?></h2>
					</div>

					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>">
						<?php esc_html_e( 'View all', 'digital-products-pro-full' ); ?>
					</a>
				</header>

				<div class="dpp-creator-panel__placeholder">
					<p>
						<?php esc_html_e( 'Your latest products will appear here.', 'digital-products-pro-full' ); ?>
					</p>
				</div>
			</section>

			<section class="dpp-creator-panel">
				<header class="dpp-creator-panel__header">
					<div>
						<p class="dpp-eyebrow">
							<?php esc_html_e( 'Operations', 'digital-products-pro-full' ); ?>
						</p>

						<h2><?php esc_html_e( 'Automation status', 'digital-products-pro-full' ); ?></h2>
					</div>
				</header>

				<div class="dpp-creator-panel__placeholder">
					<p>
						<?php esc_html_e( 'Connect n8n workflows to display their status here.', 'digital-products-pro-full' ); ?>
					</p>
				</div>
			</section>
		</div>

		<section class="dpp-creator-panel dpp-creator-actions">
			<header class="dpp-creator-panel__header">
				<div>
					<p class="dpp-eyebrow">
						<?php esc_html_e( 'Quick access', 'digital-products-pro-full' ); ?>
					</p>

					<h2><?php esc_html_e( 'Manage your store', 'digital-products-pro-full' ); ?></h2>
				</div>
			</header>

			<div class="dpp-creator-actions__grid">
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>">
					<strong><?php esc_html_e( 'Products', 'digital-products-pro-full' ); ?></strong>
					<span><?php esc_html_e( 'Manage your digital catalog', 'digital-products-pro-full' ); ?></span>
				</a>

				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=shop_order' ) ); ?>">
					<strong><?php esc_html_e( 'Orders', 'digital-products-pro-full' ); ?></strong>
					<span><?php esc_html_e( 'Review customer purchases', 'digital-products-pro-full' ); ?></span>
				</a>

				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-reports' ) ); ?>">
					<strong><?php esc_html_e( 'Reports', 'digital-products-pro-full' ); ?></strong>
					<span><?php esc_html_e( 'Review store performance', 'digital-products-pro-full' ); ?></span>
				</a>

				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings' ) ); ?>">
					<strong><?php esc_html_e( 'Settings', 'digital-products-pro-full' ); ?></strong>
					<span><?php esc_html_e( 'Configure WooCommerce', 'digital-products-pro-full' ); ?></span>
				</a>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();