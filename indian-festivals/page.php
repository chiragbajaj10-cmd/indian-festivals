<?php
/**
 * Default page template. Elementor-edited pages render their own layout.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="main-wrap full">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) ) :
			the_content();
		else :
			?>
			<article <?php post_class( 'paper content-paper' ); ?>>
				<?php if ( ifs_opt( 'show_breadcrumbs', true ) ) : ?>
					<?php
					if ( function_exists( 'ifs_breadcrumbs' ) ) {
						ifs_breadcrumbs();
					}
					?>
				<?php endif; ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<p class="entry-meta"><?php printf( esc_html__( 'Last updated %s', 'indian-festivals' ), esc_html( get_the_modified_date() ) ); ?></p>
				<div class="entry-content"><?php the_content(); ?></div>
				<?php wp_link_pages(); ?>
			</article>
			<?php
		endif;
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	endwhile;
	?>
</main>
<?php
get_footer();
