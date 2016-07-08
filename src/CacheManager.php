<?php

namespace ProteusThemes\CustomizerUtils;

/**
 * A set of reusable static functions, that come handy in customizer controls and settings.
 */

class CacheManager {
	/**
	 * The singleton manager instance
	 *
	 * @see wp-includes/class-wp-customize-manager.php
	 * @var WP_Customize_Manager
	 */
	private $wp_customize;

	/**
	 * Holds the array for the DynamiCSS.
	 */
	private $theme_mod_name;

	public function __construct( $wp_customize = null, $theme_mod_name = 'cached_css' ) {
		if ( is_a( $wp_customize, '\WP_Customize_Manager' ) ) {
			$this->wp_customize = $wp_customize;
		}

		$this->theme_mod_name = $theme_mod_name;
	}

	/**
	 * Cache the rendered CSS after the settings are saved in the DB.
	 * This is purely a performance improvement.
	 *
	 * Used by hook: add_action( 'customize_save_after', ... );
	 *
	 * @return void
	 */
	public function cache_rendered_css( $callable_selectors_filter = false ) {
		set_theme_mod( $this->theme_mod_name, $this->render_css( $callable_selectors_filter ) );
	}


	/**
	 * Render the CSS from all the settings which are of type `Setting\DynamicCSS`
	 *
	 * @return string text/css
	 */
	public function render_css( $callable_selectors_filter = false ) {
		$out = array_map( function( $setting ) use ( $callable_selectors_filter ) {
			return $setting->render_css( $callable_selectors_filter );
		}, $this->get_dynamic_css_settings() );

		$css_string = implode( PHP_EOL, $out );

		return $css_string;
	}


	/**
	 * Get only the CSS settings of type `Setting\DynamicCSS`.
	 *
	 * @see is_dynamic_css_setting
	 * @return array
	 */
	private function get_dynamic_css_settings() {
		return array_filter( $this->wp_customize->settings(), array( $this, 'is_dynamic_css_setting' ) );
	}


	/**
	 * Helper conditional function for filtering the settings.
	 *
	 * @param  mixed $setting
	 * @return boolean
	 */
	public static function is_dynamic_css_setting( $setting ) {
		return is_a( $setting, '\ProteusThemes\CustomizerUtils\Setting\DynamicCSS' );
	}
}
