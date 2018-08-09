<?php
/**
 * Creates REST Endpoints
 *
 * @since 0.1.0
 *
 * @package PyIS_DPD_HelpScout
 * @subpackage PyIS_DPD_HelpScout/core/rest
 */

defined( 'ABSPATH' ) || die();

class PyIS_DPD_HelpScout_REST {

	public $dpd_data;

    /**
	 * PyIS_DPD_HelpScout_REST constructor.
	 *
	 * @since 0.1.0
	 */
    function __construct() {

        add_action( 'rest_api_init', array( $this, 'create_routes' ) );

    }

    /**
     * Creates a WP REST API route for CognitoForms to POST JSON tool_box
     * 
     * @since       0.1.0
     * @access      public
     * @return      void
     */
    public function create_routes() {

        register_rest_route( 'pyis/v1', '/helpscout/dpd/get-data', array(
            'methods' => 'POST',
            'callback' => array( $this, 'get_data' ),
        ) );
		
		register_rest_route( 'pyis/v1', '/helpscout/dpd/regenerate-data', array(
            'methods' => 'POST',
            'callback' => array( $this, 'regenerate_data' ),
        ) );

    }

    /**
     * Callback for our REST Endpoint to initially populate the HelpScout App
     * 
     * @param       object $request WP_REST_Request Object
	 * 
	 * @access		public
	 * @since		1.0.0
     * @return      string JSON
     */
    public function get_data( $request ) {

        // Capture incoming JSON from HelpScout
		$this->helpscout_data = $this->get_incoming_data();

		// Ensure the request is valid. Also ensures random people aren't abusing the endpoint
		if ( ! $this->validate() ) {
			$this->respond( __( 'Access Denied', 'pyis-dpd-helpscout' ) );
			exit;
		}
		
		$this->dpd_data = array();
		
		foreach ( $this->helpscout_data['customer']['emails'] as $email ) {
			
			// Use Helpscout Data to get data from DPD
			$purchases = PYISDPDHELPSCOUT()->dpd_api->get_customer_purchases_by_email( $email );
			
			$this->dpd_data[ $email ] = $purchases; 
			
		}
		
		// Build HTML out of our data
		$html = $this->build_response_html();
		
		// Give HelpScout the HTML as JSON
		$this->respond( $html );

    }
	
	/**
	 * Callback for our REST Endpoint to regenerate the Download URLs
	 * 
     * @param       object $request WP_REST_Request Object
     * 
     * @access		public
     * @since		1.0.0
     * @return      string JSON
	 */
	public function regenerate_data() {
		
		// We can't use $this->get_incoming_data() because we are passing other things in $_POST too
		// https://stackoverflow.com/a/44291958
		if ( isset( $_POST['helpscout_data'] ) ) {
		
			// Puts it in the same format as when we initially read it in $this->get_data()
			$this->helpscout_data = json_decode( stripslashes( $_POST['helpscout_data'] ), true );
			
		}
		else {
			
			$this->respond( __( 'Access Denied', 'pyis-dpd-helpscout' ) );
			exit;
			
		}
		
		// Ensure the request is valid. Also ensures random people aren't abusing the endpoint
		if ( ! $this->validate() ) {
			$this->respond( __( 'Access Denied', 'pyis-dpd-helpscout' ) );
			exit;
		}
		
		$this->respond( 'success' );
		exit;
		
		// https://github.com/facebook/php-webdriver via Composer
		require_once PyIS_DPD_HelpScout_DIR . '/core/library/phpwebdriver/vendor/autoload.php';
		
		// start Chrome with 5 second timeout
		$host = 'http://localhost:4444/wd/hub'; // this is the default
		$capabilities = DesiredCapabilities::chrome();
		$driver = RemoteWebDriver::create( $host, $capabilities, 5000 );
		
	}
	
	/**
	 * Captures incoming JSON
	 * Stored as an Associative Array so we can use isset() which is more precise for our needs
	 * 
	 * @acess		private
	 * @since		1.0.0
	 * @return		array Associative Array representation of the JSON
	 */
	private function get_incoming_data() {
		
		$json = file_get_contents( 'php://input' );
		
		return json_decode( $json, true );
		
	}
	
	/**
	 * Ensures the Request to the WP Site is valid
	 * 
	 * @access		private
	 * @since		1.0.0
	 * @return		boolean Valid/Invalid Request
	 */
	private function validate() {
		
		// we need at least this
		if ( ! isset( $this->helpscout_data['customer']['email'] ) && 
			! isset( $this->helpscout_data['customer']['emails'] ) ) {
			return false;
		}
		
		// check request signature
		if ( isset( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) && 
			$_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] == self::hash_secret_key( get_option( 'pyis_dpd_helpscout_secret_key' ), $this->helpscout_data ) ) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Hashes the Secret Key to match the Signature from HelpScout
	 * 
	 * @param		string $secret_key Secret Key stored in WP Database
	 *                                                    
	 * @access		private
	 * @since		1.0.0
	 * @return		string Hashed Secret Key
	 */
	private function hash_secret_key( $secret_key ) {
		
		return base64_encode( hash_hmac( 'sha1', json_encode( $this->helpscout_data ), $secret_key, true ) );
		
	}
	
	/**
	 * Constructs HTML for the Response to HelpScout
	 * 
	 * @access		private
	 * @since		1.0.0
	 * @return		string HTML
	 */
	private function build_response_html() {
		
		// build HTML output
		$html = '<div id="dpd-helpscout-content">';
		
			$html .= '<span id="dpd-helpscout-data" class="hidden-input" style="display: none;">' . json_encode( $this->helpscout_data ) . '</span>';
			$html .= '<span id="dpd-helpscout-secret-key" class="hidden-input" style="display: none;">' . $this->hash_secret_key( get_option( 'pyis_dpd_helpscout_secret_key' ) ) . '</span>';

			$html .= '<span id="dpd-helpscout-chrome-extension-loading" class="badge red">' . __( 'Waiting for Chrome Extension...', 'pyis-dpd-helpscout' ) . '</span>';

			foreach ( $this->dpd_data as $email => $purchases ) {
				$html .= str_replace( '\t', '', $this->dpd_row( $email, $purchases ) );
			}
		
		$html .= '</div>';
		
		return $html;
		
	}
	
	/**
	 * Generates HTML for each Tag
	 * 
	 * @param		string $tag Tag from Drip
	 *							  
	 * @access		public
	 * @since		1.0.0
	 * @return		string HTML
	 */
	public function dpd_row( $email, $purchases ) {
		
		// Scoping for passing through the Secret Key
		$helpscout_data = $this->helpscout_data;
		
		ob_start();
		
		include PyIS_DPD_HelpScout_DIR . 'core/views/pyis-dpd-helpscout-row.php';
		
		$html = ob_get_clean();
		
		return $html;
		
	}
	
	/**
	 * Renders Response after a Request
	 * 
	 * @param		string  $html HTML to be sent to HelpScout
	 * @param		integer $code HTTP Response Code. Defaults to 200
	 *													   
	 * @access		private
	 * @since		1.0.0
	 * @return		void
	 */
	private function respond( $html, $code = 200 ) {
		
		$response = array( 'html' => $html );
		
		// Clear output, other plugins might have thrown dumb errors by now.
		if ( ob_get_level() > 0 ) {
			ob_end_clean();
		}
		
		status_header( $code );
		
		header( "Content-Type: application/json" );
		echo json_encode( $response );
		
		die();
		
	}

}