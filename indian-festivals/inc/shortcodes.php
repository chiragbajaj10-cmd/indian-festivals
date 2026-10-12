<?php
/**
 * Shortcodes. Elementor widgets render these shortcodes, so every widget works in Elementor Free.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrap a renderer so it can be used as a shortcode.
 *
 * @param callable $callback Function that echoes HTML.
 * @return string
 */
function ifs_capture( $callback ) {
	ob_start();
	call_user_func( $callback );
	return ob_get_clean();
}

add_shortcode(
	'ifs_upcoming_festivals',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'count' => 5 ), $atts, 'ifs_upcoming_festivals' );
		return ifs_capture(
			function () use ( $atts ) {
				ifs_festival_cards( max( 1, min( 12, (int) $atts['count'] ) ) );
			}
		);
	}
);

add_shortcode(
	'ifs_panchang_today',
	function () {
		return ifs_capture( 'ifs_panchang_panel' );
	}
);

add_shortcode(
	'ifs_vrat_cards',
	function () {
		return ifs_capture( 'ifs_section_vrat' );
	}
);

add_shortcode(
	'ifs_deity_bands',
	function () {
		return ifs_capture( 'ifs_section_deity' );
	}
);

add_shortcode(
	'ifs_journey',
	function () {
		return ifs_capture( 'ifs_section_journey' );
	}
);

add_shortcode(
	'ifs_info_strip',
	function () {
		return ifs_capture( 'ifs_section_info_strip' );
	}
);

add_shortcode(
	'ifs_tools_grid',
	function () {
		return ifs_capture( 'ifs_section_tools' );
	}
);

add_shortcode(
	'ifs_ad_slot',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'slot' => 'in_content' ), $atts, 'ifs_ad_slot' );
		$slot = in_array( $atts['slot'], array( 'header', 'in_content', 'sidebar', 'footer' ), true ) ? $atts['slot'] : 'in_content';
		return ifs_capture(
			function () use ( $slot ) {
				ifs_ad_slot( $slot );
			}
		);
	}
);

/**
 * Countdown to a festival (next upcoming by default, or by slug).
 */
add_shortcode(
	'ifs_countdown',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'festival' => '' ), $atts, 'ifs_countdown' );
		$post = null;
		if ( $atts['festival'] ) {
			$post = get_page_by_path( sanitize_title( $atts['festival'] ), OBJECT, 'festival' );
		}
		if ( ! $post ) {
			$next = ifs_upcoming_festivals( 1 );
			$post = $next ? $next[0] : null;
		}
		if ( ! $post ) {
			return '<p class="small-note">' . esc_html__( 'No upcoming festival.', 'indian-festivals' ) . '</p>';
		}
		$date = ifs_meta( 'date', $post->ID );
		ob_start();
		?>
		<div class="ifs-countdown" data-date="<?php echo esc_attr( $date ); ?>" data-name="<?php echo esc_attr( $post->post_title ); ?>">
			<p class="section-sub" style="margin:0 0 10px"><?php echo esc_html( sprintf( __( 'Countdown to %s', 'indian-festivals' ), $post->post_title ) ); ?></p>
			<div class="countdown" aria-live="polite">
				<div><span data-unit="d">--</span><small><?php esc_html_e( 'Days', 'indian-festivals' ); ?></small></div>
				<div><span data-unit="h">--</span><small><?php esc_html_e( 'Hours', 'indian-festivals' ); ?></small></div>
				<div><span data-unit="m">--</span><small><?php esc_html_e( 'Minutes', 'indian-festivals' ); ?></small></div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
);

/**
 * Wish generator: festival, name, language and design, with text and image share.
 */
add_shortcode(
	'ifs_wish_generator',
	function () {
		$festivals = get_posts(
			array(
				'post_type'      => 'festival',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
		ob_start();
		?>
		<div class="ifs-wish" data-wish>
			<div class="wish-box">
				<div>
					<label for="wish-festival"><?php esc_html_e( 'Festival', 'indian-festivals' ); ?></label>
					<select id="wish-festival">
						<?php foreach ( $festivals as $f ) : ?>
							<option value="<?php echo esc_attr( $f->post_title ); ?>"><?php echo esc_html( $f->post_title ); ?></option>
						<?php endforeach; ?>
						<?php if ( ! $festivals ) : ?>
							<option value="Festival"><?php esc_html_e( 'Festival', 'indian-festivals' ); ?></option>
						<?php endif; ?>
					</select>
				</div>
				<div>
					<label for="wish-name"><?php esc_html_e( 'Name', 'indian-festivals' ); ?></label>
					<input id="wish-name" type="text" maxlength="40" placeholder="<?php esc_attr_e( 'Your name', 'indian-festivals' ); ?>">
				</div>
				<div>
					<label for="wish-lang"><?php esc_html_e( 'Language', 'indian-festivals' ); ?></label>
					<select id="wish-lang">
						<option value="en"><?php esc_html_e( 'English', 'indian-festivals' ); ?></option>
						<option value="hi"><?php esc_html_e( 'Hindi', 'indian-festivals' ); ?></option>
					</select>
				</div>
				<div>
					<label for="wish-design"><?php esc_html_e( 'Design', 'indian-festivals' ); ?></label>
					<select id="wish-design">
						<option value="gold"><?php esc_html_e( 'Gold on parchment', 'indian-festivals' ); ?></option>
						<option value="maroon"><?php esc_html_e( 'Maroon', 'indian-festivals' ); ?></option>
						<option value="navy"><?php esc_html_e( 'Navy', 'indian-festivals' ); ?></option>
					</select>
				</div>
			</div>
			<p style="margin:14px 0"><button type="button" class="btn" data-wish-generate><?php esc_html_e( 'Generate wish', 'indian-festivals' ); ?></button>
			<button type="button" class="btn btn-ghost" data-wish-image><?php esc_html_e( 'Download image', 'indian-festivals' ); ?></button>
			<a class="btn btn-ghost" data-wish-whatsapp href="#" target="_blank" rel="noopener"><?php esc_html_e( 'Share on WhatsApp', 'indian-festivals' ); ?></a></p>
			<div class="wish-output" data-wish-output aria-live="polite"><?php esc_html_e( 'Your wish will appear here.', 'indian-festivals' ); ?></div>
		</div>
		<?php
		return ob_get_clean();
	}
);
