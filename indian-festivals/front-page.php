<?php
/**
 * Devotional front page. Elementor-edited pages retain visual editability.
 * When no Elementor layout is supplied, render the reference-inspired scene.
 *
 * @package IndianFestivals
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ifs_front_id = get_queried_object_id();
if ( $ifs_front_id && get_post_meta( $ifs_front_id, '_elementor_edit_mode', true ) ) {
	echo '<main id="main" role="main">';
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	echo '</main>';
	get_footer();
	return;
}

$ifs_art_base = IFS_URI . '/assets/images/';
$ifs_icon_url = IFS_URI . '/assets/images/temple-icons.svg#';
$ifs_today    = ifs_today();
$ifs_date     = new DateTimeImmutable( $ifs_today . ' 12:00:00', new DateTimeZone( 'UTC' ) );
$ifs_city_id  = ifs_city_id_by_name( (string) ifs_opt( 'default_city', 'Varanasi' ) );
$ifs_panchang = ifs_get_panchang( $ifs_today, $ifs_city_id );

$ifs_popular_cards = array(
	array( 'HOLI', 'holi', 'fest-holi.jpg', 'Festival of Colors', '14 MAR' ),
	array( 'DIWALI', 'diwali', 'fest-diwali.jpg', 'Festival of Lights', '20 OCT' ),
	array( 'GANESH CHATURTHI', 'ganesh-chaturthi', 'fest-ganesh.jpg', 'New Beginnings', '27 AUG' ),
	array( 'NAVRATRI', 'navratri', 'fest-navratri.jpg', 'Victory of Good', '22 SEP' ),
	array( 'RAKSHA BANDHAN', 'raksha-bandhan', 'fest-raksha.jpg', 'Bond of Love', '09 AUG' ),
);
$ifs_deity_cards = array(
	array( 'Ganesha', 'ganesha', 'fest-ganesh.jpg', 'Remover of Obstacles' ),
	array( 'Mahadev', 'mahadev', 'deity-shiva.jpg', 'The Destroyer of Ego' ),
	array( 'Vishnu', 'vishnu', 'deity-vishnu.jpg', 'Preserver of Universe' ),
	array( 'Adi Shakti', 'adi-shakti', 'fest-navratri.jpg', 'Source of Energy' ),
	array( 'Hanuman', 'hanuman', 'deity-hanuman.jpg', 'Symbol of Devotion' ),
	array( 'Krishna', 'krishna', 'deity-krishna.jpg', 'Love & Wisdom' ),
);
$ifs_journey = array(
	array( 'Birth', 'lotus' ), array( 'Light', 'diya' ), array( 'Purity', 'conch' ),
	array( 'Prosperity', 'kalash' ), array( 'Divine', 'temple' ),
);
$ifs_panchang_rows = array(
	array( 'moon', __( 'Tithi', 'indian-festivals' ), $ifs_panchang['tithi'] ?? '—' ),
	array( 'star', __( 'Nakshatra', 'indian-festivals' ), $ifs_panchang['nakshatra'] ?? '—' ),
	array( 'yoga', __( 'Yoga', 'indian-festivals' ), $ifs_panchang['yoga'] ?? '—' ),
	array( 'karana', __( 'Karana', 'indian-festivals' ), $ifs_panchang['karana'] ?? '—' ),
	array( 'sunrise', __( 'Sunrise', 'indian-festivals' ), $ifs_panchang['sunrise'] ?? '—' ),
	array( 'sunset', __( 'Sunset', 'indian-festivals' ), $ifs_panchang['sunset'] ?? '—' ),
);
$ifs_upcoming = ifs_upcoming_festivals( 3 );
$ifs_sunday   = $ifs_date->modify( '-' . $ifs_date->format( 'w' ) . ' days' );
?>
<main id="main" class="site-main" role="main">
	<?php if ( ifs_opt( 'home_hero', true ) ) : ?>
		<section class="hero-wrapper reveal-block" aria-labelledby="hero-title">
			<span class="scene-ornament ornament-bell--left" aria-hidden="true"><img src="<?php echo esc_url( $ifs_art_base . 'ornament-bell.svg' ); ?>" alt=""></span>
			<span class="scene-ornament ornament-bell--right" aria-hidden="true"><img src="<?php echo esc_url( $ifs_art_base . 'ornament-bell.svg' ); ?>" alt=""></span>
			<span class="scene-ornament ornament-diya--left" aria-hidden="true"><img src="<?php echo esc_url( $ifs_art_base . 'ornament-diya.svg' ); ?>" alt=""></span>
			<span class="scene-ornament ornament-flower--left" aria-hidden="true"><img src="<?php echo esc_url( $ifs_art_base . 'ornament-marigold.svg' ); ?>" alt=""></span>
			<span class="scene-ornament ornament-flower--right" aria-hidden="true"><img src="<?php echo esc_url( $ifs_art_base . 'ornament-marigold.svg' ); ?>" alt=""></span>

			<div class="note-shadow"><aside class="pinned-note hero-left-note">
				<h4>Celebrate<br>Traditions<br>Keep the<br>Culture Alive</h4>
				<svg class="hero-left-note-icon ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( $ifs_icon_url . 'lotus' ); ?>"></use></svg>
			</aside></div>

			<div class="sheet-shadow"><div class="parchment-sheet hero-center-parchment">
				<div class="hero-copy">
					<p class="hero-subhead">EXPLORE</p>
					<h1 id="hero-title" class="hero-main-title">INDIAN<br>FESTIVALS</h1>
					<span class="hero-title-decoration" aria-hidden="true">◆</span>
					<p class="hero-desc">From ancient rituals to vibrant celebrations — discover the stories, dates and timeless traditions of India's festivals.</p>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'festival' ) ?: home_url( '/festivals/' ) ); ?>" class="hero-center-btn">EXPLORE FESTIVALS <span aria-hidden="true">→</span></a>
				</div>
				<div class="hero-postcard" data-tilt>
					<img src="<?php echo esc_url( $ifs_art_base . 'hero-varanasi.jpg' ); ?>" alt="Illustrated Varanasi ghats at sunset during Ganga Aarti" class="hero-postcard-img" fetchpriority="high" width="540" height="330">
					<span class="hero-postcard-caption">Varanasi · Ganga Aarti</span>
				</div>
			</div></div>

			<div class="hero-right-col"><div class="pocket-watch"><span class="pocket-watch-date" data-live="day"><?php echo esc_html( $ifs_date->format( 'j' ) ); ?></span><span class="pocket-watch-month" data-live="month"><?php echo esc_html( $ifs_date->format( 'F' ) ); ?></span></div><div class="wax-seal">ॐ</div></div>
		</section>
	<?php endif; ?>

	<?php if ( ifs_opt( 'home_popular', true ) || ifs_opt( 'home_panchang', true ) ) : ?>
		<section class="popular-and-panchang-row reveal-block" aria-label="<?php esc_attr_e( 'Popular festivals and panchang', 'indian-festivals' ); ?>">
			<?php if ( ifs_opt( 'home_popular', true ) ) : ?>
				<div class="sheet-shadow"><div class="parchment-sheet popular-parchment">
					<h2 class="golden-ribbon">POPULAR FESTIVALS</h2>
					<p class="ribbon-sub">Colorful Celebrations, Timeless Traditions</p>
					<div class="festival-cards-row">
						<?php foreach ( $ifs_popular_cards as $i => $card ) : ?>
							<?php
							$post     = get_page_by_path( $card[1], OBJECT, 'festival' );
							$img      = $post && has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'ifs-card' ) : $ifs_art_base . $card[2];
							$date     = $post && ifs_meta( 'date', $post->ID ) ? ifs_format_date( ifs_meta( 'date', $post->ID ) ) : $card[4];
							$name     = $post ? get_the_title( $post ) : $card[0];
							$excerpt  = $post && has_excerpt( $post ) ? get_the_excerpt( $post ) : $card[3];
							$link     = $post ? get_permalink( $post ) : get_post_type_archive_link( 'festival' );
							?>
							<div class="paper-card-stage" style="--card-n:<?php echo (int) $i; ?>">
								<a href="<?php echo esc_url( $link ); ?>" class="fest-card torn-card" data-tilt>
									<span class="torn-card-edge" aria-hidden="true"></span>
									<span class="fest-card-frame"><img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" class="fest-card-img" width="280" height="290"></span>
									<span class="fest-card-title"><?php echo esc_html( strtoupper( $name ) ); ?></span>
									<span class="fest-card-date"><svg class="ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( $ifs_icon_url . 'calendar' ); ?>"></use></svg><?php echo esc_html( $date ); ?></span>
									<span class="fest-card-tag"><?php echo esc_html( $excerpt ); ?></span>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
					<p class="sample-dates-note">Illustrative artwork. Dates not attached to published festivals are samples.</p>
				</div></div>
			<?php endif; ?>

			<?php if ( ifs_opt( 'home_panchang', true ) ) : ?>
				<div class="sheet-shadow"><aside class="parchment-sheet panchang-parchment" aria-labelledby="panchang-heading">
					<h2 id="panchang-heading" class="panchang-heading">TODAY'S PANCHANG</h2><div class="panchang-sep"></div>
					<?php foreach ( $ifs_panchang_rows as $row ) : ?>
						<div class="panchang-row"><svg class="panchang-icon ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( $ifs_icon_url . $row[0] ); ?>"></use></svg><span class="panchang-lbl"><?php echo esc_html( $row[1] ); ?></span><span class="panchang-val"><?php echo esc_html( $row[2] ); ?></span></div>
					<?php endforeach; ?>
					<a href="<?php echo esc_url( home_url( '/panchang/' ) ); ?>" class="panchang-btn">VIEW FULL PANCHANG <span aria-hidden="true">→</span></a>
					<small class="panchang-demo-note"><?php echo esc_html( $ifs_panchang ? ifs_opt( 'disclaimer_text', '' ) : __( 'No verified entry for today. Connect a licensed source or add an entry.', 'indian-festivals' ) ); ?></small>
				</aside></div>
			<?php endif; ?>
		</section>
	<?php endif; ?>

	<?php ifs_render_ad('in_content'); ?>

	<?php if ( ifs_opt( 'home_journey', true ) ) : ?>
		<?php get_template_part( 'template-parts/tools-rail' ); ?>
	<?php endif; ?>

	<?php ifs_render_ad('mid'); ?>

	<?php if ( ifs_opt( 'home_deity', true ) ) : ?>
		<section class="deities-and-quote-row reveal-block" aria-labelledby="deity-heading">
			<span class="scene-ornament ornament-flower--deity" aria-hidden="true"><img src="<?php echo esc_url( $ifs_art_base . 'ornament-marigold.svg' ); ?>" alt=""></span>
			<span class="scene-ornament ornament-diya--right" aria-hidden="true"><img src="<?php echo esc_url( $ifs_art_base . 'ornament-diya.svg' ); ?>" alt=""></span>
			<div class="sheet-shadow"><div class="parchment-sheet deity-parchment">
				<h2 id="deity-heading" class="golden-ribbon">EXPLORE BY DEITY</h2><p class="ribbon-sub">Seek Blessings, Find Your Path</p>
				<div class="deity-cards-grid">
					<?php foreach ( $ifs_deity_cards as $i => $card ) : ?>
						<?php
						$deity = get_page_by_path( $card[1], OBJECT, 'deity' );
						$img   = $deity && has_post_thumbnail( $deity ) ? get_the_post_thumbnail_url( $deity, 'ifs-card' ) : $ifs_art_base . $card[2];
						$name  = $deity ? get_the_title( $deity ) : $card[0];
						$role  = $deity && ifs_meta( 'epithets', $deity->ID ) ? ifs_meta( 'epithets', $deity->ID ) : $card[3];
						$link  = $deity ? get_permalink( $deity ) : get_post_type_archive_link( 'deity' );
						?>
						<div class="paper-card-stage" style="--card-n:<?php echo (int) $i; ?>">
							<a href="<?php echo esc_url( $link ); ?>" class="deity-card torn-card" data-tilt>
								<span class="torn-card-edge" aria-hidden="true"></span>
								<span class="deity-frame"><img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" class="deity-img" width="280" height="300"></span>
								<span class="deity-name"><?php echo esc_html( $name ); ?></span><span class="deity-role"><?php echo esc_html( $role ); ?></span>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div></div>
			<div class="note-shadow"><aside class="pinned-note divine-quote-note"><p class="divine-quote-text">Different forms...<br>Same divine light...<br>One eternal truth...</p><svg class="quote-lotus ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( $ifs_icon_url . 'lotus' ); ?>"></use></svg></aside></div>
		</section>
	<?php endif; ?>

	<?php if ( ifs_opt( 'home_calendar', true ) ) : ?>
		<section class="navy-dashboard reveal-block" aria-label="<?php esc_attr_e( 'Festival calendar and quick links', 'indian-festivals' ); ?>">
			<div class="navy-dash-col">
				<h2 class="navy-dash-title"><span>❖</span> FESTIVAL CALENDAR</h2>
				<div class="mini-cal-header"><span data-live="month-year"><?php echo esc_html( $ifs_date->format( 'F Y' ) ); ?></span> <span aria-hidden="true">→</span></div>
				<div class="mini-cal-grid" data-live-week>
					<?php foreach ( array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ) as $day ) : ?><span class="cal-day-name" data-live-dayname><?php echo esc_html( $day ); ?></span><?php endforeach; ?>
					<?php for ( $i = 0; $i < 7; $i++ ) : ?>
						<?php $date_cell = $ifs_sunday->modify( '+' . $i . ' days' ); ?>
						<span data-live-cell class="cal-cell <?php echo $date_cell->format( 'Y-m-d' ) === $ifs_today ? 'today' : ''; ?>" title="<?php echo esc_attr( $date_cell->format( 'j F Y' ) ); ?>"><?php echo esc_html( $date_cell->format( 'j' ) ); ?></span>
					<?php endfor; ?>
				</div>
			</div>

			<div class="navy-dash-col"><h2 class="navy-dash-title"><span>❖</span> UPCOMING FESTIVALS</h2>
				<?php if ( $ifs_upcoming ) : ?>
					<?php foreach ( $ifs_upcoming as $post ) : ?>
						<?php
						$when = ifs_meta( 'date', $post->ID );
						$days = $when ? max( 0, (int) floor( ( strtotime( $when . ' UTC' ) - strtotime( $ifs_today . ' UTC' ) ) / DAY_IN_SECONDS ) ) : 0;
						$img  = get_the_post_thumbnail_url( $post, 'thumbnail' ) ?: $ifs_art_base . 'fest-diwali.jpg';
						?>
						<a class="upcoming-row" href="<?php echo esc_url( get_permalink( $post ) ); ?>"><img src="<?php echo esc_url( $img ); ?>" alt="" class="upcoming-thumb" loading="lazy" width="44" height="44"><span><span class="upcoming-name"><?php echo esc_html( get_the_title( $post ) ); ?></span><span class="upcoming-date"><?php echo esc_html( ifs_format_date( $when ) ); ?></span><span class="days-pill"><?php echo esc_html( sprintf( _n( '%d day left', '%d days left', $days, 'indian-festivals' ), $days ) ); ?></span></span></a>
					<?php endforeach; ?>
				<?php else : ?>
					<p class="navy-empty-note"><?php esc_html_e( 'Add upcoming festival dates in the Festivals dashboard.', 'indian-festivals' ); ?></p>
				<?php endif; ?>
			</div>

			<div class="navy-dash-col"><h2 class="navy-dash-title"><span>❖</span> QUICK LINKS</h2>
				<?php
				$ifs_links = array(
					array( 'calendar', 'Vrat Calendar', get_post_type_archive_link( 'vrat' ) ?: home_url( '/vrats/' ) ),
					array( 'search', 'Muhurat Finder', get_post_type_archive_link( 'muhurat' ) ?: home_url( '/muhurats/' ) ),
					array( 'star', 'Festival Finder', get_post_type_archive_link( 'festival' ) ?: home_url( '/festivals/' ) ),
					array( 'print', 'Printable Calendar', home_url( '/calendar/' ) ),
					array( 'message', 'WhatsApp Alerts', ifs_opt( 'whatsapp_channel', '' ) ?: home_url( '/subscribe/' ) ),
				);
				foreach ( $ifs_links as $link ) :
					?><a class="quick-link-item" href="<?php echo esc_url( $link[2] ); ?>"><svg class="quick-link-icon ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( $ifs_icon_url . $link[0] ); ?>"></use></svg><?php echo esc_html( $link[1] ); ?></a><?php
				endforeach;
				?>
			</div>

			<div class="navy-dash-col scroll-column"><div class="pinned-note sanskrit-scroll-card"><div class="sanskrit-devanagari">सर्वे<br>भवन्तु<br>सुखिनः</div><svg class="scroll-lotus ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( $ifs_icon_url . 'lotus' ); ?>"></use></svg></div></div>
		</section>
	<?php endif; ?>
	<?php ifs_render_ad('footer'); ?>
</main>
<?php
get_footer();
