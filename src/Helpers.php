<?php

namespace ProteusThemes\CustomizerUtils;

/**
 * A set of reusable static functions, that come handy in customizer controls and settings.
 */

class Helpers {
	/**
	 * Returns all published pages (IDs and titles).
	 *
	 * @return array with key: ID and value: title
	 */
	public static function get_all_pages_id_title() {
		$args = array(
			'sort_order'  => 'ASC',
			'sort_column' => 'post_title',
			'post_type'   => 'page',
			'post_status' => 'publish',
		);
		$pages = get_pages( $args );

		// Create the pages map with the default value of none and the custom url option.
		$featured_page_choices               = array();
		$featured_page_choices['none']       = esc_html__( 'None', 'wp-customier-utilities' );
		$featured_page_choices['custom-url'] = esc_html__( 'Custom URL', 'wp-customier-utilities' );

		// Parse through the objects returned and add the key value pairs to the featured_page_choices map.
		foreach ( $pages as $page ) {
			$featured_page_choices[ $page->ID ] = $page->post_title;
		}

		return $featured_page_choices;
	}


	/**
	 * Returns true if the featured page is set to custom URL.
	 *
	 * @return boolean
	 */
	public static function is_theme_mod_specific_value( $theme_mod_name, $specific_value, $default = 'none' ) {
		return $specific_value === get_theme_mod( sanitize_key( $theme_mod_name ), $default );
	}


	/**
	 * Returns true if header background image is set.
	 *
	 * @return boolean
	 */
	public static function is_theme_mod_not_empty( $theme_mod_name, $default = '' ) {
		$theme_mod_value = get_theme_mod( sanitize_key( $theme_mod_name ), $default );
		return ! empty( $theme_mod_value );
	}


	/**
	 * Get the dimensions of the logo image when the setting is saved.
	 * This is purely a performance improvement.
	 *
	 * Used by hook: add_action( 'customize_save_logo_img', ..., 10, 1 );
	 *
	 * @return void
	 */
	public static function save_logo_dimensions( $setting, $theme_mod_name = 'logo_dimensions_array' ) {
		$logo_width_height = array();
		$img_data          = getimagesize( esc_url( $setting->post_value() ) );

		if ( is_array( $img_data ) ) {
			$logo_width_height = array_slice( $img_data, 0, 2 );
			$logo_width_height = array_combine( array( 'width', 'height' ), $logo_width_height );
		}

		set_theme_mod( sanitize_key( $theme_mod_name ), $logo_width_height );
	}


	/**
	 * Function that is hooked to wp_head and outputs only in customizer additional
	 * <style> tag for DynamicCSS.
	 */
	public static function add_dynamic_css_style_tag() {
		echo '<style id="wp-utils-dynamic-css-style-tag" type="text/css"></style>';
	}


	/**
	 * Filter for the selectors that contain a WooCommerce-specific selectors (most common case).
	 *
	 * @return boolean
	 */
	public static function is_not_woocommerce_css_selector( $css_selector ) {
		return false === strpos( $css_selector, '.woocommerce' );
	}


	/**
	 * A wp_kses extension function - add script tag to the wp_kses allow tags.
	 *
	 * @param  string $data The string to be sanitized.
	 * @return string       Sanitized string.
	 */
	public static function wp_kses_script( $data ) {
		global $allowedposttags;
		$allowedposttags_script = $allowedposttags;
		$allowedposttags_script['script'] = array( 'type' => array (), 'src' => array () );

		return wp_kses( $data, $allowedposttags_script );
	}

	/**
	 * A helper function to sanitize a boolean.
	 * Used for the checkbox setting sanitization.
	 *
	 * @param boolean $input Input data.
	 *
	 * @return bool
	 */
	public static function sanitize_boolean( $input ) {
		return ( isset( $input ) && true == $input );
	}


	/**
	 * Returns true if the theme mod value can be found in the passed array.
	 *
	 * @param string $theme_mod_name The theme mod name/key, to retrieve the value from.
	 * @param array  $values         The array of values, the theme mod value should be tested against.
	 * @param string $default        The default value of the theme mod, if the theme mod does not exists.
	 *
	 * @return boolean
	 */
	public static function is_theme_mod_in_array( $theme_mod_name, $values = array(), $default = 'default' ) {
		$theme_mod_value = get_theme_mod( sanitize_key( $theme_mod_name ), $default );

		return in_array( $theme_mod_value, $values, true );
	}


	/**
	 * Processes and alters the arguments to create DynamicCSS when theme supports multiple skins.
	 *
	 * @param array $args Arguments to be passed to DynamicCSS setting.
	 * @param string $selected_skin Currently selected theme skin.
	 *
	 * @return array Modified array.
	 */
	public static function process_multiskin_args( $args, $selected_skin = 'default' ) {
		self::maybe_mutate_args( 'default', $args, $selected_skin );
		self::maybe_mutate_args( 'css_props', $args, $selected_skin );

		return $args;
	}


	/**
	 * Checks and changes values in $args according to skin.
	 *
	 * @param string $key_to_check Key in the $args for checking and potentially mutating.
	 * @param array $args Arguments to be passed to DynamicCSS setting.
	 * @param string $selected_skin Currently selected theme skin.
	 *
	 * @return void Mutates $args.
	 */
	protected static function maybe_mutate_args( $key_to_check, &$args, $selected_skin = 'default' ) {
		if (
			array_key_exists( $key_to_check, $args ) &&
			is_array( $args[ $key_to_check ] ) &&
			array_key_exists( $selected_skin, $args[ $key_to_check ] )
		) {
			$args[ $key_to_check ] = $args[ $key_to_check ][ $selected_skin ];
		}
	}
}
