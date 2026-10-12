<?php
/**
 * Single template for posts and every content type (festival, vrat, muhurat, katha,
 * deity, greeting, web story, holiday, panchang).
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ifs_breadcrumbs' ) ) {
	/**
	 * Breadcrumb trail.
	 */
	function ifs_breadcrumbs() {
		$type  = get_post_type();
		$obj   = get_post_type_object( $type );
		$items = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'indian-festivals' ) . '</a>' );
		if ( $obj && 'post' !== $type && $obj->has_archive ) {
			$items[] = '<a href="' . esc_url( get_post_type_archive_link( $type ) ) . '">' . esc_html( $obj->labels->name ) . '</a>';
		} elseif ( 'post' === $type ) {
			$items[] = '<a href="' . esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ) . '">' . esc_html__( 'Articles', 'indian-festivals' ) . '</a>';
		}
		$items[] = esc_html( get_the_title() );
		echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'indian-festivals' ) . '">' . implode( ' › ', $items ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'ifs_terms_text' ) ) {
	/**
	 * Comma list of term names for a taxonomy.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @return string
	 */
	function ifs_terms_text( $taxonomy ) {
		$terms = get_the_terms( get_the_ID(), $taxonomy );
		return $terms && ! is_wp_error( $terms ) ? implode( ', ', wp_list_pluck( $terms, 'name' ) ) : '';
	}
}

if ( ! function_exists( 'ifs_facts' ) ) {
	/**
	 * Key facts for each content type.
	 */
	function ifs_facts() {
		$type  = get_post_type();
		$rows  = array();
		$id    = get_the_ID();
		switch ( $type ) {
			case 'festival':
				$rows = array(
					__( 'Date', 'indian-festivals' )         => ifs_format_date( ifs_meta( 'date' ) ),
					__( 'End date', 'indian-festivals' )     => ifs_format_date( ifs_meta( 'end_date' ) ),
					__( 'Tithi', 'indian-festivals' )        => ifs_meta( 'tithi' ),
					__( 'Puja muhurat', 'indian-festivals' ) => ifs_meta( 'puja_muhurat' ),
					__( 'Deity', 'indian-festivals' )        => ifs_meta( 'deity' ),
					__( 'Region', 'indian-festivals' )       => ifs_terms_text( 'ifs_region' ),
					__( 'Tradition', 'indian-festivals' )    => ifs_terms_text( 'ifs_tradition' ),
				);
				break;
			case 'vrat':
				$rows = array(
					__( 'Vrat type', 'indian-festivals' )   => ucfirst( ifs_meta( 'vrat_type' ) ),
					__( 'Date', 'indian-festivals' )        => ifs_format_date( ifs_meta( 'date' ) ),
					__( 'Start time', 'indian-festivals' )  => ifs_meta( 'start_time' ),
					__( 'End time', 'indian-festivals' )    => ifs_meta( 'end_time' ),
					__( 'Parana time', 'indian-festivals' ) => ifs_meta( 'parana_time' ),
				);
				break;
			case 'muhurat':
				$rows = array(
					__( 'Category', 'indian-festivals' ) => ucwords( str_replace( '_', ' ', ifs_meta( 'category' ) ) ),
					__( 'Date', 'indian-festivals' )     => ifs_format_date( ifs_meta( 'date' ) ),
					__( 'Start', 'indian-festivals' )    => ifs_meta( 'start_time' ),
					__( 'End', 'indian-festivals' )      => ifs_meta( 'end_time' ),
				);
				break;
			case 'deity':
				$rows = array(
					__( 'Epithets', 'indian-festivals' ) => ifs_meta( 'epithets' ),
					__( 'Symbols', 'indian-festivals' )  => ifs_meta( 'symbols' ),
				);
				break;
			case 'panchang':
				$city = (int) ifs_meta( 'city_id' );
				$rows = array(
					__( 'Date', 'indian-festivals' )      => ifs_format_date( ifs_meta( 'date' ) ),
					__( 'City', 'indian-festivals' )      => $city ? get_the_title( $city ) : '',
					__( 'Tithi', 'indian-festivals' )     => ifs_meta( 'tithi' ),
					__( 'Nakshatra', 'indian-festivals' ) => ifs_meta( 'nakshatra' ),
					__( 'Yoga', 'indian-festivals' )      => ifs_meta( 'yoga' ),
					__( 'Karana', 'indian-festivals' )    => ifs_meta( 'karana' ),
					__( 'Sunrise', 'indian-festivals' )   => ifs_meta( 'sunrise' ),
					__( 'Sunset', 'indian-festivals' )    => ifs_meta( 'sunset' ),
					__( 'Moonrise', 'indian-festivals' )  => ifs_meta( 'moonrise' ),
				);
				break;
			case 'holiday':
				$rows = array(
					__( 'Date', 'indian-festivals' )  => ifs_format_date( ifs_meta( 'date' ) ),
					__( 'State', 'indian-festivals' ) => ifs_meta( 'state' ),
					__( 'Type', 'indian-festivals' )  => ucfirst( ifs_meta( 'type' ) ),
				);
				break;
			default:
				return;
		}
		$rows = array_filter( $rows, 'strlen' );
		if ( ! $rows ) {
			return;
		}
		echo '<dl class="facts">';
		foreach ( $rows as $label => $value ) {
			echo '<dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $value ) . '</dd>';
		}
		echo '</dl>';

		if ( 'festival' === $type ) {
			$date = ifs_meta( 'date' );
			$end  = ifs_meta( 'end_date' ) ?: $date;
			if ( $date ) {
				$google = add_query_arg(
					array(
						'action'  => 'TEMPLATE',
						'text'    => get_the_title(),
						'dates'   => str_replace( '-', '', $date ) . '/' . gmdate( 'Ymd', strtotime( $end . ' +1 day' ) ),
						'details' => wp_strip_all_tags( get_the_excerpt() ),
					),
					'https://calendar.google.com/calendar/render'
				);
				echo '<p class="share-row"><a href="' . esc_url( home_url( '/?ifs_ics=' . $id ) ) . '">📆 ' . esc_html__( 'Download .ics', 'indian-festivals' ) . '</a><a href="' . esc_url( $google ) . '" target="_blank" rel="noopener">' . esc_html__( 'Add to Google Calendar', 'indian-festivals' ) . '</a></p>';
			}
		}
	}
}

