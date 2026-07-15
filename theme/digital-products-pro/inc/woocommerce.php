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