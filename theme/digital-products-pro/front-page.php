<?php
/**
 * Front page template.
 *
 * @package DigitalProductsPro
 */

get_header();
?>
<main>
<?php dpp_section( 'hero' ); ?>
<?php dpp_section( 'features' ); ?>
<?php dpp_section( 'how-it-works' ); ?>
<?php dpp_section( 'modules' ); ?>
<?php dpp_section( 'automation' ); ?>
<?php dpp_section( 'testimonials' ); ?>
<?php dpp_section( 'pricing' ); ?>
<?php dpp_section( 'faq' ); ?>
<?php dpp_section( 'cta' ); ?>
</main>
<?php get_footer(); ?>
