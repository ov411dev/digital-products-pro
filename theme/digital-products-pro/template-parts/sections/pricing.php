<?php
/**
 * Pricing section template.
 *
 * @package DigitalProductsPro
 */

?>
<section class="section pricing" id="pricing"><div class="container price-grid"><div class="price-copy"><span class="eyebrow">Pricing</span><h2>Launch your digital product system</h2><p>Use this section to sell a product, course, template pack, or consultation.</p></div><div class="price-card"><span>Starter Pack</span><h3><?php echo esc_html( dpp_get( 'dpp_price', '$49' ) ); ?></h3><ul><li>Landing page theme</li><li>WooCommerce support</li><li>Automation section</li><li>English demo content</li></ul><?php dpp_btn( 'Buy Now', dpp_get( 'dpp_primary_url', '#' ), 'primary large' ); ?></div></div></section>
