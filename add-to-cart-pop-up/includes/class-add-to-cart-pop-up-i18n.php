<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.toptal.com/resume/andrew-schultz
 * @since      1.0.0
 *
 * @package    Add_To_Cart_Pop_Up
 * @subpackage Add_To_Cart_Pop_Up/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Add_To_Cart_Pop_Up
 * @subpackage Add_To_Cart_Pop_Up/includes
 * @author     Andrew Schultz <andrew.schultz@toptal.com>
 */
class Add_To_Cart_Pop_Up_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'add-to-cart-pop-up',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
