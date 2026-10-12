<?php
/**
 * Fallback template: blog listing.
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
			<h1 class="entry-title"><?php echo esc_html( is_home() && ! is_front_page() ? single_post_title( '', false ) : __( 'Latest Articles', 'indian-festivals' ) ); ?></h1>
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
			<p class="notice-box"><?php esc_html_e( 'Nothing found yet.', 'indian-festivals' ); ?></p>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
