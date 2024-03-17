=== Express, Certified Post, Bike Delivery and Iranian Postal Companies for WooCommerce ===
Contributors: dedidata, parsmizban, farhad0
Tags: woocommerce, shipping, order, weight based shipping, woocommerce shipping
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 5.0.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://dedidata.com

Express & Certified Post, Bike Delivery and Iranian Postal Companies for WooCommerce

== Description ==

This plugin adds express, certified, courier, and other shipping methods to WooCommerce, automatically calculating the shipping costs.

= Specifications =
* Adding express and certified shipping methods, bike courier, and cash on delivery.
* Automatic calculation of postage rates for certified and express mail.
* Ability to add a fixed amount.
* Ability to add an amount as a percentage.
* Determining the distance from the origin to the destination and automatic rate calculation.
* Calculation based on currency units: Rial, Toman, and Thousand Toman.
* Calculation based on weight units: Grams and Kilograms.
* Detection of invalid destination postal codes.
* Calculation of rates for invalid postal codes.
* Automatic calculation of insurance amount.
* Automatic calculation of postal tax amount.
* Rate calculation based on weight.
* Automatic addition of Iranian currency units if not available.
* Automatic addition of the list of Iranian provinces if not available.
* Rounding up amounts below 100 Tomans.
Validated by:
https://wpreadme.com/
https://wordpress.org/plugins/developers/readme-validator

== Installation ==

### Minimum Requirements

* WordPress 6.0 or greater
* PHP 7.0 or greater is required (PHP 8.0 or greater is recommended)
* MySQL 5.6 or greater, OR MariaDB version 10.1 or greater, is required.
* WooCommerce

### You can install this plugin in two ways:

= Automatic installation (Install from within WordPress) =

Automatic installation is the easiest option, WordPress will handles the file transfer, and you won’t need to leave your web browser.

1. Log in to your WordPress dashboard
2. Navigate to the Plugins menu within your dashboard
3. click “Add New.”
4. In the search field type the name of this plugin and then click “Search Plugins.”
5. Once you’ve found us,  you can view details about it such as the point release, rating, and description.
6. Most importantly of course, you can install it by! Click “Install Now,” and WordPress will take it from there.
7. Activate the plugin from your Plugins page

= Manual installation =

Manual installation method requires downloading the this plugin and uploading it to your web server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation).

1. Download the plugin ZIP file and extract it
2. Upload the extracted folder to the /wp-content/plugins/ directory
3. Activate the plugin through the ‘Plugins’ menu in WordPress

== Upgrade Notice ==

Automatic updates should work smoothly, but we still recommend you back up your site.

== Frequently Asked Questions ==

= Does this plugin automatically calculate postage rates? =

The calculation method is based on postal rates, weight, insurance amount, postal tax, and the distance from the origin to the destination relative to the position of the two provinces. It's important to note that the calculated amount is automatically converted to the currency unit of Iran and the weight unit specified in your WooCommerce settings.

= What currency units are supported in this module? =

The accepted currency units are Rial, Toman, and Thousand Toman.

= What weight units are supported in this module? =

The accepted weight units are grams and kilograms.
 
= Which website should we visit for support? =

Support is available through the forum of the author website.

== Screenshots ==
1. Management Section
2. Calculating Rates in the Shopping Cart

== Changelog ==
= 5.0.2 =
Whole plugin rewritten
Multilingual support
Updated prices
Standard Coding

= 3.6.0 =
Updating Postal Rates
Adding Maximum Weight for Packages
Correcting Descriptions
Fixing Some Minor Issues

= 3.5.0 =
Calculating Based on Postal Rates for the Year 2019
Calculating Adjacent, Non-Adjacent, and Same Province Rates
Addressing Some Issues

= 3.0.0 =
Fixing Issues with Province Calculations
Adding Two Methods: Bike Courier and Cash on Delivery

= 2.0.0 =
Updating Postal Rates and Calculation Methods, etc.

= 1.0.9 =
Updating Postal Rates

= 1.0.8 =
Correcting Invalid Postal Code Calculation

= 1.0.7 =
Correction of Calculations

= 1.0.6 =
* Correction of Express Rate Calculation for Weights Over 2 Kilograms

= 1.0.4 =
* Updating Custom Postal Rates

= 1.0.3 =
* Removing the utility file as per the request of WooCommerce Persian and...
* Rounding up amounts below 100 Toman upon request

= 1.0.1 =
* Resolving Calculation Issue

= 1.0.0 =
* First Version of the Plugin
