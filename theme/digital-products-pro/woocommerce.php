<?php
/**
 * WooCommerce page template.
 *
 * @package DigitalProductsPro
 */

get_header();
?>

<main id="primary" class="dpp-commerce">
	<div class="container dpp-commerce__container">
		<?php woocommerce_content(); ?>
	</div>
</main>

<?php
get_footer();