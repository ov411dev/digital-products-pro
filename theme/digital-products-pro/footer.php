<?php
/**
 * Site footer template.
 *
 * @package DigitalProductsPro
 */

?>
<footer class="site-footer">
<div class="container footer-grid">
	<div><h3><?php bloginfo( 'name' ); ?></h3><p>A modern WordPress system for selling digital products, templates, courses, and AI automation packs.</p></div>
	<div><h4>Sections</h4><a href="#features">Features</a><a href="#modules">Modules</a><a href="#pricing">Pricing</a><a href="#faq">FAQ</a></div>
	<div><h4>Contact</h4><a href="mailto:<?php echo esc_attr( dpp_get( 'dpp_email', 'hello@example.com' ) ); ?>"><?php echo esc_html( dpp_get( 'dpp_email', 'hello@example.com' ) ); ?></a><a href="<?php echo esc_url( dpp_get( 'dpp_telegram_url', '#' ) ); ?>">Telegram</a></div>
</div>
</footer>
<?php wp_footer(); ?>
</body></html>
