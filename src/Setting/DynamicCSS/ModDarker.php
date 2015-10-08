<?php

namespace ProteusThemes\CustomizerUtils\Setting\DynamicCSS;

/**
 * Returns darker shade of the color.
 */

class ModDarker implements ModInterface {

	private $darker;

	/**
	 * @param integer $darker Darker in percents.
	 */
	public function __construct( $darker = 10 ) {
		$this->darker = (int) $darker;
	}

	/**
	 * Must-implement function from interface. Returns modified value.
	 * @param  string $in input hex-dec color code
	 * @return string     hex-dex darker variant
	 */
	public function modify( $in ) {
		$color = new \Mexitek\PHPColors\Color( $in );

		return '#' . $color->darken( $this->darker );
	}
}