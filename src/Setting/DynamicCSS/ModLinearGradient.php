<?php

namespace ProteusThemes\CustomizerUtils\Setting\DynamicCSS;

/**
 * Return modified variant of the color
 */

class ModLinearGradient implements ModInterface {
	private $orientation;

	public function __construct( ModInterface $modifier, $orientation = 'to bottom' ) {
		$this->modifier    = $modifier;
		$this->orientation = $orientation;
	}

	public function modify( $in ) {
		$firstColor  = $in;
		$secondColor = $this->modifier->modify( $in );

		return '#' . $currentColor->darken( $this->darker );

		return sprintf( '%2$s linear-gradient(%1$s, %2$s, #%3$s)', $this->orientation, $firstColor, $secondColor );
	}
}