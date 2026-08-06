<?php
/**
 * Workflow list table.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Display n8n workflows in a WordPress list table.
 */
final class DPPA_Workflow_Table extends WP_List_Table {

	/**
	 * Workflow service.
	 *
	 * @var DPPA_Workflows
	 */
	private $workflows;

	/**
	 * N8n base URL.
	 *
	 * @var string
	 */
	private $n8n_url;

	/**
	 * Initialize the workflow table.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'dppa_workflow',
				'plural'   => 'dppa_workflows',
				'ajax'     => false,
			)
		);

		$this->workflows = new DPPA_Workflows();

		$settings      = DPPA_Settings::get_settings();
		$this->n8n_url = isset( $settings['n8n_url'] )
			? untrailingslashit( $settings['n8n_url'] )
			: '';
	}

	/**
	 * Return table columns.
	 *
	 * @return array<string, string>
	 */
	public function get_columns() {
		return array(
			'status'       => __( 'Status', 'digital-products-pro-automation' ),
			'name'         => __( 'Workflow', 'digital-products-pro-automation' ),
			'description'  => __( 'Description', 'digital-products-pro-automation' ),
			'capabilities' => __( 'Capabilities', 'digital-products-pro-automation' ),
			'category'     => __( 'Category', 'digital-products-pro-automation' ),
			'version'      => __( 'Version', 'digital-products-pro-automation' ),
			'tags'         => __( 'Tags', 'digital-products-pro-automation' ),
			'updated_at'   => __( 'Updated', 'digital-products-pro-automation' ),
			'id'           => __( 'ID', 'digital-products-pro-automation' ),
		);
	}

	/**
	 * Prepare workflow table items.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable,
		);

		$items = DPPA_Workflow_Discovery_Provider::get_all();

		if ( is_wp_error( $items ) ) {
			$this->items = array();

			add_settings_error(
				'dppa_workflows',
				'dppa_workflow_discovery_error',
				$items->get_error_message(),
				'error'
			);

			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only table search parameter.
		$search = isset( $_REQUEST['s'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) )
			: '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( '' !== $search ) {
			$items = array_filter(
				$items,
				static function ( $item ) use ( $search ) {
					if ( ! is_array( $item ) ) {
						return false;
					}

					$searchable_values = array(
						isset( $item['name'] )
							? (string) $item['name']
							: '',
						isset( $item['description'] )
							? (string) $item['description']
							: '',
						isset( $item['category'] )
							? (string) $item['category']
							: '',
					);

					foreach ( $searchable_values as $value ) {
						if ( false !== stripos( $value, $search ) ) {
							return true;
						}
					}

					return false;
				}
			);
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only table status filter.
		$status_filter = isset( $_REQUEST['workflow_status'] )
			? sanitize_key( wp_unslash( $_REQUEST['workflow_status'] ) )
			: '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( in_array( $status_filter, array( 'active', 'disabled' ), true ) ) {
			$items = array_filter(
				$items,
				static function ( $item ) use ( $status_filter ) {
					if ( ! is_array( $item ) ) {
						return false;
					}

					$is_active = ! empty( $item['active'] );

					if ( 'active' === $status_filter ) {
						return $is_active;
					}

					return ! $is_active;
				}
			);
		}

		$items = array_values( $items );

		$this->sort_items( $items );

		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = count( $items );

		$this->items = array_slice(
			$items,
			( $current_page - 1 ) * $per_page,
			$per_page
		);

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => (int) ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Display a fallback value for unknown columns.
	 *
	 * @param array<string, mixed> $item        Workflow record.
	 * @param string               $column_name Column name.
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return isset( $item['id'] )
					? esc_html( (string) $item['id'] )
					: '—';

			case 'updated_at':
				return $this->format_date(
					isset( $item['updatedAt'] )
						? (string) $item['updatedAt']
						: ''
				);

			default:
				return '—';
		}
	}

	/**
	 * Display the workflow status.
	 *
	 * @param array<string, mixed> $item Workflow record.
	 * @return string
	 */
	public function column_status( $item ) {
		$is_active = ! empty( $item['active'] );

		$class = $is_active
			? 'dppa-status-badge--active'
			: 'dppa-status-badge--disabled';

		$label = $is_active
			? __( 'Active', 'digital-products-pro-automation' )
			: __( 'Disabled', 'digital-products-pro-automation' );

		return sprintf(
			'<span class="dppa-status-badge %1$s">
                <span class="dppa-status-badge__dot" aria-hidden="true"></span>
                <span class="dppa-status-badge__label">%2$s</span>
            </span>',
			esc_attr( $class ),
			esc_html( $label )
		);
	}

