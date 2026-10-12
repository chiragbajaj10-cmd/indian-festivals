<?php
/**
 * Sidebar: widgets, with a default upcoming festivals box and an ad slot.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<aside id="secondary" class="sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'indian-festivals' ); ?>">
	<?php if ( ifs_opt( 'home_ads', true ) ) : ?>
		<?php ifs_ad_slot( 'sidebar' ); ?>
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php else : ?>
		<section class="widget paper" style="padding:16px">
			<h2 class="widget-title"><?php esc_html_e( 'Upcoming Festivals', 'indian-festivals' ); ?></h2>
			<?php
			foreach ( ifs_upcoming_festivals( 5 ) as $post ) :
				?>
				<p style="margin:6px 0"><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( $post->post_title ); ?></a><br><small><?php echo esc_html( ifs_festival_date_label( $post->ID ) ); ?></small></p>
			<?php endforeach; ?>
		</section>
		<section class="widget paper" style="padding:16px">
			<h2 class="widget-title"><?php esc_html_e( 'Search', 'indian-festivals' ); ?></h2>
			<?php get_search_form(); ?>
		</section>
	<?php endif; ?>
</aside>
