<?php
/**
 * PyImageSearch Address Collection Settings
 *
 * @since 0.1.0
 *
 * @package PyIS_DPD_HelpScout
 * @subpackage PyIS_DPD_HelpScout/core/admin
 */

defined( 'ABSPATH' ) || die();

class PyIS_DPD_HelpScout_Settings {

    /**
	 * PyIS_DPD_HelpScout_Settings constructor.
	 *
	 * @since 0.1.0
	 */
    function __construct() {

        add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
        
        add_action( 'admin_init', array( $this, 'register_options' ) );
		
		add_action( 'pyis_dpd_helpscout_instructions', array( $this, 'helpscout_instructions' ) );

    }
    
    /**
     * Create the Admin Page to hold our Settings
     * 
     * @access      public
     * @since       0.1.0
     * @return      void
     */
    public function create_admin_page() {
        
        $submenu_page = add_submenu_page(
            'options-general.php',
            _x( 'PyImageSearch DPD+HelpScout', 'Admin Page Title', 'pyis-dpd-helpscout' ),
            _x( 'DPD+Helpscout', 'Admin Menu Title', 'pyis-dpd-helpscout' ),
            'manage_options',
            'pyis-dpd-helpscout',
            array( $this, 'admin_page_content' )
        );
        
    }
    
    /**
     * Create the Content/Form for our Admin Page
     * 
     * @access      public
     * @since       0.1.0
     * @return      void
     */
    public function admin_page_content() { ?>

        <div class="wrap pyis-dpd-helpscout-settings">
			
			<form method="post" action="options.php">
				
				<?php echo wp_nonce_field( 'pyis_dpd_helpscout_settings', 'pyis_dpd_helpscout_nonce' ); ?>

				<?php settings_fields( 'pyis_dpd_helpscout' ); ?>

				<?php do_settings_sections( 'pyis-dpd-helpscout' ); ?>

				<?php submit_button(); ?>

			</form>

        </div>

        <?php
        
    }
    
    /**
     * Register our Options so the Admin Page knows what to Save
     * 
     * @access      public
     * @since       0.1.0
     * @return      void
     */
    public function register_options() {
        
        add_settings_section(
            'pyis_dpd_helpscout',
            __( 'DPD+HelpScout Integration Settings', 'pyis-dpd-helpscout' ),
            '__return_false',
            'pyis-dpd-helpscout'
        );
		
		foreach ( $this->get_settings() as $id => $field ) {
			
			$field = wp_parse_args( $field, array(
				'settings_label' => '',
				'label' => false,
				'name' => $id,
			) );
			
			$callback = 'pyis_dpd_helpscout_do_field_' . $field['type'];
			
			add_settings_field(
				$id,
				$field['settings_label'],
				( is_callable( $callback ) ) ? 'pyis_dpd_helpscout_do_field_' . $field['type'] : 'pyis_dpd_helpscout_missing_callback',
				'pyis-dpd-helpscout',
				'pyis_dpd_helpscout',
				$field
			);
			
			register_setting( 'pyis_dpd_helpscout', $id );
			
		}
        
    }
	
	/**
	 * Outputs the Instructions for configuring the HelpScout App
	 * 
	 * @access		public
	 * @since		1.1.0
	 * @param		array $args Unused
	 */
	public function helpscout_instructions( $args ) {
		
		?>

		<p>
			<a href="//secure.helpscout.net/apps/custom/" target="_blank">
				<?php echo __( 'Create a HelpScout Custom App with the following options:', 'pyis-dpd-helpscout' ); ?>
			</a>
		</p>

		<ul style="list-style: disc; margin-top: 0.5em; margin-left: 2em;">
			<li>
				<?php echo __( 'App Name: <code>DPD URLs</code>', 'pyis-dpd-helpscout' ); ?>
			</li>
			<li>
				<?php echo __( 'Content Type: <code>Dynamic Content</code>', 'pyis-dpd-helpscout' ); ?>
			</li>
			<li>
				<?php printf( __( 'Callback URL: <code>%s/wp-json/pyis/v1/helpscout/dpd/get-data</code>', 'pyis-dpd-helpscout' ), get_site_url() ); ?>
			</li>
			<li>
				<?php echo __( 'Secret Key: The same value entered below.', 'pyis-dpd-helpscout' ); ?>
			</li>
		</ul>

		<?php
		
	}
	
	/**
	 * Holds the Settings Array
	 * 
	 * @access		public
	 * @since		1.1.0
	 * @return		array Settings Array
	 */
	public function get_settings() {
		
		return apply_filters( 'pyis_dpd_helpscout', array(
			'instructions' => array(
				'type' => 'hook',
				'settings_label' => __( 'HelpScout App Setup', 'pyis-dpd-helpscout' ),
			),
			'pyis_dpd_helpscout_secret_key' => array(
				'type' => 'text',
				'settings_label' => __( 'HelpScout Secret Key', 'pyis-dpd-helpscout' ),
				'no_init' => true,
				'option_field' => true,
				'description' => '<p class="description">' .
									__( "This is used to help ensure people aren't abusing your API Endpoint.", 'pyis-dpd-helpscout' ) . 
								 '</p>',
				'description_tip' => false,
				'input_atts' => array(
					'required' => true,
				),
			),
			'pyis_dpd_account_id' => array(
				'type' => 'text',
				'settings_label' => __( 'DPD Account Email Address', 'pyis-dpd-helpscout' ),
				'no_init' => true,
				'option_field' => true,
				'description' => '<p class="description">' . 
									__( 'This is needed for API Requests.', 'pyis-dpd-helpscout' ) . 
								 '</p>',
				'description_tip' => false,
				'input_atts' => array(
					'required' => true,
				),
			),
			'pyis_dpd_api_key' => array(
				'type' => 'text',
				'settings_label' => __( 'DPD API Key', 'pyis-dpd-helpscout' ),
				'no_init' => true,
				'option_field' => true,
				'description' => '<a href="//getdpd.com/user/profile" target="_blank">' . 
									__( 'Find your API Key Here', 'pyis-dpd-helpscout' ) . 
								 '</a>',
				'description_tip' => false,
				'input_atts' => array(
					'required' => true,
				),
			),
		) );
		
	}
    
}