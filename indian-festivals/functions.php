<?php
/**
 * Indian Festivals theme bootstrap.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IFS_VERSION', '2.4.0' );
define( 'IFS_DIR', get_template_directory() );
define( 'IFS_URI', get_template_directory_uri() );

require_once IFS_DIR . '/inc/compat.php';

// Do not load feature modules on an unsupported host. This prevents a generic
// critical-error screen and replaces it with a clear compatibility notice.
if ( ! ifs_host_is_compatible() ) {
	add_action( 'admin_notices', 'ifs_compatibility_notice' );
	return;
}

require_once IFS_DIR . '/inc/post-types.php';
require_once IFS_DIR . '/inc/customizer.php';
require_once IFS_DIR . '/inc/meta-boxes.php';
require_once IFS_DIR . '/inc/panchang.php';
require_once IFS_DIR . '/inc/sections.php';
require_once IFS_DIR . '/inc/shortcodes.php';
require_once IFS_DIR . '/inc/seo.php';
require_once IFS_DIR . '/inc/seo-tools.php';
require_once IFS_DIR . '/inc/ads.php';
require_once IFS_DIR . '/inc/theme-options.php';
require_once IFS_DIR . '/inc/post-views.php';
require_once IFS_DIR . '/inc/share.php';
require_once IFS_DIR . '/inc/csv.php';
require_once IFS_DIR . '/inc/elementor.php';

/**
 * Theme setup.
 */
function ifs_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 360,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	register_nav_menus(
		array(
			'primary-left'  => __( 'Primary Left', 'indian-festivals' ),
			'primary-right' => __( 'Primary Right', 'indian-festivals' ),
			'footer'        => __( 'Footer', 'indian-festivals' ),
			'mobile'        => __( 'Mobile', 'indian-festivals' ),
		)
	);
	add_image_size( 'ifs-card', 600, 420, true );
	add_image_size( 'ifs-wide', 1200, 500, true );
	load_theme_textdomain( 'indian-festivals', IFS_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'ifs_setup' );

/**
 * Widget areas: sidebar and four footer columns.
 */
function ifs_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'indian-festivals' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<section id="%1$s" class="widget paper %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number */
				'name'          => sprintf( __( 'Footer Column %d', 'indian-festivals' ), $i ),
				'id'            => 'footer-' . $i,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'ifs_widgets_init' );

/**
 * Build the Google Fonts URL from the chosen heading and body fonts.
 */
function ifs_font_url() {
	if ( ifs_opt( 'self_host_fonts', false ) ) {
		return '';
	}
	$families = array_unique( array( ifs_opt( 'heading_font', 'Cinzel' ), ifs_opt( 'body_font', 'Crimson Text' ), 'Noto Sans Devanagari' ) );
	$parts    = array();
	foreach ( $families as $family ) {
		$parts[] = 'family=' . rawurlencode( $family ) . ':wght@400;600;700';
	}
	return 'https://fonts.googleapis.com/css2?' . implode( '&', $parts ) . '&display=swap';
}

/**
 * Enqueue styles and scripts.
 */
