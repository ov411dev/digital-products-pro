<?php
/**
 * Helper functions.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Get a theme mod value with an optional fallback.
 *
 * @param string $key The theme mod key.
 * @param mixed  $fallback The fallback value if the theme mod is not set.
 * @return mixed The theme mod value or the fallback.
 */
function dpp_get( $key, $fallback = '' ) {
	return get_theme_mod( $key, $fallback );
}
/**
 * Render a section template part.
 *
 * @param string $name The name of the section template part.
 */
function dpp_section( $name ) {
	get_template_part( 'template-parts/sections/' . $name );
}

/**
 * Render a button with the given text, URL, and optional class.
 *
 * @param string $text The button text.
 * @param string $url The button URL.
 * @param string $button_class Optional. The button CSS class. Default 'primary'.
 */
function dpp_btn( $text, $url, $button_class = 'primary' ) {
	printf( '<a class="btn %s" href="%s">%s</a>', esc_attr( $button_class ), esc_url( $url ), esc_html( $text ) );
}
