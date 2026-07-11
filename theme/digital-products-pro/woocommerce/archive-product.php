<?php
/**
 * Archive template.
 *
 * @package DigitalProductsPro
 */

get_header();
?>
<main class="container woo-wrap">
<h1><?php woocommerce_page_title(); ?></h1>
<?php
if ( woocommerce_product_loop() ) :
	woocommerce_product_loop_start();
	while ( have_posts() ) :
		the_post();
		wc_get_template_part( 'content', 'product' );
endwhile;
	woocommerce_product_loop_end();
else :
	wc_get_template( 'loop/no-products-found.php' );
endif;
?>
</main>
<?php get_footer(); ?>
