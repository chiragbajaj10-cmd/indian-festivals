<?php
/**
 * Torn-paper archive card shared by search, blog and content archives.
 *
 * @package IndianFestivals
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ifs_id   = get_the_ID();
$ifs_type = get_post_type( $ifs_id );
$ifs_obj  = get_post_type_object( $ifs_type );
$ifs_date = '';
if ( in_array( $ifs_type, array( 'festival', 'vrat', 'muhurat', 'holiday' ), true ) ) {
	$ifs_date = ifs_format_date( ifs_meta( 'date', $ifs_id ) );
} elseif ( 'post' === $ifs_type ) {
	$ifs_date = get_the_date();
}
$ifs_thumb = get_the_post_thumbnail_url( $ifs_id, 'ifs-card' );
if ( ! $ifs_thumb ) {
	$ifs_art = array(
		'holi' => 'fest-holi.jpg', 'diwali' => 'fest-diwali.jpg',
		'ganesh-chaturthi' => 'fest-ganesh.jpg', 'navratri' => 'fest-navratri.jpg',
		'raksha-bandhan' => 'fest-raksha.jpg',
		'ganesha' => 'fest-ganesh.jpg', 'mahadev' => 'deity-shiva.jpg',
		'vishnu' => 'deity-vishnu.jpg', 'hanuman' => 'deity-hanuman.jpg',
		'krishna' => 'deity-krishna.jpg',
	);
	$ifs_thumb = IFS_URI . '/assets/images/' . ( $ifs_art[ get_post_field( 'post_name', $ifs_id ) ] ?? 'hero-varanasi.jpg' );
}
?>
<div class="paper-card-stage" style="--card-n:<?php echo (int) ( $ifs_id % 5 ); ?>">
	<a class="torn-card archive-card" href="<?php echo esc_url( get_permalink( $ifs_id ) ); ?>" data-tilt>
		<span class="torn-card-edge" aria-hidden="true"></span>
		<span class="archive-photo"><img src="<?php echo esc_url( $ifs_thumb ); ?>" alt="" loading="lazy" width="400" height="270"></span>
		<small class="archive-type"><?php echo esc_html( $ifs_obj ? $ifs_obj->labels->singular_name : '' ); ?></small>
		<strong class="archive-name"><?php echo esc_html( get_the_title( $ifs_id ) ); ?></strong>
		<?php if ( $ifs_date ) : ?><span class="archive-date"><?php echo esc_html( $ifs_date ); ?></span><?php endif; ?>
		<?php if ( ifs_opt( 'archive_excerpt', true ) ) : ?>
			<span class="archive-desc"><?php echo esc_html( ifs_excerpt_words( get_post( $ifs_id ), (int) ifs_opt( 'archive_excerpt_length', 16 ) ) ); ?></span>
		<?php endif; ?>
		<?php if ( ifs_opt( 'archive_readmore', true ) ) : ?>
			<span class="archive-readmore"><?php echo esc_html( ifs_opt( 'archive_readmore_text', 'Read more' ) ); ?> →</span>
		<?php endif; ?>
		<?php if ( ifs_opt( 'archive_show_views', true ) ) : ?>
			<span class="archive-views"><?php echo ifs_post_views_html( $ifs_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endif; ?>
	</a>
</div>
