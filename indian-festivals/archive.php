<?php
/**
 * Archive template for every content type and taxonomy.
 * Festivals archive has a list view and a calendar view (?view=calendar).
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$ifs_calendar_view = 'calendar' === ( isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<main id="main" class="main-wrap">
	<div>
		<header class="paper content-paper">
			<h1 class="entry-title"><?php the_archive_title(); ?></h1>
			<div class="entry-meta"><?php the_archive_description(); ?></div>
			<?php if ( is_post_type_archive( 'festival' ) ) : ?>
				<p class="share-row">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'festival' ) ); ?>"><?php esc_html_e( 'List view', 'indian-festivals' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'view', 'calendar', get_post_type_archive_link( 'festival' ) ) ); ?>"><?php esc_html_e( 'Calendar view', 'indian-festivals' ); ?></a>
				</p>
			<?php endif; ?>
		</header>

		<?php if ( $ifs_calendar_view && is_post_type_archive( 'festival' ) ) : ?>
			<?php ifs_section_calendar(); ?>
		<?php endif; ?>

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
			<p class="notice-box"><?php esc_html_e( 'Nothing to show yet. Add entries in the dashboard.', 'indian-festivals' ); ?></p>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
