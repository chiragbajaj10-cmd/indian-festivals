<?php
/**
 * Theme Options: a dedicated admin page (Indian Festivals > Theme Options) with tabs
 * for Post Views, Archives, Share Buttons, Advertisement, Ad Blocker Detector,
 * Background, Styling and Integrations.
 *
 * Values are stored as theme mods, the same store the Customizer uses, so ifs_opt()
 * reads them everywhere without any extra code.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tabs shown in the left-hand menu of the Theme Options page.
 *
 * @return array slug => label
 */
function ifs_theme_option_tabs() {
	return array(
		'post-views'    => __( 'Post Views', 'indian-festivals' ),
		'archives'      => __( 'Archives', 'indian-festivals' ),
		'share'         => __( 'Share Buttons', 'indian-festivals' ),
		'advertisement' => __( 'Advertisement', 'indian-festivals' ),
		'ad-blocker'    => __( 'Ad Blocker Detector', 'indian-festivals' ),
		'background'    => __( 'Background', 'indian-festivals' ),
		'styling'       => __( 'Styling', 'indian-festivals' ),
		'integrations'  => __( 'Integrations', 'indian-festivals' ),
	);
}

/**
 * Every field on every tab.
 *
 * Each field is: label, type, default, and optional extra.
 *   number  => extra = array( min, max )
 *   select  => extra = array( value => label )
 *
 * @return array tab => array( key => array( label, type, default, extra? ) )
 */
