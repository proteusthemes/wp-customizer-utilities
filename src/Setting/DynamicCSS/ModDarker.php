<?php

namespace ProteusThemes\CustomizerUtils\Setting\DynamicCSS;

/**
 * Return modified variant of the color
 */

class ModDarker implements ModInterface {
	private $darker;

	public function __construct( $darker = 10 ) {
		$this->darker = (int) $darker;
	}

	public function modify( $in ) {
		$color = new \Mexitek\PHPColors\Color( $in );

		return '#' . $color->darken( $this->darker );
	}
}