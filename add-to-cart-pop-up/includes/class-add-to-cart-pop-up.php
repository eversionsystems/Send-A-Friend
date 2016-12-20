<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.toptal.com/resume/andrew-schultz
 * @since      1.0.0
 *
 * @package    Add_To_Cart_Pop_Up
 * @subpackage Add_To_Cart_Pop_Up/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Add_To_Cart_Pop_Up
 * @subpackage Add_To_Cart_Pop_Up/includes
 * @author     Andrew Schultz <andrew.schultz@toptal.com>
 */
class Add_To_Cart_Pop_Up {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Add_To_Cart_Pop_Up_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'add-to-cart-pop-up';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Add_To_Cart_Pop_Up_Loader. Orchestrates the hooks of the plugin.
	 * - Add_To_Cart_Pop_Up_i18n. Defines internationalization functionality.
	 * - Add_To_Cart_Pop_Up_Admin. Defines all hooks for the admin area.
	 * - Add_To_Cart_Pop_Up_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-add-to-cart-pop-up-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-add-to-cart-pop-up-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-add-to-cart-pop-up-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-add-to-cart-pop-up-public.php';
		
		/**
		 * Twilio PHP SDK
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/twilio-php-master/Twilio/autoload.php';
		
		/**
		 * PassKit-v2 PHP SDK
		 */
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/passkit/passkit-v2-sdk.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-PassKit-v2.php';

		$this->loader = new Add_To_Cart_Pop_Up_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Add_To_Cart_Pop_Up_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Add_To_Cart_Pop_Up_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Add_To_Cart_Pop_Up_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Add_To_Cart_Pop_Up_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'atc_remove_actions' );
		
		// AJAX calls for adding the user entered data into the cart cache
		$this->loader->add_action( 'wp_ajax_js_action_launch_cart_pop_up', $plugin_public, 'atc_add_customer_product_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_js_action_launch_cart_pop_up', $plugin_public, 'atc_add_customer_product_data' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'atc_add_pop_up_html' );
		
		// AJAX call for editing data
		$this->loader->add_action( 'wp_ajax_js_action_edit_cart_pop_up', $plugin_public, 'atc_edit_customer_product_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_js_action_edit_cart_pop_up', $plugin_public, 'atc_edit_customer_product_data' );
		
		// AJAX for updating cart item data
		$this->loader->add_action( 'wp_ajax_js_action_update_cart_line_item', $plugin_public, 'atc_update_cart_line_item' );
		$this->loader->add_action( 'wp_ajax_nopriv_js_action_update_cart_line_item', $plugin_public, 'atc_update_cart_line_item' );
		
		// override woocommerce templates via plugin
		// use the child theme instead
		//$this->loader->add_filter( 'woocommerce_locate_template', $plugin_public, 'atc_woocommerce_locate_template', 10, 3 );
		
		// update cart fragments
		$this->loader->add_filter( 'woocommerce_add_to_cart_fragments', $plugin_public, 'atc_woocommerce_header_add_to_cart_fragment' );
		$this->loader->add_filter( 'add_to_cart_fragments', $plugin_public, 'atc_woocommerce_header_add_to_cart_fragment' );
		
		// show recipient data on cart page
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_public, 'atc_add_recipient_name_cart', 10, 2 );
		
		// add cart item as unique row that won't update an existing same product's quantity
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'atc_add_individual_line_item', 10, 2 );
		
		// send passkit coupon after new order created, woocommerce_new_order missing get_items() returns empty 
		// http://stackoverflow.com/questions/35563364/woocommerce-get-order-items-on-checkout
		//$this->loader->add_action( 'woocommerce_checkout_order_processed', $plugin_public, 'atc_create_passkit_coupon' );
		// use the instead for after payment completed
		$this->loader->add_action( 'woocommerce_payment_complete', $plugin_public, 'atc_create_passkit_coupon' );
		
		// add recipient data to the order
		$this->loader->add_action( 'woocommerce_add_order_item_meta', $plugin_public, 'atc_add_values_to_order_item_meta', 1, 3 );
		
		// change labels for attributes for recipient data
		$this->loader->add_action( 'woocommerce_attribute_label', $plugin_public, 'atc_change_recipient_order_meta_labels', 10, 3 );
		
		// check cart updated
		//$this->loader->add_action( 'woocommerce_cart_updated', $plugin_public, 'cart_session_updated' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Add_To_Cart_Pop_Up_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
