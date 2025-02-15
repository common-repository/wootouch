<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WTC_Rewrite_Rules {

	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_Rewrite_Rules
	 */
	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->hooks();
	}

	/**
	 * Setup hooks
	 */
	private function hooks() {
		add_filter( 'rewrite_rules_array', array( $this, 'add_rewrite_rule' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'wp_loaded', array( $this, 'flush_rules' ) );
	}

	/**
	 * Flush rules if they're not set yet
	 */
	public function flush_rules() {
		$rules = get_option( 'rewrite_rules' );

		if ( ! isset( $rules[ WooTouch::WEBSERVICE_REWRITE ] ) ) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
	}

	/**
	 * Add webservice rewrite rules to WordPress rewrite rules
	 *
	 * @param $rules
	 *
	 * @return array rules
	 */
	public function add_rewrite_rule( $rules ) {
		$newrules = array();
		$newrules[ WooTouch::WEBSERVICE_REWRITE ] 	= 'index.php?webservice=1&wootouchservice=$matches[1]';

		return $newrules + $rules;
	}

	/**
	 * Add custom query variables to WordPress query variables
	 *
	 * @param $vars
	 *
	 * @return array query_vars
	 */
	public function add_query_vars( $vars ) {
		array_push( $vars, 'webservice' );
		array_push( $vars, 'wootouchservice' );
		return $vars;
	}

}
