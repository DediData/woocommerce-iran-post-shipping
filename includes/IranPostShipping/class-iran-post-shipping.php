<?php
/**
 * Iran Post Shipping Main Class
 *
 * @package Iran_Post_Shipping
 */

declare(strict_types=1);

namespace IranPostShipping;

/**
 * Class Iran_Post_Shipping
 */
final class Iran_Post_Shipping extends \DediData\Singleton {

	/**
	 * Plugin URL
	 * 
	 * @var string $plugin_url
	 */
	protected $plugin_url;

	/**
	 * Plugin Folder
	 * 
	 * @var string $plugin_folder
	 */
	protected $plugin_folder;

	/**
	 * Plugin Name
	 * 
	 * @var string $plugin_name
	 */
	protected $plugin_name;

	/**
	 * Plugin Version
	 * 
	 * @var string $plugin_version
	 */
	protected $plugin_version;
	
	/**
	 * Plugin Slug
	 * 
	 * @var string $plugin_slug
	 */
	protected $plugin_slug;

	/**
	 * Plugin File
	 * 
	 * @var string $plugin_file
	 */
	protected $plugin_file;

	/**
	 * Constructor
	 * 
	 * @param mixed $plugin_file Plugin File Name.
	 * @see https://developer.wordpress.org/reference/functions/register_activation_hook
	 * @see https://developer.wordpress.org/reference/functions/register_deactivation_hook
	 * @see https://developer.wordpress.org/reference/functions/register_uninstall_hook
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	protected function __construct( $plugin_file = null ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once \ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;
		}
		$this->plugin_file = $plugin_file;
		$this->set_plugin_info();
		register_activation_hook( $plugin_file, array( $this, 'activate' ) );
		register_deactivation_hook( $plugin_file, array( $this, 'deactivate' ) );
		register_uninstall_hook( $plugin_file, self::class . '::uninstall' );
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ), 11 );
			$this->admin();
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ), 11 );
			$this->run();
		}
		add_action( 'woocommerce_shipping_init', array( $this, 'woocommerce_iran_post_init' ) );
	}

	/**
	 * The function is used to load frontend scripts and styles in a WordPress plugin, with support for
	 * RTL (right-to-left) languages.
	 * 
	 * @return void
	 */
	public function load_frontend_scripts() {
		/*
		if ( is_rtl() ) {
			wp_register_style( $this->plugin_slug . '-rtl', $this->plugin_url . '/assets/public/css/style.rtl.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug . '-rtl' );
		} else {
			wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/public/css/style.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug );
		}

		wp_register_script( $this->plugin_slug, $this->plugin_url . '/assets/public/js/script.js', array(), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_slug );
		*/
	}

	/**
	 * Styles for Admin
	 * 
	 * @return void
	 */
	public function load_admin_scripts() {
		/*
		if ( is_rtl() ) {
			wp_register_style( $this->plugin_slug . '-rtl', $this->plugin_url . '/assets/admin/css/style.rtl.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug . '-rtl' );
		} else {
			wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/admin/css/style.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug );
		}

		wp_register_script( $this->plugin_slug, $this->plugin_url . '/assets/admin/js/script.js', array(), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_slug );
		*/
	}

	/**
	 * Activate the plugin
	 * 
	 * @return void
	 * @see https://developer.wordpress.org/reference/functions/add_option
	 */
	public function activate() {
		// add_option( $this->plugin_slug );
	}

	/**
	 * Run when plugins deactivated
	 * 
	 * @return void
	 */
	public function deactivate() {
		// Clear any temporary data stored by plugin.
		// Flush Cache/Temp.
		// Flush Permalinks.
	}

	/**
	 * Initialize Shipping Methods
	 * 
	 * @return void
	 */
	public function woocommerce_iran_post_init() {
		add_filter( 'woocommerce_shipping_methods', array( $this, 'iran_post_shipping_methods' ) );
	}

	/**
	 * It takes a string as a parameter and replaces certain Arabic letters with their corresponding Persian letters.
	 * 
	 * @param string $text String to replace letters.
	 * @return void
	 */
	public function fix_arabic_letters( string $text ) {
		$text = str_replace( 'ي', 'ی', $text );
		$text = str_replace( 'ك', 'ک', $text );
		$text = str_replace( 'آ', 'ا', $text );
		$text = str_replace( ' ', '', $text );
	}

	/**
	 * The function adds shipping methods for Iran to an existing array of shipping methods.
	 * 
	 * @param array<string> $methods Contains the shipping methods available for Iran.
	 * @return array<string> Shipping methods.
	 */
	public function iran_post_shipping_methods( array $methods ): array {
		$methods[] = 'IranPostShipping\WC_Shipping_COD';
		$methods[] = 'IranPostShipping\WC_Shipping_Bike';
		$methods[] = 'IranPostShipping\WC_Shipping_Express';
		$methods[] = 'IranPostShipping\WC_Shipping_Certified';
		return $methods;
	}

	/**
	 * Uninstall plugin
	 * 
	 * @return void
	 * @see https://developer.wordpress.org/reference/functions/delete_option
	 */
	public static function uninstall() {
		// delete_option( 'woocommerce-iran-post-shipping' );
		// Remove Tables from wpdb
		// global $wpdb;
		// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}woocommerce-iran-post-shipping");
		// Clear any cached data that has been removed.
		wp_cache_flush();
	}

	/**
	 * Set Plugin Info
	 * 
	 * @return void
	 */
	private function set_plugin_info() {
		$this->plugin_slug = basename( $this->plugin_file, '.php' );
		$this->plugin_url  = plugins_url( '', $this->plugin_file );

		if ( ! function_exists( 'get_plugins' ) ) {
			include_once \ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugin_folder  = plugin_dir_path( $this->plugin_file );
		$plugin_info          = get_plugins( '/' . plugin_basename( $this->plugin_folder ) );
		$plugin_file_name     = basename( $this->plugin_file );
		$this->plugin_version = $plugin_info[ $plugin_file_name ]['Version'];
		$this->plugin_name    = $plugin_info[ $plugin_file_name ]['Name'];
	}

	/**
	 * The function "run" is a placeholder function in PHP with no code inside.
	 * 
	 * @return void
	 */
	private function run() {
		// nothing for now
	}

	/**
	 * The admin function includes the options.php file and registers the admin menu.
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	private function admin() {
		// include( plugin_dir_path(__FILE__) . 'options.php' );
		add_action( 'admin_menu', array( '\IranPostShipping\Admin_Menu', 'get_instance' ) );
	}
}
