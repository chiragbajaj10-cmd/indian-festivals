<?php
/**
 * Generic Elementor widget that renders one of the theme shortcodes.
 * Loaded only when Elementor is active (see elementor.php).
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\\Elementor\\Widget_Base' ) || class_exists( 'IFS_Shortcode_Widget' ) ) {
	return;
}

class IFS_Shortcode_Widget extends \Elementor\Widget_Base {

	/**
	 * Widget configuration.
	 *
	 * @var array
	 */
	private $ifs_config = array();

	/**
	 * @param array      $data   Element data.
	 * @param array|null $args   Element args.
	 * @param array      $config Widget config: slug, title, icon, shortcode.
	 */
	public function __construct( $data = array(), $args = null, $config = array() ) {
		$this->ifs_config = wp_parse_args(
			$config,
			array(
				'slug'      => 'ifs-widget',
				'title'     => 'Indian Festivals',
				'icon'      => 'eicon-star',
				'shortcode' => 'ifs_upcoming_festivals',
			)
		);
		parent::__construct( $data, $args );
	}

	public function get_name() {
		return $this->ifs_config['slug'];
	}

	public function get_title() {
		return $this->ifs_config['title'];
	}

	public function get_icon() {
		return $this->ifs_config['icon'];
	}

	public function get_categories() {
		return array( 'indian-festivals' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ifs_settings',
			array(
				'label' => __( 'Settings', 'indian-festivals' ),
			)
		);
		$this->add_control(
			'ifs_attrs',
			array(
				'label'       => __( 'Shortcode attributes', 'indian-festivals' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'count="5"',
				'description' => __( 'Optional. Example: count="5" or slot="header" for the ad slot.', 'indian-festivals' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$attrs    = isset( $settings['ifs_attrs'] ) ? preg_replace( '/[^A-Za-z0-9_\-=" .:,]/', '', (string) $settings['ifs_attrs'] ) : '';
		// Shortcode output is escaped inside each renderer.
		echo do_shortcode( '[' . $this->ifs_config['shortcode'] . ' ' . $attrs . ']' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
