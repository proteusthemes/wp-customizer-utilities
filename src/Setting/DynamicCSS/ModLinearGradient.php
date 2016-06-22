<?php

namespace ProteusThemes\CustomizerUtils\Setting\DynamicCSS;

/**
 * Returns linear gradient CSS property.
 *
 * First color is left untouched, second color is modified with the @param @modifier
 */

class ModLinearGradient implements ModInterface {

	private $orientation;

	/**
	 * @param ModInterface $modifier    Instance which modifies the 2nd parameter.
	 * @param string       $orientation Orientation in the string notation or in degrees (needs css unit in this case as well).
	 */
	public function __construct( ModInterface $modifier, $orientation = 'to bottom' ) {
		$this->modifier    = $modifier;
		$this->orientation = $orientation;
	}

	/**
	 * Must-implement function from interface. Returns modified value.
	 * @param  string $in input hex-dec color code
	 * @return string     linear-gradient
	 */
	public function modify( $in ) {
		$firstColor  = $in;
		$secondColor = $this->modifier->modify( $in );

		return sprintf( '%2$s linear-gradient(%1$s, %2$s, %3$s)', $this->orientation, $firstColor, $secondColor );
	}
}
