<?php
/*
Plugin Name: روش حمل و نقل پست ایران برای ووکامرس
Plugin URI: http://parsmizban.com
Description: این پلاگین روش حمل و نقل پست سفارشی و پیشتاز ایران را محاسبه و به سیستم اضافه می کند.
Version: 2.0
Author: پارس میزبان (فرهاد سخایی)
Author URI: http://parsmizban.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	function woocommerce_iran_post_init() {
		include ('includes/sefareshi.php');
		include ('includes/pishtaz.php');
	}

	add_action( 'woocommerce_shipping_init', 'woocommerce_iran_post_init' );
}
