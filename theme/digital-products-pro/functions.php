<?php
/**
 * Functions and definitions.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'DPP_VERSION', '2.0.0' );
define( 'DPP_DIR', get_template_directory() );
define( 'DPP_URI', get_template_directory_uri() );
require_once DPP_DIR . '/inc/setup.php';
require_once DPP_DIR . '/inc/assets.php';
require_once DPP_DIR . '/inc/customizer.php';
require_once DPP_DIR . '/inc/helpers.php';
require_once DPP_DIR . '/inc/content.php';
require_once DPP_DIR . '/inc/woocommerce.php';