	/**
	 * Display the workflow name and row actions.
	 *
	 * @param array<string, mixed> $item Workflow record.
	 * @return string
	 */
	public function column_name( $item ) {
		$name = isset( $item['name'] )
			? (string) $item['name']
			: __( 'Untitled workflow', 'digital-products-pro-automation' );

		$workflow_id = isset( $item['id'] )
			? (string) $item['id']
			: '';

		$actions = array();

		/*
		* Open workflow in n8n.
		*/
		if ( '' !== $this->n8n_url && '' !== $workflow_id ) {
			$workflow_url = sprintf(
				'%1$s/workflow/%2$s',
				$this->n8n_url,
				rawurlencode( $workflow_id )
			);

			$actions['open'] = sprintf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
				esc_url( $workflow_url ),
				esc_html__(
					'Open in n8n',
					'digital-products-pro-automation'
				)
			);
		}

		/*
		* Run workflow through the configured runner webhook.
		*/
		$runner_workflow_id = 'Lx8GCAUid8OBQYp5';

		if (
			'' !== $workflow_id &&
			$runner_workflow_id !== $workflow_id &&
			'' !== DPPA_Settings::get_runner_webhook_url()
		) {
			if ( $this->can_run_workflow( $item ) ) {
				$run_url = add_query_arg(
					array(
						'page'        => 'dppa-run-workflow',
						'workflow_id' => $workflow_id,
					),
					admin_url( 'admin.php' )
				);

				$requires_confirmation = ! empty(
					$item['requires_confirmation']
				);

				$actions['run'] = sprintf(
					'<a href="%1$s" class="dppa-run-workflow" data-requires-confirmation="%2$s">%3$s</a>',
					esc_url( $run_url ),
					$requires_confirmation ? '1' : '0',
					esc_html__(
						'Run',
						'digital-products-pro-automation'
					)
				);

			} elseif ( ! empty( $item['maintenance'] ) ) {
				$actions['unavailable'] = esc_html__(
					'Maintenance',
					'digital-products-pro-automation'
				);
			} elseif ( empty( $item['supports_manual_run'] ) ) {
				$actions['unavailable'] = esc_html__(
					'API only',
					'digital-products-pro-automation'
				);
			} else {
				$actions['unavailable'] = esc_html__(
					'Unavailable',
					'digital-products-pro-automation'
				);
			}
		}

