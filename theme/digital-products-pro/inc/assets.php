<?php
/**
 * Front-end assets.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue compiled Vite assets, with source-file fallback.
 *
 * @return void
 */
function dpp_enqueue_assets() {
	$theme_dir    = get_template_directory();
	$theme_uri    = get_template_directory_uri();
	$manifest     = $theme_dir . '/dist/manifest.json';
	$script_handle = '';

	if ( file_exists( $manifest ) ) {
		$manifest_contents = file_get_contents( $manifest );
		$assets            = json_decode( $manifest_contents, true );

		if ( is_array( $assets ) ) {
			$style_index = 0;

			foreach ( $assets as $entry ) {
				if ( empty( $entry['file'] ) ) {
					continue;
				}

				$file = ltrim( $entry['file'], '/' );

				/*
				 * A standalone CSS input appears as its own manifest entry,
				 * with the generated stylesheet stored in "file".
				 */
				if ( str_ends_with( $file, '.css' ) ) {
					wp_enqueue_style(
						'dpp-vite-style-' . $style_index,
						$theme_uri . '/dist/' . $file,
						array(),
						DPP_VERSION
					);

					++$style_index;
				}

				/*
				 * JavaScript entries may also reference imported CSS files.
				 */
				if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
					foreach ( $entry['css'] as $css_file ) {
						wp_enqueue_style(
							'dpp-vite-style-' . $style_index,
							$theme_uri . '/dist/' . ltrim( $css_file, '/' ),
							array(),
							DPP_VERSION
						);

						++$style_index;
					}
				}

				if ( str_ends_with( $file, '.js' ) ) {
					$script_handle = 'dpp-vite-script';

					wp_enqueue_script(
						$script_handle,
						$theme_uri . '/dist/' . $file,
						array(),
						DPP_VERSION,
						true
					);
				}
			}
		}
	}

	/*
	 * Fall back to uncompiled source assets when no valid build exists.
	 */
	if ( ! wp_style_is( 'dpp-vite-style-0', 'enqueued' ) ) {
		wp_enqueue_style(
			'dpp-main',
			$theme_uri . '/assets/css/main.css',
			array(),
			DPP_VERSION
		);
	}

	if ( empty( $script_handle ) ) {
		$script_handle = 'dpp-main';

		wp_enqueue_script(
			$script_handle,
			$theme_uri . '/assets/js/main.js',
			array(),
			DPP_VERSION,
			true
		);
	}

	wp_localize_script(
		$script_handle,
		'DPPTheme',
		array(
			'darkModeDefault' => (bool) get_theme_mod(
				'dpp_dark_mode_default',
				false
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'dpp_enqueue_assets' );