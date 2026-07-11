<?php
/**
 * Woocommerce settings.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_action(
	'after_setup_theme',
	function () {
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 600,
				'single_image_width'    => 900,
			)
		);
	}
);
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