		return sprintf(
			'<strong>%1$s</strong>%2$s',
			esc_html( $name ),
			$this->row_actions( $actions )
		);
	}
	/**
	 * Display workflow tags.
	 *
	 * @param array<string, mixed> $item Workflow record.
	 * @return string
	 */
	public function column_tags( $item ) {
		if ( empty( $item['tags'] ) || ! is_array( $item['tags'] ) ) {
			return '—';
		}

		$tags = array();

		foreach ( $item['tags'] as $tag ) {
			if ( is_array( $tag ) && ! empty( $tag['name'] ) ) {
				$tags[] = (string) $tag['name'];
			} elseif ( is_string( $tag ) ) {
				$tags[] = $tag;
			}
		}

		if ( empty( $tags ) ) {
			return '—';
		}

		return esc_html( implode( ', ', $tags ) );
	}

	/**
	 * Render the workflow description.
	 *
	 * @param array<string, mixed> $item Workflow item.
	 * @return string
	 */
	public function column_description( $item ) {
		return ! empty( $item['description'] )
			? esc_html( (string) $item['description'] )
			: '&mdash;';
	}

	/**
	 * Render the workflow category.
	 *
	 * @param array<string, mixed> $item Workflow item.
	 * @return string
	 */
	public function column_category( $item ) {
		return ! empty( $item['category'] )
			? esc_html( (string) $item['category'] )
			: '&mdash;';
	}

	/**
	 * Render the workflow version.
	 *
	 * @param array<string, mixed> $item Workflow item.
	 * @return string
	 */
	public function column_version( $item ) {
		return ! empty( $item['version'] )
			? esc_html( (string) $item['version'] )
			: '&mdash;';
	}

	/**
	 * Render the workflow update date.
	 *
	 * @param array<string, mixed> $item Workflow item.
	 * @return string
	 */
	public function column_updated_at( $item ) {
		if ( empty( $item['updated_at'] ) ) {
			return '&mdash;';
		}

		$timestamp = strtotime( (string) $item['updated_at'] );

		if ( false === $timestamp ) {
			return '&mdash;';
		}

		return esc_html(
			wp_date(
				get_option( 'date_format' ),
				$timestamp
			)
		);
	}

	/**
	 * Render workflow capabilities.
	 *
	 * @param array<string, mixed> $item Workflow item.
	 * @return string
	 */
	public function column_capabilities( $item ) {
		$capabilities = array();

		$execution_mode = isset( $item['execution_mode'] )
			? sanitize_key( (string) $item['execution_mode'] )
			: 'sync';

		if ( ! empty( $item['maintenance'] ) ) {
			$capabilities[] = __( 'Maintenance', 'digital-products-pro-automation' );
		}

		if ( ! empty( $item['deprecated'] ) ) {
			$capabilities[] = __( 'Deprecated', 'digital-products-pro-automation' );
		}

		$capabilities[] = 'async' === $execution_mode
			? __( 'Async', 'digital-products-pro-automation' )
			: __( 'Sync', 'digital-products-pro-automation' );

		$capabilities[] = ! empty( $item['supports_manual_run'] )
			? __( 'Manual', 'digital-products-pro-automation' )
			: __( 'API only', 'digital-products-pro-automation' );

		if ( ! empty( $item['has_schema'] ) ) {
			$capabilities[] = __( 'Schema', 'digital-products-pro-automation' );
		}

		if ( ! empty( $item['requires_confirmation'] ) ) {
			$capabilities[] = __(
				'Confirmation required',
				'digital-products-pro-automation'
			);
		}

		return esc_html( implode( ' · ', $capabilities ) );
	}

	/**
	 * Message shown when no workflows exist.
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e(
			'No workflows were found.',
			'digital-products-pro-automation'
		);
	}

	/**
	 * Format an n8n date.
	 *
	 * @param string $date Date value.
	 * @return string
	 */
	private function format_date( $date ) {
		if ( '' === $date ) {
			return '—';
		}

		$timestamp = strtotime( $date );

		if ( false === $timestamp ) {
			return '—';
		}

		return esc_html(
			wp_date(
				get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
				$timestamp
			)
		);
	}

	/**
	 * Return sortable columns.
	 *
	 * @return array<string, array<int, mixed>>
	 */
	protected function get_sortable_columns() {
		return array(
			'status'     => array( 'status', false ),
			'name'       => array( 'name', false ),
			'updated_at' => array( 'updated_at', true ),
		);
	}

	/**
	 * Sort workflow records.
	 *
	 * @param array<int, array<string, mixed>> $items Workflow records.
	 * @return void
	 */
	private function sort_items( &$items ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$orderby = isset( $_REQUEST['orderby'] )
			? sanitize_key( wp_unslash( $_REQUEST['orderby'] ) )
			: 'updated_at';

		$order = isset( $_REQUEST['order'] )
			? strtolower( sanitize_key( wp_unslash( $_REQUEST['order'] ) ) )
			: 'desc';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		if ( ! in_array( $order, array( 'asc', 'desc' ), true ) ) {
			$order = 'desc';
		}

		$allowed_orderby = array(
			'status',
			'name',
			'updated_at',
		);

		if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
			$orderby = 'updated_at';
		}

		usort(
			$items,
			static function ( $first, $second ) use ( $orderby, $order ) {
				switch ( $orderby ) {
					case 'status':
						$first_value  = ! empty( $first['active'] ) ? 1 : 0;
						$second_value = ! empty( $second['active'] ) ? 1 : 0;
						break;

					case 'name':
						$first_value = isset( $first['name'] )
							? strtolower( (string) $first['name'] )
							: '';

						$second_value = isset( $second['name'] )
							? strtolower( (string) $second['name'] )
							: '';
						break;

					case 'updated_at':
					default:
						$first_value = ! empty( $first['updatedAt'] )
							? strtotime( (string) $first['updatedAt'] )
							: 0;

						$second_value = ! empty( $second['updatedAt'] )
							? strtotime( (string) $second['updatedAt'] )
							: 0;
						break;
				}

				if ( $first_value === $second_value ) {
					return 0;
				}

				$result = $first_value <=> $second_value;

				return 'asc' === $order ? $result : -$result;
			}
		);
	}

	/**
	 * Display controls above the workflow table.
	 *
	 * @param string $which Table position.
	 * @return void
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$current_status = isset( $_REQUEST['workflow_status'] )
			? sanitize_key( wp_unslash( $_REQUEST['workflow_status'] ) )
			: '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		?>
		<div class="alignleft actions">
			<label class="screen-reader-text" for="dppa-workflow-status-filter">
				<?php
				esc_html_e(
					'Filter workflows by status',
					'digital-products-pro-automation'
				);
				?>
			</label>

			<select
				id="dppa-workflow-status-filter"
				name="workflow_status"
			>
				<option value="">
					<?php
					esc_html_e(
						'All statuses',
						'digital-products-pro-automation'
					);
					?>
				</option>

				<option
					value="active"
					<?php selected( $current_status, 'active' ); ?>
				>
					<?php
					esc_html_e(
						'Active',
						'digital-products-pro-automation'
					);
					?>
				</option>

				<option
					value="disabled"
					<?php selected( $current_status, 'disabled' ); ?>
				>
					<?php
					esc_html_e(
						'Disabled',
						'digital-products-pro-automation'
					);
					?>
				</option>
			</select>

			<?php
			submit_button(
				__( 'Filter', 'digital-products-pro-automation' ),
				'',
				'filter_action',
				false
			);
			?>
		</div>
		<?php
	}

	/**
	 * Determine whether a workflow can be run manually.
	 *
	 * @param array<string, mixed> $workflow Workflow data.
	 * @return bool
	 */
	private function can_run_workflow( $workflow ) {
		return ! empty( $workflow['active'] ) &&
			! empty( $workflow['runnable'] ) &&
			! empty( $workflow['supports_manual_run'] ) &&
			empty( $workflow['maintenance'] );
	}
}