function ifs_theme_option_fields() {
	return array(
		'post-views' => array(
			'post_views_on'      => array( __( 'Count post views', 'indian-festivals' ), 'checkbox', true ),
			'post_views_icon'    => array( __( 'Views icon', 'indian-festivals' ), 'select', 'eye', array( 'eye' => __( 'Eye', 'indian-festivals' ), 'fire' => __( 'Fire', 'indian-festivals' ) ) ),
			'post_views_start'   => array( __( 'Starting views for posts with no count yet', 'indian-festivals' ), 'number', 10, array( 0, 1000000 ) ),
			'post_views_colored' => array( __( 'Colour the number by popularity', 'indian-festivals' ), 'checkbox', true ),
			'post_views_warm'    => array( __( 'Warm from (views)', 'indian-festivals' ), 'number', 100, array( 0, 10000000 ) ),
			'post_views_hot'     => array( __( 'Hot from (views)', 'indian-festivals' ), 'number', 500, array( 0, 10000000 ) ),
			'post_views_vhot'    => array( __( 'Very hot from (views)', 'indian-festivals' ), 'number', 1000, array( 0, 10000000 ) ),
		),
		'archives'   => array(
			'archive_excerpt'        => array( __( 'Show excerpt on cards', 'indian-festivals' ), 'checkbox', true ),
			'archive_excerpt_length' => array( __( 'Excerpt length (words)', 'indian-festivals' ), 'number', 16, array( 4, 60 ) ),
			'archive_readmore'       => array( __( 'Show a read more link', 'indian-festivals' ), 'checkbox', true ),
			'archive_readmore_text'  => array( __( 'Read more text', 'indian-festivals' ), 'text', 'Read more' ),
			'archive_show_views'     => array( __( 'Show view counts on cards', 'indian-festivals' ), 'checkbox', true ),
		),
		'share'      => array(
			'share_position' => array( __( 'Button position', 'indian-festivals' ), 'select', 'default', array( 'default' => __( 'Left', 'indian-festivals' ), 'centre' => __( 'Centre', 'indian-festivals' ), 'inverted' => __( 'Right', 'indian-festivals' ) ) ),
			'share_title'    => array( __( 'Show the "Share" heading', 'indian-festivals' ), 'checkbox', true ),
			'share_x_user'   => array( __( 'X (Twitter) username, optional', 'indian-festivals' ), 'text', '' ),
			'share_x'        => array( __( 'X', 'indian-festivals' ), 'checkbox', true ),
			'share_facebook' => array( __( 'Facebook', 'indian-festivals' ), 'checkbox', true ),
			'share_linkedin' => array( __( 'LinkedIn', 'indian-festivals' ), 'checkbox', true ),
			'share_pinterest' => array( __( 'Pinterest', 'indian-festivals' ), 'checkbox', true ),
			'share_reddit'   => array( __( 'Reddit', 'indian-festivals' ), 'checkbox', false ),
			'share_telegram' => array( __( 'Telegram', 'indian-festivals' ), 'checkbox', true ),
			'share_whatsapp' => array( __( 'WhatsApp', 'indian-festivals' ), 'checkbox', true ),
			'share_pocket'   => array( __( 'Pocket', 'indian-festivals' ), 'checkbox', false ),
			'share_email'    => array( __( 'Email', 'indian-festivals' ), 'checkbox', true ),
		),
		'advertisement' => array(
			'ad_rail_enabled'   => array( __( 'Show left and right skyscraper ads', 'indian-festivals' ), 'checkbox', true ),
			'ad_rail_min_width' => array( __( 'Show skyscrapers only on screens at least (px) wide', 'indian-festivals' ), 'number', 1100, array( 800, 1920 ) ),
			'ad_rail_offset'    => array( __( 'Move skyscrapers up or down (px from centre)', 'indian-festivals' ), 'number', 0, array( -600, 600 ) ),
		),
		'ad-blocker' => array(
			'adblock_on'      => array( __( 'Detect ad blockers and show a message', 'indian-festivals' ), 'checkbox', false ),
			'adblock_title'   => array( __( 'Title', 'indian-festivals' ), 'text', 'Adblock Detected' ),
			'adblock_message' => array( __( 'Message', 'indian-festivals' ), 'textarea', 'Ads help keep this site free. Please turn off your ad blocker for this site, then reload the page.' ),
			'adblock_delay'   => array( __( 'Seconds before the message appears', 'indian-festivals' ), 'number', 10, array( 0, 120 ) ),
			'adblock_dismiss' => array( __( 'Visitors can close the message', 'indian-festivals' ), 'checkbox', true ),
			'adblock_bg'      => array( __( 'Box background colour (#rrggbb)', 'indian-festivals' ), 'color', '#1a1208' ),
		),
		'background' => array(
			'bg_color'      => array( __( 'Background colour (#rrggbb)', 'indian-festivals' ), 'color', '' ),
			'bg_color2'     => array( __( 'Second colour for a gradient (#rrggbb)', 'indian-festivals' ), 'color', '' ),
			'bg_image_type' => array( __( 'Background image', 'indian-festivals' ), 'select', 'none', array( 'none' => __( 'None', 'indian-festivals' ), 'image' => __( 'Image', 'indian-festivals' ) ) ),
			'bg_image'      => array( __( 'Background image URL', 'indian-festivals' ), 'url', '' ),
			'bg_dimmer'     => array( __( 'Dim the background', 'indian-festivals' ), 'checkbox', false ),
		),
		'styling'    => array(
			'dark_skin'         => array( __( 'Dark skin', 'indian-festivals' ), 'checkbox', false ),
			'custom_body_classes' => array( __( 'Extra body classes (space separated)', 'indian-festivals' ), 'text', '' ),
		),
		'integrations' => array(
			'instagram_token'  => array( __( 'Instagram access token (stored only)', 'indian-festivals' ), 'text', '' ),
			'key_google_maps'  => array( __( 'Google Maps API key', 'indian-festivals' ), 'text', '' ),
			'key_youtube'      => array( __( 'YouTube API key', 'indian-festivals' ), 'text', '' ),
			'key_openweather'  => array( __( 'OpenWeather API key', 'indian-festivals' ), 'text', '' ),
			'key_cse'          => array( __( 'Google Custom Search Engine ID', 'indian-festivals' ), 'text', '' ),
		),
	);
}

/**
 * Default value for every Theme Options field, merged into ifs_defaults().
 *
 * @return array
 */
function ifs_theme_option_defaults() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$cache = array();
	foreach ( ifs_theme_option_fields() as $fields ) {
		foreach ( $fields as $key => $field ) {
			$cache[ $key ] = $field[2];
		}
	}
	return $cache;
}

/**
 * Sanitize one submitted value by its field type.
 *
 * @param string $type Field type.
 * @param mixed  $raw  Submitted value.
 * @param array  $field Field definition.
 * @return mixed
 */
