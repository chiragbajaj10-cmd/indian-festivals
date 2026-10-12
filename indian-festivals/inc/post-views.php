<?php
/**
 * Post views: counts single-page visits, shows the count with an eye or fire icon,
 * and colours the number by popularity. Settings live in Theme Options > Post Views.
 *
 * Note: visits are counted on each page load. Full-page caching plugins may serve
 * cached pages without running this, so counts can be lower on cached sites.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Count one view for a singular page, ignoring admins and previews.
 */
function ifs_post_views_track() {
	if ( is_admin() || is_preview() || ! is_singular() || ! ifs_opt( 'post_views_on', true ) ) {
		return;
	}
	if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		return;
	}
	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}
	$stored = get_post_meta( $post_id, '_ifs_views', true );
	$base   = ( '' === $stored ) ? (int) ifs_opt( 'post_views_start', 10 ) : (int) $stored;
	update_post_meta( $post_id, '_ifs_views', $base + 1 );
}
add_action( 'template_redirect', 'ifs_post_views_track' );

/**
 * Current view count, using the starting number for posts that have never been counted.
 *
 * @param int $post_id Post ID (defaults to the current post).
 * @return int
 */
function ifs_post_views_count( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$stored  = get_post_meta( $post_id, '_ifs_views', true );
	return ( '' === $stored ) ? (int) ifs_opt( 'post_views_start', 10 ) : (int) $stored;
}

/**
 * Markup for the view count, or an empty string when views are switched off.
 *
 * @param int $post_id Post ID (defaults to the current post).
 * @return string Safe HTML.
 */
function ifs_post_views_html( $post_id = 0 ) {
	if ( ! ifs_opt( 'post_views_on', true ) ) {
		return '';
	}
	$post_id = $post_id ? $post_id : get_the_ID();
	$count   = ifs_post_views_count( $post_id );

	$class = 'ifs-views';
	if ( ifs_opt( 'post_views_colored', true ) ) {
		if ( $count >= (int) ifs_opt( 'post_views_vhot', 1000 ) ) {
			$class .= ' is-very-hot';
		} elseif ( $count >= (int) ifs_opt( 'post_views_hot', 500 ) ) {
			$class .= ' is-hot';
		} elseif ( $count >= (int) ifs_opt( 'post_views_warm', 100 ) ) {
			$class .= ' is-warm';
		}
	}

	$icon = 'fire' === ifs_opt( 'post_views_icon', 'eye' ) ? '🔥' : '👁';

	return '<span class="' . esc_attr( $class ) . '"><span aria-hidden="true">' . $icon . '</span> '
		. esc_html( number_format_i18n( $count ) ) . ' <span class="sr-only">' . esc_html__( 'views', 'indian-festivals' ) . '</span></span>';
}
