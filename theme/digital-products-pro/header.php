<?php
/**
 * Site header template.
 *
 * @package DigitalProductsPro
 */

$site_name = get_bloginfo( 'name' );

if ( empty( $site_name ) ) {
	$site_name = 'Digital Products Pro';
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="dpp-site-header">
	<div class="container dpp-header__inner">
		<a class="dpp-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<span class="dpp-brand__mark" aria-hidden="true">D</span>
				<span><?php echo esc_html( $site_name ); ?></span>
			<?php endif; ?>
		</a>

		<button class="dpp-menu-toggle" type="button" aria-expanded="false" aria-controls="dpp-primary-navigation" data-menu-toggle>
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'digital-products-pro-full' ); ?></span>
			<span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
		</button>

		<nav id="dpp-primary-navigation" class="dpp-primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'digital-products-pro-full' ); ?>" data-primary-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => false,
					'items_wrap'     => '<ul class="dpp-primary-nav__list">%3$s</ul>',
				)
			);
			?>
			<a class="dpp-button dpp-button--primary dpp-header__cta" href="<?php echo esc_url( dpp_get( 'dpp_primary_url', '#pricing' ) ); ?>">
				<?php esc_html_e( 'Get Started', 'digital-products-pro-full' ); ?>
			</a>
			<button class="dpp-theme-toggle" type="button" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'digital-products-pro-full' ); ?>" data-theme-toggle>
				<span aria-hidden="true">◐</span>
			</button>
		</nav>
	</div>
</header>