function ifs_sanitize_theme_option( $type, $raw, $field ) {
	switch ( $type ) {
		case 'checkbox':
			return (bool) $raw;
		case 'number':
			$n = (int) $raw;
			return max( $field[3][0], min( $field[3][1], $n ) );
		case 'select':
			$v = sanitize_key( (string) $raw );
			return array_key_exists( $v, $field[3] ) ? $v : $field[2];
		case 'color':
			$c = sanitize_hex_color( (string) $raw );
			return $c ? $c : '';
		case 'url':
			return esc_url_raw( (string) $raw );
		case 'textarea':
			return sanitize_textarea_field( (string) $raw );
		default:
			return sanitize_text_field( (string) $raw );
	}
}

/**
 * Save handler for the Theme Options form. Nonce and capability protected.
 */
function ifs_handle_save_theme_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to change these settings.', 'indian-festivals' ) );
	}
	check_admin_referer( 'ifs_theme_options', 'ifs_to_nonce' );

	$tabs   = array_keys( ifs_theme_option_tabs() );
	$fields = ifs_theme_option_fields();
	$tab    = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : '';
	if ( ! isset( $fields[ $tab ] ) ) {
		$tab = $tabs[0];
	}
	$input = isset( $_POST['ifs'] ) && is_array( $_POST['ifs'] ) ? wp_unslash( $_POST['ifs'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	foreach ( $fields[ $tab ] as $key => $field ) {
		$type = $field[1];
		if ( 'checkbox' === $type ) {
			// Unticked boxes are not posted at all, so absence means off.
			$value = ! empty( $input[ $key ] );
		} else {
			$value = isset( $input[ $key ] ) ? $input[ $key ] : '';
		}
		set_theme_mod( 'ifs_' . $key, ifs_sanitize_theme_option( $type, $value, $field ) );
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'  => 'ifs-theme-options',
				'tab'   => $tab,
				'saved' => '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_ifs_save_theme_options', 'ifs_handle_save_theme_options' );

/**
 * Left-hand menu item.
 */
function ifs_theme_options_menu() {
	add_menu_page(
		__( 'Theme Options', 'indian-festivals' ),
		__( 'Theme Options', 'indian-festivals' ),
		'manage_options',
		'ifs-theme-options',
		'ifs_theme_options_page',
		'dashicons-admin-generic',
		30
	);
}
add_action( 'admin_menu', 'ifs_theme_options_menu' );

/**
 * Render one input for a Theme Options field.
 *
 * @param string $key   Field key.
 * @param array  $field Field definition.
 */
function ifs_theme_option_input( $key, $field ) {
	$value = ifs_opt( $key, $field[2] );
	$id    = 'ifs-' . $key;
	$name  = 'ifs[' . $key . ']';
	$type  = $field[1];

	switch ( $type ) {
		case 'checkbox':
			printf(
				'<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( (bool) $value, true, false ),
				esc_html__( 'On', 'indian-festivals' )
			);
			break;

		case 'number':
			printf(
				'<input type="number" id="%1$s" name="%2$s" value="%3$s" min="%4$d" max="%5$d" class="small-text">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value ),
				(int) $field[3][0],
				(int) $field[3][1]
			);
			break;

		case 'select':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $field[3] as $option_value => $option_label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $option_value ),
					selected( (string) $value, (string) $option_value, false ),
					esc_html( $option_label )
				);
			}
			echo '</select>';
			break;

		case 'textarea':
			printf(
				'<textarea id="%1$s" name="%2$s" rows="3" class="large-text">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( (string) $value )
			);
			break;

		case 'url':
			printf(
				'<input type="url" id="%1$s" name="%2$s" value="%3$s" class="regular-text">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_url( (string) $value )
			);
			break;

		case 'color':
			printf(
				'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="regular-text" placeholder="#rrggbb" pattern="#?[0-9a-fA-F]{6}">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value )
			);
			break;

		default:
			printf(
				'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="regular-text">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value )
			);
	}
}

/**
 * Render the Theme Options page.
 */
