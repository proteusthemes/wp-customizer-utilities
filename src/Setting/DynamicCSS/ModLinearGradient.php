<?php

namespace ProteusThemes\CustomizerUtils\Setting\DynamicCSS;

/**
 * Returns linear gradient CSS property.
 *
 * First color is left untouched, second color is modified with the @param @modifier
 */

class ModLinearGradient implements ModInterface {

	private $modifier;
	private $orientation;
	private $first_modifier;

	/**
	 * @param ModInterface $modifier       Instance which modifies the 2nd parameter.
	 * @param string       $orientation    Orientation in the string notation or in degrees (needs css unit in this case as well).
	 * @param ModInterface $first_modifier Instance which modifies the 1st parameter, which is optional.
	 */
	public function __construct( ModInterface $modifier, $orientation = 'to bottom', ModInterface $first_modifier = null ) {
		$this->modifier       = $modifier;
		$this->orientation    = $orientation;
		$this->first_modifier = $first_modifier;
	}

	/**
	 * Must-implement function from interface. Returns modified value.
	 * @param  string $in input hex-dec color code
	 * @return string     linear-gradient
	 */
	public function modify( $in ) {
		$firstColor  = $in;
		$secondColor = $this->modifier->modify( $in );

		if ( isset( $this->first_modifier ) ) {
			$firstColor = $this->first_modifier->modify( $in );
		}

		return sprintf( '%2$s linear-gradient(%1$s, %2$s, %3$s)', $this->orientation, $firstColor, $secondColor );
	}
}
