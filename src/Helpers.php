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
	 * Filter for the selectors that contain a plugin-specific selectors.
	 *
	 * @return boolean True if the $needle is not found in $css_selector
	 */
	public static function is_not_plugin_specific_css_selector( $css_selector, $needle = '.woocommerce' ) {
		return false === strpos( $css_selector, $needle );
	}
}
