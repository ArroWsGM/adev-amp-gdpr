<?php
/*
Plugin Name: Adev AMP GDPR
Description: Wordpress plugin for adding GDPR alerts to AMP.
Version: 0.1.1
Author: ArroWs Development
Author URI: https://arrows-dev.com
Text Domain: adev-amp-gdpr
Domain Path: /languages
License: GPLv2 or later
*/
use Arrowsgm\AmpGDPR\AmpGDPR;
use Arrowsgm\AmpGDPR\Settings;

if ( ! defined( 'ADEV_AMP_GDPR_PTD' ) ) {
    define( 'ADEV_AMP_GDPR_PTD', 'adev-amp-gdpr' );
}
if ( ! defined( 'ADEV_AMP_GDPR_PATH' ) ) {
    define( 'ADEV_AMP_GDPR_PATH', dirname( __FILE__ ) );
}
if ( ! defined( 'ADEV_AMP_GDPR_TEXT' ) ) {
    define( 'ADEV_AMP_GDPR_TEXT', __( 'This site uses cookie.', ADEV_AMP_GDPR_PTD ) );
}

/**
 * load textdomain
 *
 * @return void
 */
add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( ADEV_AMP_GDPR_PTD, false, basename( dirname( __FILE__ ) ) . '/languages' );
} );

require ADEV_AMP_GDPR_PATH . '/vendor/autoload.php';

if ( is_admin() ) {
	new Settings();
}

AmpGDPR::getInstance();