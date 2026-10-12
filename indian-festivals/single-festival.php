<?php
/**
 * Single festival template, Drik Panchang style: location + year bar, maroon
 * banner, olive date card beside the deity image, Timings / About / Muhurat /
 * Summary tabs, timings card, Katha box and article.
 *
 * Year navigation: publish one festival post per year with slugs like
 * "diwali-2025", "diwali-2026". The template finds sibling year posts
 * automatically. ?view=timings|about|muhurat|summary switches the tab and
 * ?city= sets the location label.
 *
 * @package IndianFestivals
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$post_id = get_the_ID();
	$slug    = get_post_field( 'post_name', $post_id );

	// Base slug + year: "diwali-2026" -> base "diwali", year 2026.
	$year = 0;
	$base = $slug;
	if ( preg_match( '/-(\d{4})$/', $slug, $m ) ) {
		$year = (int) $m[1];
		$base = substr( $slug, 0, -5 );
	}
	if ( ! $year ) {
		$year = (int) ifs_meta( 'year', $post_id );
	}
	if ( ! $year ) {
		$year = (int) wp_date( 'Y' );
	}

	// Requested view and city.
	$view = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : 'timings'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $view, array( 'timings', 'about', 'muhurat', 'summary' ), true ) ) {
		$view = 'timings';
	}
	$city = isset( $_GET['city'] ) ? sanitize_text_field( wp_unslash( $_GET['city'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$city = $city ? substr( $city, 0, 60 ) : ifs_opt( 'default_city', 'Delhi, India' );

	// Date parts.
	$date_iso = ifs_meta( 'date', $post_id );
	$ts       = $date_iso ? strtotime( $date_iso . ' 00:00:00 UTC' ) : time();
	$day      = (int) gmdate( 'j', $ts );
	$suffix   = 'th';
	if ( ! in_array( $day % 100, array( 11, 12, 13 ), true ) ) {
		$map    = array( 1 => 'st', 2 => 'nd', 3 => 'rd' );
		$suffix = $map[ $day % 10 ] ?? 'th';
	}
	$wd_en = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	$wd_hi = array( 'रविवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'गुरुवार', 'शुक्रवार', 'शनिवार' );
	$wday  = (int) gmdate( 'w', $ts );

	$tithi         = ifs_meta( 'tithi', $post_id );
	$muhurat_label = ifs_meta( 'puja_muhurat', $post_id );
	$muhurat_start = ifs_meta( 'muhurat_start', $post_id );
	$muhurat_end   = ifs_meta( 'muhurat_end', $post_id );
	$avoid_label   = ifs_meta( 'avoid_label', $post_id );
	$avoid_start   = ifs_meta( 'avoid_start', $post_id );
	$avoid_end     = ifs_meta( 'avoid_end', $post_id );
	$tithi_start   = ifs_meta( 'tithi_start', $post_id );
	$tithi_end     = ifs_meta( 'tithi_end', $post_id );
	$tithi_short   = $tithi ? trim( preg_replace( '/\s*\(.*$/', '', $tithi ) ) : __( 'Tithi', 'indian-festivals' );

	if ( ! function_exists( 'ifs_dp_duration' ) ) {
		function ifs_dp_duration( $start, $end ) {
			$to_min = function ( $t ) {
				if ( ! preg_match( '/(\d{1,2}):(\d{2})\s*(AM|PM)/i', $t, $m ) ) {
					return null;
				}
				$h = (int) $m[1] % 12;
				if ( 'pm' === strtolower( $m[3] ) ) {
					$h += 12;
				}
				return $h * 60 + (int) $m[2];
			};
			$a = $to_min( $start );
			$b = $to_min( $end );
			if ( null === $a || null === $b ) {
				return '';
			}
			$diff = $b - $a;
			if ( $diff < 0 ) {
				$diff += 24 * 60;
			}
			return sprintf( '%02d Hours %02d Mins', intdiv( $diff, 60 ), $diff % 60 );
		}
	}

	// Sibling year posts for Prev / Next navigation.
	$year_link = function ( $target ) use ( $base, $view, $city ) {
		$sib = get_page_by_path( $base . '-' . $target, OBJECT, 'festival' );
		if ( ! $sib ) {
			return '';
		}
		return add_query_arg( array( 'view' => $view, 'city' => $city ), get_permalink( $sib ) );
	};
	$prev_url = $year_link( $year - 1 );
	$next_url = $year_link( $year + 1 );
	$tab_url  = function ( $v ) use ( $city ) {
		return add_query_arg(
			array(
				'view' => $v,
				'city' => $city,
			),
			get_permalink()
		);
	};

	$cities  = array( 'Delhi, India', 'Mumbai, India', 'Varanasi, India', 'Chennai, India', 'Kolkata, India', 'Bengaluru, India', 'Jaipur, India', 'Ujjain, India', 'Pune, India', 'Ahmedabad, India' );
	$art     = get_the_post_thumbnail_url( $post_id, 'large' );
	$art_map = array(
		'holi' => 'fest-holi.jpg', 'diwali' => 'fest-diwali.jpg', 'ganesh-chaturthi' => 'fest-ganesh.jpg',
		'navratri' => 'fest-navratri.jpg', 'raksha-bandhan' => 'fest-raksha.jpg',
	);
	if ( ! $art ) {
		$art = IFS_URI . '/assets/images/' . ( $art_map[ $base ] ?? 'hero-varanasi.jpg' );
	}
	?>
	<main id="main" class="site-main" role="main">
		<div class="dp-festhead">
			<form class="dp-locbar" action="<?php echo esc_url( get_permalink() ); ?>" method="get">
				<input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>">
				<label class="dp-locfield">
					<span aria-hidden="true">📍</span>
					<span class="sr-only"><?php esc_html_e( 'Location', 'indian-festivals' ); ?></span>
					<input name="city" value="<?php echo esc_attr( $city ); ?>" list="dp-cities" autocomplete="off">
					<datalist id="dp-cities">
						<?php foreach ( $cities as $c ) : ?>
							<option value="<?php echo esc_attr( $c ); ?>"></option>
						<?php endforeach; ?>
					</datalist>
				</label>
				<label class="dp-yearfield">
					<span aria-hidden="true">📅</span>
					<span class="sr-only"><?php esc_html_e( 'Year', 'indian-festivals' ); ?></span>
					<input value="<?php echo esc_attr( $year ); ?>" inputmode="numeric" pattern="[0-9]{4}" disabled>
				</label>
				<div class="dp-yearnav" role="group" aria-label="<?php esc_attr_e( 'Change year', 'indian-festivals' ); ?>">
					<?php if ( $prev_url ) : ?>
						<a href="<?php echo esc_url( $prev_url ); ?>">‹ <?php esc_html_e( 'Prev Year', 'indian-festivals' ); ?></a>
					<?php else : ?>
						<span class="is-disabled">‹ <?php esc_html_e( 'Prev Year', 'indian-festivals' ); ?></span>
					<?php endif; ?>
					<a href="<?php echo esc_url( $tab_url( $view ) ); ?>"><?php esc_html_e( 'Current Year', 'indian-festivals' ); ?></a>
					<?php if ( $next_url ) : ?>
						<a href="<?php echo esc_url( $next_url ); ?>"><?php esc_html_e( 'Next Year', 'indian-festivals' ); ?> ›</a>
					<?php else : ?>
						<span class="is-disabled"><?php esc_html_e( 'Next Year', 'indian-festivals' ); ?> ›</span>
					<?php endif; ?>
				</div>
			</form>

			<div class="dp-banner">
				<span><?php echo esc_html( $city ); ?></span>
				<span><?php echo esc_html( $year . ' ' . get_the_title() ); ?></span>
			</div>

			<div class="dp-hero-cards">
				<div class="dp-datecard">
					<p class="dp-datecard-name"><?php echo esc_html( $year . ' ' . get_the_title() ); ?></p>
					<p class="dp-datecard-day"><?php echo esc_html( $day . $suffix ); ?></p>
					<p class="dp-datecard-month"><?php echo esc_html( gmdate( 'F Y', $ts ) ); ?></p>
					<p class="dp-datecard-week"><?php echo esc_html( $wd_en[ $wday ] . ' / ' . $wd_hi[ $wday ] ); ?></p>
				</div>
				<div class="dp-imagecard">
					<img src="<?php echo esc_url( $art ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s illustration', 'indian-festivals' ), get_the_title() ) ); ?>" width="520" height="420">
				</div>
			</div>

			<nav class="dp-tabs" aria-label="<?php esc_attr_e( 'Festival pages', 'indian-festivals' ); ?>">
				<a href="<?php echo esc_url( $tab_url( 'timings' ) ); ?>" class="<?php echo 'timings' === $view ? 'is-active' : ''; ?>"><?php esc_html_e( 'Timings', 'indian-festivals' ); ?></a>
				<a href="<?php echo esc_url( $tab_url( 'about' ) ); ?>" class="<?php echo 'about' === $view ? 'is-active' : ''; ?>"><?php echo esc_html( sprintf( __( 'About %s', 'indian-festivals' ), get_the_title() ) ); ?></a>
				<a href="<?php echo esc_url( $tab_url( 'muhurat' ) ); ?>" class="<?php echo 'muhurat' === $view ? 'is-active' : ''; ?>"><?php esc_html_e( 'Muhurat', 'indian-festivals' ); ?></a>
				<a href="<?php echo esc_url( $tab_url( 'summary' ) ); ?>" class="<?php echo 'summary' === $view ? 'is-active' : ''; ?>"><?php esc_html_e( 'Summary', 'indian-festivals' ); ?></a>
			</nav>
		</div>

		<?php if ( 'timings' === $view || 'muhurat' === $view ) : ?>
			<section class="dp-timings" aria-label="<?php echo esc_attr( sprintf( __( '%s timings', 'indian-festivals' ), get_the_title() ) ); ?>" data-time-scope>
				<h2 class="dp-timings-ribbon"><?php echo esc_html( get_the_title() . ' ' . __( 'Timings', 'indian-festivals' ) . ' ' . $year ); ?></h2>
				<div class="dp-timings-body">
					<div class="dp-format" role="group" aria-label="<?php esc_attr_e( 'Time format', 'indian-festivals' ); ?>">
						<span aria-hidden="true">🕐</span>
						<button type="button" class="is-active" data-time-format="12" aria-pressed="true">12 Hour</button>
						<button type="button" data-time-format="24" aria-pressed="false">24 Hour</button>
					</div>

					<p class="dp-timings-on"><?php echo esc_html( get_the_title() ); ?> on <strong><?php echo esc_html( gmdate( 'l, F j, Y', $ts ) ); ?></strong></p>

					<?php if ( $muhurat_label && $muhurat_start ) : ?>
						<div class="dp-muhurat">
							<p class="dp-muhurat-line"><?php echo esc_html( $muhurat_label ); ?> - <span data-time="12"><?php echo esc_html( $muhurat_start ); ?></span> to <span data-time="12"><?php echo esc_html( $muhurat_end ); ?></span></p>
							<?php $dur = ifs_dp_duration( $muhurat_start, $muhurat_end ); ?>
							<?php if ( $dur ) : ?>
								<p class="dp-duration"><?php echo esc_html( 'Duration - ' . $dur ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $avoid_label && $avoid_start ) : ?>
						<div class="dp-muhurat dp-muhurat--avoid">
							<p class="dp-muhurat-line"><?php echo esc_html( $avoid_label ); ?> - <span data-time="12"><?php echo esc_html( $avoid_start ); ?></span> to <span data-time="12"><?php echo esc_html( $avoid_end ); ?></span></p>
							<?php $adur = ifs_dp_duration( $avoid_start, $avoid_end ); ?>
							<?php if ( $adur ) : ?>
								<p class="dp-duration"><?php echo esc_html( 'Duration - ' . $adur ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<dl class="dp-tithis">
						<?php if ( $tithi_start ) : ?>
							<div><dt><?php echo esc_html( $tithi_short . ' ' . __( 'Tithi Begins', 'indian-festivals' ) ); ?></dt><dd data-time-text><?php echo esc_html( $tithi_start ); ?></dd></div>
						<?php endif; ?>
						<?php if ( $tithi_end ) : ?>
							<div><dt><?php echo esc_html( $tithi_short . ' ' . __( 'Tithi Ends', 'indian-festivals' ) ); ?></dt><dd data-time-text><?php echo esc_html( $tithi_end ); ?></dd></div>
						<?php endif; ?>
					</dl>

					<?php if ( 'timings' === $view ) : ?>
						<p class="dp-links">
							<a href="<?php echo esc_url( home_url( '/panchang/' ) ); ?>"><?php echo esc_html( sprintf( __( 'Panchang for %s', 'indian-festivals' ), get_the_title() ) ); ?></a>
							<a href="<?php echo esc_url( home_url( '/panchang/' ) ); ?>"><?php echo esc_html( sprintf( __( 'Choghadiya Muhurat on %s', 'indian-festivals' ), get_the_title() ) ); ?></a>
						</p>
						<?php $katha_title = ifs_meta( 'katha_title', $post_id ); ?>
						<?php if ( $katha_title ) : ?>
							<a href="<?php echo esc_url( $tab_url( 'about' ) . '#katha' ); ?>" class="dp-katha">
								<span class="dp-katha-icon" aria-hidden="true">📖</span>
								<span><strong><?php echo esc_html( sprintf( __( 'Vrat Katha %s — %s', 'indian-festivals' ), get_the_title(), $katha_title ) ); ?></strong><small><?php esc_html_e( 'Read the story behind the festival', 'indian-festivals' ); ?></small></span>
							</a>
						<?php endif; ?>
					<?php endif; ?>

					<p class="dp-notes"><strong><?php esc_html_e( 'Notes:', 'indian-festivals' ); ?></strong> <?php echo esc_html( sprintf( __( 'All timings are shown for %s. Verify with an authentic panchang before observing.', 'indian-festivals' ), $city ) ); ?></p>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( 'about' === $view ) : ?>
			<article class="dp-article" aria-label="<?php echo esc_attr( sprintf( __( 'About %s', 'indian-festivals' ), get_the_title() ) ); ?>">
				<h2><?php echo esc_html( sprintf( __( 'About %s %d', 'indian-festivals' ), get_the_title(), $year ) ); ?></h2>
				<dl class="dp-facts">
					<dt><?php esc_html_e( 'Date', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( gmdate( 'l, F j, Y', $ts ) ); ?></dd>
					<dt><?php esc_html_e( 'Tithi', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( $tithi ? $tithi : '—' ); ?></dd>
					<dt><?php esc_html_e( 'Deity', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( ifs_meta( 'deity', $post_id ) ? ifs_meta( 'deity', $post_id ) : '—' ); ?></dd>
				</dl>
				<div class="entry-content"><?php the_content(); ?></div>
				<?php $sig = ifs_meta( 'significance', $post_id ); ?>
				<?php if ( $sig ) : ?>
					<h3><?php esc_html_e( 'Significance', 'indian-festivals' ); ?></h3>
					<p><?php echo esc_html( $sig ); ?></p>
				<?php endif; ?>
				<?php $rit = ifs_meta( 'rituals', $post_id ); ?>
				<?php if ( $rit ) : ?>
					<h3><?php esc_html_e( 'How it is observed', 'indian-festivals' ); ?></h3>
					<p><?php echo esc_html( $rit ); ?></p>
				<?php endif; ?>
				<?php $kt = ifs_meta( 'katha_title', $post_id ); ?>
				<?php if ( $kt ) : ?>
					<h3 id="katha"><?php echo esc_html( sprintf( __( 'Vrat Katha — %s', 'indian-festivals' ), $kt ) ); ?></h3>
					<p><?php echo esc_html( ifs_meta( 'katha_text', $post_id ) ); ?></p>
				<?php endif; ?>
			</article>
		<?php endif; ?>

		<?php if ( 'muhurat' === $view ) : ?>
			<article class="dp-article" aria-label="<?php echo esc_attr( sprintf( __( '%s muhurat explained', 'indian-festivals' ), get_the_title() ) ); ?>">
				<h2><?php echo esc_html( sprintf( __( '%d %s Muhurat for %s', 'indian-festivals' ), $year, get_the_title(), $city ) ); ?></h2>
				<p><?php echo esc_html( sprintf( __( 'The %s is the most auspicious window for %s observances in %s. Timings shift with sunrise, so use the location box above to check your city.', 'indian-festivals' ), $muhurat_label ? $muhurat_label : __( 'main muhurat', 'indian-festivals' ), get_the_title(), $city ) ); ?></p>
				<p><?php esc_html_e( 'When a muhurat and an avoid-period overlap, priests generally prefer the portion of the muhurat that falls outside it.', 'indian-festivals' ); ?></p>
				<p class="dp-links">
					<a href="<?php echo esc_url( home_url( '/panchang/' ) ); ?>"><?php esc_html_e( 'Full panchang for the day', 'indian-festivals' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/panchang/' ) ); ?>"><?php esc_html_e( 'Choghadiya for the day', 'indian-festivals' ); ?></a>
				</p>
			</article>
		<?php endif; ?>

		<?php if ( 'summary' === $view ) : ?>
			<article class="dp-article" aria-label="<?php echo esc_attr( sprintf( __( '%s summary', 'indian-festivals' ), get_the_title() ) ); ?>">
				<h2><?php echo esc_html( sprintf( __( '%s %d — Summary', 'indian-festivals' ), get_the_title(), $year ) ); ?></h2>
				<dl class="dp-facts">
					<dt><?php esc_html_e( 'Date', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( gmdate( 'l, F j, Y', $ts ) ); ?></dd>
					<dt><?php esc_html_e( 'Tithi', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( $tithi ? $tithi : '—' ); ?></dd>
					<dt><?php esc_html_e( 'Main muhurat', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( trim( $muhurat_label . ' — ' . $muhurat_start . ' to ' . $muhurat_end, ' —' ) ); ?></dd>
					<dt><?php esc_html_e( 'Deity', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( ifs_meta( 'deity', $post_id ) ? ifs_meta( 'deity', $post_id ) : '—' ); ?></dd>
					<dt><?php esc_html_e( 'City', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( $city ); ?></dd>
				</dl>
				<?php $summary = ifs_meta( 'summary', $post_id ); ?>
				<?php if ( $summary ) : ?>
					<p><?php echo esc_html( $summary ); ?></p>
				<?php endif; ?>
			</article>
		<?php endif; ?>

		<nav class="dp-siblings" aria-label="<?php esc_attr_e( 'More pages for this festival', 'indian-festivals' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'festival' ) ); ?>">← <?php esc_html_e( 'All festivals', 'indian-festivals' ); ?></a>
		</nav>
	</main>
	<?php
endwhile;

get_footer();
