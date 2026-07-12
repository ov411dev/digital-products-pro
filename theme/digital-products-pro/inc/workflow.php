<?php
/**
 * Workflow showcase data.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return workflow showcase steps.
 *
 * @return array<int, array<string, mixed>>
 */
function dpp_workflow_steps() {
	$steps = array(
		array(
			'id'          => 'create',
			'label'       => __( 'Create Product', 'digital-products-pro-full' ),
			'title'       => __( 'Create and package your digital product', 'digital-products-pro-full' ),
			'description' => __( 'Build an ebook, template, course, software download, AI prompt pack, or automation workflow.', 'digital-products-pro-full' ),
			'status'      => __( 'Product ready', 'digital-products-pro-full' ),
			'duration'    => __( 'Draft stage', 'digital-products-pro-full' ),
			'actions'     => array(
				__( 'Upload product files', 'digital-products-pro-full' ),
				__( 'Set pricing and access rules', 'digital-products-pro-full' ),
				__( 'Generate product copy with AI', 'digital-products-pro-full' ),
				__( 'Publish the sales page', 'digital-products-pro-full' ),
			),
		),
		array(
			'id'          => 'order',
			'label'       => __( 'Customer Order', 'digital-products-pro-full' ),
			'title'       => __( 'A customer places an order', 'digital-products-pro-full' ),
			'description' => __( 'WooCommerce creates the order and securely stores the customer and product information.', 'digital-products-pro-full' ),
			'status'      => __( 'Order created', 'digital-products-pro-full' ),
			'duration'    => __( '0.3 seconds', 'digital-products-pro-full' ),
			'actions'     => array(
				__( 'Customer account identified', 'digital-products-pro-full' ),
				__( 'Order details recorded', 'digital-products-pro-full' ),
				__( 'Inventory rules checked', 'digital-products-pro-full' ),
				__( 'Checkout session secured', 'digital-products-pro-full' ),
			),
		),
		array(
			'id'          => 'payment',
			'label'       => __( 'Payment', 'digital-products-pro-full' ),
			'title'       => __( 'Payment is confirmed', 'digital-products-pro-full' ),
			'description' => __( 'The payment gateway confirms the transaction and WooCommerce updates the order status.', 'digital-products-pro-full' ),
			'status'      => __( 'Payment successful', 'digital-products-pro-full' ),
			'duration'    => __( '0.9 seconds', 'digital-products-pro-full' ),
			'actions'     => array(
				__( 'Payment authorized', 'digital-products-pro-full' ),
				__( 'Invoice created', 'digital-products-pro-full' ),
				__( 'Order marked as paid', 'digital-products-pro-full' ),
				__( 'Automation webhook triggered', 'digital-products-pro-full' ),
			),
		),
		array(
			'id'          => 'automation',
			'label'       => __( 'n8n Automation', 'digital-products-pro-full' ),
			'title'       => __( 'The automation workflow runs', 'digital-products-pro-full' ),
			'description' => __( 'n8n processes the order and coordinates delivery, notifications, CRM updates, and analytics.', 'digital-products-pro-full' ),
			'status'      => __( 'Workflow completed', 'digital-products-pro-full' ),
			'duration'    => __( '4.2 seconds', 'digital-products-pro-full' ),
			'actions'     => array(
				__( 'Download access generated', 'digital-products-pro-full' ),
				__( 'Telegram notification sent', 'digital-products-pro-full' ),
				__( 'CRM contact updated', 'digital-products-pro-full' ),
				__( 'Analytics event recorded', 'digital-products-pro-full' ),
			),
		),
		array(
			'id'          => 'delivery',
			'label'       => __( 'Delivery', 'digital-products-pro-full' ),
			'title'       => __( 'The customer receives the product', 'digital-products-pro-full' ),
			'description' => __( 'Secure download instructions and account access are delivered automatically.', 'digital-products-pro-full' ),
			'status'      => __( 'Product delivered', 'digital-products-pro-full' ),
			'duration'    => __( 'Instant', 'digital-products-pro-full' ),
			'actions'     => array(
				__( 'Secure download link created', 'digital-products-pro-full' ),
				__( 'Delivery email sent', 'digital-products-pro-full' ),
				__( 'Customer dashboard updated', 'digital-products-pro-full' ),
				__( 'Access event logged', 'digital-products-pro-full' ),
			),
		),
		array(
			'id'          => 'follow-up',
			'label'       => __( 'Follow-up', 'digital-products-pro-full' ),
			'title'       => __( 'Customer engagement continues', 'digital-products-pro-full' ),
			'description' => __( 'Automated follow-up messages help customers succeed and create opportunities for repeat sales.', 'digital-products-pro-full' ),
			'status'      => __( 'Sequence scheduled', 'digital-products-pro-full' ),
			'duration'    => __( 'After purchase', 'digital-products-pro-full' ),
			'actions'     => array(
				__( 'Welcome email scheduled', 'digital-products-pro-full' ),
				__( 'Usage guidance delivered', 'digital-products-pro-full' ),
				__( 'Review request prepared', 'digital-products-pro-full' ),
				__( 'Related product offer queued', 'digital-products-pro-full' ),
			),
		),
	);

	/**
	 * Filter workflow showcase steps.
	 *
	 * @param array<int, array<string, mixed>> $steps Workflow steps.
	 */
	return apply_filters( 'dpp_workflow_steps', $steps );
}
