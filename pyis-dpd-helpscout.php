<?php
/*
 * Plugin Name: PyImageSearch DPD+HelpScout
 * Plugin URL: https://github.com/Py-Image/dpd-helpscout-wordpress
 * Description: Displays DPD Information for a Customer in HelpScout
 * Version: 1.0.0
 * Text Domain: pyis-dpd-helpscout
 * Author: Eric Defore
 * Author URI: http://realbigmarketing.com
 * Contributors: d4mation
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'PyIS_DPD_HelpScout' ) ) {

    /**
     * Main PyIS_DPD_HelpScout class
     *
     * @since       1.0.0
     */
    class PyIS_DPD_HelpScout {
        
        /**
         * @var         PyIS_DPD_HelpScout $plugin_data Holds Plugin Header Info
         * @since       1.0.0
         */
        public $plugin_data;
        
        /**
         * @var         PyIS_DPD_HelpScout $settings Admin Settings
         * @since       1.0.0
         */
        public $settings;
        
        /**
         * @var         PyIS_DPD_HelpScout $rest REST Endpoints
         * @since       1.0.0
         */
        public $rest;
        
        /**
         * @var         PyIS_DPD_HelpScout $dpd_api DPD API Class
         * @since       1.0.0
         */
        public $dpd_api;
		
		/**
         * @var         PyIS_DPD_HelpScout $field_helpers RBM Field Helpers
         * @since       1.0.0
         */
		public $field_helpers;

        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true PyIS_DPD_HelpScout
         */
        public static function instance() {
            
            static $instance = null;
            
            if ( null === $instance ) {
                $instance = new static();
            }
            
            return $instance;

        }
        
        protected function __construct() {
            
            $this->setup_constants();
            $this->load_textdomain();
            
            global $wp_version;
            global $wp_settings_errors;
			
			require_once __DIR__ . '/core/library/rbm-field-helpers/rbm-field-helpers.php';
		
			$this->field_helpers = new RBM_FieldHelpers( array(
				'ID'   => 'pyis_dpd_helpscout', // Your Theme/Plugin uses this to differentiate its instance of RBM FH from others when saving/grabbing data
				'l10n' => array(
					'field_table'    => array(
						'delete_row'    => __( 'Delete Row', 'pyis-dpd-helpscout' ),
						'delete_column' => __( 'Delete Column', 'pyis-dpd-helpscout' ),
					),
					'field_select'   => array(
						'no_options'       => __( 'No select options.', 'pyis-dpd-helpscout' ),
						'error_loading'    => __( 'The results could not be loaded', 'pyis-dpd-helpscout' ),
						/* translators: %d is number of characters over input limit */
						'input_too_long'   => __( 'Please delete %d character(s)', 'pyis-dpd-helpscout' ),
						/* translators: %d is number of characters under input limit */
						'input_too_short'  => __( 'Please enter %d or more characters', 'pyis-dpd-helpscout' ),
						'loading_more'     => __( 'Loading more results...', 'pyis-dpd-helpscout' ),
						/* translators: %d is maximum number items selectable */
						'maximum_selected' => __( 'You can only select %d item(s)', 'pyis-dpd-helpscout' ),
						'no_results'       => __( 'No results found', 'pyis-dpd-helpscout' ),
						'searching'        => __( 'Searching...', 'pyis-dpd-helpscout' ),
					),
					'field_repeater' => array(
						'collapsable_title' => __( 'New Row', 'pyis-dpd-helpscout' ),
						'confirm_delete'    => __( 'Are you sure you want to delete this element?', 'pyis-dpd-helpscout' ),
						'delete_item'       => __( 'Delete', 'pyis-dpd-helpscout' ),
						'add_item'          => __( 'Add', 'pyis-dpd-helpscout' ),
					),
					'field_media'    => array(
						'button_text'        => __( 'Upload / Choose Media', 'pyis-dpd-helpscout' ),
						'button_remove_text' => __( 'Remove Media', 'pyis-dpd-helpscout' ),
						'window_title'       => __( 'Choose Media', 'pyis-dpd-helpscout' ),
					),
					'field_checkbox' => array(
						'no_options_text' => __( 'No options available.', 'pyis-dpd-helpscout' ),
					),
				),
			) );
            
            if ( is_admin() ) {
            
                if ( version_compare( $wp_version, '4.4' ) == -1 ) {

                    $this->admin_notices[] = sprintf(
                        _x( '%s requires your WordPress installation to be at least v%s or higher!', 'Super Old WordPress Installation Error', 'pyis-dpd-helpscout' ),
                        '<strong>' . $this->plugin_data['Name'] . '</strong>',
                        '4.4'
                    );

                    if ( ! has_action( 'admin_notices', array( $this, 'admin_notices' ) ) ) {
                        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
                    }

                    return;

                }

				if ( version_compare( phpversion(), '5.6' ) == -1 ) {

                    $this->admin_notices[] = sprintf(
                        _x( '%s requires your PHP Version to be at least v%s or higher!', 'Super Old PHP Version Error', 'pyis-dpd-helpscout' ),
                        '<strong>' . $this->plugin_data['Name'] . '</strong>',
                        '5.6'
                    );

                    if ( ! has_action( 'admin_notices', array( $this, 'admin_notices' ) ) ) {
                        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
                    }

                    return;

                }

                $api_key = get_option( 'pyis_dpd_api_key' );
                $api_key = ( $api_key ) ? $api_key : '';

                $account_id = get_option( 'pyis_dpd_account_id' );
                $account_id = ( $account_id ) ? $account_id : '';

                if ( ! $api_key || ! $account_id ) {

                    $this->admin_notices[] = sprintf( 
                        _x( 'In order to communicate with DPD, you must enter some credentials in the %s%s Settings Page%s!', 'DPD API Credentials Needed', 'pyis-dpd-helpscout' ), 
                        '<a href="' . get_admin_url( null, 'options-general.php?page=pyis-dpd-helpscout' ) . '" title="' . sprintf( _x( '%s Settings', 'Settings Page Link from Error Message', 'pyis-dpd-helpscout' ), $this->plugin_data['Name'] ) . '">',
                        '<strong>' . $this->plugin_data['Name'] . '</strong>', '</a>'
                    );

                    if ( ! has_action( 'admin_notices', array( $this, 'admin_notices' ) ) ) {
                        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
                    }

                    // Not breaking execution for this error

                }
                
            }
            
            $this->require_necessities();
            
            // Register our CSS/JS for the whole plugin
            add_action( 'init', array( $this, 'register_scripts' ) );
            
        }

        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            
            // WP Loads things so weird. I really want this function.
            if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }
            
            // Only call this once, accessible always
            $this->plugin_data = get_plugin_data( __FILE__ );

            if ( ! defined( 'PyIS_DPD_HelpScout_VER' ) ) {
                // Plugin version
                define( 'PyIS_DPD_HelpScout_VER', $this->plugin_data['Version'] );
            }

            if ( ! defined( 'PyIS_DPD_HelpScout_DIR' ) ) {
                // Plugin path
                define( 'PyIS_DPD_HelpScout_DIR', plugin_dir_path( __FILE__ ) );
            }

            if ( ! defined( 'PyIS_DPD_HelpScout_URL' ) ) {
                // Plugin URL
                define( 'PyIS_DPD_HelpScout_URL', plugin_dir_url( __FILE__ ) );
            }
            
            if ( ! defined( 'PyIS_DPD_HelpScout_FILE' ) ) {
                // Plugin File
                define( 'PyIS_DPD_HelpScout_FILE', __FILE__ );
            }

        }

        /**
         * Internationalization
         *
         * @access      private 
         * @since       1.0.0
         * @return      void
         */
        private function load_textdomain() {
            
            $lang_dir = PyIS_DPD_HelpScout_DIR . '/languages/';
            
            /**
             * Allows the ability to override the translation directory within the plugin to check.
             *
             * @since 1.0.0
             */
            $lang_dir = apply_filters( 'pyis_dpd_helpscout_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'pyis-dpd-helpscout' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'pyis-dpd-helpscout', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/pyis-dpd-helpscout/' . $mofile;

            if ( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/pyis-dpd-helpscout/ folder
                // This way translations can be overridden via the Theme/Child Theme
                load_textdomain( 'pyis-dpd-helpscout', $mofile_global );
            }
            else if ( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/pyis-dpd-helpscout/languages/ folder
                load_textdomain( 'pyis-dpd-helpscout', $mofile_local );
            }
            else {
                // Load the default language files
                load_plugin_textdomain( 'pyis-dpd-helpscout', false, $lang_dir );
            }

        }
        
        /**
         * Include different aspects of the Plugin
         * 
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function require_necessities() {
            
            if ( is_admin() ) {
                
                require_once PyIS_DPD_HelpScout_DIR . '/core/admin/pyis-dpd-helpscout-settings.php';
                $this->settings = new PyIS_DPD_HelpScout_Settings();
                
            }
            
            $api_key = get_option( 'pyis_dpd_api_key' );
            $api_key = ( $api_key ) ? $api_key : '';
            
            $account_id = get_option( 'pyis_dpd_account_id' );
            $account_id = ( $account_id ) ? $account_id : '';
            
            require_once PyIS_DPD_HelpScout_DIR . '/core/api/pyis-dpd-helpscout-dpd-api.php';
            $this->dpd_api = new PyIS_DPD_HelpScout_API_DPD( $account_id, $api_key );
            
            require_once PyIS_DPD_HelpScout_DIR . '/core/rest/pyis-dpd-helpscout-rest.php';
            $this->rest = new PyIS_DPD_HelpScout_REST();
            
        }
        
        /**
         * Register our CSS/JS to use later
         * 
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function register_scripts() {
            
            wp_register_style(
                'pyis-dpd-helpscout-admin',
                PyIS_DPD_HelpScout_URL . '/assets/css/admin.css',
                null,
                defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PyIS_DPD_HelpScout_VER
            );
            
            wp_register_script(
                'pyis-dpd-helpscout-admin',
                PyIS_DPD_HelpScout_URL . '/assets/js/admin.js',
                array( 'jquery' ),
                defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PyIS_DPD_HelpScout_VER,
                true
            );
            
        }
        
        /**
        * Show admin notices.
        * 
        * @access    public
        * @since     1.0.0
        * @return    HTML
        */
        public function admin_notices() {
            ?>
            <div class="error">
                <?php foreach ( $this->admin_notices as $notice ) : ?>
                    <p>
                        <?php echo $notice; ?>
                    </p>
                <?php endforeach; ?>
            </div>
            <?php
        }

    }

} // End Class Exists Check

/**
 * The main function responsible for returning the one true PyIS_DPD_HelpScout
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \PyIS_DPD_HelpScout The one true PyIS_DPD_HelpScout
 */
add_action( 'plugins_loaded', 'PyIS_DPD_HelpScout_load' );
function PyIS_DPD_HelpScout_load() {
        
    require_once __DIR__ . '/core/pyis-dpd-helpscout-functions.php';
    PYISDPDHELPSCOUT();

}