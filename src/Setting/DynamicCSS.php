<?php

namespace ProteusThemes\CustomizerUtils\Setting;

/**
 * Custom setting data type, capable of auto-generating the CSS output for the color variants.
 *
 * Since quite some settings in the customizer are color-CSS related, we can abstract out that
 * in a way that we have a custom data type which is capable to dynamically generate the CSS
 * out from the provided array `$css_props`.
 */

class DynamicCSS extends \WP_Customize_Setting {
	/**
	 * CSS properties mapped to the CSS selectors.
	 * Each propery can have multiple selectors, gruped in @media queries.
	 *
	 * @var array( // list of all css properties this setting controls
	 * 	array( // each property in it's own array
	 * 		'name'  => 'color',
	 * 		'selectors' => array(
	 * 			'noop' => array( // regular selectors
	 * 				'.selector1',
	 * 				'.selector2',
	 * 				array( // indicates a new master selector (needed for example for '*::placeholder')
	 * 					'.selector1',
	 * 					'.selector2',
	 * 				),
	 * 			),
	 * 			'@media (min-width: 900px)' => array( // selectors which should be in MQ
	 * 				'.selector3',
	 * 				'.selector4',
	 * 			),
	 * 		),
	 * 		'modifier'  => $darker10, // separate data type
	 * 	)
	 */
	public $css_props = array();

	/**
	 * Default transport method for this setting type is 'postMessage'.
	 *
	 * @access public
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * Active callback - should be CSS be printed out of not, depending on the context.
	 *
	 * @access protected
	 * @var callable | null
	 */
	protected $active_callback = null;

	/**
	 * Getter function for the raw $css_props property.
	 * @return 2D array
	 */
	public function get_css_props() {
		return $this->css_props;
	}

	/**
	 * Return all variants of the CSS propery with selectors.
	 * Optionally filtered with the modifier.
	 *
	 * @param string $name     Name of the css propery, ie. color, background-color
	 * @param string $modifier Optional modifier to further filter down the css props returned.
	 * @return array
	 */
	public function get_single_css_prop( $name, $modifier = false ) {
		return array_filter( $this->css_props, function ( $property ) {
			if ( false !== $modifier ) {
				return $name === $property['name'] && $modifier == $property['modifier'];
			}
			else {
				return $name === $property['name'];
			}
		} );
	}

	/**
	 * Render the entire CSS for this setting.
	 * @return string text/css
	 */
	public function render_css( $callable_selectors_filter = false ) {
		$out = array();

		foreach ( $this->css_props as $property ) {
			foreach ( $property['selectors'] as $mq => $selectors ) {
				if ( empty( $selectors ) ) {
					continue;
				}

				$main_selectors = array_filter( $selectors, function ( $selector ) {
					return is_string( $selector );
				} );

				$all_selector_groups = array( $main_selectors ) + array_filter( $selectors, function ( $selector ) {
					return is_array( $selector );
				} );

				if ( is_callable( $callable_selectors_filter ) ) { // optionally filter out some selectors
					foreach ( $all_selector_groups as $key => $inner_selectors ) {
						$all_selector_groups[ $key ] = array_filter( $inner_selectors, $callable_selectors_filter );
					}
				}

				$value = $this->value();

				if ( is_callable( $this->active_callback ) && false === call_user_func( $this->active_callback, $value, $selectors, $mq ) ) {
					continue;
				}

				if ( array_key_exists( 'modifier', $property ) ) {
					$value = $this->apply_modifier( $value, $property['modifier'] );
				}

				foreach ( $all_selector_groups as $selectors_group ) {
					if ( 'noop' === $mq ) { // essentially no media query
							$out[] = sprintf( '%1$s { %2$s: %3$s; }', implode( ', ', $selectors_group ), $property['name'], $value );
						}
					else { // we have an actual media query
						$out[] = sprintf( '%4$s { %1$s { %2$s: %3$s; } }', implode( ', ', $selectors_group ), $property['name'], $value, $mq );
					}
				}
			}
		}

		return implode( PHP_EOL, $out );
	}

	/**
	 * Apply modifier to the untouched value.
	 * @param  string                           $in       Setting value.
	 * @param  callable|DynamicCSS\ModInterface $modifier
	 * @return string Modified value.
	 */
	private function apply_modifier( $in, $modifier ) {
		$out = $in;

		if ( $modifier instanceof DynamicCSS\ModInterface ) {
			$out = $modifier->modify( $out );
		}
		else if ( is_callable( $modifier ) ) {
			$out = call_user_func( $modifier, $out );
		}

		return $out;
	}
}
