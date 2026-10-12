<?php
/**
 * 404 page.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="site-main" role="main">
	<section class="paper content-paper error-404" style="margin-top:22px">
		<div class="code" aria-hidden="true">404</div>
		<h1 class="entry-title"><?php esc_html_e( 'This page has gone on a pilgrimage', 'indian-festivals' ); ?></h1>
		<p><?php esc_html_e( 'The page you are looking for does not exist or has moved.', 'indian-festivals' ); ?></p>
		<?php get_search_form(); ?>
		<p class="share-row" style="justify-content:center">
			<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'indian-festivals' ); ?></a>
			<a class="btn btn-ghost" href="<?php echo esc_url( get_post_type_archive_link( 'festival' ) ); ?>"><?php esc_html_e( 'Festivals', 'indian-festivals' ); ?></a>
			<a class="btn btn-ghost" href="<?php echo esc_url( get_post_type_archive_link( 'vrat' ) ); ?>"><?php esc_html_e( 'Vrat & Fasts', 'indian-festivals' ); ?></a>
		</p>
	</section>
</main>
<?php
get_footer();