get_header();
?>
<main id="main" class="main-wrap">
	<div>
		<?php
		while ( have_posts() ) :
			the_post();
			$ifs_type = get_post_type();
			?>
			<article <?php post_class( 'paper content-paper' ); ?>>
				<?php if ( ifs_opt( 'show_breadcrumbs', true ) ) : ?>
					<?php ifs_breadcrumbs(); ?>
				<?php endif; ?>

				<h1 class="entry-title"><?php the_title(); ?></h1>
				<p class="entry-meta">
					<?php
					if ( 'post' === $ifs_type ) {
						/* translators: 1: author, 2: published date, 3: updated date */
						printf( esc_html__( 'By %1$s · Published %2$s · Last updated %3$s', 'indian-festivals' ), esc_html( get_the_author() ), esc_html( get_the_date() ), esc_html( get_the_modified_date() ) );
					} else {
						/* translators: %s: last updated date */
						printf( esc_html__( 'Last updated %s · Verify dates with an authentic panchang', 'indian-festivals' ), esc_html( get_the_modified_date() ) );
					}
					?>
					<?php echo ifs_post_views_html( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>

				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'ifs-wide', array( 'loading' => ifs_opt( 'perf_lazy', true ) ? 'lazy' : 'eager', 'style' => 'width:100%;height:auto;border:3px solid #fff7e0' ) ); ?>
				<?php endif; ?>

				<?php ifs_facts(); ?>

				<?php if ( 'katha' === $ifs_type && ifs_meta( 'audio_url' ) ) : ?>
					<audio controls preload="none" src="<?php echo esc_url( ifs_meta( 'audio_url' ) ); ?>" style="width:100%;margin:12px 0"></audio>
				<?php endif; ?>

				<?php if ( 'web_story' === $ifs_type ) : ?>
					<div class="grid-3" style="margin:12px 0">
						<?php foreach ( preg_split( '/\r\n|\r|\n/', ifs_meta( 'slides' ) ) as $slide ) : ?>
							<?php
							$parts = array_map( 'trim', explode( '|', $slide ) );
							if ( empty( $parts[0] ) ) {
								continue;
							}
							?>
							<div class="story-tile" style="<?php echo ! empty( $parts[2] ) ? 'background-image:url(' . esc_url( $parts[2] ) . ')' : ''; ?>">
								<div><strong><?php echo esc_html( $parts[0] ); ?></strong><br><?php echo esc_html( $parts[1] ?? '' ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="entry-content">
					<?php the_content(); ?>
				</div>

				<?php if ( 'deity' === $ifs_type && ifs_meta( 'related_festivals' ) ) : ?>
					<p class="small-note"><?php esc_html_e( 'Related festivals:', 'indian-festivals' ); ?> <?php echo esc_html( ifs_meta( 'related_festivals' ) ); ?></p>
				<?php endif; ?>

				<?php if ( ifs_opt( 'show_share', true ) ) : ?>
					<?php ifs_share_buttons( get_the_ID() ); ?>
				<?php endif; ?>

				<p class="small-note"><?php echo esc_html( ifs_opt( 'disclaimer_text', '' ) ); ?></p>

				<?php if ( ifs_opt( 'show_author_box', true ) && 'post' === $ifs_type ) : ?>
					<div class="author-box">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 56, '', '', array( 'class' => 'avatar' ) ); ?>
						<div>
							<strong><?php echo esc_html( get_the_author() ); ?></strong>
							<p class="small-note" style="text-align:left;margin:4px 0 0"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ifs_opt( 'show_prev_next', true ) ) : ?>
					<nav class="prev-next" aria-label="<?php esc_attr_e( 'Post navigation', 'indian-festivals' ); ?>">
						<span><?php previous_post_link( '← %link' ); ?></span>
						<span><?php next_post_link( '%link →' ); ?></span>
					</nav>
				<?php endif; ?>

				<?php if ( ifs_opt( 'show_related', true ) ) : ?>
					<?php
					$related = get_posts(
						array(
							'post_type'      => $ifs_type,
							'post_status'    => 'publish',
							'posts_per_page' => 3,
							'post__not_in'   => array( get_the_ID() ),
							'no_found_rows'  => true,
						)
					);
					if ( $related ) :
						?>
						<h2 class="related-title ribbon" style="font-size:16px"><?php esc_html_e( 'Related', 'indian-festivals' ); ?></h2>
						<div class="archive-grid" style="--ifs-archive-cols:3">
							<?php
							foreach ( $related as $rel ) :
								setup_postdata( $rel );
								get_template_part( 'template-parts/card' );
							endforeach;
							wp_reset_postdata();
							?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( comments_open() || get_comments_number() ) : ?>
					<div class="comments-area"><?php comments_template(); ?></div>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
