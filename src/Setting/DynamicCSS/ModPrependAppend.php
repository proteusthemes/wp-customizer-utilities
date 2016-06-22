<?php

namespace ProteusThemes\CustomizerUtils\Setting\DynamicCSS;

/**
 * Returns the same value but with prepended or appended string.
 */

class ModPrependAppend implements ModInterface {

	private $prefix;
	private $suffix;

	/**
	 * @param string $prefix Prefix.
	 * @param string $suffix Suffix.
	 */
	public function __construct( $prefix = '', $suffix = '' ) {
		$this->prefix = $prefix;
		$this->suffix = $suffix;
	}

	/**
	 * Must-implement function from interface. Returns modified value.
	 * @param  string $in Input value.
	 * @return string     Prepended / appended value.
	 */
	public function modify( $in ) {
		return $this->prefix . $in . $this->suffix;
	}
}
