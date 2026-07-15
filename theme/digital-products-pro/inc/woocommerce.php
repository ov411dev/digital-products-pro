<?php
/**
 * WooCommerce integration.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return whether WooCommerce is active.
 *
 * @return bool
 */
function dpp_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Remove the default WooCommerce sidebar.
 *
 * The product experience will use a full-width layout. Filters can be added
 * later through blocks or a dedicated shop sidebar.
 *
 * @return void
 */
function dpp_woocommerce_remove_sidebar() {
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'wp', 'dpp_woocommerce_remove_sidebar' );

/**
 * Add a product-card content wrapper.
 *
 * @return void
 */
function dpp_product_card_content_start() {
	echo '<div class="dpp-product-card__content">';
}
add_action(
	'woocommerce_before_shop_loop_item_title',
	'dpp_product_card_content_start',
	20
);

/**
 * Close the product-card content wrapper.
 *
 * @return void
 */
function dpp_product_card_content_end() {
	echo '</div>';
}
add_action(
	'woocommerce_after_shop_loop_item',
	'dpp_product_card_content_end',
	20
);

/**
 * Add a product-type badge to archive cards.
 *
 * @return void
 */
function dpp_product_card_badge() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$badge = '';

	if ( $product->is_downloadable() ) {
		$badge = __( 'Digital download', 'digital-products-pro-full' );
	} elseif ( $product->is_virtual() ) {
		$badge = __( 'Virtual product', 'digital-products-pro-full' );
	} elseif ( $product->is_on_sale() ) {
		$badge = __( 'On sale', 'digital-products-pro-full' );
	}

	if ( empty( $badge ) ) {
		return;
	}
	?>
	<span class="dpp-product-card__badge">
		<?php echo esc_html( $badge ); ?>
	</span>
	<?php
}
add_action(
	'woocommerce_before_shop_loop_item_title',
	'dpp_product_card_badge',
	8
);

/**
 * Display a short product description in archive cards.
 *
 * @return void
 */
function dpp_product_card_excerpt() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$description = $product->get_short_description();

	if ( empty( $description ) ) {
		$description = $product->get_description();
	}

	if ( empty( $description ) ) {
		return;
	}
	?>
	<p class="dpp-product-card__excerpt">
		<?php
		echo esc_html(
			wp_trim_words(
				wp_strip_all_tags( $description ),
				18
			)
		);
		?>
	</p>
	<?php
}
add_action(
	'woocommerce_after_shop_loop_item_title',
	'dpp_product_card_excerpt',
	7
);

/**
 * Add theme classes to WooCommerce product cards.
 *
 * @param string[]   $classes Product classes.
 * @param WC_Product $product Product object.
 *
 * @return string[]
 */
function dpp_product_loop_classes( $classes, $product ) {
	if ( $product instanceof WC_Product ) {
		$classes[] = 'dpp-product-card';
	}

	return $classes;
}
add_filter(
	'woocommerce_post_class',
	'dpp_product_loop_classes',
	10,
	2
);

/**
 * Customize archive button text.
 *
 * @param string     $text    Button text.
 * @param WC_Product $product Product object.
 *
 * @return string
 */
function dpp_product_button_text( $text, $product ) {
	if ( ! $product instanceof WC_Product ) {
		return $text;
	}

	if ( $product->is_downloadable() || $product->is_virtual() ) {
		return __( 'View Product', 'digital-products-pro-full' );
	}

	return $text;
}

add_filter(
	'woocommerce_product_add_to_cart_text',
	'dpp_product_button_text',
	10,
	2
);

/**
 * Add a feature list below the product summary.
 *
 * @return void
 */
function dpp_single_product_highlights() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$highlights = array(
		__( 'Instant digital delivery', 'digital-products-pro-full' ),
		__( 'Secure customer download access', 'digital-products-pro-full' ),
		__( 'Compatible with WooCommerce accounts', 'digital-products-pro-full' ),
	);

	if ( $product->is_downloadable() ) {
		$highlights[] = __( 'Downloadable files included', 'digital-products-pro-full' );
	}

	if ( $product->is_virtual() ) {
		$highlights[] = __( 'No shipping required', 'digital-products-pro-full' );
	}
	?>
	<ul class="dpp-product-highlights">
		<?php foreach ( $highlights as $highlight ) : ?>
			<li>
				<span aria-hidden="true">✓</span>
				<?php echo esc_html( $highlight ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}
add_action(
	'woocommerce_single_product_summary',
	'dpp_single_product_highlights',
	25
);

/**
 * Add supporting copy below the purchase controls.
 *
 * @return void
 */
function dpp_single_product_purchase_note() {
	?>
	<p class="dpp-purchase-note">
		<span aria-hidden="true">🔒</span>
		<?php esc_html_e( 'Secure checkout and immediate account access after purchase.', 'digital-products-pro-full' ); ?>
	</p>
	<?php
}
add_action(
	'woocommerce_single_product_summary',
	'dpp_single_product_purchase_note',
	35
);