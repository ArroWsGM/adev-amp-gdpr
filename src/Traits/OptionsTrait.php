<?php

namespace Arrowsgm\AmpGDPR\Traits;


trait OptionsTrait {
	static $options = [];

	/**
	 * Returns plugin options
	 *
	 * @return array|string
	 */
	public static function getOptions() {
		if ( empty( self::$options ) ) {
			self::$options = get_option( self::PTD . '_plugin_options' );
		}

		return self::$options;
	}

	/**
	 * Returns plugin option value
	 *
	 * @param string $option
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function getOption( $option, $default = null ) {
		$options = self::getOptions();

		if ( ! $option || ! is_array( $options ) || empty( $options ) ) {
			return $default;
		}

		if ( array_key_exists( $option, $options ) ) {
			return $options[ $option ];
		} else {
			return $default;
		}
	}
}