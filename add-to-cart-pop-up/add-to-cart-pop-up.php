<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.toptal.com/resume/andrew-schultz
 * @since             1.0.0
 * @package           Add_To_Cart_Pop_Up
 *
 * @wordpress-plugin
 * Plugin Name:       Add To Cart Pop Up
 * Plugin URI:        toptal.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Andrew Schultz
 * Author URI:        https://www.toptal.com/resume/andrew-schultz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       add-to-cart-pop-up
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PASSKIT_API_KEY', '1yDpZEAUpj5DKFCBT91I2' );
define( 'PASSKIT_API_SECRET', 'r.9B3Ffb9ale/2BMhbyqz.bCN51MsuvnpbYNejFH7WzfsA4L9wrH2' );
define ( 'TWILIO_NUMBER', '61418892126' );
define( 'TWILIO_SID', 'ACd51c63d1451cf3500294ef5f50026b58' );
define( 'TWILIO_TOKEN', '0d0d2a8e03a7dac1c0ce43f4177cf88a' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-add-to-cart-pop-up-activator.php
 */
function activate_add_to_cart_pop_up() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-add-to-cart-pop-up-activator.php';
	Add_To_Cart_Pop_Up_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-add-to-cart-pop-up-deactivator.php
 */
function deactivate_add_to_cart_pop_up() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-add-to-cart-pop-up-deactivator.php';
	Add_To_Cart_Pop_Up_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_add_to_cart_pop_up' );
register_deactivation_hook( __FILE__, 'deactivate_add_to_cart_pop_up' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-add-to-cart-pop-up.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_add_to_cart_pop_up() {

	$plugin = new Add_To_Cart_Pop_Up();
	$plugin->run();

}

function write_log ( $log )  {
	if ( true === WP_DEBUG ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}
	
run_add_to_cart_pop_up();
