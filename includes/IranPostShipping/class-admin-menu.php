<?php
/**
 * Iran Post Shipping Admin Menu Main Class
 *
 * @package Iran_Post_Shipping
 */

declare(strict_types=1);

namespace IranPostShipping;

/**
 * Class Admin_Menu
 */
final class Admin_Menu extends \DediData\Singleton {

	/**
	 * Constructor
	 */
	protected function __construct() {
		add_menu_page(
			esc_html__( 'Iran Shipping Methods', 'woocommerce-iran-post-shipping' ),
			esc_html__( 'Iran Shipping Methods', 'woocommerce-iran-post-shipping' ),
			'manage_options',
			'admin.php?page=wc-settings&tab=shipping',
			'',
			plugins_url( 'woocommerce-iran-post-shipping/assets/post-arm.png' ),
			56
		);

		add_submenu_page(
			'admin.php?page=wc-settings&tab=shipping',
			esc_html__( 'Bike Delivery', 'woocommerce-iran-post-shipping' ),
			esc_html__( 'Bike Delivery', 'woocommerce-iran-post-shipping' ),
			'manage_options',
			'admin.php?page=wc-settings&tab=shipping&section=iran_shipping_bike',
			''
		);

		add_submenu_page(
			'admin.php?page=wc-settings&tab=shipping',
			esc_html__( 'Certified Post', 'woocommerce-iran-post-shipping' ),
			esc_html__( 'Certified Post', 'woocommerce-iran-post-shipping' ),
			'manage_options',
			'admin.php?page=wc-settings&tab=shipping&section=iran_shipping_certified',
			''
		);

		add_submenu_page(
			'admin.php?page=wc-settings&tab=shipping',
			esc_html__( 'Express Post', 'woocommerce-iran-post-shipping' ),
			esc_html__( 'Express Post', 'woocommerce-iran-post-shipping' ),
			'manage_options',
			'admin.php?page=wc-settings&tab=shipping&section=iran_shipping_express',
			''
		);

		add_submenu_page(
			'admin.php?page=wc-settings&tab=shipping',
			esc_html__( 'Cash on Delivery', 'woocommerce-iran-post-shipping' ),
			esc_html__( 'Cash on Delivery', 'woocommerce-iran-post-shipping' ),
			'manage_options',
			'admin.php?page=wc-settings&tab=shipping&section=iran_shipping_cod',
			''
		);

		add_submenu_page(
			'admin.php?page=wc-settings&tab=shipping',
			esc_html__( 'DediData Plugins', 'woocommerce-iran-post-shipping' ),
			esc_html__( 'DediData Plugins', 'woocommerce-iran-post-shipping' ),
			'manage_options',
			'plugin-install.php?s=dedidata&tab=search&type=author',
			''
		);

		add_submenu_page(
			'admin.php?page=wc-settings&tab=shipping',
			esc_html__( 'DediData Themes', 'woocommerce-iran-post-shipping' ),
			esc_html__( 'DediData Themes', 'woocommerce-iran-post-shipping' ),
			'manage_options',
			'theme-install.php?search=dedidata',
			''
		);

		add_submenu_page(
			'admin.php?page=wc-settings&tab=shipping',
			esc_html__( 'Other DediData Services', 'woocommerce-iran-post-shipping' ),
			esc_html__( 'Other DediData Services', 'woocommerce-iran-post-shipping' ),
			'manage_options',
			esc_html__( 'https://dedidata.com', 'woocommerce-iran-post-shipping' ),
			''
		);
	}
}
