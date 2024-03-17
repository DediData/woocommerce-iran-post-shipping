<?php
/**
 * WC Shipping Bike
 *
 * @package Iran_Post_Shipping
 */

declare(strict_types=1);

namespace IranPostShipping;

/**
 * The WC_Shipping_Bike class is a subclass of WC_Shipping_Method.
 */
final class WC_Shipping_Bike extends \WC_Shipping_Method {

	/**
	 * Constructor for shipping class
	 *
	 * @return void
	 */
	public function __construct() {
		// Id for your shipping method. Should be unique.
		$this->id = 'iran_shipping_bike';
		// Title shown in admin
		$this->method_title = esc_html__( 'Bike Delivery', 'woocommerce-iran-post-shipping' );
		// Description shown in admin
		$this->method_description = __(
			'<h3>Bike Courier Delivery Method for WooCommerce</h3>
			<p style="text-align: justify">This method activates the motorcycle courier delivery option.</p>
			<p style="text-align: justify"><strong>Calculation Method: </strong>Considering the diverse rates for bike couriers in different cities, this method calculates the delivery fee based on the destination. By setting the origin province and city as the sender\'s location, if the destination province and city match the origin, this method will be activated during the order. If you want to consider the entire province, leave the city name blank and specify the province.</p>
			<p style="text-align: justify">
				<strong>Important Notes:</strong>
				<ol>
					<li>Ensure that you enter the weight for each product, and the acceptable weight units in WooCommerce settings are grams and kilograms.</li>
					<li>It is recommended to set the "Default Customer Location" option in WooCommerce general settings.</li>
					<li>This plugin is coded in accordance with WooCommerce standards and codes; however, other plugins, including "Persian WooCommerce", may introduce changes to this list that could affect the functionality of this plugin. If you encounter any issues, deactivate other plugins parallel to this one and test again. Additionally, WooCommerce, like WordPress, is inherently in Persian and does not require a Persian language plugin.</li>
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
		$this->source_state       = $this->get_option( 'source_state' );
		$this->source_city        = $this->get_option( 'source_city' );
		$this->fix_price          = $this->get_option( 'fix_price' );
		$this->free_for_price     = $this->get_option( 'free_for_price' );
		$this->disable_for_higher = $this->get_option( 'disable_for_higher' );
		// IRR or IRT or IRHT
		$this->current_currency = get_woocommerce_currency();
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
				'description' => esc_html__( 'This option controls the title that the user sees during the checkout. If you have set a fixed fee for the courier and want to charge the courier fee during the order, remove the phrase (Courier fee, collected upon package delivery)', 'woocommerce-iran-post-shipping' ),
				'default'     => esc_html__( 'Bike courier (courier fee is collected upon package delivery)', 'woocommerce-iran-post-shipping' ),
				'desc_tip'    => true,
			),
			'free_for_price'     => array(
				'title'       => esc_html__( 'Free shipping for the total order amount (amount in Iranian Rials)', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'If the total order amount equals or exceeds this value, it will be free of charge.', 'woocommerce-iran-post-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'disable_for_higher' => array(
				'title'       => esc_html__( 'If the weight of the shopping cart exceeds this amount, deactivate this method (weight in grams).', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'You can set the maximum acceptable weight for the shopping cart for this method.', 'woocommerce-iran-post-shipping' ),
				'default'     => '50000',
				'desc_tip'    => true,
			),
			'source_state'       => array(
				'title'       => esc_html__( 'Origin province (seller)', 'woocommerce-iran-post-shipping' ),
				'type'        => 'select',
				'description' => esc_html__( 'Please select the province where the courier is active in this section.', 'woocommerce-iran-post-shipping' ),
				'default'     => 'THR',
				'desc_tip'    => true,
				'options'     => array(
					'EAZ' => esc_html__( 'East Azerbaijan', 'woocommerce-iran-post-shipping' ),
					'WAZ' => esc_html__( 'West Azerbaijan', 'woocommerce-iran-post-shipping' ),
					'ADL' => esc_html__( 'Ardabil', 'woocommerce-iran-post-shipping' ),
					'ESF' => esc_html__( 'Isfahan', 'woocommerce-iran-post-shipping' ),
					'ABZ' => esc_html__( 'Alborz', 'woocommerce-iran-post-shipping' ),
					'ILM' => esc_html__( 'Ilam', 'woocommerce-iran-post-shipping' ),
					'BHR' => esc_html__( 'Bushehr', 'woocommerce-iran-post-shipping' ),
					'THR' => esc_html__( 'Tehran', 'woocommerce-iran-post-shipping' ),
					'CHB' => esc_html__( 'Chaharmahal and Bakhtiari', 'woocommerce-iran-post-shipping' ),
					'SKH' => esc_html__( 'South Khorasan', 'woocommerce-iran-post-shipping' ),
					'RKH' => esc_html__( 'Razavi Khorasan', 'woocommerce-iran-post-shipping' ),
					'NKH' => esc_html__( 'North Khorasan', 'woocommerce-iran-post-shipping' ),
					'KHZ' => esc_html__( 'Khuzestan', 'woocommerce-iran-post-shipping' ),
					'ZJN' => esc_html__( 'Zanjan', 'woocommerce-iran-post-shipping' ),
					'SMN' => esc_html__( 'Semnan', 'woocommerce-iran-post-shipping' ),
					'SBN' => esc_html__( 'Sistan and Baluchestan', 'woocommerce-iran-post-shipping' ),
					'FRS' => esc_html__( 'Fars', 'woocommerce-iran-post-shipping' ),
					'GZN' => esc_html__( 'Qazvin', 'woocommerce-iran-post-shipping' ),
					'QHM' => esc_html__( 'Qom', 'woocommerce-iran-post-shipping' ),
					'KRD' => esc_html__( 'Kurdistan', 'woocommerce-iran-post-shipping' ),
					'KRN' => esc_html__( 'Kerman', 'woocommerce-iran-post-shipping' ),
					'KRH' => esc_html__( 'Kermanshah', 'woocommerce-iran-post-shipping' ),
					'KBD' => esc_html__( 'Kohgiluyeh and Boyer-Ahmad', 'woocommerce-iran-post-shipping' ),
					'GLS' => esc_html__( 'Golestan', 'woocommerce-iran-post-shipping' ),
					'GIL' => esc_html__( 'Gilan', 'woocommerce-iran-post-shipping' ),
					'LRS' => esc_html__( 'Lorestan', 'woocommerce-iran-post-shipping' ),
					'MZN' => esc_html__( 'Mazandaran', 'woocommerce-iran-post-shipping' ),
					'MKZ' => esc_html__( 'Markazi', 'woocommerce-iran-post-shipping' ),
					'HRZ' => esc_html__( 'Hormozgan', 'woocommerce-iran-post-shipping' ),
					'HDN' => esc_html__( 'Hamedan', 'woocommerce-iran-post-shipping' ),
					'YZD' => esc_html__( 'Yazd', 'woocommerce-iran-post-shipping' ),
				),
			),
			'source_city'        => array(
				'title'       => 'Origin City (Seller)',
				'type'        => 'text',
				'description' => esc_html__( 'The courier delivery is only active in this city.', 'woocommerce-iran-post-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'fix_price'          => array(
				'title'       => esc_html__( 'Fixed fee (amount in Iranian Rials).', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'If you wish to consider a fixed fee for the bike courier, enter this amount in Iranian Rials. Otherwise, if you want the amount to be calculated as cash on delivery, leave this field empty.', 'woocommerce-iran-post-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Calculate_Shipping function.
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
		if ( $cart_weight > intval( $this->disable_for_higher ) ) {
			$this->enabled = 'no';
			return;
		}
		// find destination state
		$this->destination_state = $package['destination']['state'];
		// Iran Country
		if (
			'IR' !== $package['destination']['country'] ||
			( $this->destination_state !== $this->source_state && '' !== $package['destination']['state'] )
		) {
			return;
		}
		$this->destination_city = $package['destination']['city'];
		$this->source_city      = IRAN_POST_SHIPPING()->fix_arabic_letters( $this->source_city );
		$this->destination_city = IRAN_POST_SHIPPING()->fix_arabic_letters( $this->destination_city );
		// if cities are same or not
		if ( $this->source_city === $this->destination_city || '' === $this->source_city || '' === $this->destination_city ) {
			if ( '' !== $this->fix_price ) {
				$this->fix_price = intval( $this->fix_price );
				$shipping_total  = $this->fix_price;
			}
			// convert currency to current selected currency
			if ( 'IRT' === $this->current_currency ) {
				$shipping_total = ceil( $shipping_total / 10 );
			} elseif ( 'IRHT' === $this->current_currency ) {
				$shipping_total = ceil( $shipping_total / 10000 );
			}
			if ( '' !== $this->source_city ) {
				// Translators: The source city
				$this->title .= sprintf( esc_html_x( ' (Only for the city of %s)', 'The source city', 'woocommerce-iran-post-shipping' ), $this->source_city );
			} elseif ( '' !== $this->source_state ) {
				// Translators: The source province
				$this->title .= sprintf( esc_html_x( ' (Only for the province of %s)', 'The source province', 'woocommerce-iran-post-shipping' ), $this->form_fields['source_state']['options'][ $this->source_state ] );
			}

			$package_cost = $package['contents_cost'];
			// Convert current currency to rial
			if ( 'IRT' === $this->current_currency ) {
				$package_cost = $package['contents_cost'] * 10;
			} elseif ( 'IRHT' === $this->current_currency ) {
				$package_cost = $package['contents_cost'] * 10000;
			}

			$this->free_for_price = '' !== $this->free_for_price ? intval( $this->free_for_price ) : '';
			if ( '' !== $this->free_for_price && $package_cost >= $this->free_for_price ) {
				$shipping_total = 0;
			}

			// Register the rate
			$rate = array(
				'id'       => $this->id,
				'label'    => $this->title,
				'cost'     => $shipping_total,
				'calc_tax' => 'per_order',
			);
			$this->add_rate( $rate );
		}//end if
	}
}
