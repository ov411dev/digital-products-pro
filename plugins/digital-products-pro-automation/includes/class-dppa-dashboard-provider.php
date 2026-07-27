<?php
/**
 * Creator Dashboard automation provider.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supply automation data to the theme.
 */
final class DPPA_Dashboard_Provider {

	/**
	 * Dashboard statistics transient.
	 *
	 * @var string
	 */
	const STATS_TRANSIENT = 'dppa_dashboard_stats';

	/**
	 * Dashboard cache lifetime.
	 *
	 * @var int
	 */
	const CACHE_DURATION = MINUTE_IN_SECONDS;

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter(
			'dpp_automation_center_data',
			array( __CLASS__, 'provide_data' )
		);
	}

	/**
	 * Provide automation data to the dashboard.
	 *
	 * @param array<string, mixed> $data Existing dashboard data.
	 * @return array<string, mixed>
	 */
	public static function provide_data( $data ) {
		$settings          = DPPA_Settings::get_settings();
		$connection_status = DPPA_Settings::get_connection_status();
		$client            = new DPPA_API_Client();

		$data['n8n_url'] = $settings['n8n_url'];

		if ( ! $client->is_configured() ) {
			$data['connection_status'] = 'not_connected';

			return $data;
		}

		$data['connection_status'] = $connection_status['status'];

		if ( 'connected' !== $connection_status['status'] ) {
			return $data;
		}

		$stats = get_transient( self::STATS_TRANSIENT );

		if ( false === $stats || ! is_array( $stats ) ) {
			$workflows_service  = new DPPA_Workflows( $client );
			$executions_service = new DPPA_Executions( $client );

			$workflows_response  = $workflows_service->get_all();
			$executions_response = $executions_service->get_recent();

			if (
				is_wp_error( $workflows_response ) ||
				is_wp_error( $executions_response )
			) {
				$data['connection_status'] = 'error';

				return $data;
			}

			$workflow_items = $workflows_service->extract_items(
				$workflows_response
			);

			$execution_items = $executions_service->extract_items(
				$executions_response
			);

			$stats = self::build_statistics(
				$workflow_items,
				$execution_items
			);

			set_transient(
				self::STATS_TRANSIENT,
				$stats,
				self::CACHE_DURATION
			);
		}

		$data['workflow_count']     = $stats['workflow_count'];
		$data['active_workflows']   = $stats['active_count'];
		$data['inactive_workflows'] = $stats['inactive_count'];
		$data['failed_executions']  = $stats['failed_count'];
		$data['last_success']       = $stats['last_success'];
		$data['last_sync']          = current_time( 'mysql' );

		return $data;
	}

	/**
	 * Build dashboard statistics.
	 *
	 * @param array<int, array<string, mixed>> $workflows Workflow records.
	 * @param array<int, array<string, mixed>> $executions Execution records.
	 * @return array<string, mixed>
	 */
	private static function build_statistics( $workflows, $executions ) {
		$stats = array(
			'workflow_count' => count( $workflows ),
			'active_count'   => 0,
			'inactive_count' => 0,
			'failed_count'   => 0,
			'last_success'   => '',
		);

		foreach ( $workflows as $workflow ) {
			if ( ! is_array( $workflow ) ) {
				continue;
			}

			$is_active = false;

			if ( isset( $workflow['active'] ) ) {
				$is_active = (bool) $workflow['active'];
			} elseif ( isset( $workflow['enabled'] ) ) {
				$is_active = (bool) $workflow['enabled'];
			}

			if ( $is_active ) {
				++$stats['active_count'];
			} else {
				++$stats['inactive_count'];
			}
		}

		$latest_success = 0;

		foreach ( $executions as $execution ) {
			if ( ! is_array( $execution ) ) {
				continue;
			}

			$status = isset( $execution['status'] )
				? sanitize_key( $execution['status'] )
				: '';

			if (
				in_array(
					$status,
					array( 'error', 'failed', 'crashed' ),
					true
				)
			) {
				++$stats['failed_count'];

				continue;
			}

			$is_success = in_array(
				$status,
				array( 'success', 'succeeded' ),
				true
			);

			if (
				! $is_success &&
				isset( $execution['finished'] )
			) {
				$is_success = (bool) $execution['finished'];
			}

			if ( ! $is_success ) {
				continue;
			}

			$timestamp = '';

			if ( ! empty( $execution['stoppedAt'] ) ) {
				$timestamp = $execution['stoppedAt'];
			} elseif ( ! empty( $execution['finishedAt'] ) ) {
				$timestamp = $execution['finishedAt'];
			} elseif ( ! empty( $execution['updatedAt'] ) ) {
				$timestamp = $execution['updatedAt'];
			}

			if ( '' === $timestamp ) {
				continue;
			}

			$unix_time = strtotime( $timestamp );

			if (
				false !== $unix_time &&
				$unix_time > $latest_success
			) {
				$latest_success = $unix_time;
			}
		}

		if ( $latest_success > 0 ) {
			$stats['last_success'] = gmdate(
				'Y-m-d H:i:s',
				$latest_success
			);
		}

		return $stats;
	}

	/**
	 * Clear cached dashboard statistics.
	 *
	 * @return void
	 */
	public static function clear_cache() {
		delete_transient( self::STATS_TRANSIENT );
	}
}
