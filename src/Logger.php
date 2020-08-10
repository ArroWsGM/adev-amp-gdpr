<?php

namespace Arrowsgm\AmpGDPR;


class Logger {
	/**
	 * Prepare data and write it to log
	 *
	 * @param mixed $log
	 *
	 * @uses log_write()
	 */
	public static function write( $log ) {
		if(
			defined('ADEV_AMP_GDPR_PATH')
			&& defined('ADEV_AMP_GDPR_PTD')
		) {
			if ( is_array( $log ) || is_object( $log ) ) {
				self::log_write( print_r( $log, true ) );
			} else {
				self::log_write( $log );
			}
		}
	}

	/**
	 * Write content into a file
	 *
	 * @param string $log
	 *
	 * @uses $dir
	 * @uses _B4ST_TTD
	 */
	protected static function log_write( $log ) {
		$file   = ADEV_AMP_GDPR_PATH . '/_' . ADEV_AMP_GDPR_PTD . '_.log';
		$output = '[' . date( "Y-m-d H:i:s" ) . ']: ';
		$output .= $log;
		$output .= "\n";
		file_put_contents( $file, $output, FILE_APPEND | LOCK_EX );
	}
}