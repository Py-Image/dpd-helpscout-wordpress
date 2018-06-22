<?php
/**
 * DPD API Communication Class
 *
 * @since 1.0.0
 *
 * @package PyIS_DPD_HelpScout
 * @subpackage PyIS_DPD_HelpScout/core/api
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'PyIS_DPD_HelpScout_API_Class' ) ) {
	require_once PyIS_DPD_HelpScout_DIR . 'core/api/pyis-dpd-helpscout-api.php';
}

class PyIS_DPD_HelpScout_API_DPD extends PyIS_DPD_HelpScout_API_Class {

	/**
	 * @var			String $api_key Holds set API Key
	 * @since		1.0.0
	 */
	private $api_key = '';
	
	/**
	 * @var			String $user_email The Account ID the API Key belongs to. Yep, we need both.
	 * @since		1.0.0
	 */
	private $user_email = '';
	
	/**
	 * @var			String $api_endpoint Holds set API Endpoint
	 * @since		1.0.0
	 */
	public $api_endpoint = 'https://api.getdpd.com/v2';

	/**
	 * PyIS_DPD_HelpScout_API_DPD constructor.
	 * 
	 * @since		1.0.0
	 */
	function __construct( $user_email, $api_key ) {

		$this->user_email = trim( $user_email );
		$this->api_key = trim( $api_key );
		
		$this->set_headers( array(
			'Authorization: Basic ' . base64_encode( $this->user_email . ':' . $this->api_key ),
		) );

	}
	
	/**
	 * Get a DPD Customer Object by Email
	 * 
	 * @param		string $email Email Address
	 *                             
	 * @access		public
	 * @since		1.0.0
	 * @return		object DPD Customer Object on success, false on failure
	 */
	public function get_customer_by_email( $email ) {
		
		$customers = $this->get( 'customers?email=' . $email );
		
		// No matches found
		if ( ! is_array( $customers ) ) return false;
		
		// Return first result
		return reset( $customers );
		
	}

}