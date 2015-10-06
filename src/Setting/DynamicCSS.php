<?php

namespace ProteusThemes\CustomizerUtils\Setting;

/**
 * Custom setting data type, capable of auto-generating the CSS output for the color variants.
 *
 * Since quite some settings in the customizer are color-CSS related, we can abstract out that
 * in a way that we have a custom data type `ProteusThemes_Customize_Setting_Dynamic_CSS` which is capable
 * of dynamically generate the CSS out from the provided array `$css_map`.
 */

class DynamicCSS extends \WP_Customize_Setting {
	/**
	 * 2D Array the CSS properties maped to the CSS selectors.
	 * Each propery can have multiple selectors.
	 *
	 * @var array {
	 *   'color' => array( '.css-selector-1', '.css-selector-2' ),
	 *   'background-color' => array( '.css-selector-2', '.css-selector-3' ),
	 * }
	 */
	public $css_map = array();

	/**
	 * Constant for supporting the filtering values in rendered CSS
	 */
	const FILTER_SEPARATOR = '|';

	/**
	 * Default transport method for this setting type is 'postMessage'.
	 *
	 * @access public
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * Getter function for the $css_map class property.
	 * @return 2D array
	 */
	public function get_css_map() {
		return $this->css_map;
	}

	/**
	 * Return all the CSS properties of the current setting.
	 * @return array
	 */
	public function get_all_css_properties() {
		return array_keys( $this->get_css_map() );
	}

	/**
	 * Generate the master CSS selector groups (by media queries) for a single CSS property of the setting. 2D array.
	 * @param  string $css_property
	 * @return array Each mq definition has its own group, the special one is general which does not have MQ
	 */
	public function css_selector_groups_for_property( $css_property ) {
		$selector_media_groups = array();


		if ( array_key_exists( $css_property , $this->css_map ) ) {
			// walk through the selectors and add them to the right group, based by appended media queries
			foreach ( $this->css_map[ $css_property ] as $css_selector ) {
				if ( self::is_filterable_string( $css_selector ) ) {
					// here were save in $css_selector and $media_query vars the actually useful numbers
					list( $css_selector, $media_query )      = explode( self::FILTER_SEPARATOR, $css_selector );
					$selector_media_groups[ $media_query ][] = $css_selector;
				}
				else {
					// selector is not special, so we can save it in general group (no MQ) just as is
					$selector_media_groups['general'][] = $css_selector;
				}
			}
		}

		return $selector_media_groups;
	}

	/**
	 * Return valid CSS selector for all groups (without MQ) returned by method css_selector_groups_for_property
	 * @param  string $css_property
	 * @return string valid CSS selector
	 */
	public function plain_selectors_for_all_groups( $css_property ) {
		$selectors = array();
		$all_groups = $this->css_selector_groups_for_property( $css_property );

		foreach ( $all_groups as $group_selectors ) {
			$selectors = array_merge( $selectors, $group_selectors );
		}

		$selectors = array_unique( $selectors );

		return implode( ', ', $selectors );
	}

	/**
	 * Render the CSS for this setting.
	 * @return string text/css
	 */
	public function render_css() {
		$out = '';

		foreach ( $this->get_css_map() as $css_prop_raw => $css_selectors ) {
			// we get here the $css_prop and $value
			extract( $this->filter_css_property( $css_prop_raw ) );

			foreach ( $this->css_selector_groups_for_property( $css_prop_raw ) as $media_query => $css_selectors_arr ) {
				$css_selectors = implode( ', ', $css_selectors_arr );

				if ( 'general' === $media_query ) { // essentially no media query
					$out .= sprintf( '%1$s { %2$s: %3$s; }', $css_selectors, $css_prop, $value );
				}
				else { // we have an actual media query
					$out .= sprintf( '%4$s { %1$s { %2$s: %3$s; } }', $css_selectors, $css_prop, $value, $media_query );
				}

				$out .= PHP_EOL;
			}
		}

		return $out;
	}

	/**
	 * Detects if filter is needed and outut the associative array with prepared values.
	 * @param  string $css_property might be filterable
	 * @return array Keys must be `css_property` and `value`, with the values prepared to be directly used.
	 */
	public function filter_css_property( $css_property ) {
		// defaults
		$out = array(
			'css_prop' => $css_property,
			'value'    => $this->value()
		);

		if ( self::is_filterable_string( $css_property ) ) {
			list( $css_property, $filter ) = explode( self::FILTER_SEPARATOR, $css_property );

			$out['css_prop'] = trim( $css_property );

			// for filters: lowercase characters, numbers and underscore _ allowed
			if ( preg_match( '/^([a-z0-9\_]+)(?:\((\w+)\))?$/i', trim( $filter ), $matches ) ) {
				switch ( $matches[1] ) {
					case 'darken':
						$darkerHex    = new \Mexitek\PHPColors\Color( $out['value'] );
						$out['value'] = '#' . $darkerHex->darken( (int) $matches[2] );
						break;
					case 'lighten':
						$lighterHex   = new \Mexitek\PHPColors\Color( $out['value'] );
						$out['value'] = '#' . $lighterHex->lighten( (int) $matches[2] );
						break;
					case 'important':
						$out['value'] .= ' !important';
						break;
					case 'url':
						$out['value'] = sprintf( 'url("%s")', $out['value'] );
						break;
					case 'linear_gradient_to_bottom':
						$secondColor = new \Mexitek\PHPColors\Color( $out['value'] );
						$out['value'] = sprintf( '%1$s linear-gradient(to bottom, %1$s, #%2$s)', $this->value(), $secondColor->darken( (int) $matches[2] ) );
						break;
					default:
						# already defined in first line of this func
						break;
				}
			}
		}

		return $out;
	}

	/**
	 * If the CSS property contains the defined FILTER_SEPARATOR and return true/false regarding this test
	 * @param  string  $css_property
	 * @return boolean
	 */
	public static function is_filterable_string( $css_property ) {
		return strpos( $css_property, self::FILTER_SEPARATOR ) > 0;
	}
}