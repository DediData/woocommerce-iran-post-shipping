<?php
/**
 * WC Shipping COD
 *
 * @package Iran_Post_Shipping
 */

declare(strict_types=1);

namespace IranPostShipping;

/**
 * The WC_Shipping_COD class is a subclass of WC_Shipping_Method.
 */
final class WC_Shipping_COD extends \WC_Shipping_Method {
		
	/**
	 * Constructor for shipping class
	 *
	 * @return void
	 */
	public function __construct() {
		// Id for your shipping method. Should be unique.
		$this->id = 'iran_shipping_cod';
		// Title shown in admin
		$this->method_title = esc_html__( 'COD (Cash on Delivery)', 'woocommerce-iran-post-shipping' );
		// Description shown in admin
		$this->method_description = __(
			'<h3>Shipping Method by Courier Companies with Cash on Delivery</h3>
			<p style="text-align: justify">With this method, you can send your desired package through any of the courier companies such as Tipax, Chapar, TNT, etc., which accept payment for shipping as cash on delivery.</p>
			<p style="text-align: justify"><strong>Calculation Method:</strong>Since the rates for these companies vary in different cities, the shipping cost will be calculated at the destination. Please note that you should review the terms and conditions of these companies and consider them before placing an order.</p>
			<p style="text-align: justify">
				<strong>Important Points:</strong>
				<ol>
					<li>Ensure that you enter the weight for each product, and the acceptable weight units in the WooCommerce settings are grams and kilograms.</li>
					<li>It is recommended to set the "Default Customer Location" in the general WooCommerce settings.</li>
					<li>This plugin is coded according to WooCommerce coding standards, and the state values are created based on WooCommerce\'s own principles. However, other plugins, such as "Persian WooCommerce", may introduce changes to this list that may cause issues with the functionality of this plugin. If you encounter any problems, deactivate other plugins parallel to this one and test again. Additionally, WooCommerce, like WordPress, is already translated into Persian and does not require a separate translation plugin.</li>
				</ol>
			</p>',
			'woocommerce-iran-post-shipping'
		);
		$this->init();
	}

	/**
	 * Init your settings
	 *
	 * @return void
	 */
	public function init() {
		// Load the settings API
		// This is part of the settings API. Override the method to add your own settings
		$this->init_form_fields();
		// This is part of the settings API. Loads settings you previously init.
		$this->init_settings();

		// Define user set variables
		$this->enabled            = $this->get_option( 'enabled' );
		$this->title              = $this->get_option( 'title' );
		$this->extra_cost         = intval( $this->get_option( 'extra_cost' ) );
		$this->extra_cost_percent = intval( $this->get_option( 'extra_cost_percent' ) );
		// IRR or IRT or IRHT
		$this->current_currency = get_woocommerce_currency();
		$this->minimum_weight   = $this->get_option( 'minimum_weight' );
		// g or kg
		$this->current_weight_unit = get_option( 'woocommerce_weight_unit' );
		// Save settings in admin if you have any defined
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'            => array(
				'title'   => esc_html__( 'Active/Inactive', 'woocommerce-iran-post-shipping' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Activate this shipping method', 'woocommerce-iran-post-shipping' ),
				'default' => 'no',
			),
			'title'              => array(
				'title'       => esc_html__( 'Method Title', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'This option controls the title that the user sees during the checkout process. You can specify the name of the shipping company in this section.', 'woocommerce-iran-post-shipping' ),
				'default'     => esc_html__( 'Company Name (Shipping Fee Collected on Delivery)', 'woocommerce-iran-post-shipping' ),
				'desc_tip'    => true,
			),
			'extra_cost'         => array(
				'title'       => esc_html__( 'Additional costs in Iranian Rial', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'In this section, you can enter additional costs in addition to the postage rate, such as packaging costs, etc. Enter the fixed amount in Iranian Rial.', 'woocommerce-iran-post-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'extra_cost_percent' => array(
				'title'       => esc_html__( 'Additional costs as a percentage', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'In this section, you can enter additional costs as a percentage in addition to the postage rate. Enter only the numeric value in this section. For example, for 2%, enter the number 2.', 'woocommerce-iran-post-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'minimum_weight'     => array(
				'title'       => esc_html__( 'Active only for a weight greater than or equal to this amount (in grams)', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'If you enter this value, this shipping method will only be active for a total weight greater than or equal to this amount.', 'woocommerce-iran-post-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Calculate_shipping function.
	 *
	 * @param mixed $package Package object.
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function calculate_shipping( $package = array() ) {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$woocommerce    = $GLOBALS['woocommerce'];
		$shipping_total = 0;
		$cart_weight    = $woocommerce->cart->cart_contents_weight;
		// Convert current weight unit to gram
		if ( 'kg' === $this->current_weight_unit ) {
			$cart_weight *= 1000;
		}

		// Detect the weight plan
		if ( '' === $this->minimum_weight ) {
			return;
		}
		$this->minimum_weight = intval( $this->minimum_weight );
		if ( $cart_weight < $this->minimum_weight ) {
			return;
		}

		$package_cost = $package['contents_cost'];
		// Convert current currency to rial
		if ( 'IRT' === $this->current_currency ) {
			$package_cost = $package['contents_cost'] * 10;
		} elseif ( 'IRHT' === $this->current_currency ) {
			$package_cost = $package['contents_cost'] * 10000;
		}

		$shipping_total  += ceil( $package_cost * $this->extra_cost_percent / 100 );
		$this->extra_cost = intval( $this->extra_cost );
		$shipping_total  += $this->extra_cost;

		// convert currency to current selected currency
		if ( 'IRT' === $this->current_currency ) {
			$shipping_total = ceil( $shipping_total / 10 );
		} elseif ( 'IRHT' === $this->current_currency ) {
			$shipping_total = ceil( $shipping_total / 10000 );
		}

		// Register the rate
		$rate = array(
			'id'       => $this->id,
			'label'    => $this->title,
			'cost'     => $shipping_total,
			'calc_tax' => 'per_order',
		);
		$this->add_rate( $rate );
	}
}