function ifs_theme_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$tabs   = ifs_theme_option_tabs();
	$fields = ifs_theme_option_fields();
	$tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'post-views'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'post-views';
	}
	?>
	<div class="wrap ifs-to">
		<h1><?php esc_html_e( 'Indian Festivals Theme Options', 'indian-festivals' ); ?></h1>
		<?php if ( isset( $_GET['saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'indian-festivals' ); ?></p></div>
		<?php endif; ?>

		<div class="ifs-to-layout">
			<nav class="ifs-to-nav" aria-label="<?php esc_attr_e( 'Theme options sections', 'indian-festivals' ); ?>">
				<?php foreach ( $tabs as $slug => $label ) : ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'ifs-theme-options', 'tab' => $slug ), admin_url( 'admin.php' ) ) ); ?>" class="<?php echo $slug === $tab ? 'is-active' : ''; ?>"<?php echo $slug === $tab ? ' aria-current="page"' : ''; ?>>
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ifs-to-form">
				<input type="hidden" name="action" value="ifs_save_theme_options">
				<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>">
				<?php wp_nonce_field( 'ifs_theme_options', 'ifs_to_nonce' ); ?>

				<h2><?php echo esc_html( $tabs[ $tab ] ); ?></h2>
				<table class="form-table" role="presentation">
					<tbody>
						<?php foreach ( $fields[ $tab ] as $key => $field ) : ?>
							<tr>
								<th scope="row"><label for="ifs-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
								<td><?php ifs_theme_option_input( $key, $field ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php if ( 'ad-blocker' === $tab ) : ?>
					<p class="description"><?php esc_html_e( 'Google AdSense policy discourages blocking visitors until they disable an ad blocker. Keep "Visitors can close the message" switched on.', 'indian-festivals' ); ?></p>
				<?php endif; ?>
				<?php if ( 'advertisement' === $tab ) : ?>
					<p class="description"><?php esc_html_e( 'Ad code for each slot is set under Appearance > Customize > Theme: Ads and Tracking.', 'indian-festivals' ); ?></p>
				<?php endif; ?>
				<?php if ( 'integrations' === $tab ) : ?>
					<p class="description"><?php esc_html_e( 'Keys are saved for future features. Instagram connection needs a Meta app and is not automated in this version.', 'indian-festivals' ); ?></p>
				<?php endif; ?>

				<?php submit_button( __( 'Save changes', 'indian-festivals' ) ); ?>
			</form>
		</div>
	</div>
	<?php
}

/**
 * Minimal admin layout for the Theme Options page.
 */
