<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://fsylum.net
 * @since             1.0.0
 * @package           woocommerce_manychat
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce-Manychat
 * Plugin URI:        https://cosmo.cat
 * Description:       Integrate Manychat into your Woocommerce shop!
 * Version:           0.1.11
 * Author:            Nicola Cavallazzi
 * Author URI:        http://cosmo.cat
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-manychat
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/NicolaCavallazzi/woocommerce-manychat/',
	__FILE__,
	'woocommerce-manychat'
);


//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-manychat-activator.php
 */
function activate_woocommerce_manychat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-manychat-activator.php';
	woocommerce_manychat_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-manychat-deactivator.php
 */
function deactivate_woocommerce_manychat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-manychat-deactivator.php';
	woocommerce_manychat_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_manychat' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_manychat' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-manychat.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_manychat() {

	$plugin = new woocommerce_manychat();
	$plugin->run();

}
run_woocommerce_manychat();
