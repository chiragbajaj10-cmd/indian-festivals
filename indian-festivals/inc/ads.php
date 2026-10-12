<?php
/**
 * Ad placement manager. Every slot has code, height and a placement rule, so ads
 * can be switched on or off per page type. Filled in Appearance > Customize >
 * Theme: Ads and Tracking. Until code is added, each slot reserves its height
 * and shows a labelled placeholder, so the layout never jumps.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Every ad slot, where it appears and how tall it is.
 *
 * @return array key => array( label, default height, default placement )
 */
function ifs_ad_slots() {
	return array(
		'header'      => array( 'header' => __( 'Header banner', 'indian-festivals' ), 'height' => 90, 'placement' => 'all' ),
		'top'         => array( 'header' => __( 'Top leaderboard', 'indian-festivals' ), 'height' => 90, 'placement' => 'all' ),
		'in_content'  => array( 'header' => __( 'In content', 'indian-festivals' ), 'height' => 250, 'placement' => 'all' ),
		'mid'         => array( 'header' => __( 'Mid content', 'indian-festivals' ), 'height' => 90, 'placement' => 'all' ),
		'rail_left'   => array( 'header' => __( 'Left skyscraper', 'indian-festivals' ), 'height' => 600, 'placement' => 'all' ),
		'rail_right'  => array( 'header' => __( 'Right skyscraper', 'indian-festivals' ), 'height' => 600, 'placement' => 'all' ),
		'sidebar'     => array( 'header' => __( 'Sidebar', 'indian-festivals' ), 'height' => 250, 'placement' => 'single' ),
		'below_title' => array( 'header' => __( 'Below the page title', 'indian-festivals' ), 'height' => 90, 'placement' => 'single' ),
		'footer'      => array( 'header' => __( 'Above footer', 'indian-festivals' ), 'height' => 90, 'placement' => 'all' ),
		'between'     => array( 'header' => __( 'Between archive cards', 'indian-festivals' ), 'height' => 280, 'placement' => 'archive' ),
	);
}

/**
 * Where a slot is allowed to appear.
 *
 * @param string $slot      Slot key.
 * @param string $placement Placement value from the Customizer.
 * @return bool
 */
function ifs_ad_allowed( $slot, $placement ) {
	$all = array( 'all', '' );
	if ( in_array( $placement, $all, true ) ) {
		return true;
	}
	$context = is_front_page() ? 'home'
		: ( is_singular() ? 'single' : ( is_archive() || is_search() ? 'archive' : 'home' ) );
	$map = array(
		'home'    => array( 'home', 'all' ),
		'single'  => array( 'single', 'all', 'festival' ),
		'archive' => array( 'archive', 'all' ),
	);
	$allowed = $map[ $context ] ?? array( 'all' );
	if ( 'festival' === $placement ) {
		return is_singular( 'festival' );
	}
	return in_array( $placement, $allowed, true );
}

/**
 * Render one ad slot with its reserved height.
 *
 * @param string $slot  Slot key from ifs_ad_slots().
 * @param string $extra Extra classes.
 */
function ifs_render_ad( $slot, $extra = '' ) {
	$slots = ifs_ad_slots();
	if ( ! isset( $slots[ $slot ] ) ) {
		return;
	}
	$code      = trim( (string) ifs_opt( 'ad_' . $slot, '' ) );
	$placement = (string) ifs_opt( 'ad_place_' . $slot, $slots[ $slot ]['placement'] );
	$height    = (int) ifs_opt( 'ad_height_' . $slot, $slots[ $slot ]['height'] );

	if ( ! ifs_ad_allowed( $slot, $placement ) ) {
		return;
	}
	if ( '' === $code && ! current_user_can( 'manage_options' ) ) {
		// Do not reserve empty space for regular visitors.
		return;
	}

	$style = 'min-height:' . max( 40, $height ) . 'px;';
	printf(
		'<aside class="ad-slot ad-slot--%1$s %2$s" data-ad-slot="%1$s" style="%3$s" aria-label="%4$s">',
		esc_attr( $slot ),
		esc_attr( $extra ),
		esc_attr( $style ),
		esc_attr__( 'Advertisement', 'indian-festivals' )
	);

	if ( '' !== $code ) {
		echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin-supplied ad code.
	} else {
		printf(
			'<span class="ad-slot-label"><span class="ad-slot-word">%1$s</span><span class="ad-slot-size">%2$s × %3$d</span></span>',
			esc_html__( 'Advertisement', 'indian-festivals' ),
			esc_html( $slot ),
			$height
		);
	}
	echo '</aside>';
}

/**
 * Register the placement and height settings for each slot.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function ifs_ad_customize( $wp_customize ) {
	foreach ( ifs_ad_slots() as $key => $slot ) {
		$wp_customize->add_setting(
			'ifs_ad_place_' . $key,
			array(
				'default'           => $slot['placement'],
				'sanitize_callback' => 'sanitize_key',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'ifs_ad_place_' . $key,
			array(
				'label'   => sprintf( __( '%s — show on', 'indian-festivals' ), $slot['header'] ),
				'section' => 'ifs_ads',
				'type'    => 'select',
				'choices' => array(
					'all'     => __( 'Every page', 'indian-festivals' ),
					'home'    => __( 'Home page only', 'indian-festivals' ),
					'single'  => __( 'Single posts and pages', 'indian-festivals' ),
					'festival'=> __( 'Festival pages only', 'indian-festivals' ),
					'archive' => __( 'Archives and search only', 'indian-festivals' ),
					'none'    => __( 'Nowhere (disabled)', 'indian-festivals' ),
				),
			)
		);

		$wp_customize->add_setting(
			'ifs_ad_height_' . $key,
			array(
				'default'           => $slot['height'],
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'ifs_ad_height_' . $key,
			array(
				'label'       => sprintf( __( '%s — reserved height (px)', 'indian-festivals' ), $slot['header'] ),
				'section'     => 'ifs_ads',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 40,
					'max'  => 800,
					'step' => 10,
				),
			)
		);
	}
}
add_action( 'customize_register', 'ifs_ad_customize' );

/**
 * AdSense / analytics helper: inject the loader once if a publisher ID is set.
 */
function ifs_adsense_loader() {
	$pub = trim( (string) ifs_opt( 'adsense_publisher_id', '' ) );
	if ( ! $pub || ! preg_match( '/^ca-pub-\d{6,}$/', $pub ) ) {
		return;
	}
	echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr( $pub ) . '" crossorigin="anonymous"></script>' . "\n";
}
add_action( 'wp_head', 'ifs_adsense_loader', 2 );
