<?php
/**
 * Provides helper functions.
 *
 * @since      1.0.0
 *
 * @package    PyIS_DPD_HelpScout
 * @subpackage PyIS_DPD_HelpScout/core
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since 1.0.0
 *
 * @return PyIS_DPD_HelpScout
 */
function PYISDPDHELPSCOUT() {
	return PyIS_DPD_HelpScout::instance();
}

/**
 * Quick access to plugin field helpers.
 *
 * @since 1.0.0
 *
 * @return RBM_FieldHelpers
 */
function pyis_dpd_helpscout_fieldhelpers() {
	return PYISDPDHELPSCOUT()->field_helpers;
}

/**
 * Initializes a field group for automatic saving.
 *
 * @since 1.0.0
 *
 * @param $group
 */
function pyis_dpd_helpscout_init_field_group( $group ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->save->initialize_fields( $group );
}

/**
 * Gets a meta field helpers field.
 *
 * @since 1.0.0
 *
 * @param string $name Field name.
 * @param string|int $post_ID Optional post ID.
 * @param mixed $default Default value if none is retrieved.
 * @param array $args
 *
 * @return mixed Field value
 */
function pyis_dpd_helpscout_get_field( $name, $post_ID = false, $default = '', $args = array() ) {
    $value = pyis_dpd_helpscout_fieldhelpers()->fields->get_meta_field( $name, $post_ID, $args );
    return $value !== false ? $value : $default;
}

/**
 * Gets a option field helpers field.
 *
 * @since 1.0.0
 *
 * @param string $name Field name.
 * @param mixed $default Default value if none is retrieved.
 * @param array $args
 *
 * @return mixed Field value
 */
function pyis_dpd_helpscout_get_option_field( $name, $default = '', $args = array() ) {
	$value = pyis_dpd_helpscout_fieldhelpers()->fields->get_option_field( $name, $args );
	return $value !== false ? $value : $default;
}

/**
 * Outputs a text field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_text( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_text( $args['name'], $args );
}

/**
 * Outputs a password field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_password( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_password( $args['name'], $args );
}

/**
 * Outputs a textarea field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_textarea( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_textarea( $args['name'], $args );
}

/**
 * Outputs a checkbox field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_checkbox( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_checkbox( $args['name'], $args );
}

/**
 * Outputs a toggle field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_toggle( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_toggle( $args['name'], $args );
}

/**
 * Outputs a radio field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_radio( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_radio( $args['name'], $args );
}

/**
 * Outputs a select field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_select( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_select( $args['name'], $args );
}

/**
 * Outputs a number field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_number( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_number( $args['name'], $args );
}

/**
 * Outputs an image field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_media( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_media( $args['name'], $args );
}

/**
 * Outputs a datepicker field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_datepicker( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_datepicker( $args['name'], $args );
}

/**
 * Outputs a timepicker field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_timepicker( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_timepicker( $args['name'], $args );
}

/**
 * Outputs a datetimepicker field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_datetimepicker( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_datetimepicker( $args['name'], $args );
}

/**
 * Outputs a colorpicker field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_colorpicker( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_colorpicker( $args['name'], $args );
}

/**
 * Outputs a list field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_list( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_list( $args['name'], $args );
}

/**
 * Outputs a hidden field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_hidden( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_hidden( $args['name'], $args );
}

/**
 * Outputs a table field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_table( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_table( $args['name'], $args );
}

/**
 * Outputs a HTML field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_do_field_html( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_html( $args['name'], $args );
}

/**
 * Outputs a repeater field.
 *
 * @since 1.0.0
 *
 * @param mixed $values
 */
function pyis_dpd_helpscout_do_field_repeater( $args = array() ) {
	pyis_dpd_helpscout_fieldhelpers()->fields->do_field_repeater( $args['name'], $args );
}

/**
 * Outputs a hook. Useful for arbitrary HTML
 *
 * @since 1.0.0
 *
 * @param mixed $values
 */
function pyis_dpd_helpscout_do_field_hook( $args = array() ) {
	do_action( 'pyis_dpd_helpscout_' . $args['name'], $args );
}

/**
 * Outputs a String if a Callback Function does not exist for an Options Page Field
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function pyis_dpd_helpscout_missing_callback( $args ) {
	
	printf( 
		_x( 'A callback function called "pyis_dpd_helpscout_do_field_%s" does not exist.', '%s is the Field Type', 'pyis-dpd-helpscout' ),
		$args['type']
	);
		
}