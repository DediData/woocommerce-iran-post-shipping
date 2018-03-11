<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Iran_Sefareshi_Shipping' ) ) {
	class WC_Shipping_Iran_Sefareshi extends WC_Shipping_Method {
		/**
		 * Constructor for shipping class
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			$this->id                 = 'iran_sefareshi_shipping'; // Id for your shipping method. Should be uunique.
			$this->method_title       = __( 'پست سفارشی' );  // Title shown in admin
			$this->method_description = __( '<h3>روش پست سفارشی ایران برای ووکامرس</h3>
			<p style="text-align: justify;">این روش مبلغ پستی را بر اساس نرخ پستی سال 1395 (آخرین نرخ محاسبه فعلی) محاسبه می کند. نحوه محاسبه بر اساس نرخ پستی بر اساس وزن، مبلغ بیمه، مالیات پستی و مسافت از مبدا تا مقصد بر اساس موقعیت دو استان نسبت به هم می باشد. لازم به ذکر است که مبلغ محاسباتی به صورت اتوماتیک بر اساس واحد پول ایران و واحد وزن تعیین شده در تنظیمات ووکامرس شما محاسبه و به صورت اتوماتیک به واحد های تعیین شده تبدیل میشود. واحد های پول قابل قبول، ریال، تومان و هزار تومان است و واحد های وزنی مورد قبول، گرم و کیلوگرم می باشد. توجه داشته باشید که نرخ های دیگر از جمله، هزینه پاکت، هزینه جعبه، بسته بندی، تمبر، مرسولات شکستنی، اضافه نرخ ها از مقر فرستنده، نرخ قبول خارج از وقت اداری، استعلام رهگیری الکترونیکی، شناسه الکترونیکی و دیگر موارد اختیاری در این نرخ محاسبه نشده اند و کاملا اختیاری می باشند. (باجه های پستی باید فقط با درخواست ارسال کننده این نرخ ها را اضافه کنند). در صورتی که نیاز به اضافه کردن مبلغ خاصی به این نرخ دارید می توانید از گزینه های هزینه های اضافی، به صورت مبلغ و یا درصد اضافه نمائید.
			<a href="https://parsmizban.com/" target="_blank" clain">جهت مشاهده دیگر افزونه های ایجاد شده ما و دیگر سرویس های ما اینجا را کلیک نمائید.</a></p>' ); // Description shown in admin
			$this->init();
		}

		/**
		 * Init your settings
		 *
		 * @access public
		 * @return void
		 */
		function init() {
			// Load the settings API
			$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
			$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

			// Define user set variables
			$this->enabled				= $this->get_option( 'enabled' );
			$this->title				= $this->get_option( 'title' );
			$this->extra_cost			= $this->get_option( 'extra_cost' );
			$this->extra_cost_percent	= $this->get_option( 'extra_cost_percent' );
			$this->source_state			= $this->get_option( 'source_state' );
			$this->current_currency		= get_woocommerce_currency(); // IRR or IRT or IRHT
			$this->current_weight_unit	= get_option('woocommerce_weight_unit'); // g or kg

			// Save settings in admin if you have any defined
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		/**
		 * Initialise Gateway Settings Form Fields
		 *
		 * @access public
		 * @return void
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
					'type' 			=> 'checkbox',
					'label' 		=> __( 'Enable this shipping method', 'woocommerce' ),
					'default' 		=> 'no',
				),
				'title' => array(
					'title' 		=> __( 'Method Title', 'woocommerce' ),
					'type' 			=> 'text',
					'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
					'default'		=> __( 'پست سفارشی' ),
					'desc_tip'		=> true
				),
				'extra_cost' => array(
					'title' 		=> __( 'هزینه های اضافی' ),
					'type' 			=> 'text',
					'description' 	=> __( 'در این قسمت هزینه های اضافی علاوه بر نرخ پستی را می توانید وارد نمائید ، از قبیل هزینه های بسته بندی و غیره - مبلغ ثابت را به ریال وارد نمائید.' ),
					'default'		=> __( '' ),
					'desc_tip'		=> true
				),
				'extra_cost_percent' => array(
					'title' 		=> __( 'هزینه های اضافی به درصد' ),
					'type' 			=> 'text',
					'description' 	=> __( 'در این قسمت هزینه های اضافی علاوه بر نرخ پستی را می توانید به درصد وارد نمائید - در این قسمت فقط عدد را وارد نمائید برای مثال برای 2% ، عدد 2 را وارد نمائید' ),
					'default'		=> __( '' ),
					'desc_tip'		=> true
				),
				'source_state' => array(
					'title' 		=> __( 'استان مبدا (فروشنده)' ),
					'type' 			=> 'select',
					'description' 	=> __( 'لطفا در این قسمت استانی که محصولات از آنجا ارسال می شوند را انتخاب نمائید' ),
					'default'		=> 'TE',
					'desc_tip'		=> true,
					'options' 		=> array(
						'AE' => __( 'آذربایجان شرقی' ),
						'AW' => __( 'آذربایجان غربی' ),
						'AR' => __( 'اردبیل' ),
						'IS' => __( 'اصفهان' ),
						'AL' => __( 'البرز' ),
						'IL' => __( 'ایلام' ),
						'BU' => __( 'بوشهر' ),
						'TE' => __( 'تهران' ),
						'CM' => __( 'چهارمحال و بختیاری' ),
						'KJ' => __( 'خراسان جنوبی' ),
						'KV' => __( 'خراسان رضوی' ),
						'KS' => __( 'خراسان شمالی' ),
						'KZ' => __( 'خوزستان' ),
						'ZA' => __( 'زنجان' ),
						'SM' => __( 'سمنان' ),
						'SB' => __( 'سیستان و بلوچستان' ),
						'FA' => __( 'فارس' ),
						'QZ' => __( 'قزوین' ),
						'QM' => __( 'قم' ),
						'KD' => __( 'کردستان' ),
						'KE' => __( 'کرمان' ),
						'BK' => __( 'کرمانشاه' ),
						'KB' => __( 'کهگیلویه و بویراحمد' ),
						'GO' => __( 'گلستان' ),
						'GI' => __( 'گیلان' ),
						'LO' => __( 'لرستان' ),
						'MN' => __( 'مازندران' ),
						'MK' => __( 'مرکزی' ),
						'HG' => __( 'هرمزگان' ),
						'HD' => __( 'همدان' ),
						'YA' => __( 'یزد' ),
					)
				)
			);
		}

		/**
		 * check if states are same or not
		 *
		 * @access public
		 * @param string $source
		 * @param string destination
		 * @return string
		 * in     = same
		 * out    = non beside
		 */
		public function check_states( $source, $destination){
			if ( $source == $destination )
				return 'in';
			else return 'out';
		}
		 
		
		/**
		 * calculate_shipping function.
		 *
		 * @access public
		 * @param mixed $package
		 * @return void
		 */
		public function calculate_shipping( $package = array() ) {
			global $woocommerce;
			$shipping_total = 0;
			
			// convert current currency to rial
			if ($this->current_currency =='IRT') {
				$this->extra_cost = $this->extra_cost * 10;
				$tmp_package_cost = $package['contents_cost'] * 10;
			} elseif ($this->current_currency =='IRHT'){
				$this->extra_cost = $this->extra_cost * 10000;
				$tmp_package_cost = $package['contents_cost'] * 10000;
			}
			
			//convert current weight unit to gram
			$cart_weight = $woocommerce->cart->cart_contents_weight;
			
			if ($this->current_weight_unit == 'kg') {
				$tmp_weight = $cart_weight * 1000;
			} else $tmp_weight = $cart_weight;

			// Iran Post Prices (prices are in Rial)
			//http://www.post.ir/_DouranPortal/Documents/%D9%86%D8%B1%D8%AE%D9%86%D8%A7%D9%85%D9%87%2095_20170620_085848.pdf
			$rate_price['500']['in'] 		= 32000;
			$rate_price['500']['out'] 		= 38000;
			
			$rate_price['1000']['in'] 		= 42000;
			$rate_price['1000']['out'] 		= 52000;
			
			$rate_price['2000']['in'] 		= 60000;
			$rate_price['2000']['out'] 		= 68000;
			
			$rate_price['9999']['in'] 		= 22500;
			$rate_price['9999']['out'] 		= 25000;
			
			// insurance (bime)
			$insurance = 6500;
			
			// post tax percent (#%)
			$post_tax = 9; // 9%
			
			// detect the weight plan
			if ( $tmp_weight <= 500 )
				$weight_indicator = '500';
			elseif ( $tmp_weight > 500 && $tmp_weight <= 1000 )
				$weight_indicator = '1000';
			elseif ( $tmp_weight > 1000 && $tmp_weight <= 2000 )
				$weight_indicator = '2000';
			elseif ( $tmp_weight > 2000 )
				$weight_indicator = '9999';

			// find destination state
			if ( $package['destination']['country'] == 'IR' ) {//Iran country
				$this->destination_state = $package['destination']['state']; //example: TE
										//$package['destination']['postcode'] //1234567890
										//$package['destination']['city']
			}

			// if states are beside or are same or not beside each other
			$checked_state = $this->check_states( $this->source_state , $this->destination_state);

			// calculate
			if ( $weight_indicator != '9999' ) { // is less than 2000 gram
				$shipping_total = $rate_price[$weight_indicator][$checked_state];
			} elseif ( $weight_indicator == '9999' ) { // is more than 2000 gram
				$shipping_total = $rate_price[$weight_indicator][$checked_state] * ceil( $tmp_weight / 1000);
			}

			// invalid post code price
			// 25%
			$invalid_postcode = ceil ( ( $shipping_total * 25 ) / 100 );

			// check invalid post code
			switch ( $package['destination']['postcode'] ){
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
				case ( strlen ( $package['destination']['postcode'] ) < 10 ):
				case ( strlen ( $package['destination']['postcode'] ) > 10 ):
					$shipping_total += $invalid_postcode;
					break;
			}

			// insurance (bime)
			$shipping_total += $insurance;
			
			// post tax
			$shipping_total += ceil( ( $shipping_total * $post_tax ) / 100 );

			// round to up for amounts fewer than 1000 rials
			$shipping_total = ( ceil ( $shipping_total / 1000 ) ) * 1000;
			
			// convert currency to current selected currency
			if ( $this->current_currency == 'IRT' ) {
				$shipping_total = ceil ( $shipping_total / 10 );
			} elseif ( $this->current_currency == 'IRHT' ) {
				$shipping_total = ceil ( $shipping_total / 10000 );
			}
			
			$this->extra_cost_percent   = intval ($this->extra_cost_percent);
			$this->extra_cost			= intval ($this->extra_cost);
			$shipping_total +=  ceil ( ( $shipping_total * $this->extra_cost_percent) / 100 );
			$shipping_total += $this->extra_cost;

			// Register the rate
			$rate = array(
				'id' => $this->id,
				'label' => $this->title,
				'cost' => $shipping_total,
				'calc_tax' => 'per_order'
			);
			$this->add_rate( $rate );
		}
	}
}

function iran_sefareshi_shipping( $methods ) {
	$methods[] = 'WC_Shipping_Iran_Sefareshi';
	return $methods;
}

add_filter( 'woocommerce_shipping_methods', 'iran_sefareshi_shipping' );
