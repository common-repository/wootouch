<?php
/*
 * Plugin Name: Wootouch
 * Plugin URI:  http://wootouch.com/
 * Description: Woocommerce Mobile Application Plugin. It creat connection  between the Wootouch Mobile Application and WooCommerce website.
 * Author: Lujayninfoways
 * Author URI:  http://www.lujayninfoways.com/ 
 * Version: 1.0
 * License: GPL v3

 * Copyright: (c) 2017-2018, LujaynInfoways (info@lujayninfoways.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   wootouch
 * @author    LujaynInfoways
 * @category  Utility
 * @copyright Copyright (c) 2017-2018, LujaynInfoways.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 
*/ 


if ( ! defined( 'PLUGIN_DIR' ) ) {
	define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WTC_PLUGIN_FILE' ) ) {
	define( 'WTC_PLUGIN_FILE', __FILE__ );
}
 
class WooTouch {


const WEBSERVICE_REWRITE = 'webservice/([a-zA-Z0-9_-]+)$';
	const OPTION_KEY         = 'wtc_options';

	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WP_Simple_Web_Service
	 */
	public static function get() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Function that runs on install
	 */
	public static function install() {

		// Clear the permalinks
		flush_rewrite_rules();
		
	}

	/**
	 * Constructor
	 */
	private function __construct() {

		// Load files
		$this->includes();

		// Init
		$this->init();

	}

	/**
	 * Load required files
	 */
	private function includes() {
		
		require_once( PLUGIN_DIR . 'include/include-wtc-rewrite-rules.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/products.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/category.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/subcategory.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/createuser.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/forgetpassword.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/login.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/orders.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/products_detail.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/savebilling.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/saveshipping.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/state.php' );
        require_once( PLUGIN_DIR . 'wootouchservice/change_password.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/country.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/edituser.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/list_review.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/orderdetail.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/remove_coupon.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/save_review.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/setting_option.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/shipping_methods.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/sortcategory.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/applycoupon.php' );
		require_once( PLUGIN_DIR . 'wootouchservice/save_order.php' );
		
	
		
		if ( is_admin() ) {
			// Backend

			require_once( PLUGIN_DIR . 'include/include-wtc-settings.php' );

		}
		else {
			// Frondend

			require_once( PLUGIN_DIR . 'include/include-wtc-catch-request.php' );
			require_once( PLUGIN_DIR . 'include/include-wtc-output.php' );
		}

	}

	/**
	 * Initialize class
	 */
	private function init() {
		
		//Edit by dev
		//register_activation_hook(__FILE__, array(__CLASS__, 'check_plugin_activated'));
		//add_action('admin_init', array(__CLASS__, 'check_plugin_activated'));
		//Edit end
		
		// Setup Rewrite Rules
		WTC_Rewrite_Rules::get();

		// Default webservice
		WTC_get_products::get();
		WTC_get_category::get();
		WTC_get_subcategory::get();
		WTC_get_createuser::get();
		WTC_get_forgetpassword::get();
		WTC_get_login::get();
		WTC_get_orders::get();
		WTC_get_products_detail::get();
		WTC_get_savebilling::get();
		WTC_get_saveshipping::get();
		WTC_get_state::get();
		WTC_get_country::get();
		WTC_get_change_password::get();
		WTC_get_edituser::get();
		WTC_get_list_review::get();
		WTC_get_orderdetail::get();
		WTC_get_remove_coupon::get();
		WTC_get_save_review::get();
		WTC_get_setting_option::get();
		WTC_get_shipping_methods::get();
		WTC_get_sortcategory::get();
		WTC_get_save_order::get();
		WTC_get_applycoupon::get();
		
		
		if ( is_admin() ) {
			// Backend

			// Setup settings
			WTC_Settings::get();
	
		}
		else {
			// Frondend

			// Catch request
			WTC_Catch_Request::get();
		}

	}
	
	/**
	 * The correct way to throw an error in a webservice
	 *
	 * @param $error_string
	 */
	public function throw_error( $error_string ) {
		wp_die( '<b>Webservice error:</b> ' . $error_string );
		
	}

	/**
	 * Function to get the plugin options
	 *
	 * @return array
	 */
	public function get_options() {
		return get_option( self::OPTION_KEY, array() );
	}

	/**
	 * Function to save the plugin options
	 *
	 * @param $options
	 */
	public function save_options( $options ) {
		update_option( self::OPTION_KEY, $options );
	}
	
	public static function check_plugin_activated() {
				
    $plugin = is_plugin_active("woocommerce/woocommerce.php");
    
    if (!$plugin) {
      deactivate_plugins(plugin_basename(__FILE__));
      add_action('admin_notices', array(__CLASS__, 'disabled_notice'));
      if (isset($_GET['activate']))
        unset($_GET['activate']);
    }
    
  }
  


	public static function disabled_notice() {
    global $current_screen;
    if ($current_screen->parent_base == 'plugins'):
      ?>
      <div class="error" style="padding: 8px 8px;">
        <strong>
          <?= __('WOO MALL requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> activated in order to work. Please install and activate <a href="' . admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce') . '" target="_blank">WooCommerce</a> first.') ?>
        </strong>
      </div>
      <?php
    endif;
  }
    
}
  /**
	 *
	 * @Create wootouch setting options
	 */
	
 
  	function WTC_create_db() {
			
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'wtc_settings';
		$screen_url =  plugin_dir_url(WTC_PLUGIN_FILE ) . '/images/logo.png';
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL,
			image_url varchar(255) NOT NULL,
			back_color tinytext NOT NULL,
			font_color tinytext NOT NULL,
			terms longtext NOT NULL,
			contact longtext NOT NULL,
			UNIQUE KEY id (id)
			) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'id' => 1, 
			'image_url' => $screen_url, 
			'back_color' => '#96588a',
			'font_color'			=> '#ffffff',
			'terms'				=> 'Terms And Condition',
			'contact'		=> '<h3>Address</h3>
			A-606, Fairdeal House, Near Swastik<br />
			Society Cross Road, Navrangpura,<br />
			Ahmedabad, Gujarat 380009.<br />
			<h3>Email</h3><br />
			<a href="mailto:info@wootouch.com">info@wootouch.com</a>
			<br /><br />
			<a href="mailto:sales@wootouch.com">sales@wootouch.com</a>
			<h3>phone</h3><br />
			+91 9974845340<br />
			<br /><br />
			+91 9898837321'
		) 
	);
	
	}
	
  /***********/
	register_activation_hook( __FILE__, 'WTC_create_db' ); 
	/****Delete Plugin Remove Tables *****/
 
	function WTC_deactivation()
	{
		
			global $wpdb;
			$table_name = $wpdb->prefix . "wtc_settings";
			$sql = "DROP TABLE IF EXISTS $table_name";
			$wpdb->query($sql);
			//die($sql);
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			delete_option("WTC_db_version");
			
	}
	
	//register_uninstall_hook( __FILE__, 'WTC_deactivation' );
	register_deactivation_hook( __FILE__, 'WTC_deactivation' );

	function WooTouch() {
		return WooTouch::get();
	}

// Load plugin
add_action( 'plugins_loaded', create_function( '', 'WooTouch::get();' ) );

// Install hook
register_activation_hook( PLUGIN_DIR, array( 'WooTouch', 'install' ) );
//register_deactivation_hook( PLUGIN_DIR, array( 'WooTouch', 'uninstall' ) );
?>
