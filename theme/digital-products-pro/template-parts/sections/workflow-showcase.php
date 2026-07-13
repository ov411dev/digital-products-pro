<?php
/**
 * Interactive workflow showcase section.
 *
 * @package DigitalProductsPro
 */

$workflow_steps = dpp_workflow_steps();

if ( empty( $workflow_steps ) ) {
	return;
}

$initial_step = $workflow_steps[0];
?>
<section
	class="dpp-workflow section"
	id="platform"
	aria-labelledby="dpp-workflow-title"
	data-workflow-showcase
>
	<div class="container">
		<div class="section-head">
			<span><?php esc_html_e( 'How it works', 'digital-products-pro-full' ); ?></span>

			<h2 id="dpp-workflow-title">
				<?php esc_html_e( 'From checkout to delivery, fully automated.', 'digital-products-pro-full' ); ?>
			</h2>

			<p>
				<?php esc_html_e( 'Explore each stage of the platform workflow and see what happens behind the scenes.', 'digital-products-pro-full' ); ?>
			</p>
		</div>

		<div class="dpp-workflow__layout">
			<div
				class="dpp-workflow__steps"
				role="tablist"
				aria-label="<?php esc_attr_e( 'Workflow stages', 'digital-products-pro-full' ); ?>"
			>
				<?php foreach ( $workflow_steps as $index => $step ) : ?>
					<button
						class="dpp-workflow__step<?php echo 0 === $index ? ' is-active' : ''; ?>"
						type="button"
						role="tab"
						id="workflow-tab-<?php echo esc_attr( $step['id'] ); ?>"
						aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						aria-controls="workflow-panel"
						tabindex="<?php echo 0 === $index ? '0' : '-1'; ?>"
						data-workflow-step="<?php echo esc_attr( $index ); ?>"
					>
						<span class="dpp-workflow__number">
							<?php echo esc_html( (string) ( $index + 1 ) ); ?>
						</span>

						<span>
							<strong><?php echo esc_html( $step['label'] ); ?></strong>
							<small><?php echo esc_html( $step['status'] ); ?></small>
						</span>
					</button>
				<?php endforeach; ?>
			</div>

			<div
				class="dpp-workflow__panel"
				id="workflow-panel"
				role="tabpanel"
				aria-labelledby="workflow-tab-<?php echo esc_attr( $initial_step['id'] ); ?>"
				tabindex="0"
				data-workflow-panel
			>
				<div class="dpp-workflow__panel-header">
					<div>
						<p data-workflow-status>
							<?php echo esc_html( $initial_step['status'] ); ?>
						</p>

						<h3 data-workflow-title>
							<?php echo esc_html( $initial_step['title'] ); ?>
						</h3>
					</div>

					<span data-workflow-duration>
						<?php echo esc_html( $initial_step['duration'] ); ?>
					</span>
				</div>

				<p class="dpp-workflow__description" data-workflow-description>
					<?php echo esc_html( $initial_step['description'] ); ?>
				</p>

				<ul class="dpp-workflow__actions" data-workflow-actions>
					<?php foreach ( $initial_step['actions'] as $workflow_action ) : ?>
						<li>
							<span aria-hidden="true">✓</span>
							<?php echo esc_html( $workflow_action ); ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="dpp-workflow__progress" aria-hidden="true">
					<span data-workflow-progress></span>
				</div>
			</div>
		</div>
	</div>

	<script type="application/json" data-workflow-data>
		<?php echo wp_json_encode( $workflow_steps ); ?>
	</script>
</section>