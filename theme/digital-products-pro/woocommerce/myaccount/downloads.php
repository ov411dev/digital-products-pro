<?php
/**
 * Customer downloads library.
 *
 * @package DigitalProductsPro
 * @version 7.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action( 'woocommerce_before_account_downloads', $has_downloads );
?>

<section class="dpp-download-library" aria-labelledby="dpp-download-library-title">
	<header class="dpp-download-library__header">
		<div>
			<p class="dpp-eyebrow">
				<?php esc_html_e( 'Your library', 'digital-products-pro-full' ); ?>
			</p>

			<h2 id="dpp-download-library-title">
				<?php esc_html_e( 'Digital downloads', 'digital-products-pro-full' ); ?>
			</h2>

			<p>
				<?php
				esc_html_e(
					'Access the products and files included with your purchases.',
					'digital-products-pro-full'
				);
				?>
			</p>
		</div>

		<a
			class="dpp-button dpp-button--secondary"
			href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
		>
			<?php esc_html_e( 'Browse products', 'digital-products-pro-full' ); ?>
		</a>
	</header>

	<?php if ( $has_downloads ) : ?>
		<?php do_action( 'woocommerce_before_available_downloads' ); ?>

		<div class="dpp-download-grid">
			<?php foreach ( $downloads as $download ) : ?>
				<?php
				$product_id = isset( $download['product_id'] )
					? absint( $download['product_id'] )
					: 0;

				$product = $product_id ? wc_get_product( $product_id ) : false;

				$product_name = isset( $download['product_name'] )
					? $download['product_name']
					: __( 'Digital product', 'digital-products-pro-full' );

				$product_url = isset( $download['product_url'] )
					? $download['product_url']
					: '';

				$download_name = isset( $download['download_name'] )
					? $download['download_name']
					: __( 'Download file', 'digital-products-pro-full' );

				$download_url = isset( $download['download_url'] )
					? $download['download_url']
					: '';

				$downloads_remaining = isset( $download['downloads_remaining'] )
					? $download['downloads_remaining']
					: '';

				$access_expires = isset( $download['access_expires'] )
					? $download['access_expires']
					: null;
				?>

				<article class="dpp-download-card">
					<div class="dpp-download-card__visual">
						<?php if ( $product && $product->get_image_id() ) : ?>
							<?php
							echo wp_kses_post(
								$product->get_image(
									'woocommerce_thumbnail',
									array(
										'class'   => 'dpp-download-card__image',
										'loading' => 'lazy',
									)
								)
							);
							?>
						<?php else : ?>
							<span class="dpp-download-card__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" focusable="false">
									<path
										d="M12 3v11m0 0 4-4m-4 4-4-4M5 17v3h14v-3"
										fill="none"
										stroke="currentColor"
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="1.8"
									/>
								</svg>
							</span>
						<?php endif; ?>
					</div>

					<div class="dpp-download-card__content">
						<div class="dpp-download-card__heading">
							<div>
								<span class="dpp-download-card__label">
									<?php esc_html_e( 'Purchased product', 'digital-products-pro-full' ); ?>
								</span>

								<h3>
									<?php if ( $product_url ) : ?>
										<a href="<?php echo esc_url( $product_url ); ?>">
											<?php echo esc_html( $product_name ); ?>
										</a>
									<?php else : ?>
										<?php echo esc_html( $product_name ); ?>
									<?php endif; ?>
								</h3>
							</div>

							<span class="dpp-download-card__badge">
								<?php esc_html_e( 'Available', 'digital-products-pro-full' ); ?>
							</span>
						</div>

						<p class="dpp-download-card__file">
							<?php echo esc_html( $download_name ); ?>
						</p>

						<dl class="dpp-download-card__meta">
							<div>
								<dt>
									<?php esc_html_e( 'Downloads remaining', 'digital-products-pro-full' ); ?>
								</dt>

								<dd>
									<?php
									if ( '' === $downloads_remaining ) {
										esc_html_e( 'Unlimited', 'digital-products-pro-full' );
									} else {
										echo esc_html( (string) $downloads_remaining );
									}
									?>
								</dd>
							</div>

							<div>
								<dt>
									<?php esc_html_e( 'Access expires', 'digital-products-pro-full' ); ?>
								</dt>

								<dd>
									<?php
									if ( empty( $access_expires ) ) {
										esc_html_e( 'Never', 'digital-products-pro-full' );
									} else {
										echo esc_html(
											date_i18n(
												get_option( 'date_format' ),
												strtotime( $access_expires )
											)
										);
									}
									?>
								</dd>
							</div>
						</dl>

						<div class="dpp-download-card__actions">
							<?php if ( $download_url ) : ?>
								<a
									class="dpp-button dpp-button--primary"
									href="<?php echo esc_url( $download_url ); ?>"
								>
									<?php esc_html_e( 'Download file', 'digital-products-pro-full' ); ?>
								</a>
							<?php endif; ?>

							<?php if ( $product_url ) : ?>
								<a
									class="dpp-download-card__product-link"
									href="<?php echo esc_url( $product_url ); ?>"
								>
									<?php esc_html_e( 'View product', 'digital-products-pro-full' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<?php do_action( 'woocommerce_after_available_downloads' ); ?>
	<?php else : ?>
		<div class="dpp-download-empty">
			<span class="dpp-download-empty__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" focusable="false">
					<path
						d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9ZM4 7.5l8 4.5 8-4.5M12 12v9"
						fill="none"
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="1.7"
					/>
				</svg>
			</span>

			<h3>
				<?php esc_html_e( 'Your library is empty', 'digital-products-pro-full' ); ?>
			</h3>

			<p>
				<?php
				esc_html_e(
					'Purchase a digital product and its files will appear here automatically.',
					'digital-products-pro-full'
				);
				?>
			</p>

			<a
				class="dpp-button dpp-button--primary"
				href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
			>
				<?php esc_html_e( 'Browse digital products', 'digital-products-pro-full' ); ?>
			</a>
		</div>
	<?php endif; ?>
</section>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>