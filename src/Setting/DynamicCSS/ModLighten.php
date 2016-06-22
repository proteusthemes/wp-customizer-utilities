<?php

namespace ProteusThemes\CustomizerUtils\Setting\DynamicCSS;

/**
 * Returns lighter shade of the color.
 */

class ModLighten implements ModInterface {

	private $lighten;

	/**
	 * @param integer $lighten Lighter in percents.
	 */
	public function __construct( $lighten = 10 ) {
		$this->lighten = (int) $lighten;
	}

	/**
	 * Must-implement function from interface. Returns modified value.
	 * @param  string $in input hex-dec color code
	 * @return string     hex-dex lighter variant
	 */
	public function modify( $in ) {
		$color = new \Mexitek\PHPColors\Color( $in );

		return '#' . $color->lighten( $this->lighten );
	}
}
