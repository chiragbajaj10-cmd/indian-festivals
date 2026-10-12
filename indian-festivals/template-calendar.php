<?php
/**
 * Template Name: Festival Calendar (Indian Festivals)
 *
 * A year grid with every festival marked. Uses the festival posts' date field.
 * Create a page, set the template, and the calendar appears below the content.
 * ?year=2027 switches the year.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ifs_year = isset( $_GET['year'] ) ? absint( $_GET['year'] ) : (int) wp_date( 'Y' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ifs_year = max( 2020, min( 2035, $ifs_year ) );

// Collect festivals whose date falls in the chosen year.
$ifs_all = get_posts(
	array(
		'post_type'      => 'festival',
		'post_status'    => 'publish',
		'posts_per_page' => 500,
		'no_found_rows'  => true,
		'meta_key'       => '_ifs_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
	)
);
$ifs_by_day = array();
foreach ( $ifs_all as $ifs_post ) {
	$iso = ifs_meta( 'date', $ifs_post->ID );
	if ( ! $iso || (int) substr( $iso, 0, 4 ) !== $ifs_year ) {
		continue;
	}
	$ifs_by_day[ $iso ][] = $ifs_post;
}

$ifs_months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
$ifs_dow    = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
?>
<main id="main" class="site-main" role="main">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article <?php post_class( 'dp-article' ); ?>>
			<h2><?php the_title(); ?></h2>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>

	<div class="cal-yearnav" role="group" aria-label="<?php esc_attr_e( 'Choose year', 'indian-festivals' ); ?>">
		<?php for ( $y = $ifs_year - 1; $y <= $ifs_year + 1; $y++ ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'year', $y, get_permalink() ) ); ?>" class="<?php echo $y === $ifs_year ? 'is-active' : ''; ?>">
				<?php echo esc_html( $y ); ?>
			</a>
		<?php endfor; ?>
	</div>

	<div class="cal-year-grid">
		<?php for ( $m = 0; $m < 12; $m++ ) : ?>
			<?php
			$first = (int) gmdate( 'w', gmmktime( 0, 0, 0, $m + 1, 1, $ifs_year ) );
			$days  = (int) gmdate( 't', gmmktime( 0, 0, 0, $m + 1, 1, $ifs_year ) );
			?>
			<section class="cal-month" aria-label="<?php echo esc_attr( $ifs_months[ $m ] . ' ' . $ifs_year ); ?>">
				<h2 class="cal-month-name"><?php echo esc_html( $ifs_months[ $m ] ); ?></h2>
				<div class="cal-grid">
					<?php foreach ( $ifs_dow as $d ) : ?>
						<span class="cal-dow"><?php echo esc_html( $d ); ?></span>
					<?php endforeach; ?>
					<?php for ( $e = 0; $e < $first; $e++ ) : ?>
						<span class="cal-day is-empty"></span>
					<?php endfor; ?>
					<?php for ( $day = 1; $day <= $days; $day++ ) : ?>
						<?php
						$iso     = sprintf( '%04d-%02d-%02d', $ifs_year, $m + 1, $day );
						$entries = $ifs_by_day[ $iso ] ?? array();
						?>
						<span class="cal-day<?php echo $entries ? ' has-festival' : ''; ?>">
							<span class="cal-num"><?php echo (int) $day; ?></span>
							<?php foreach ( $entries as $entry ) : ?>
								<a class="cal-fest" href="<?php echo esc_url( get_permalink( $entry ) ); ?>"><?php echo esc_html( get_the_title( $entry ) ); ?></a>
							<?php endforeach; ?>
						</span>
					<?php endfor; ?>
				</div>
			</section>
		<?php endfor; ?>
	</div>

	<p class="interior-disclaimer"><?php echo esc_html( ifs_opt( 'disclaimer_text', '' ) ); ?></p>
</main>
<?php
get_footer();
