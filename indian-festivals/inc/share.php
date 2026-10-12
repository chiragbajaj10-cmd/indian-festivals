<?php
/**
 * Share buttons. Each network is switched on or off in Theme Options > Share Buttons.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the share buttons for a post.
 *
 * @param int $post_id Post ID (defaults to the current post).
 */
function ifs_share_buttons( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	$url     = get_permalink( $post_id );
	$title   = get_the_title( $post_id );
	$image   = get_the_post_thumbnail_url( $post_id, 'large' );
	$x_user  = ltrim( (string) ifs_opt( 'share_x_user', '' ), '@' );
	$enc_url = rawurlencode( $url );
	$enc_ttl = rawurlencode( $title );

	$networks = array(
		'x'         => array( 'X', 'https://twitter.com/intent/tweet?url=' . $enc_url . '&text=' . $enc_ttl . ( $x_user ? '&via=' . rawurlencode( $x_user ) : '' ) ),
		'facebook'  => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . $enc_url ),
		'linkedin'  => array( 'LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . $enc_url ),
		'pinterest' => array( 'Pinterest', 'https://pinterest.com/pin/create/button/?url=' . $enc_url . '&description=' . $enc_ttl . ( $image ? '&media=' . rawurlencode( $image ) : '' ) ),
		'reddit'    => array( 'Reddit', 'https://www.reddit.com/submit?url=' . $enc_url . '&title=' . $enc_ttl ),
		'telegram'  => array( 'Telegram', 'https://t.me/share/url?url=' . $enc_url . '&text=' . $enc_ttl ),
		'whatsapp'  => array( 'WhatsApp', 'https://wa.me/?text=' . rawurlencode( $title . ' ' . $url ) ),
		'pocket'    => array( 'Pocket', 'https://getpocket.com/save?url=' . $enc_url . '&title=' . $enc_ttl ),
		'email'     => array( 'Email', 'mailto:?subject=' . $enc_ttl . '&body=' . rawurlencode( $url ) ),
	);

	$position = sanitize_html_class( (string) ifs_opt( 'share_position', 'default' ) );

	echo '<div class="ifs-share ifs-share--' . esc_attr( $position ) . '">';
	if ( ifs_opt( 'share_title', true ) ) {
		echo '<span class="ifs-share-title">' . esc_html__( 'Share', 'indian-festivals' ) . '</span>';
	}
	foreach ( $networks as $key => $network ) {
		if ( ! ifs_opt( 'share_' . $key, false ) ) {
			continue;
		}
		$external = 'email' !== $key;
		printf(
			'<a class="ifs-share-btn ifs-share-%1$s" href="%2$s"%3$s>%4$s</a>',
			esc_attr( $key ),
			esc_url( $network[1] ),
			$external ? ' target="_blank" rel="noopener"' : '',
			esc_html( $network[0] )
		);
	}
	echo '</div>';
}
