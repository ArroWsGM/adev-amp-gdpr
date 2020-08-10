<?php

namespace Arrowsgm\AmpGDPR;


use Arrowsgm\AmpGDPR\Traits\OptionsTrait;

class AmpGDPR {
	use OptionsTrait;

	/**
	 * Static property to hold our singleton instance
	 */
	static $instance = false;

	/**
	 * @var string
	 */
	const PTD = ADEV_AMP_GDPR_PTD;
	const GDPR_TEXT = ADEV_AMP_GDPR_TEXT;

	/**
	 * constructor.
	 */
	public function __construct() {
		if (
			! defined( 'ADEV_AMP_GDPR_PTD' )
			|| ! defined( 'ADEV_AMP_GDPR_PATH' )
			|| ! defined( 'ADEV_AMP_GDPR_TEXT' )
		) {
			wp_die( 'Core constants missing in  ' . basename( __FILE__, '.php' ) );
		}

		add_action( 'amp_post_template_footer', function ( $amp_template ) {
			if ( self::getOption( 'is_on' ) ):
				$policy_link = $this->get_policy_link();
				$policy_link = self::getOption( 'show_policy' ) ? "&nbsp;$policy_link" : '';
				?>
                <amp-consent layout="nodisplay" id="consent-element">
                <script type="application/json">
{
    "consentInstanceId": "<?php echo esc_attr( self::PTD ) ?>-consent",
    "consentRequired": true,
    "promptUI": "consent-ui"
}

                </script>
                <div id="consent-ui" class="adev-gdpr">
					<?php echo self::getOption( 'gdpr_text', self::GDPR_TEXT ) . $policy_link; ?>
                    <button on="tap:consent-element.accept" class="adev-gdpr-accept"
                            role="button"><?php _e( 'Accept', self::PTD ); ?></button>
                    <button on="tap:consent-element.dismiss" class="adev-gdpr-dismiss"
                            role="button"><?php _e( 'Dismiss', self::PTD ) ?></button>
                </div>
                </amp-consent><?php
			endif;
		} );

		add_action( 'amp_post_template_css', function () {
			if ( self::getOption( 'is_on' ) ):
?>amp-consent{background-color:#fff}.adev-gdpr{margin:0 auto;max-width:calc(<?php echo self::getOption( 'message_max_width', 840 ) ?>px - 32px);padding:1.25em 16px;position:relative;color:#0a0a0a}.adev-gdpr a[target="_blank"]:after{background-image:url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjEzIiBoZWlnaHQ9IjEzIiBmaWxsPSIjMWE3M2U4Ij48cGF0aCBkPSJNMTkgMTlINVY1aDdWM0g1YTIgMiAwIDAgMC0yIDJ2MTRhMiAyIDAgMCAwIDIgMmgxNGMxLjEgMCAyLS45IDItMnYtN2gtMnY3ek0xNCAzdjJoMy41OWwtOS44MyA5LjgzIDEuNDEgMS40MUwxOSA2LjQxVjEwaDJWM2gtN3oiLz48cGF0aCBmaWxsPSJub25lIiBkPSJNMCAwaDI0djI0SDBWMHoiLz48L3N2Zz4=);background-repeat:no-repeat;content:'';display:inline-block;height:.8125rem;margin:0 .1875rem 0 .25rem;position:relative;top:.125rem;width:.8125rem}.adev-gdpr-accept,.adev-gdpr-dismiss{border:none;background:0 0;padding:0}.adev-gdpr-accept{cursor:pointer;text-decoration:underline}.adev-gdpr-dismiss{font-size:0;padding:.345rem .75rem;position:absolute;right:.5rem;top:.5rem;display:block;text-align:center;cursor:pointer}.adev-gdpr-dismiss:before{content:'x';font-size:1.25rem;color:#ccc;display:block}<?php
			endif;
		} );

		add_filter( 'amp_post_template_data', function ( $data ) {
			if ( self::getOption( 'is_on' ) ) {
				$data['amp_component_scripts']['amp-consent'] = 'https://cdn.ampproject.org/v0/amp-consent-0.1.js';
			}

			return $data;
		} );
	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * returns it.
	 *
	 * @return AmpGDPR
	 */
	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_policy_link() {
		$page_id = get_option( 'wp_page_for_privacy_policy' );
		$page    = $page_id ? get_post( $page_id ) : null;

		if ( $page ) {
			$title = self::getOption( 'policy_btn_text' ) ?: $page->post_title;
			$link  = get_the_permalink( $page );

			if ( $link ) {
				return '<a href="' . $link . '" target="_blank">' . $title . '</a>';
			}
		}

		return '';
	}
}