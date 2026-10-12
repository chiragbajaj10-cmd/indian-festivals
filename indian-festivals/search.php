<?php
/**
 * Search results.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="main-wrap">
	<div>
		<header class="paper content-paper">
			<h1 class="entry-title">
				<?php
				/* translators: %s: search query */
				printf( esc_html__( 'Results for "%s"', 'indian-festivals' ), esc_html( get_search_query() ) );
				?>
			</h1>
			<?php get_search_form(); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="archive-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card' );
				endwhile;
				?>
			</div>
			<nav class="pagination" aria-label="<?php esc_attr_e( 'Pagination', 'indian-festivals' ); ?>">
				<?php the_posts_pagination(); ?>
			</nav>
		<?php else : ?>
			<p class="notice-box"><?php esc_html_e( 'No results. Try a festival name, deity or vrat type.', 'indian-festivals' ); ?></p>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
