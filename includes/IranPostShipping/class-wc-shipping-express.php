<?php
/**
 * WC Shipping Express
 *
 * @package Iran_Post_Shipping
 */

declare(strict_types=1);

namespace IranPostShipping;

/**
 * The WC_Shipping_Express class is a subclass of WC_Shipping_Method.
 */
final class WC_Shipping_Express extends \WC_Shipping_Method {
	
	/**
	 * Constructor for shipping class
	 *
	 * @return void
	 */
	public function __construct() {
		// Id for your shipping method. Should be unique.
		$this->id = 'iran_shipping_express';
		// Title shown in admin
		$this->method_title = esc_html__( 'Express Post', 'woocommerce-iran-post-shipping' );
		// Description shown in admin
		$this->method_description = __(
			'<h3>Iran Express Post Shipping Method for WooCommerce</h3>
			<p style="text-align: justify">This method calculates the postage amount based on the postal rates for the year 1402 (latest current calculation).</p>
			<p style="text-align: justify"><strong>Calculation Method:</strong>The amount is calculated based on the postal rate, weight, insurance amount, postage tax, and the distance from the origin to the destination, considering the relationship between the two provinces. It\'s important to note that the calculated amount is automatically converted to the currency unit of Iran and the weight unit specified in your WooCommerce settings.</p>
			<p style="text-align: justify">
				<strong>Important Notes:</strong>
				<ol>
					<li>Ensure that you enter the weight for each product, and the acceptable weight units in the WooCommerce settings are grams and kilograms.</li>
					<li>Other rates, such as pocket cost, box cost, packaging, stamp, breakable items cost, additional rates from the sender\'s location, off-hours acceptance rate, electronic tracking inquiry, electronic ID, and other optional items, are not included in this rate calculation and are entirely optional (post offices are obliged to add these rates only upon the sender\'s request).</li>
					<li>If you need to add a specific amount to this rate, you can use the additional cost options, either as a fixed amount or a percentage.</li>
					<li>It is recommended to set the "Default Customer Location" in the general WooCommerce settings.</li>
					<li>This plugin is coded according to WooCommerce coding standards, and the state values are created based on WooCommerce\'s own principles. However, other plugins, such as "WooCommerce Persian", may introduce changes to this list that may cause issues with the functionality of this plugin. If you encounter any problems, deactivate other plugins parallel to this one and test again. Additionally, WooCommerce, like WordPress, is already translated into Persian and does not require a separate translation plugin.</li>
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
		$this->source_state       = $this->get_option( 'source_state' );
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
				'description' => esc_html__( 'This option controls the title that the user sees during the checkout.', 'woocommerce-iran-post-shipping' ),
				'default'     => esc_html__( 'Express Post', 'woocommerce-iran-post-shipping' ),
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
			'free_for_price'     => array(
				'title'       => esc_html__( 'Free shipping for total orders (amount in Iranian Rials)', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'If the total order amount equals or exceeds this value, it will be eligible for free shipping', 'woocommerce-iran-post-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'disable_for_higher' => array(
				'title'       => esc_html__( 'If the weight of the shopping cart exceeds this amount, this method will be deactivated (weight in grams)', 'woocommerce-iran-post-shipping' ),
				'type'        => 'text',
				'description' => esc_html__( 'You can specify the maximum acceptable weight in the shopping cart for this method.', 'woocommerce-iran-post-shipping' ),
				'default'     => '20000',
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
		);
	}

	/**
	 * Check if states are beside each other
	 *
	 * @param string $source      Source address.
	 * @param string $destination Destination address.
	 * @return string
	 * in     = same
	 * beside = beside
	 * out    = non beside
	 */
	public function check_states_beside( string $source, string $destination ) {
		$is_beside               = array();
		$is_beside['EAZ']['WAZ'] = true;
		$is_beside['EAZ']['ADL'] = true;
		$is_beside['EAZ']['ZJN'] = true;
		$is_beside['WAZ']['EAZ'] = true;
		$is_beside['WAZ']['KRD'] = true;
		$is_beside['WAZ']['ZJN'] = true;
		$is_beside['ADL']['EAZ'] = true;
		$is_beside['ADL']['GIL'] = true;
		$is_beside['ADL']['ZJN'] = true;
		$is_beside['ESF']['CHB'] = true;
		$is_beside['ESF']['LRS'] = true;
		$is_beside['ESF']['KBD'] = true;
		$is_beside['ESF']['MKZ'] = true;
		$is_beside['ESF']['QHM'] = true;
		$is_beside['ESF']['SMN'] = true;
		$is_beside['ESF']['SKH'] = true;
		$is_beside['ESF']['YZD'] = true;
		$is_beside['ESF']['FRS'] = true;
		$is_beside['ABZ']['THR'] = true;
		$is_beside['ABZ']['MKZ'] = true;
		$is_beside['ABZ']['GZN'] = true;
		$is_beside['ABZ']['MZN'] = true;
		$is_beside['ILM']['KRH'] = true;
		$is_beside['ILM']['LRS'] = true;
		$is_beside['ILM']['KHZ'] = true;
		$is_beside['BHR']['KBD'] = true;
		$is_beside['BHR']['KHZ'] = true;
		$is_beside['BHR']['FRS'] = true;
		$is_beside['BHR']['HRZ'] = true;
		$is_beside['THR']['ABZ'] = true;
		$is_beside['THR']['MKZ'] = true;
		$is_beside['THR']['QHM'] = true;
		$is_beside['THR']['MZN'] = true;
		$is_beside['THR']['SMN'] = true;
		$is_beside['CHB']['KBD'] = true;
		$is_beside['CHB']['KHZ'] = true;
		$is_beside['CHB']['LRS'] = true;
		$is_beside['CHB']['ESF'] = true;
		$is_beside['SKH']['SBN'] = true;
		$is_beside['SKH']['KRN'] = true;
		$is_beside['SKH']['YZD'] = true;
		$is_beside['SKH']['ESF'] = true;
		$is_beside['SKH']['SMN'] = true;
		$is_beside['SKH']['RKH'] = true;
		$is_beside['RKH']['SKH'] = true;
		$is_beside['RKH']['NKH'] = true;
		$is_beside['RKH']['SMN'] = true;
		$is_beside['NKH']['RKH'] = true;
		$is_beside['NKH']['GLS'] = true;
		$is_beside['NKH']['SMN'] = true;
		$is_beside['KHZ']['ILM'] = true;
		$is_beside['KHZ']['BHR'] = true;
		$is_beside['KHZ']['LRS'] = true;
		$is_beside['KHZ']['KBD'] = true;
		$is_beside['KHZ']['CHB'] = true;
		$is_beside['ZJN']['GIL'] = true;
		$is_beside['ZJN']['ADL'] = true;
		$is_beside['ZJN']['EAZ'] = true;
		$is_beside['ZJN']['WAZ'] = true;
		$is_beside['ZJN']['KRD'] = true;
		$is_beside['ZJN']['HDN'] = true;
		$is_beside['ZJN']['GZN'] = true;
		$is_beside['SMN']['MZN'] = true;
		$is_beside['SMN']['THR'] = true;
		$is_beside['SMN']['QHM'] = true;
		$is_beside['SMN']['ESF'] = true;
		$is_beside['SMN']['NKH'] = true;
		$is_beside['SMN']['RKH'] = true;
		$is_beside['SMN']['SKH'] = true;
		$is_beside['SBN']['SKH'] = true;
		$is_beside['SBN']['KRN'] = true;
		$is_beside['SBN']['HRZ'] = true;
		$is_beside['FRS']['ESF'] = true;
		$is_beside['FRS']['YZD'] = true;
		$is_beside['FRS']['BHR'] = true;
		$is_beside['FRS']['HRZ'] = true;
		$is_beside['FRS']['KBD'] = true;
		$is_beside['FRS']['KRN'] = true;
		$is_beside['GZN']['ZJN'] = true;
		$is_beside['GZN']['HDN'] = true;
		$is_beside['GZN']['MKZ'] = true;
		$is_beside['GZN']['ABZ'] = true;
		$is_beside['GZN']['MZN'] = true;
		$is_beside['GZN']['GIL'] = true;
		$is_beside['QHM']['THR'] = true;
		$is_beside['QHM']['MKZ'] = true;
		$is_beside['QHM']['SMN'] = true;
		$is_beside['QHM']['ESF'] = true;
		$is_beside['KRD']['WAZ'] = true;
		$is_beside['KRD']['KRH'] = true;
		$is_beside['KRD']['HDN'] = true;
		$is_beside['KRD']['ZJN'] = true;
		$is_beside['KRN']['YZD'] = true;
		$is_beside['KRN']['FRS'] = true;
		$is_beside['KRN']['HRZ'] = true;
		$is_beside['KRN']['SBN'] = true;
		$is_beside['KRN']['SKH'] = true;
		$is_beside['KRH']['KRD'] = true;
		$is_beside['KRH']['HDN'] = true;
		$is_beside['KRH']['LRS'] = true;
		$is_beside['KRH']['ILM'] = true;
		$is_beside['KBD']['CHB'] = true;
		$is_beside['KBD']['KHZ'] = true;
		$is_beside['KBD']['BHR'] = true;
		$is_beside['KBD']['FRS'] = true;
		$is_beside['KBD']['ESF'] = true;
		$is_beside['GLS']['MZN'] = true;
		$is_beside['GLS']['NKH'] = true;
		$is_beside['GLS']['SMN'] = true;
		$is_beside['GIL']['MZN'] = true;
		$is_beside['GIL']['ADL'] = true;
		$is_beside['GIL']['ZJN'] = true;
		$is_beside['GIL']['GZN'] = true;
		$is_beside['LRS']['ILM'] = true;
		$is_beside['LRS']['KRH'] = true;
		$is_beside['LRS']['HDN'] = true;
		$is_beside['LRS']['MKZ'] = true;
		$is_beside['LRS']['ESF'] = true;
		$is_beside['LRS']['CHB'] = true;
		$is_beside['LRS']['KHZ'] = true;
		$is_beside['MZN']['GLS'] = true;
		$is_beside['MZN']['SMN'] = true;
		$is_beside['MZN']['THR'] = true;
		$is_beside['MZN']['ABZ'] = true;
		$is_beside['MZN']['ESF'] = true;
		$is_beside['MZN']['GZN'] = true;
		$is_beside['MZN']['GIL'] = true;
		$is_beside['MKZ']['ESF'] = true;
		$is_beside['MKZ']['QHM'] = true;
		$is_beside['MKZ']['THR'] = true;
		$is_beside['MKZ']['ABZ'] = true;
		$is_beside['MKZ']['LRS'] = true;
		$is_beside['MKZ']['GZN'] = true;
		$is_beside['MKZ']['HDN'] = true;
		$is_beside['HRZ']['BHR'] = true;
		$is_beside['HRZ']['FRS'] = true;
		$is_beside['HRZ']['KRN'] = true;
		$is_beside['HRZ']['SBN'] = true;
		$is_beside['HDN']['KRH'] = true;
		$is_beside['HDN']['LRS'] = true;
		$is_beside['HDN']['KRD'] = true;
		$is_beside['HDN']['MKZ'] = true;
		$is_beside['HDN']['GZN'] = true;
		$is_beside['HDN']['ZJN'] = true;
		$is_beside['YZD']['ESF'] = true;
		$is_beside['YZD']['FRS'] = true;
		$is_beside['YZD']['KRN'] = true;
		$is_beside['YZD']['SKH'] = true;

		if ( isset( $is_beside[ $source ][ $destination ] ) && $is_beside[ $source ][ $destination ] ) {
			return 'beside';
		}
		if ( $source === $destination ) {
			return 'in';
		}
		return 'out';
	}

	/**
	 * Calculate Shipping function.
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
		
		$package_cost = $package['contents_cost'];
		// Convert current currency to rial
		if ( 'IRT' === $this->current_currency ) {
			$package_cost = $package['contents_cost'] * 10;
		} elseif ( 'IRHT' === $this->current_currency ) {
			$package_cost = $package['contents_cost'] * 10000;
		}

		$this->free_for_price = '' !== $this->free_for_price ? intval( $this->free_for_price ) : '';
		if ( '' === $this->free_for_price || $package_cost < $this->free_for_price ) {
			$rate_price = array();
			// Iran Post Prices (prices are in Rial)
			$rate_price['500']['in']      = 142790;
			$rate_price['500']['beside']  = 187480;
			$rate_price['500']['out']     = 200560;
			$rate_price['1000']['in']     = 178760;
			$rate_price['1000']['beside'] = 235440;
			$rate_price['1000']['out']    = 261600;
			$rate_price['2000']['in']     = 231080;
			$rate_price['2000']['beside'] = 294300;
			$rate_price['2000']['out']    = 322640;
			$rate_price['3000']['in']     = 285580;
			$rate_price['3000']['beside'] = 348800;
			$rate_price['3000']['out']    = 377140;
			$rate_price['9999']['in']     = 50000;
			$rate_price['9999']['beside'] = 50000;
			$rate_price['9999']['out']    = 50000;
			
			// insurance
			$insurance = 8000;
			
			// post tax percent (#%)
			// 9%
			$post_tax = 9;
			
			// detect the weight plan
			if ( $cart_weight <= 500 ) {
				$weight_indicator = '500';
			} elseif ( $cart_weight > 500 && $cart_weight <= 1000 ) {
				$weight_indicator = '1000';
			} elseif ( $cart_weight > 1000 && $cart_weight <= 2000 ) {
				$weight_indicator = '2000';
			} elseif ( $cart_weight > 2000 && $cart_weight <= 3000 ) {
				$weight_indicator = '3000';
			} elseif ( $cart_weight > 3000 ) {
				$weight_indicator = '9999';
			}

			// find destination state
			if ( 'IR' === $package['destination']['country'] ) {
				// Iran country
				// Example: TE
				$this->destination_state = $package['destination']['state'];
				// $package['destination']['postcode'] //1234567890
				// $package['destination']['city']
			}

			// if states are beside or are same or not beside each other
			$checked_state = $this->check_states_beside( $this->source_state, $this->destination_state );
			
			// calculate
			if ( '9999' !== $weight_indicator ) {
				// Is less than 3000 grams
				$shipping_total = $rate_price[ $weight_indicator ][ $checked_state ];
			} elseif ( '9999' === $weight_indicator ) {
				// Is more than 3000 grams
				$shipping_total = $rate_price['3000'][ $checked_state ] + ( $rate_price['9999'][ $checked_state ] * ceil( ( $cart_weight - 3000 ) / 1000 ) );
			}

			// invalid post code price
			// 25%
			$invalid_postcode = ceil( $shipping_total * 25 / 100 );

			// check invalid post code
			switch ( $package['destination']['postcode'] ) {
				case '1234567890':
				case '1111111111':
				case '2222222222':
				case '3333333333':
				case '4444444444':
				case '5555555555':
				case '6666666666':
				case '7777777777':
				case '8888888888':
				case '9999999999':
				case '0000000000':
				case '0987654321':
				case '1234567891':
				case '0123456789':
				case '7894561230':
				case strlen( $package['destination']['postcode'] ) < 10:
				case strlen( $package['destination']['postcode'] ) > 10:
					$shipping_total += $invalid_postcode;
					// Fall-through is intentional for other cases
					// No additional action needed for those cases
					// Intentional fall-through
				default:
					// No additional action needed for other cases
					break;
			}//end switch

			// insurance
			$shipping_total += $insurance;
			
			// post tax
			$shipping_total += ceil( $shipping_total * $post_tax / 100 );

			// round to up for amounts fewer than 1000 rials
			$shipping_total = ( ceil( $shipping_total / 1000 ) ) * 1000;
			
			$shipping_total  += ceil( $shipping_total * $this->extra_cost_percent / 100 );
			$this->extra_cost = intval( $this->extra_cost );
			$shipping_total  += $this->extra_cost;

			// convert currency to current selected currency
			if ( 'IRT' === $this->current_currency ) {
				$shipping_total = ceil( $shipping_total / 10 );
			} elseif ( 'IRHT' === $this->current_currency ) {
				$shipping_total = ceil( $shipping_total / 10000 );
			}
		}//end if
		
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
