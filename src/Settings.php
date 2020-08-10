<?php

namespace Arrowsgm\AmpGDPR;


class Settings {
	const PTD = ADEV_AMP_GDPR_PTD;
	const PATH = ADEV_AMP_GDPR_PATH;
	const MAX_WIDTH = 320;
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;
	private $on;
	private $notices = self::PTD . '_settings_errors';

	/**
	 * Start up
	 */
	public function __construct() {
		if (
			! defined( 'ADEV_AMP_GDPR_PTD' ) ||
			! defined( 'ADEV_AMP_GDPR_PATH' )
		) {
			wp_die( 'Core constants missing in  ' . basename( __FILE__, '.php' ) );
		}

		$this->on = self::PTD . '_plugin_options';

		add_action( 'admin_menu', [ $this, 'add_submenu' ] );
		add_action( 'admin_init', [ $this, 'page_init' ] );

		add_action( 'admin_notices', function () {
			settings_errors( $this->notices, true, true );
		} );

		//add setting page link for plugin list page
		add_filter( 'plugin_action_links', [ $this, 'plugin_action_links' ], 10, 2 );
	}

	//add setting page link for plugin list page
	public function plugin_action_links( $links, $plugin_name ) {
		if ( $plugin_name == plugin_basename( self::PATH . '/index.php' ) ) {
			$args    = [ 'page' => $this->on ];
			$url     = add_query_arg( $args, admin_url( 'options-general.php' ) );
			$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', self::PTD ) . '</a>';
		}

		return $links;
	}

	public function add_submenu() {
		add_submenu_page(
			'options-general.php',
			__( 'Adds GDPR compliance to AMP pages', self::PTD ),
			__( 'AMP GDPR', self::PTD ),
			'manage_options',
			$this->on,
			[ $this, 'settings_page' ]
		);
	}

	/**
	 * Options page callback
	 */
	public function settings_page() {
		// Set class property
		$this->options = get_option( $this->on );
		?>
        <div class="wrap">
            <h1><?php _e( 'Plugin settings', self::PTD ) ?></h1>
            <form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( self::PTD . '-plugin-options' );
				do_settings_sections( self::PTD . '-settings' );
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			self::PTD . '-plugin-options', // Option group
			$this->on, // Option name
			[ $this, 'sanitize' ] // Sanitize
		);

		add_settings_section(
			'root_section', // ID
			__( 'Customize GDPR compliance message', self::PTD ), // Title
			[ $this, 'print_root_section_info' ], // Callback
			self::PTD . '-settings' // Page
		);

		add_settings_field(
			'is_on',
			__( 'Show GDPR message', self::PTD ),
			[ $this, 'is_on_callback' ],
			self::PTD . '-settings',
			'root_section'
		);

		add_settings_field(
			'message_max_width',
			__( 'GDPR message max width', self::PTD ),
			[ $this, 'message_max_width_callback' ],
			self::PTD . '-settings',
			'root_section'
		);

		add_settings_field(
			'general_fields',
			__( 'Add text to show as GDPR compliance message', self::PTD ),
			[ $this, 'gdpr_text_callback' ],
			self::PTD . '-settings',
			'root_section'
		);

		add_settings_field(
			'show_policy',
			__( 'Show policy link', self::PTD ),
			[ $this, 'show_policy_callback' ],
			self::PTD . '-settings',
			'root_section'
		);

		add_settings_field(
			'policy_btn_text',
			__( 'Policy link text', self::PTD ),
			[ $this, 'policy_btn_text_callback' ],
			self::PTD . '-settings',
			'root_section'
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input  = [];
		$has_errors = false;

		if ( isset( $input['is_on'] ) ) {
			$new_input['is_on'] = true;
		}

		if ( isset( $input['message_max_width'] ) ) {
			$message_max_width              = absint( $input['message_max_width'] );
			$new_input['message_max_width'] = $message_max_width < self::MAX_WIDTH ? self::MAX_WIDTH : $message_max_width;
		}

		if ( isset( $input['gdpr_text'] ) && ! empty( trim( strip_tags( $input['gdpr_text'] ) ) ) ) {
			$new_input['gdpr_text'] = wp_kses_post( $input['gdpr_text'] );
		}

		if ( isset( $input['show_policy'] ) ) {
			$new_input['show_policy'] = true;
		}

		if ( isset( $input['policy_btn_text'] ) && ! empty( trim( strip_tags( $input['policy_btn_text'] ) ) ) ) {
			$new_input['policy_btn_text'] = trim( strip_tags( $input['policy_btn_text'] ) );
		}

		if ( ! $has_errors ) {
			add_settings_error(
				$this->notices,
				self::PTD . 'settings_updated',
				__( 'Options saved.', self::PTD ),
				'updated'
			);
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_root_section_info() {
		_e( 'This allow you to set up the GDPR compliance for AMP version of site.', self::PTD );
	}

	/**
	 * settings callback
	 */
	public function is_on_callback() {
		?>
        <section class="other-section"><?php
		$is_on   = isset( $this->options['is_on'] ) ? $this->options['is_on'] : false;
		$checked = $is_on ? 'checked' : '';
		echo '<p><label><input
                        type="checkbox"
                        name="' . $this->on . '[is_on]"
                        value="1"
                        ' . $checked . '
                        >' . __( 'Show GDPR?', self::PTD ) . '</label></p>';
		?></section><?php
	}

	/**
	 * settings callback
	 */
	public function message_max_width_callback() {
		?>
        <section class="other-section"><?php
		$message_max_width = isset( $this->options['message_max_width'] ) ? $this->options['message_max_width'] : 0;
		echo '<p><input type="number" id="message_max_width" min="' . self::MAX_WIDTH . '" name="' . $this->on . '[message_max_width]" value="' . $message_max_width . '"></p>';
		?></section><?php
	}

	public function gdpr_text_callback() {
		$content = isset( $this->options['gdpr_text'] ) ? $this->options['gdpr_text'] : '';

		echo '<section class="other-section">';
		$name = $this->on . '[gdpr_text]';
		wp_editor( $content, 'gdpr_text', [
			'wpautop'       => false,
			'media_buttons' => false,
			'teeny'         => true,
			'textarea_name' => $name,
		] );
		echo '</section>';
	}

	/**
	 * settings callback
	 */
	public function show_policy_callback() {
		?>
        <section class="other-section"><?php
		$show_policy   = isset( $this->options['show_policy'] ) ? $this->options['show_policy'] : false;
		$checked = $show_policy ? 'checked' : '';
		echo '<p><label><input
                        type="checkbox"
                        name="' . $this->on . '[show_policy]"
                        value="1"
                        ' . $checked . '
                        >' . __( 'Show policy link?', self::PTD ) . '</label></p>';
		?></section><?php
	}

	/**
	 * settings callback
	 */
	public function policy_btn_text_callback() {
		?>
        <section class="other-section"><?php
		$policy_btn_text = isset( $this->options['policy_btn_text'] ) ? $this->options['policy_btn_text'] : '';
		echo '<p><input type="text" id="policy_btn_text" name="' . $this->on . '[policy_btn_text]" value="' . $policy_btn_text . '"></p>';
		?></section><?php
	}
}