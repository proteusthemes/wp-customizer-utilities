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
}
