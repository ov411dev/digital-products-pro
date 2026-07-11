<?php
/**
 * Hero section template.
 *
 * @package DigitalProductsPro
 */

?>
<section class="hero"><div class="container hero-grid"><div><span class="badge"><?php echo esc_html( dpp_get( 'dpp_badge' ) ); ?></span><h1><?php echo esc_html( dpp_get( 'dpp_hero_title' ) ); ?></h1><p class="hero-text"><?php echo esc_html( dpp_get( 'dpp_hero_text' ) ); ?></p><div class="hero-actions"><?php dpp_btn( dpp_get( 'dpp_primary_button' ), dpp_get( 'dpp_primary_url' ), 'primary' ); ?><?php dpp_btn( dpp_get( 'dpp_secondary_button' ), dpp_get( 'dpp_secondary_url' ), 'secondary' ); ?></div><div class="trust-row"><span>Instant download</span><span>WordPress ready</span><span>n8n automation</span></div></div><div class="product-card"><div class="screen"><span>Digital Product Dashboard</span><h2>$12,480</h2><p>Sample monthly digital revenue</p><div class="bars"><i></i><i></i><i></i><i></i></div></div></div></div></section>
