<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.toptal.com/resume/andrew-schultz
 * @since      1.0.0
 *
 * @package    Add_To_Cart_Pop_Up
 * @subpackage Add_To_Cart_Pop_Up/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Add_To_Cart_Pop_Up
 * @subpackage Add_To_Cart_Pop_Up/public
 * @author     Andrew Schultz <andrew.schultz@toptal.com>
 */
class Add_To_Cart_Pop_Up_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Add_To_Cart_Pop_Up_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Add_To_Cart_Pop_Up_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//is_front_page
		//if ( is_product() OR is_archive() ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/add-to-cart-pop-up-public.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'bootstrap-theme', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css', array(), $this->version, 'all' );
		//}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Add_To_Cart_Pop_Up_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Add_To_Cart_Pop_Up_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//if ( is_product() OR is_archive() ) {
			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/add-to-cart-pop-up-public.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), $this->version, true );
			// included with WooCommerce
			//wp_enqueue_script( 'blockUI', plugin_dir_url( __FILE__ ) . 'js/jquery.blockUI.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'validator',  plugin_dir_url( __FILE__ ) . 'js/validator.min.js', array( 'jquery' ), $this->version, true );

			$args = array(
				'nonce' => wp_create_nonce( 'add-to-cart-nonce' ),
				'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			);

			wp_localize_script( $this->plugin_name, 'addToCartArgs', $args );
			wp_enqueue_script( $this->plugin_name );
		//}
	}

	/**
	 * Remove actions.
	 *
	 * @since    1.0.0
	 */
	public function atc_remove_actions() {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'atc_replace_add_to_cart_button' ), 10 );

		//add_action( 'woocommerce_single_product_summary', 'woocommerce_template_loop_add_to_cart', 30 );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'atc_replace_add_to_cart_button' ), 30 );
	}

	/**
	 * Override the line items cart key so it creates a new line item.
	 *
	 * @since    1.0.0
	 */
	public function atc_add_individual_line_item( $cart_item_data, $product_id ) {
		$cart_item_data['unique_key'] = md5( microtime(). rand() . 'Toptal' );

		return $cart_item_data;
	}

	/**
	 * Override the line items cart key so it creates a new line item.
	 *
	 * @since    1.0.0
	 */
	public function atc_replace_add_to_cart_button() {
		global $product;
		//$link = $product->get_permalink();
		//echo '<a href="' . esc_attr($link) . '">Add to Cart</a>';
		if ( is_product() ) {
			?><button class="single_add_to_cart_button shop-skin-btn shop-flat-btn alt btn-show-pop-up" data-product-id="<?php echo $product->id; ?>"><?php Mk_SVG_Icons::get_svg_icon_by_class_name(true,'mk-moon-cart-plus', 16); ?><?php echo $product->single_add_to_cart_text(); ?></button><?php
		}
		else {
			echo '<a href="#" class="product_loop_button product_type_simple add_to_cart_button ajax_add_to_cart btn-show-pop-up" data-product-id="'. $product->id . '">' . Mk_SVG_Icons::get_svg_icon_by_class_name( false, 'mk-moon-cart-plus', 24 ) . 'Add to cart</a>';
		}
			//echo '<a href="#" class="single_add_to_cart_button shop-skin-btn shop-flat-btn alt btn-show-pop-up" data-product-id="'. $product->id . '">' . Mk_SVG_Icons::get_svg_icon_by_class_name( false, 'mk-moon-cart-plus', 16 ) . 'Add to cart</a>';
	}

	/**
	 * After product is added to cart via AJAX update the session with extra meta data.
	 *
	 * @since    1.0.0
	 */
	public function atc_add_customer_product_data() {
		// check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'add-to-cart-nonce' ) )
			wp_die( 'Security check' );

		$product_id = $_POST['product_id'];

		$cart_item_data = array();
		$cart_item_data['recipient_name'] = sanitize_text_field( $_POST['recipient_name'] );
		$cart_item_data['recipient_phone_number'] = sanitize_text_field( $_POST['recipient_phone_number'] );
		$cart_item_data['recipient_email'] = sanitize_email( $_POST['recipient_email'] );
		$cart_item_data['recipient_message'] = sanitize_text_field( $_POST['recipient_message'] );
		//$cart_item_data['recipient_note'] = sanitize_text_field( $_POST['recipient_note'] );

		WC()->cart->add_to_cart( $product_id, 1, null, null, $cart_item_data );

		do_action( 'woocommerce_ajax_added_to_cart', $product_id );
		//add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );

		WC_AJAX::get_refreshed_fragments();

		/*
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] == $product_id ) {
				WC()->cart->cart_contents[$cart_item_key]['recipient_name'] = sanitize_text_field( $_POST['recipient_name'] );
				WC()->cart->cart_contents[$cart_item_key]['recipient_phone_number'] = sanitize_text_field( $_POST['recipient_phone_number'] );
				WC()->cart->cart_contents[$cart_item_key]['recipient_email'] = sanitize_email( $_POST['recipient_email'] );
				WC()->cart->cart_contents[$cart_item_key]['recipient_message'] = sanitize_text_field( $_POST['recipient_message'] );
				WC()->cart->cart_contents[$cart_item_key]['recipient_note'] = sanitize_text_field( $_POST['recipient_note'] );
			}
		}

		//write_log( WC()->cart->get_cart() );
		WC()->cart->set_session();
		*/

		echo json_encode( array( 'success' => true ) );
		wp_die();
	}

	/**
	 * Display recipient data on the cart and checkout pages.
	 *
	 * @since    1.0.0
	 */
	public function atc_add_recipient_name_cart( $values, $cart_item ) {
		if ( is_cart() OR is_checkout() ) {
			if ( isset( $cart_item['recipient_name'] ) )
				$recipient_name = $cart_item['recipient_name'];

			if ( ! empty( $recipient_name ) ) {
				$values[] = array(
						//'name' => apply_filters( 'wcpv_sold_by_text', esc_html__( 'Sold By', 'woocommerce-product-vendors' ) ),
						'name' => esc_html__( 'Recipient', 'add-to-cart-pop-up' ),
						'display' => '<em>' . esc_html( $recipient_name ) . '</em>',
					);
			}
		}

		if ( is_checkout() ) {
			if ( isset( $cart_item['recipient_phone_number'] ) )
				$recipient_phone_number = $cart_item['recipient_phone_number'];
			if ( isset( $cart_item['recipient_message'] ) )
				$recipient_message = $cart_item['recipient_message'];
			//if ( isset( $cart_item['recipient_note'] ) )
				//$recipient_note = $cart_item['recipient_note'];

			if ( ! empty( $recipient_phone_number ) ) {
				$values[] = array(
						'name' => 'Phone Number',
						'display' => '<em>' . esc_html( $recipient_phone_number ) . '</em>',
					);
			}
			if ( ! empty( $recipient_message ) ) {
				$values[] = array(
						'name' => 'Message',
						'display' => '<em>' . esc_html( $recipient_message ) . '</em>',
					);
			}
			//if ( ! empty( $recipient_note ) ) {
				//$values[] = array(
					//	'name' => 'Note',
						//'display' => '<em>' . esc_html( $recipient_note ) . '</em>',
					//);
			//}
		}

		return $values;
	}

	/**
	 * Edit previously entered recipient meta data.
	 *
	 * @since    1.0.0
	 */
	public function atc_edit_customer_product_data() {
		// check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'add-to-cart-nonce' ) )
			wp_die( 'Security check' );

		$cart_item = WC()->cart->get_cart_item( $_POST['cart_item_key'] );

		//wp_send_json
		echo json_encode( array( 'success' => true,
							'name' => $cart_item['recipient_name'],
							'phoneNumber' => $cart_item['recipient_phone_number'],
							'email' => $cart_item['recipient_email'],
							'message' => $cart_item['recipient_message'],
							//'note' => $cart_item['recipient_note'],
						) );
		wp_die();
	}

	public function atc_update_cart_line_item() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'add-to-cart-nonce' ) )
			wp_die( 'Security check' );

		//$cart_item = WC()->cart->get_cart_item( $_POST['cart_item_key'] );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item_key == $_POST['cart_item_key'] ) {
				WC()->cart->cart_contents[$cart_item_key]['recipient_name'] = sanitize_text_field( $_POST['recipient_name'] );
				WC()->cart->cart_contents[$cart_item_key]['recipient_phone_number'] = sanitize_text_field( $_POST['recipient_phone_number'] );
				WC()->cart->cart_contents[$cart_item_key]['recipient_email'] = sanitize_email( $_POST['recipient_email'] );
				WC()->cart->cart_contents[$cart_item_key]['recipient_message'] = sanitize_text_field( $_POST['recipient_message'] );
				//WC()->cart->cart_contents[$cart_item_key]['recipient_note'] = sanitize_text_field( $_POST['recipient_note'] );
			}
		}

		//write_log( WC()->cart->get_cart() );
		WC()->cart->set_session();

		//wp_send_json
		echo json_encode( array( 'success' => true ) );
		wp_die();
	}

	/**
	 * Add modal window HTML to the bottom of each WooCommerce page.
	 *
	 * @since    1.0.0
	 */
	public function atc_add_pop_up_html() {
		global $post;

		//if ( is_product_category() )
			//write_log("WOOCOMMERCE CAT");

		//if ( is_archive() )
			//write_log("WOOCOMMERCE ARCHIVE");

		//if ( is_product() OR is_archive()  ) {
			/*$recipient = array();
			$product_id = $post->ID;

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( $cart_item['product_id'] == $product_id ) {
					if ( isset( WC()->cart->cart_contents[$cart_item_key]['recipient_name'] ) )
						$recipient['recipient_name'] = WC()->cart->cart_contents[$cart_item_key]['recipient_name'];
					if ( isset( WC()->cart->cart_contents[$cart_item_key]['recipient_phone_number'] ) )
						$recipient['recipient_phone_number'] = WC()->cart->cart_contents[$cart_item_key]['recipient_phone_number'];
					if ( isset( WC()->cart->cart_contents[$cart_item_key]['recipient_email'] ) )
						$recipient['recipient_email'] = WC()->cart->cart_contents[$cart_item_key]['recipient_email'];
					if ( isset( WC()->cart->cart_contents[$cart_item_key]['recipient_message'] ) )
						$recipient['recipient_message'] = WC()->cart->cart_contents[$cart_item_key]['recipient_message'];
					if ( isset( WC()->cart->cart_contents[$cart_item_key]['recipient_note'] ) )
						$recipient['recipient_note'] = WC()->cart->cart_contents[$cart_item_key]['recipient_note'];
				}
			}

			if ( isset( $recipient['recipient_name'] ) )
				$recipient_name = $recipient['recipient_name'];
			else
				$recipient_name = '';
			if ( isset( $recipient['recipient_phone_number'] ) )
				$recipient_phone_number = $recipient['recipient_phone_number'];
			else
				$recipient_phone_number = '';
			if ( isset( $recipient['recipient_email'] ) )
				$recipient_email = $recipient['recipient_email'];
			else
				$recipient_email = '';
			if ( isset( $recipient['recipient_message'] ) )
				$recipient_message = $recipient['recipient_message'];
			else
				$recipient_message = '';
			if ( isset( $recipient['recipient_note'] ) )
				$recipient_note = $recipient['recipient_note'];
			else
				$recipient_note = '';
			*/

			/*
			$recipient_cb_options = '';
			$recipient_messages = array( 'I love you', 'I miss you', 'Thank you', 'Thinking of you', 'Thanks for the help', 'I appreciate you' );

			foreach( $recipient_messages as $message ) {
				//if ( $recipient_message == $message )
					//$recipient_cb_options .= "<option selected>$message</option>";
				//else
					$recipient_cb_options .= "<option>$message</option>";
			}
			*/

			if ( is_cart() ) {
				$edit_redirect_url = WC()->cart->get_cart_url();
			}
			elseif( is_checkout() ) {
				$edit_redirect_url = WC()->cart->get_checkout_url();
			}
			else {
				$edit_redirect_url = '';
			}

			if( isset($_SERVER['HTTPS'] ) )
				$protocol = "https://";
			else
				$protocol = "http://";

			$continue_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			echo '<!-- Modal -->
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:999999">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<form id="send_friend" data-toggle="validator" role="form">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<h4 class="modal-title" id="myModalLabel">Recipient Details</h4>
							</div>
							<div class="modal-body">
								<div class="container">
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label for="recipient_name">Name</label>
												<input class="form-control" id="recipient_name" name="recipient_name" required="true" size="120" type="text" autocomplete="off"  />
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label for = "devlivery_options">Send Gift Via</label>
												<label class = "checkbox-inline">
													<input type = "radio" name="devlivery_options" id="delivery_method_both" value="both" checked> Both
												</label>
												<label class = "checkbox-inline">
													<input type = "radio" name="devlivery_options" id="delivery_method_phone" value="phone"> SMS
												</label>
												<label class = "checkbox-inline">
													<input type = "radio" name="devlivery_options" id="delivery_method_email" value="email"> Email
												</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group phone-number-field">
												<label for="recipient_phone_number">Phone Number</label>
												<input class="form-control" id="recipient_phone_number" name="recipient_phone_number" required="true" size="10" type="text" autocomplete="off" />
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group email-field">
												<label for="recipient_email">Email</label>
												<input type="email" class="form-control" id="recipient_email" name="recipient_email" required="true" placeholder="Enter email" autocomplete="off" />
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label for="recipient_message">Message</label>
												<textarea class="form-control" id="recipient_message" name="recipient_message" rows="3" required="true" placeholder="i.e. Thank you, I miss you, I love you, Thinking of you, Thanks for the help, or I appreciate you" ></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-secondary btn-add-cart-meta" data-dismiss="modal" data-product-id="" data-redirect-url="' . $continue_url . '">Continue Shopping</button>
								<button type="submit" class="btn btn-primary btn-add-cart-meta btn-save-cart-meta" data-product-id="" data-redirect-url="' . WC()->cart->get_checkout_url() . '">Checkout</button>
								<button type="submit" class="btn btn-primary btn-edit-cart-meta" style="display:none" data-cart-item-key="0" data-redirect-url="' . $edit_redirect_url . '">Save</button>
							</div>
							</form>
						</div>
					</div>
				</div>';
		//}

		//echo '<h1>EXECUTED</h1>';
	}

	public function atc_change_recipient_order_meta_labels( $array,  $int,  $int ) {
		if ( $array == 'recipient_name' )
			return 'Name';
		elseif ( $array == 'recipient_phone_number' )
			return 'Phone';
		elseif ( $array == 'recipient_email' )
			return 'Email';
		elseif ( $array == 'recipient_message' )
			return 'Message';
		//elseif ( $array == 'recipient_note' )
			//return 'Note';
		else
			return $array;
	}

	/**
	 * Create a passkit coupon after WooCommerce order is created.
	 *
	 * @since    1.0.0
	 */
	public function atc_create_passkit_coupon( $order_id ) {
		$order = new WC_Order( $order_id );
		$pk = new PassKit( PASSKIT_API_KEY, PASSKIT_API_SECRET );
		
		write_log($pk);

		if ( is_user_logged_in() ) {
			//$current_user = wp_get_current_user();
			//$user_email = $current_user->user_email;
			//$display_name = $current_user->display_name;
			$user_email = $order->billing_email;
			$display_name = $order->billing_first_name . ' ' . $order->billing_last_name;
		}
		else {
			// guest checkout
			$user_email = $order->billing_email;
			$display_name = $order->billing_first_name . ' ' . $order->billing_last_name;
		}

		$order_items = $order->get_items();

        foreach( $order_items as $item_key => $item ) {
			$product = new WC_Product( $item['product_id'] );
			$sku = $product->get_sku();
			$recipient_email = wc_get_order_item_meta( $item_key, 'recipient_email' );
			$recipient_phone_number = wc_get_order_item_meta( $item_key, 'recipient_phone_number' );
			$recipient_message = wc_get_order_item_meta( $item_key, 'recipient_message' );
			
			write_log('SKU ' . $sku . ' email ' . $recipient_email );

			if ( ! empty( $sku ) AND ! empty( $recipient_email ) ) {
				//$data = array( 'recoveryEmail' => $recipient_email );
				$data = array();
				$result = $pk->issuePass( $sku, $data );
				//$result = $pk->issuePass( $sku );
				
				write_log($result);

				if ( $result['success'] == 1) {
					$passkit_serial = $result['serial'];
					$passkit_url = $result['url'];
					$passbook_serial = $result['passbookSerial'];
					$passkit_share_id = $result['shareID'];
					$passkit_id = $result['uniqueID'];

					// email user
					$headers[] = 'From: ' . $display_name . ' <' . $user_email . '>';
					$message = '<table>
       <tr>
              <td align="center"><img src="http://sendafriend.com/wp-content/uploads/2016/10/footer-logo.png"></td>
       </tr>
       <tr>
              <td>Your friend ' . $display_name . ' has sent you a gift from Send a Friend. Click the link to see your gift:</td>
       </tr>
			 <tr>
              <td align="left"><a href="https://www.facebook.com/sharer/sharer.php?u=sendafriend.com"><img src="http://sendafriend.com/wp-content/uploads/2016/12/Facebook.png"></a></td>
       </tr>
			 <tr>
              <td align="left"><a href="http://twitter.com/?status=I want to thank [Insert friends name], for my gift from www.sendafriend.com!"><img src="http://sendafriend.com/wp-content/uploads/2016/12/Twitter.png"></a></td>
       </tr>
</table>';
					$message .= '<p>' . $passkit_url . '</p>';
					$message .= '<p>' . $recipient_message . '</p>';
					$subject = 'Your Coupon From Send A Friend';

					add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );

					wp_mail( $recipient_email, $subject, $message, $headers );

					
					if ( ! empty( $recipient_phone_number ) ) {
						// send SMS
						$sid = TWILIO_SID;
						$token = TWILIO_TOKEN;

						$client = new Twilio\Rest\Client($sid, $token);
						$message = $client->messages->create(
							$recipient_phone_number, // Text this number, just use digits here
							array( 'from' => TWILIO_NUMBER, // From a valid Twilio number
									'body' => 'Your friend ' . $display_name . ' has sent you a gift from Send a Friend. Click the link to see your gift: ' . $passkit_url
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Add custom recipient data to the order.
	 *
	 * @since    1.0.0
	 */
	public function atc_add_values_to_order_item_meta( $item_id,  $values,  $cart_item_key ) {
		$recipient_name = $values['recipient_name'];
		$recipient_phone_number = $values['recipient_phone_number'];
		$recipient_email = $values['recipient_email'];
		$recipient_message = $values['recipient_message'];
		//$recipient_note = $values['recipient_note'];

        if( ! empty( $recipient_name ) )
            wc_add_order_item_meta( $item_id, 'recipient_name', $recipient_name );
		if( ! empty( $recipient_phone_number ) )
			wc_add_order_item_meta( $item_id, 'recipient_phone_number', $recipient_phone_number );
		if( ! empty( $recipient_email ) )
			wc_add_order_item_meta( $item_id, 'recipient_email', $recipient_email );
		if( ! empty( $recipient_message ) )
			wc_add_order_item_meta( $item_id, 'recipient_message', $recipient_message );
		//if( ! empty( $recipient_note ) )
			//wc_add_order_item_meta( $item_id, 'recipient_note', $recipient_note );
	}

	/**
	 * Refresh cart fragments with products added via AJAX.
	 *
	 * https://docs.woocommerce.com/document/show-cart-contents-total/
	 *
	 * @since    1.0.0
	 */
	public function atc_woocommerce_header_add_to_cart_fragment( $fragments ) {
		ob_start();
        ?>
        <a class="mk-shoping-cart-link" href="<?php echo WC()->cart->get_cart_url(); ?>">
            <?php Mk_SVG_Icons::get_svg_icon_by_class_name(true, 'mk-moon-cart-2', 16); ?>
            <span class="mk-header-cart-count"><?php echo WC()->cart->cart_contents_count; ?></span>
        </a>
        <?php
        $fragments['a.mk-shoping-cart-link'] = ob_get_clean();

        return $fragments;
	}

	/**
	 * Allow overriding of templates in plugin directory.
	 *
	 * @since    1.0.0
	 */
	function atc_woocommerce_locate_template( $template, $template_name, $template_path ) {

		global $woocommerce;

		$_template = $template;

		if ( ! $template_path ) $template_path = $woocommerce->template_url;
		$plugin_path  = plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
			  $template_path . $template_name,
			  $template_name
			)
		);

		// Modification: Get the template from this plugin, if it exists

		if ( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;

		// Use default template
		if ( ! $template )
			$template = $_template;

		// Return what we found
		return $template;
	}
}
