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

$dpp_creator_user   = wp_get_current_user();
$dpp_dashboard_data = dpp_get_creator_dashboard_data();
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

		<section
			class="dpp-creator-metrics"
			aria-label="<?php esc_attr_e( 'Business summary', 'digital-products-pro-full' ); ?>">
			<article class="dpp-creator-metric">
				<span>
					<?php esc_html_e( 'Products', 'digital-products-pro-full' ); ?>
				</span>

				<strong>
					<?php echo esc_html( number_format_i18n( $dpp_dashboard_data['product_count'] ) ); ?>
				</strong>

				<small>
					<?php esc_html_e( 'Published products', 'digital-products-pro-full' ); ?>
				</small>
			</article>

			<article class="dpp-creator-metric">
				<span>
					<?php esc_html_e( 'Orders', 'digital-products-pro-full' ); ?>
				</span>

				<strong>
					<?php echo esc_html( number_format_i18n( $dpp_dashboard_data['order_count'] ) ); ?>
				</strong>

				<small>
					<?php esc_html_e( 'Processing and completed', 'digital-products-pro-full' ); ?>
				</small>
			</article>

			<article class="dpp-creator-metric">
				<span>
					<?php esc_html_e( 'Revenue', 'digital-products-pro-full' ); ?>
				</span>

				<strong>
					<?php
					echo wp_kses_post(
						wc_price( $dpp_dashboard_data['total_revenue'] )
					);
					?>
				</strong>

				<small>
					<?php esc_html_e( 'Paid order total', 'digital-products-pro-full' ); ?>
				</small>
			</article>

			<article class="dpp-creator-metric">
				<span>
					<?php esc_html_e( 'Automations', 'digital-products-pro-full' ); ?>
				</span>

				<strong>0</strong>

				<small>
					<?php esc_html_e( 'n8n integration coming next', 'digital-products-pro-full' ); ?>
				</small>
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

				<?php if ( ! empty( $dpp_dashboard_data['recent_products'] ) ) : ?>
					<ul class="dpp-creator-list">
						<?php foreach ( $dpp_dashboard_data['recent_products'] as $product ) : ?>
							<li>
								<div class="dpp-creator-list__main">
									<?php
									echo wp_kses_post(
										$product->get_image(
											'woocommerce_thumbnail',
											array(
												'class'   => 'dpp-creator-list__image',
												'loading' => 'lazy',
											)
										)
									);
									?>

									<div>
										<strong>
											<?php echo esc_html( $product->get_name() ); ?>
										</strong>

										<small>
											<?php
											echo esc_html(
												get_post_status_object(
													$product->get_status()
												)->label
											);
											?>
										</small>
									</div>
								</div>

								<a
									href="<?php echo esc_url( get_edit_post_link( $product->get_id() ) ); ?>"
								>
									<?php esc_html_e( 'Edit', 'digital-products-pro-full' ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<div class="dpp-creator-panel__placeholder">
						<p>
							<?php esc_html_e( 'No products created yet.', 'digital-products-pro-full' ); ?>
						</p>
					</div>
				<?php endif; ?>
			</section>

			<section class="dpp-creator-panel">
				<header class="dpp-creator-panel__header">
					<div>
						<p class="dpp-eyebrow">
							<?php esc_html_e( 'Sales', 'digital-products-pro-full' ); ?>
						</p>

						<h2>
							<?php esc_html_e( 'Recent orders', 'digital-products-pro-full' ); ?>
						</h2>
					</div>
				</header>

				<div class="dpp-creator-panel__placeholder">
					<?php if ( ! empty( $dpp_dashboard_data['recent_orders'] ) ) : ?>
						<ul class="dpp-creator-list">
							<?php foreach ( $dpp_dashboard_data['recent_orders'] as $dpp_order ) : ?>
								<li>
									<div>
										<strong>
											<?php
											printf(
												/* translators: %s: order number. */
												esc_html__( 'Order #%s', 'digital-products-pro-full' ),
												esc_html( $dpp_order->get_order_number() )
											);
											?>
										</strong>

										<small>
											<?php
											echo esc_html(
												wc_get_order_status_name( $dpp_order->get_status() )
											);
											?>
										</small>
									</div>

									<div class="dpp-creator-list__order-total">
										<strong>
											<?php echo wp_kses_post( $dpp_order->get_formatted_order_total() ); ?>
										</strong>

										<a href="<?php echo esc_url( $dpp_order->get_edit_order_url() ); ?>">
											<?php esc_html_e( 'View', 'digital-products-pro-full' ); ?>
										</a>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<div class="dpp-creator-panel__placeholder">
							<p>
								<?php esc_html_e( 'No paid orders yet.', 'digital-products-pro-full' ); ?>
							</p>
						</div>
					<?php endif; ?>
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