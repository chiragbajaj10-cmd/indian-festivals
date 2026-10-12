<?php
/**
 * Elementor (free) integration. The theme still works without Elementor.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Elementor is loaded.
 *
 * @return bool
 */
function ifs_elementor_active() {
	return did_action( 'elementor/loaded' ) > 0 || class_exists( '\\Elementor\\Plugin' );
}

/**
 * Admin notice with Install or Activate buttons when Elementor is missing.
 */
function ifs_elementor_notice() {
	if ( ifs_elementor_active() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	$plugin    = 'elementor/elementor.php';
	$installed = file_exists( WP_PLUGIN_DIR . '/' . $plugin );

	if ( $installed ) {
		$url   = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin ), 'activate-plugin_' . $plugin );
		$label = __( 'Activate Elementor', 'indian-festivals' );
	} else {
		$url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		$label = __( 'Install Elementor', 'indian-festivals' );
	}

	echo '<div class="notice notice-warning"><p><strong>Indian Festivals:</strong> ';
	esc_html_e( 'Install and activate Elementor (free) to edit the Home, About and Contact pages visually. The theme works safely without it.', 'indian-festivals' );
	echo '</p><p><a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></p></div>';
}
add_action( 'admin_notices', 'ifs_elementor_notice' );

/**
 * Elementor widget category.
 *
 * @param \Elementor\Elements_Manager $manager Manager.
 */
function ifs_elementor_categories( $manager ) {
	$manager->add_category(
		'indian-festivals',
		array(
			'title' => __( 'Indian Festivals', 'indian-festivals' ),
			'icon'  => 'eicon-favorite',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'ifs_elementor_categories' );

/**
 * Widget definitions: slug, title, icon, shortcode.
 *
 * @return array
 */
function ifs_elementor_widget_list() {
	return array(
		array( 'ifs-upcoming-festivals', __( 'Upcoming Festivals', 'indian-festivals' ), 'eicon-calendar', 'ifs_upcoming_festivals' ),
		array( 'ifs-panchang-today', __( 'Panchang Today', 'indian-festivals' ), 'eicon-clock-o', 'ifs_panchang_today' ),
		array( 'ifs-vrat-cards', __( 'Vrat Cards', 'indian-festivals' ), 'eicon-heart', 'ifs_vrat_cards' ),
		array( 'ifs-deity-bands', __( 'Deity Bands', 'indian-festivals' ), 'eicon-person', 'ifs_deity_bands' ),
		array( 'ifs-festival-journey', __( 'Festival Journey', 'indian-festivals' ), 'eicon-navigation-horizontal', 'ifs_journey' ),
		array( 'ifs-countdown', __( 'Countdown', 'indian-festivals' ), 'eicon-countdown', 'ifs_countdown' ),
		array( 'ifs-wish-generator', __( 'Wish Generator', 'indian-festivals' ), 'eicon-email-field', 'ifs_wish_generator' ),
		array( 'ifs-info-strip', __( 'Info Strip', 'indian-festivals' ), 'eicon-info-circle-o', 'ifs_info_strip' ),
		array( 'ifs-ad-slot', __( 'Ad Slot', 'indian-festivals' ), 'eicon-banner', 'ifs_ad_slot' ),
		array( 'ifs-tools-grid', __( 'Tools Grid', 'indian-festivals' ), 'eicon-apps', 'ifs_tools_grid' ),
	);
}

/**
 * Register the widgets once Elementor is ready.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
 */
function ifs_elementor_register_widgets( $widgets_manager ) {
	if ( ! class_exists( '\\Elementor\\Widget_Base' ) ) {
		return;
	}
	require_once IFS_DIR . '/inc/elementor-widgets.php';
	foreach ( ifs_elementor_widget_list() as $item ) {
		$widgets_manager->register(
			new IFS_Shortcode_Widget(
				array(),
				null,
				array(
					'slug'      => $item[0],
					'title'     => $item[1],
					'icon'      => $item[2],
					'shortcode' => $item[3],
				)
			)
		);
	}
}
add_action( 'elementor/widgets/register', 'ifs_elementor_register_widgets' );