function ifs_enqueue_assets() {
	$font_url = ifs_font_url();
	$deps     = array();
	if ( $font_url ) {
		wp_enqueue_style( 'ifs-google-fonts', $font_url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		$deps[] = 'ifs-google-fonts';
	}
	wp_enqueue_style( 'ifs-style', get_stylesheet_uri(), $deps, IFS_VERSION );
	wp_add_inline_style( 'ifs-style', ifs_dynamic_css() );
	wp_enqueue_style( 'ifs-paper-motion', IFS_URI . '/assets/css/paper-motion.css', array( 'ifs-style' ), IFS_VERSION );
	wp_add_inline_style( 'ifs-paper-motion', ':root{--paper-grain-image:url("' . esc_url( IFS_URI . '/assets/images/paper-fibers.svg' ) . '");}' );
	wp_enqueue_script( 'ifs-theme', IFS_URI . '/assets/js/theme.js', array(), IFS_VERSION, true );
	wp_enqueue_script( 'ifs-scene-motion', IFS_URI . '/assets/js/scene-motion.js', array(), IFS_VERSION, true );
	wp_enqueue_style( 'ifs-torn-paper', IFS_URI . '/assets/css/torn-paper.css', array( 'ifs-paper-motion' ), IFS_VERSION );
	wp_enqueue_style( 'ifs-drik-theme', IFS_URI . '/assets/css/drik-theme.css', array( 'ifs-torn-paper' ), IFS_VERSION );
	wp_enqueue_script( 'ifs-live-context', IFS_URI . '/assets/js/live-context.js', array(), IFS_VERSION, true );
	if ( ifs_opt( 'adblock_on', false ) ) {
		wp_enqueue_script( 'ifs-ad-blocker', IFS_URI . '/assets/js/ad-blocker.js', array(), IFS_VERSION, true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	if ( ifs_opt( 'perf_remove_emoji', true ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'ifs_enqueue_assets' );

/**
 * Preload the first font file for faster first paint.
 */
function ifs_preload_fonts() {
	if ( ifs_opt( 'perf_preload_fonts', true ) && ! ifs_opt( 'self_host_fonts', false ) ) {
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	}
}
add_action( 'wp_head', 'ifs_preload_fonts', 1 );


/**
 * Output CSS variables from the Customizer (colors, type, layout).
 *
 * @return string
 */
function ifs_dynamic_css() {
	$p = ifs_palette();

	$css  = ':root{';
	$css .= '--ifs-primary:' . $p['primary'] . ';';
	$css .= '--ifs-secondary:' . $p['secondary'] . ';';
	$css .= '--ifs-accent:' . $p['accent'] . ';';
	$css .= '--ifs-background:' . $p['background'] . ';';
	$css .= '--ifs-text:' . $p['text'] . ';';
	$css .= '--ifs-link:' . $p['link'] . ';';
	$css .= '--ifs-header:' . $p['header'] . ';';
	$css .= '--ifs-footer:' . $p['footer'] . ';';
	$css .= '--ifs-button:' . $p['button'] . ';';
	$css .= '--ifs-heading:"' . ifs_opt( 'heading_font', 'Cinzel' ) . '",Georgia,serif;';
	$css .= '--ifs-body:"' . ifs_opt( 'body_font', 'Crimson Text' ) . '",Georgia,serif;';
	$css .= '--ifs-fs:' . (int) ifs_opt( 'base_font_size', 17 ) . 'px;';
	$css .= '--ifs-lh:' . (float) ifs_opt( 'line_height', 1.5 ) . ';';
	$css .= '--ifs-container:' . (int) ifs_opt( 'container_width', 1280 ) . 'px;';
	$css .= '--ifs-radius:' . (int) ifs_opt( 'radius', 6 ) . 'px;';
	$css .= '--ifs-gap:' . (int) ifs_opt( 'section_spacing', 22 ) . 'px;';
	$css .= '--ifs-archive-cols:' . (int) ifs_opt( 'archive_columns', 4 ) . ';';
	$css .= '--ad-h:' . (int) ifs_opt( 'ad_height', 250 ) . 'px;';
	$css .= '}';

	$css .= 'body{font-weight:' . (int) ifs_opt( 'body_weight', 400 ) . ';}';
	$css .= '.logo-size img,.brand img{max-height:' . (int) ifs_opt( 'logo_size', 120 ) . 'px;}';

	$custom = (string) ifs_opt( 'custom_css', '' );
	$custom = str_ireplace( '</style', '', $custom );

	return $css . "\n" . ifs_theme_option_css() . "\n" . $custom;
}

/**
 * Body classes driven by Customizer toggles.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function ifs_body_classes( $classes ) {
	$classes[] = 'ifs-layout-' . sanitize_html_class( ifs_opt( 'header_layout', 'centered' ) );
	$classes[] = 'ifs-cols-' . (int) ifs_opt( 'archive_columns', 4 );
	$classes[] = 'ifs-card-' . sanitize_html_class( ifs_opt( 'card_style', 'parchment' ) );
	$classes[] = 'ifs-border-' . sanitize_html_class( ifs_opt( 'border_style', 'double_gold' ) );
	if ( ifs_opt( 'deco_wood', true ) ) {
		$classes[] = 'ifs-deco-wood';
	}
	if ( ifs_opt( 'deco_mandala', true ) ) {
		$classes[] = 'ifs-deco-mandala';
	}
	if ( ifs_opt( 'deco_torn', true ) ) {
		$classes[] = 'ifs-torn';
	}
	if ( ifs_opt( 'deco_grain', true ) ) {
		$classes[] = 'ifs-grain';
	} else {
		$classes[] = 'ifs-no-grain';
	}
	if ( ifs_opt( 'deco_toran', true ) ) {
		$classes[] = 'ifs-toran';
	}
	if ( ifs_opt( 'sticky_on', true ) ) {
		$classes[] = 'ifs-sticky';
	}
	$classes[] = 'ifs-menu-' . sanitize_html_class( ifs_opt( 'menu_hover_style', 'underline' ) );
	return $classes;
}
add_filter( 'body_class', 'ifs_body_classes' );

/**
 * Archive query tweaks: posts per page for every archive.
 *
 * @param WP_Query $query Main query.
 */
function ifs_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_archive() || $query->is_search() ) {
		$query->set( 'posts_per_page', (int) ifs_opt( 'posts_per_page', 12 ) );
	}
	if ( $query->is_post_type_archive( 'festival' ) ) {
		$query->set( 'meta_key', '_ifs_date' );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'ifs_archive_query' );

/**
 * Flush rewrite rules when the theme is activated.
 */
function ifs_on_switch() {
	ifs_register_content_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'ifs_on_switch' );

/**
 * Human readable date in the Customizer date format.
 *
 * @param string $iso Y-m-d string.
 * @return string
 */
function ifs_format_date( $iso ) {
	if ( ! $iso ) {
		return '';
	}
	$ts = strtotime( $iso . ' 00:00:00 UTC' );
	return $ts ? wp_date( ifs_opt( 'date_format', 'j F Y' ), $ts, new DateTimeZone( 'UTC' ) ) : '';
}

/**
 * Fetch a single meta value for a post.
 *
 * @param string $key   Field key without prefix.
 * @param int    $id    Post ID (defaults to current post).
 * @return string
 */
function ifs_meta( $key, $id = 0 ) {
	$id = $id ? $id : get_the_ID();
	return (string) get_post_meta( $id, '_ifs_' . $key, true );
}

/**
 * Today's date in the site timezone (Y-m-d).
 *
 * @return string
 */
function ifs_today() {
	$tz = new DateTimeZone( ifs_opt( 'default_timezone', 'Asia/Kolkata' ) );
	return ( new DateTime( 'now', $tz ) )->format( 'Y-m-d' );
}

/**
 * Vikram Samvat year for a Gregorian date (approximate, switches at Chaitra).
 *
 * @param int $year  Gregorian year.
 * @param int $month Gregorian month (1-12).
 * @return int
 */
function ifs_vikram_samvat( $year, $month ) {
	return $month >= 4 ? $year + 57 : $year + 56;
}
