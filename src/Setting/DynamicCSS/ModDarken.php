<?php

namespace ProteusThemes\CustomizerUtils\Setting\DynamicCSS;

/**
 * Returns darker shade of the color.
 */

class ModDarken implements ModInterface {

	private $darken;

	/**
	 * @param integer $darken Darker in percents.
	 */
	public function __construct( $darken = 10 ) {
		$this->darken = (int) $darken;
	}

	/**
	 * Must-implement function from interface. Returns modified value.
	 * @param  string $in input hex-dec color code
	 * @return string     hex-dex darker variant
	 */
	public function modify( $in ) {
		$color = new \Mexitek\PHPColors\Color( $in );

		return '#' . $color->darken( $this->darken );
	}
}
