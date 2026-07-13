<?php
/**
 * Premium hero section.
 *
 * @package DigitalProductsPro
 */

$dashboard_data        = dpp_dashboard_data();
$dashboard_trend       = dpp_dashboard_trend();
$dashboard_activity    = dpp_dashboard_activity();
$dashboard_connections = dpp_dashboard_connections();
$chart_points          = dpp_dashboard_chart_points( $dashboard_trend );
$dashboard_data        = dpp_dashboard_data();
$technology_badges     = dpp_option( 'dpp_technology_badges', array() );
?>
<section class="dpp-hero" aria-labelledby="dpp-hero-title">
	<div class="dpp-hero__glow dpp-hero__glow--one" aria-hidden="true"></div>
	<div class="dpp-hero__glow dpp-hero__glow--two" aria-hidden="true"></div>

	<div class="container dpp-hero__layout">
		<div class="dpp-hero__content">
			<p class="dpp-eyebrow" data-customize-hero-badge>
				<?php echo esc_html( dpp_option( 'dpp_badge' ) ); ?>
			</p>

			<h1 id="dpp-hero-title" data-customize-hero-title>
				<?php echo esc_html( dpp_option( 'dpp_hero_title' ) ); ?>
			</h1>

			<p class="dpp-lead dpp-hero__lead" data-customize-hero-text>
				<?php echo esc_html( dpp_option( 'dpp_hero_text' ) ); ?>
			</p>

			<div class="dpp-hero__actions">
				<a
					class="dpp-button dpp-button--primary dpp-button--large"
					href="<?php echo esc_url( dpp_option( 'dpp_primary_url' ) ); ?>"
					data-customize-primary-button
				>
					<?php echo esc_html( dpp_option( 'dpp_primary_button' ) ); ?>
				</a>

				<a
					class="dpp-button dpp-button--secondary dpp-button--large"
					href="<?php echo esc_url( dpp_option( 'dpp_secondary_url' ) ); ?>"
					data-customize-secondary-button
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
			data-dashboard
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
				<?php foreach ( $dashboard_data as $metric_key => $metric ) : ?>
					<article
						class="dpp-dashboard__metric"
						data-dashboard-metric="<?php echo esc_attr( $metric_key ); ?>"
					>
						<span><?php echo esc_html( $metric['label'] ); ?></span>

						<strong data-dashboard-counter
							data-dashboard-value="<?php echo esc_attr( $metric['value'] ); ?>"
						>
							<?php echo esc_html( $metric['value'] ); ?>
						</strong>

						<small><?php echo esc_html( $metric['delta'] ); ?></small>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="dpp-dashboard__chart">
				<div class="dpp-dashboard__section-heading">
					<h3>
						<?php echo esc_html( dpp_option( 'dpp_dashboard_chart_label' ) ); ?>
					</h3>

					<span><?php esc_html_e( 'Last 12 weeks', 'digital-products-pro-full' ); ?></span>
				</div>

				<?php if ( ! empty( $chart_points ) ) : ?>
					<svg
						viewBox="0 0 520 160"
						role="img"
						aria-label="<?php esc_attr_e( 'Revenue trend chart', 'digital-products-pro-full' ); ?>"
						preserveAspectRatio="none"
					>
						<defs>
							<linearGradient id="dpp-chart-gradient" x1="0" x2="1">
								<stop offset="0%" stop-color="#8b5cf6"></stop>
								<stop offset="100%" stop-color="#22d3ee"></stop>
							</linearGradient>
						</defs>

						<polyline
							class="dpp-dashboard__chart-line"
							data-dashboard-chart-line
							points="<?php echo esc_attr( $chart_points ); ?>"
							fill="none"
							stroke="url(#dpp-chart-gradient)"
							stroke-width="5"
							stroke-linecap="round"
							stroke-linejoin="round"
						></polyline>
					</svg>
				<?php endif; ?>
			</div>

			<div class="dpp-dashboard__details">
				<section class="dpp-dashboard__activity">
					<div class="dpp-dashboard__section-heading">
						<h3>
							<?php echo esc_html( dpp_option( 'dpp_dashboard_activity_title' ) ); ?>
						</h3>

						<span><?php esc_html_e( 'Live feed', 'digital-products-pro-full' ); ?></span>
					</div>

					<?php if ( ! empty( $dashboard_activity ) ) : ?>
						<ul>
							<?php foreach ( $dashboard_activity as $activity_item ) : ?>
								<li data-dashboard-activity>
									<time><?php echo esc_html( $activity_item['time'] ); ?></time>

									<span
										class="dpp-activity-dot dpp-activity-dot--<?php echo esc_attr( $activity_item['type'] ); ?>"
										aria-hidden="true"
									></span>

									<div>
										<strong><?php echo esc_html( $activity_item['title'] ); ?></strong>
										<small><?php echo esc_html( $activity_item['detail'] ); ?></small>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</section>

				<section class="dpp-dashboard__connections">
					<div class="dpp-dashboard__section-heading">
						<h3><?php esc_html_e( 'Platform connections', 'digital-products-pro-full' ); ?></h3>
						<span><?php esc_html_e( 'System health', 'digital-products-pro-full' ); ?></span>
					</div>

					<?php if ( ! empty( $dashboard_connections ) ) : ?>
						<ul>
							<?php foreach ( $dashboard_connections as $connection ) : ?>
								<li>
									<div>
										<strong><?php echo esc_html( $connection['label'] ); ?></strong>
										<small><?php echo esc_html( $connection['status'] ); ?></small>
									</div>

									<div
										class="dpp-connection-health"
										aria-label="<?php echo esc_attr( $connection['health'] . '% health' ); ?>"
									>
										<span
											data-dashboard-health
											data-dashboard-health-value="<?php echo esc_attr( (string) $connection['health'] ); ?>"
											style="--dpp-health: <?php echo esc_attr( (string) $connection['health'] ); ?>%"
										></span>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</section>
			</div>
		</div>
	</div>
</section>
