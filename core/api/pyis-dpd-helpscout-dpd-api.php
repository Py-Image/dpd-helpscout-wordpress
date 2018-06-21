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
	 * @var			PyIS_DPD_HelpScout_API_Drip $api_key Holds set API Key
	 * @since		1.0.0
	 */
	private $api_key = '';
	
	/**
	 * @var			PyIS_DPD_HelpScout_API_Drip $account_id The Account ID the API Key belongs to. Yep, we need both.
	 * @since		1.0.0
	 */
	private $account_id = '';
	
	/**
	 * @var		PyIS_DPD_HelpScout_API_Drip $password The Account ID the API Key belongs to. Yep, we need both.
	 * @since		1.0.0
	 */
	private $password = '';
	
	/**
	 * @var			PyIS_DPD_HelpScout_API_Drip $api_endpoint Holds set API Endpoint
	 * @since		1.0.0
	 */
	public $api_endpoint = 'https://api.getdrip.com/v2/<account_id>';

	/**
	 * PyIS_DPD_HelpScout_API_Drip constructor.
	 * 
	 * @since		1.0.0
	 */
	function __construct( $api_key, $account_id, $password ) {

		$this->api_key = trim( $api_key );
		
		// Construct the appropriate API Endpoint		
		$this->account_id = trim( $account_id );
		$this->api_endpoint = str_replace( '<account_id>', $this->account_id, $this->api_endpoint );
		
		$this->password = $password;
		
		$this->set_headers( array(
			'Authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->password ),
			'Content-Type' => 'application/vnd.api+json',
		) );

	}

}