function ifs_theme_options_admin_css() {
	$screen = get_current_screen();
	if ( ! $screen || 'toplevel_page_ifs-theme-options' !== $screen->id ) {
		return;
	}
	echo '<style>.ifs-to-layout{display:flex;gap:28px;align-items:flex-start;margin-top:16px}.ifs-to-nav{display:flex;flex-direction:column;min-width:200px;background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:6px 0}.ifs-to-nav a{padding:9px 16px;text-decoration:none;color:#1d2327;border-left:3px solid transparent}.ifs-to-nav a:hover{background:#f6f7f7}.ifs-to-nav a.is-active{background:#2271b1;color:#fff;border-left-color:#135e96}.ifs-to-form{flex:1;background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:8px 22px 16px}@media (max-width:782px){.ifs-to-layout{flex-direction:column}.ifs-to-nav{width:100%;flex-direction:row;flex-wrap:wrap}}</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'admin_head', 'ifs_theme_options_admin_css' );

/**
 * CSS generated from Theme Options (ad rails, post-view colours, background,
 * dark skin, ad-blocker box). Appended to the theme's inline styles.
 *
 * @return string
 */
function ifs_theme_option_css() {
	$css = '';

	// Ad rails: hide below the chosen width and move up or down from centre.
	$min    = max( 800, (int) ifs_opt( 'ad_rail_min_width', 1100 ) );
	$offset = (int) ifs_opt( 'ad_rail_offset', 0 );
	if ( ! ifs_opt( 'ad_rail_enabled', true ) ) {
		$css .= '.wood-canvas > .ad-rail{display:none!important}';
	}
	$css .= '@media (max-width:' . ( $min - 1 ) . 'px){.wood-canvas > .ad-rail{display:none!important}}';
	$css .= '.wood-canvas > .ad-rail{top:calc(50% + ' . $offset . 'px)}';

	// Post view colours.
	$css .= '.ifs-views.is-warm{color:#b45309}.ifs-views.is-hot{color:#dc2626;font-weight:700}.ifs-views.is-very-hot{color:#b91c1c;font-weight:800}';

	// Background colour / gradient / image.
	$c1 = (string) ifs_opt( 'bg_color', '' );
	$c2 = (string) ifs_opt( 'bg_color2', '' );
	if ( $c1 && $c2 ) {
		$css .= 'body{background:linear-gradient(180deg,' . $c1 . ',' . $c2 . ')!important}';
	} elseif ( $c1 ) {
		$css .= 'body{background-color:' . $c1 . '!important}';
	}
	$image = (string) ifs_opt( 'bg_image', '' );
	if ( 'image' === ifs_opt( 'bg_image_type', 'none' ) && $image ) {
		$css .= 'body{background-image:url("' . esc_url_raw( $image ) . '")!important;background-size:cover!important;background-attachment:fixed!important}';
	}
	if ( ifs_opt( 'bg_dimmer', false ) ) {
		$css .= 'body.ifs-bg-dimmer:before{content:"";display:block!important;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:0;pointer-events:none}';
	}

	// Basic dark skin.
	$css .= 'body.ifs-dark{background-color:#120b06!important;background-image:none!important;color:#f3e4c4}'
		. 'body.ifs-dark .parchment-sheet,body.ifs-dark .archive-card,body.ifs-dark .dp-article,body.ifs-dark .pinned-note,body.ifs-dark .interior-article,body.ifs-dark .interior-header{background:#2a1c12!important;color:#f3e4c4;border-color:#5a4026!important}'
		. 'body.ifs-dark h1,body.ifs-dark h2,body.ifs-dark h3,body.ifs-dark .fest-card-title,body.ifs-dark .archive-name,body.ifs-dark .deity-name{color:#ffe2a0!important}'
		. 'body.ifs-dark p,body.ifs-dark .archive-desc,body.ifs-dark .fest-card-tag,body.ifs-dark .deity-role,body.ifs-dark .hero-desc{color:#dccaa6!important}';

	// Ad blocker message box.
	$box = (string) ifs_opt( 'adblock_bg', '#1a1208' );
	$css .= '.ifs-adblock{position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;padding:16px}'
		. '.ifs-adblock[hidden]{display:none}'
		. '.ifs-adblock-box{background:' . $box . ';color:#fff6e0;max-width:420px;padding:24px;border-radius:8px;border:2px solid #d4a74a;text-align:center}'
		. '.ifs-adblock-box h2{color:#ffe2a0;margin:0 0 10px}'
		. '.ifs-adblock-close{margin-top:14px;padding:9px 18px;border:1px solid #d4a74a;background:#8b1416;color:#fff6e0;cursor:pointer}';

	return $css;
}

/**
 * Body classes from Theme Options.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function ifs_theme_option_body_classes( $classes ) {
	if ( ifs_opt( 'dark_skin', false ) ) {
		$classes[] = 'ifs-dark';
	}
	if ( ifs_opt( 'bg_dimmer', false ) ) {
		$classes[] = 'ifs-bg-dimmer';
	}
	$extra = preg_split( '/\s+/', trim( (string) ifs_opt( 'custom_body_classes', '' ) ) );
	foreach ( (array) $extra as $class ) {
		$clean = sanitize_html_class( $class );
		if ( $clean ) {
			$classes[] = $clean;
		}
	}
	return $classes;
}
add_filter( 'body_class', 'ifs_theme_option_body_classes' );

/**
 * Ad blocker message box (hidden until the script decides to show it).
 */
function ifs_adblock_modal() {
	if ( ! ifs_opt( 'adblock_on', false ) ) {
		return;
	}
	$dismiss = (bool) ifs_opt( 'adblock_dismiss', true );
	echo '<div class="ifs-adblock" hidden data-delay="' . esc_attr( (string) (int) ifs_opt( 'adblock_delay', 10 ) ) . '" data-dismiss="' . ( $dismiss ? '1' : '0' ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<div class="ifs-adblock-box" role="alertdialog" aria-modal="true" aria-labelledby="ifs-adblock-title">';
	echo '<h2 id="ifs-adblock-title">' . esc_html( (string) ifs_opt( 'adblock_title', 'Adblock Detected' ) ) . '</h2>';
	echo '<p>' . esc_html( (string) ifs_opt( 'adblock_message', '' ) ) . '</p>';
	if ( $dismiss ) {
		echo '<button type="button" class="ifs-adblock-close">' . esc_html__( 'Continue to site', 'indian-festivals' ) . '</button>';
	}
	echo '</div></div>';
}
