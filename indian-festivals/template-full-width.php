<?php
/**
 * Template Name: Full Width (Indian Festivals)
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="site-main" role="main">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) ) {
			the_content();
		} else {
			?>
			<article <?php post_class( 'paper content-paper' ); ?> style="margin-top:22px">
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
			<?php
		}
	endwhile;
	?>
</main>
<?php
get_footer();
