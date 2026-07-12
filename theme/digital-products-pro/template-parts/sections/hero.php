<?php
/**
 * Premium hero section.
 *
 * @package DigitalProductsPro
 */

$dashboard_data    = dpp_dashboard_data();
$technology_badges = dpp_option( 'dpp_technology_badges', array() );
$automation_items  = dpp_option( 'dpp_automation_items', array() );
$recent_products   = dpp_option( 'dpp_recent_products', array() );
?>
<section class="dpp-hero" aria-labelledby="dpp-hero-title">
	<div class="dpp-hero__glow dpp-hero__glow--one" aria-hidden="true"></div>
	<div class="dpp-hero__glow dpp-hero__glow--two" aria-hidden="true"></div>

	<div class="container dpp-hero__layout">
		<div class="dpp-hero__content">
			<p class="dpp-eyebrow">
				<?php echo esc_html( dpp_option( 'dpp_badge' ) ); ?>
			</p>

			<h1 id="dpp-hero-title">
				<?php echo esc_html( dpp_option( 'dpp_hero_title' ) ); ?>
			</h1>

			<p class="dpp-lead dpp-hero__lead">
				<?php echo esc_html( dpp_option( 'dpp_hero_text' ) ); ?>
			</p>

			<div class="dpp-hero__actions">
				<a
					class="dpp-button dpp-button--primary dpp-button--large"
					href="<?php echo esc_url( dpp_option( 'dpp_primary_url' ) ); ?>"
				>
					<?php echo esc_html( dpp_option( 'dpp_primary_button' ) ); ?>
				</a>

				<a
					class="dpp-button dpp-button--secondary dpp-button--large"
					href="<?php echo esc_url( dpp_option( 'dpp_secondary_url' ) ); ?>"
				>
					<?php echo esc_html( dpp_option( 'dpp_secondary_button' ) ); ?>
				</a>
			</div>

			<?php if ( is_array( $technology_badges ) && ! empty( $technology_badges ) ) : ?>
				<div
					class="dpp-tech-row"
					aria-label="<?php esc_attr_e( 'Platform technologies', 'digital-products-pro-full' ); ?>"
				>
					<?php foreach ( $technology_badges as $badge ) : ?>
						<span><?php echo esc_html( $badge ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div
			class="dpp-dashboard"
			aria-label="<?php esc_attr_e( 'Platform dashboard preview', 'digital-products-pro-full' ); ?>"
		>
			<div class="dpp-dashboard__topbar">
				<div>
					<p class="dpp-dashboard__eyebrow">
						<?php echo esc_html( dpp_option( 'dpp_dashboard_eyebrow' ) ); ?>
					</p>
					<h2><?php echo esc_html( dpp_option( 'dpp_dashboard_title' ) ); ?></h2>
				</div>

				<span class="dpp-dashboard__status">
					<span aria-hidden="true"></span>
					<?php echo esc_html( dpp_option( 'dpp_dashboard_status' ) ); ?>
				</span>
			</div>

			<div class="dpp-dashboard__metrics">
				<?php foreach ( $dashboard_data as $metric ) : ?>
					<article>
						<span><?php echo esc_html( $metric['label'] ); ?></span>
						<strong><?php echo esc_html( $metric['value'] ); ?></strong>
						<small><?php echo esc_html( $metric['delta'] ); ?></small>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="dpp-dashboard__body">
				<div class="dpp-panel">
					<h3><?php esc_html_e( 'Automation status', 'digital-products-pro-full' ); ?></h3>

					<?php if ( is_array( $automation_items ) && ! empty( $automation_items ) ) : ?>
						<ul>
							<?php foreach ( $automation_items as $item ) : ?>
								<li>
									<span aria-hidden="true">✓</span>
									<?php echo esc_html( $item ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<div class="dpp-panel">
					<h3><?php esc_html_e( 'Recent products', 'digital-products-pro-full' ); ?></h3>

					<?php if ( is_array( $recent_products ) && ! empty( $recent_products ) ) : ?>
						<ul>
							<?php foreach ( $recent_products as $product ) : ?>
								<li><?php echo esc_html( $product ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
