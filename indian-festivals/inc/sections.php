<?php
/**
 * Home page sections. Each renderer echoes HTML so it can be used by the
 * front page, shortcodes and Elementor widgets.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Section key => [renderer, toggle option].
 *
 * @return array
 */
function ifs_home_section_map() {
	return array(
		'hero'       => array( 'ifs_section_hero', 'home_hero' ),
		'info_strip' => array( 'ifs_section_info_strip', 'home_info_strip' ),
		'popular'    => array( 'ifs_section_popular', '' ),
		'journey'    => array( 'ifs_section_journey', 'home_journey' ),
		'deity'      => array( 'ifs_section_deity', 'home_deity' ),
		'story'      => array( 'ifs_section_story', 'home_story' ),
		'vrat'       => array( 'ifs_section_vrat', 'home_vrat' ),
		'tools'      => array( 'ifs_section_tools', 'home_tools' ),
		'calendar'   => array( 'ifs_section_calendar', 'home_calendar' ),
		'articles'   => array( 'ifs_section_articles', 'home_articles' ),
		'greetings'  => array( 'ifs_section_greetings', 'home_greetings' ),
		'stories'    => array( 'ifs_section_stories', 'home_stories' ),
		'subscribe'  => array( 'ifs_section_subscribe', 'home_subscribe' ),
	);
}

/**
 * Render the home page in the order chosen in the Customizer.
 */
function ifs_render_home() {
	$map   = ifs_home_section_map();
	$order = array_filter( array_map( 'trim', explode( ',', (string) ifs_opt( 'home_order', '' ) ) ) );
	$ads   = (bool) ifs_opt( 'home_ads', true );

	foreach ( $order as $key ) {
		if ( ! isset( $map[ $key ] ) ) {
			continue;
		}
		list( $renderer, $toggle ) = $map[ $key ];
		if ( $toggle && ! ifs_opt( $toggle, true ) ) {
			continue;
		}
		echo '<div class="ifs-section ifs-section-' . esc_attr( $key ) . '">';
		call_user_func( $renderer );
		echo '</div>';

		if ( $ads && in_array( $key, array( 'popular', 'deity', 'tools' ), true ) ) {
			ifs_ad_slot( 'in_content' );
		}
	}
}

/**
 * Ad slot. Only renders when AdSense code is set for the slot.
 *
 * @param string $slot header | in_content | sidebar | footer.
 */
function ifs_ad_slot( $slot = 'in_content' ) {
	$code = (string) ifs_opt( 'ad_' . $slot, '' );
	if ( '' === trim( $code ) ) {
		return;
	}
	// Output is admin-supplied AdSense markup. See ifs_sanitize_raw_code().
	echo '<div class="ad-slot" data-slot="' . esc_attr( $slot ) . '">' . $code . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/* ---------- Shared data helpers ---------- */

/**
 * Festivals on or after today, soonest first.
 *
 * @param int $limit Max count.
 * @return WP_Post[]
 */
function ifs_upcoming_festivals( $limit = 5 ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'festival',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'no_found_rows'  => true,
			'meta_key'       => '_ifs_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_ifs_date',
					'value'   => ifs_today(),
					'compare' => '>=',
					'type'    => 'DATE',
				),
			),
		)
	);
	return $query->posts;
}

/**
 * Festival dated today, if any.
 *
 * @return WP_Post|null
 */
function ifs_todays_festival() {
	$posts = get_posts(
		array(
			'post_type'      => 'festival',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_ifs_date',
					'value' => ifs_today(),
				),
			),
		)
	);
	return $posts ? $posts[0] : null;
}

/**
 * Upcoming vrats, one per vrat type.
 *
 * @param int $limit Max count.
 * @return WP_Post[]
 */
function ifs_next_vrats( $limit = 6 ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'vrat',
			'post_status'    => 'publish',
			'posts_per_page' => 60,
			'no_found_rows'  => true,
			'meta_key'       => '_ifs_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_ifs_date',
					'value'   => ifs_today(),
					'compare' => '>=',
					'type'    => 'DATE',
				),
			),
		)
	);
	$seen = array();
	$out  = array();
	foreach ( $query->posts as $post ) {
		$type = ifs_meta( 'vrat_type', $post->ID );
		if ( isset( $seen[ $type ] ) ) {
			continue;
		}
		$seen[ $type ] = true;
		$out[]         = $post;
		if ( count( $out ) >= $limit ) {
			break;
		}
	}
	return $out;
}

/**
 * Icon for a festival name (used when no image is set).
 *
 * @param string $name Festival name.
 * @return string
 */
function ifs_festival_icon( $name ) {
	$n    = strtolower( $name );
	$map  = array(
		'holi'        => '🎨',
		'diwali'      => '🪔',
		'ganesh'      => '🐘',
		'navratri'    => '🔱',
		'raksha'      => '🧵',
		'rath'        => '🛞',
		'janmashtami' => '🦚',
		'akshaya'     => '🏺',
		'buddha'      => '☸',
	);
	foreach ( $map as $needle => $icon ) {
		if ( false !== strpos( $n, $needle ) ) {
			return $icon;
		}
	}
	return '🪷';
}

/**
 * Background attribute for a card: featured image or gradient fallback.
 *
 * @param int $post_id Post ID.
 * @param int $index   Index for gradient rotation.
 * @return string Inline style declaration.
 */
function ifs_art_attr( $post_id, $index = 0 ) {
	$url = get_the_post_thumbnail_url( $post_id, 'ifs-card' );
	if ( $url ) {
		return 'background-image:url(' . esc_url( $url ) . ')';
	}
	$grads = array(
		'linear-gradient(135deg,#e8448a,#f5a623 35%,#3fb8e8 70%,#7a3fd1)',
		'linear-gradient(160deg,#ffb347,#c2410c 60%,#5b1d05)',
		'linear-gradient(160deg,#f59e0b,#b45309 60%,#4a2508)',
		'linear-gradient(160deg,#dc2626,#7f1d1d 60%,#2d0a0a)',
		'linear-gradient(160deg,#facc15,#c2410c 60%,#6b2a0a)',
	);
	return 'background:' . $grads[ $index % count( $grads ) ];
}

/**
 * Short text for cards.
 *
 * @param WP_Post $post Post.
 * @param int     $words Word count.
 * @return string
 */
function ifs_excerpt_words( $post, $words = 12 ) {
	$text = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	return wp_trim_words( wp_strip_all_tags( $text ), $words );
}

/**
 * Date label for a festival (formatted in site format).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function ifs_festival_date_label( $post_id ) {
	return ifs_format_date( ifs_meta( 'date', $post_id ) );
}

/* ---------- Sections ---------- */

/**
 * Hero with the next festival and two main buttons.
 */
function ifs_section_hero() {
	$next     = ifs_upcoming_festivals( 1 );
	$festival = $next ? $next[0] : null;
	$img      = file_exists( IFS_DIR . '/assets/images/hero-varanasi.jpg' )
		? IFS_URI . '/assets/images/hero-varanasi.jpg'
		: '';
	if ( has_post_thumbnail( get_option( 'page_on_front' ) ) ) {
		$img = get_the_post_thumbnail_url( get_option( 'page_on_front' ), 'ifs-wide' );
	}
	$days = null;
	if ( $festival ) {
		$days = (int) floor( ( strtotime( ifs_meta( 'date', $festival->ID ) . ' UTC' ) - strtotime( ifs_today() . ' UTC' ) ) / DAY_IN_SECONDS );
	}
	?>
	<section class="paper hero" aria-labelledby="hero-title">
		<div class="hero-copy">
			<p class="eyebrow"><?php esc_html_e( 'EXPLORE', 'indian-festivals' ); ?></p>
			<h1 id="hero-title"><?php esc_html_e( 'INDIAN FESTIVALS', 'indian-festivals' ); ?></h1>
			<p><?php esc_html_e( "From ancient rituals to vibrant celebrations — discover the stories, dates and timeless traditions of India's festivals.", 'indian-festivals' ); ?></p>
			<a class="btn" href="<?php echo esc_url( get_post_type_archive_link( 'festival' ) ); ?>"><?php esc_html_e( 'Explore Festivals →', 'indian-festivals' ); ?></a>
			<?php if ( $festival ) : ?>
				<p class="hero-next">
					<?php
					printf(
						/* translators: 1: festival name, 2: date, 3: days */
						esc_html__( 'Next: %1$s on %2$s · %3$s', 'indian-festivals' ),
						'<strong>' . esc_html( $festival->post_title ) . '</strong>',
						esc_html( ifs_festival_date_label( $festival->ID ) ),
						$days >= 0 ? esc_html( sprintf( _n( '%d day to go', '%d days to go', $days, 'indian-festivals' ), $days ) ) : esc_html__( 'today', 'indian-festivals' ) // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					);
					?>
				</p>
			<?php endif; ?>
		</div>
		<div class="hero-photo">
			<?php if ( $img ) : ?>
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php esc_attr_e( 'Varanasi ghats during Ganga Aarti', 'indian-festivals' ); ?>" <?php echo ifs_opt( 'perf_lazy', true ) ? 'loading="lazy"' : ''; ?> width="600" height="300">
			<?php endif; ?>
			<span class="next-badge"><?php echo esc_html( $festival ? __( 'NEXT', 'indian-festivals' ) . ' · ' . ifs_festival_date_label( $festival->ID ) : __( 'FESTIVALS', 'indian-festivals' ) ); ?></span>
			<span class="caption"><?php echo esc_html( ifs_opt( 'default_city', 'Varanasi' ) ); ?> · <?php esc_html_e( 'Ganga Aarti', 'indian-festivals' ); ?></span>
		</div>
	</section>
	<?php
}

/**
 * Info strip: today's festival, tithi, next vrat, Rahu Kaal, city.
 */
function ifs_section_info_strip() {
	$city_name = ifs_opt( 'default_city', 'Varanasi' );
	$city_id   = ifs_city_id_by_name( $city_name );
	$today     = ifs_today();
	$panchang  = ifs_get_panchang( $today, $city_id );
	$fest      = ifs_todays_festival();
	$vrats     = ifs_next_vrats( 1 );
	$vrat      = $vrats ? $vrats[0] : null;
	$timings   = $panchang ? ifs_day_timings( $panchang['sunrise'], $panchang['sunset'], ifs_weekday( $today ) ) : array();

	$tithi = $fest ? ifs_meta( 'tithi', $fest->ID ) : '';
	if ( ! $tithi && $panchang ) {
		$tithi = $panchang['tithi'];
	}
	?>
	<div class="info-strip" role="region" aria-label="<?php esc_attr_e( 'Today at a glance', 'indian-festivals' ); ?>">
		<span><?php esc_html_e( "Today's festival:", 'indian-festivals' ); ?> <strong><?php echo esc_html( $fest ? $fest->post_title : __( 'None', 'indian-festivals' ) ); ?></strong></span>
		<span><?php esc_html_e( 'Tithi:', 'indian-festivals' ); ?> <strong><?php echo esc_html( $tithi ? $tithi : '—' ); ?></strong></span>
		<span><?php esc_html_e( 'Next vrat:', 'indian-festivals' ); ?> <strong><?php echo esc_html( $vrat ? get_the_title( $vrat ) . ' · ' . ifs_format_date( ifs_meta( 'date', $vrat->ID ) ) : '—' ); ?></strong></span>
		<span><?php esc_html_e( 'Rahu Kaal:', 'indian-festivals' ); ?> <strong><?php echo esc_html( isset( $timings['rahu_kaal'] ) ? implode( ' – ', $timings['rahu_kaal'] ) : '—' ); ?></strong></span>
		<span><?php esc_html_e( 'City:', 'indian-festivals' ); ?> <strong><?php echo esc_html( $city_name ); ?></strong></span>
	</div>
	<?php
}

/**
 * Popular festival cards and the Today's Panchang panel side by side.
 */
function ifs_section_popular() {
	$show_popular = (bool) ifs_opt( 'home_popular', true );
	$show_panchang = (bool) ifs_opt( 'home_panchang', true );
	if ( ! $show_popular && ! $show_panchang ) {
		return;
	}
	$both_class = ( $show_popular && $show_panchang ) ? '' : ' single';
	echo '<div class="two-col' . esc_attr( $both_class ) . '">';

	if ( $show_popular ) {
		echo '<section class="paper section-block" aria-labelledby="popular-title">';
		echo '<h2 id="popular-title" class="ribbon">' . esc_html__( 'Popular Festivals', 'indian-festivals' ) . '</h2>';
		echo '<p class="section-sub">' . esc_html__( 'Colorful Celebrations, Timeless Traditions', 'indian-festivals' ) . '</p>';
		ifs_festival_cards( 5 );
		echo '</section>';
	}

	if ( $show_panchang ) {
		ifs_panchang_panel();
	}

	echo '</div>';
}

/**
 * Festival cards grid.
 *
 * @param int $count Number of cards.
 */
function ifs_festival_cards( $count = 5 ) {
	$posts = ifs_upcoming_festivals( $count );
	if ( ! $posts ) {
		$posts = get_posts(
			array(
				'post_type'      => 'festival',
				'post_status'    => 'publish',
				'posts_per_page' => $count,
				'no_found_rows'  => true,
				'meta_key'       => '_ifs_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'orderby'        => 'meta_value',
				'order'          => 'DESC',
			)
		);
	}
	if ( ! $posts ) {
		echo '<p class="notice-box">' . esc_html__( 'No festivals yet. Add festivals under Festivals in the dashboard.', 'indian-festivals' ) . '</p>';
		return;
	}

	$sample_art = array(
		'holi' => 'fest-holi.jpg', 'diwali' => 'fest-diwali.jpg',
		'ganesh-chaturthi' => 'fest-ganesh.jpg', 'navratri' => 'fest-navratri.jpg',
		'raksha-bandhan' => 'fest-raksha.jpg',
	);
	echo '<div class="festival-grid">';
	foreach ( $posts as $i => $post ) {
		$slug = $post->post_name;
		$img  = get_the_post_thumbnail_url( $post, 'ifs-card' ) ?: IFS_URI . '/assets/images/' . ( $sample_art[ $slug ] ?? 'hero-varanasi.jpg' );
		?>
		<div class="paper-card-stage" style="--card-n:<?php echo (int) $i; ?>">
			<a class="fest-card torn-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>" data-tilt>
				<span class="torn-card-edge" aria-hidden="true"></span>
				<span class="fest-card-frame"><img class="fest-card-img" src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" width="280" height="280"></span>
				<span class="fest-card-title"><?php echo esc_html( strtoupper( $post->post_title ) ); ?></span>
				<span class="fest-card-date"><svg class="ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( IFS_URI . '/assets/images/temple-icons.svg#calendar' ); ?>"></use></svg><?php echo esc_html( ifs_festival_date_label( $post->ID ) ); ?></span>
				<span class="fest-card-tag"><?php echo esc_html( ifs_excerpt_words( $post, 8 ) ); ?></span>
			</a>
		</div>
		<?php
	}
	echo '</div>';
}

/**
 * Today's Panchang panel.
 */
function ifs_panchang_panel() {
	$city_name = ifs_opt( 'default_city', 'Varanasi' );
	$city_id   = ifs_city_id_by_name( $city_name );
	$today     = ifs_today();
	$p         = ifs_get_panchang( $today, $city_id );
	$timings   = $p ? ifs_day_timings( $p['sunrise'], $p['sunset'], ifs_weekday( $today ) ) : array();
	?>
	<aside class="paper paper-note panchang" aria-labelledby="panchang-title">
		<h3 id="panchang-title"><?php esc_html_e( "TODAY'S PANCHANG", 'indian-festivals' ); ?></h3>
		<div class="divider"></div>
		<?php if ( $p ) : ?>
			<?php
			$rows = array(
				array( '🌙', __( 'Tithi', 'indian-festivals' ), $p['tithi'] ),
				array( '⭐', __( 'Nakshatra', 'indian-festivals' ), $p['nakshatra'] ),
				array( '☯', __( 'Yoga', 'indian-festivals' ), $p['yoga'] ),
				array( '◐', __( 'Karana', 'indian-festivals' ), $p['karana'] ),
				array( '🌅', __( 'Sunrise', 'indian-festivals' ), $p['sunrise'] ),
				array( '🌇', __( 'Sunset', 'indian-festivals' ), $p['sunset'] ),
			);
			if ( $p['moonrise'] ) {
				$rows[] = array( '🌕', __( 'Moonrise', 'indian-festivals' ), $p['moonrise'] );
			}
			if ( $timings ) {
				$rows[] = array( '⚠', __( 'Rahu Kaal', 'indian-festivals' ), implode( ' – ', $timings['rahu_kaal'] ) );
			}
			foreach ( $rows as $row ) :
				?>
				<div class="prow">
					<span class="ico" aria-hidden="true"><?php echo esc_html( $row[0] ); ?></span>
					<span class="label"><?php echo esc_html( $row[1] ); ?></span>
					<span class="val"><?php echo esc_html( $row[2] ? $row[2] : '—' ); ?></span>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<p class="small-note"><?php esc_html_e( 'No panchang entry for today yet. Add one under Panchang Entries or connect a licensed API in Theme: Festival and Panchang.', 'indian-festivals' ); ?></p>
		<?php endif; ?>
		<a class="btn btn-block" href="<?php echo esc_url( home_url( '/panchang/' ) ); ?>"><?php esc_html_e( 'View Full Panchang →', 'indian-festivals' ); ?></a>
		<p class="small-note"><?php echo esc_html( ifs_opt( 'disclaimer_text', '' ) ); ?></p>
	</aside>
	<?php
}

/**
 * Festival Journey: upcoming festivals as a path.
 */
function ifs_section_journey() {
	$icons = array( '🪷', '🪔', '🐚', '🏺', '🛕', '🎉', '🙏', '🌺' );
	$posts = ifs_upcoming_festivals( 5 );
	?>
	<section class="journey" aria-labelledby="journey-title">
		<h2 id="journey-title" class="ribbon"><?php esc_html_e( 'The Festival Journey', 'indian-festivals' ); ?></h2>
		<p class="section-sub" style="color:#f1e2b8"><?php esc_html_e( 'Upcoming festivals, one step at a time', 'indian-festivals' ); ?></p>
		<?php if ( $posts ) : ?>
			<div class="journey-track">
				<?php foreach ( $posts as $i => $post ) : ?>
					<div class="jstep">
						<a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
							<div class="jicon" aria-hidden="true"><?php echo esc_html( $icons[ $i % count( $icons ) ] ); ?></div>
							<div><?php echo esc_html( $post->post_title ); ?></div>
							<small><?php echo esc_html( ifs_festival_date_label( $post->ID ) ); ?></small>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="small-note" style="color:#f1e2b8"><?php esc_html_e( 'Upcoming festivals will appear here.', 'indian-festivals' ); ?></p>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Browse by deity bands plus a quote note.
 */
function ifs_section_deity() {
	$fallback = array(
		array( 'Ganesha', 'ganesha', 'Remover of Obstacles', 'fest-ganesh.jpg' ),
		array( 'Mahadev', 'mahadev', 'The Destroyer of Ego', 'deity-shiva.jpg' ),
		array( 'Vishnu', 'vishnu', 'Preserver of Universe', 'deity-vishnu.jpg' ),
		array( 'Adi Shakti', 'adi-shakti', 'Source of Energy', 'fest-navratri.jpg' ),
		array( 'Hanuman', 'hanuman', 'Symbol of Devotion', 'deity-hanuman.jpg' ),
		array( 'Krishna', 'krishna', 'Love & Wisdom', 'deity-krishna.jpg' ),
	);
	$deities = get_posts( array( 'post_type' => 'deity', 'post_status' => 'publish', 'posts_per_page' => 6, 'orderby' => 'menu_order title', 'order' => 'ASC', 'no_found_rows' => true ) );
	$cards   = array();
	if ( $deities ) {
		foreach ( $deities as $post ) {
			$match = array_filter( $fallback, function ( $item ) use ( $post ) { return $item[1] === $post->post_name; } );
			$art   = $match ? array_values( $match )[0][3] : 'hero-varanasi.jpg';
			$cards[] = array( $post->post_title, ifs_meta( 'epithets', $post->ID ), get_the_post_thumbnail_url( $post, 'ifs-card' ) ?: IFS_URI . '/assets/images/' . $art, get_permalink( $post ) );
		}
	} else {
		foreach ( $fallback as $item ) {
			$cards[] = array( $item[0], $item[2], IFS_URI . '/assets/images/' . $item[3], get_post_type_archive_link( 'deity' ) );
		}
	}
	?>
	<section class="deities-and-quote-row reveal-block" aria-labelledby="deity-title">
		<div class="sheet-shadow"><div class="parchment-sheet deity-parchment">
			<h2 id="deity-title" class="golden-ribbon"><?php esc_html_e( 'Explore by Deity', 'indian-festivals' ); ?></h2>
			<p class="ribbon-sub"><?php esc_html_e( 'Seek Blessings, Find Your Path', 'indian-festivals' ); ?></p>
			<div class="deity-cards-grid">
				<?php foreach ( $cards as $i => $card ) : ?>
					<div class="paper-card-stage" style="--card-n:<?php echo (int) $i; ?>"><a class="deity-card torn-card" href="<?php echo esc_url( $card[3] ); ?>" data-tilt>
						<span class="torn-card-edge" aria-hidden="true"></span>
						<span class="deity-frame"><img class="deity-img" src="<?php echo esc_url( $card[2] ); ?>" alt="" loading="lazy" width="280" height="300"></span>
						<span class="deity-name"><?php echo esc_html( $card[0] ); ?></span><span class="deity-role"><?php echo esc_html( $card[1] ); ?></span>
					</a></div>
				<?php endforeach; ?>
			</div>
		</div></div>
		<div class="note-shadow"><aside class="pinned-note divine-quote-note" aria-label="<?php esc_attr_e( 'Quote', 'indian-festivals' ); ?>">
			<p class="divine-quote-text"><?php esc_html_e( 'Different forms…', 'indian-festivals' ); ?><br><?php esc_html_e( 'Same divine light…', 'indian-festivals' ); ?><br><?php esc_html_e( 'One eternal truth…', 'indian-festivals' ); ?></p>
			<svg class="quote-lotus ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( IFS_URI . '/assets/images/temple-icons.svg#lotus' ); ?>"></use></svg>
		</aside></div>
	</section>
	<?php
}

/**
 * Today's story: steps, facts and a quote for today's (or the next) festival.
 */
function ifs_section_story() {
	$festival = ifs_todays_festival();
	if ( ! $festival ) {
		$next     = ifs_upcoming_festivals( 1 );
		$festival = $next ? $next[0] : null;
	}
	if ( ! $festival ) {
		return;
	}
	$muhurat = ifs_meta( 'puja_muhurat', $festival->ID );
	?>
	<section class="paper section-block story" aria-labelledby="story-title">
		<div>
			<h2 id="story-title" class="ribbon" style="font-size:17px"><?php esc_html_e( "Today's Story", 'indian-festivals' ); ?></h2>
			<h3 style="margin-top:14px"><?php echo esc_html( $festival->post_title ); ?></h3>
			<p><?php echo esc_html( ifs_excerpt_words( $festival, 40 ) ); ?></p>
			<ol>
				<li><?php esc_html_e( 'Prepare: clean the home and the puja space before sunrise.', 'indian-festivals' ); ?></li>
				<li><?php echo esc_html( sprintf( __( 'Observe: perform puja during the muhurat %s.', 'indian-festivals' ), $muhurat ? '(' . $muhurat . ')' : __( '(check your panchang)', 'indian-festivals' ) ) ); ?></li>
				<li><?php esc_html_e( 'Share: greetings, prasad and charity in the spirit of the day.', 'indian-festivals' ); ?></li>
			</ol>
		</div>
		<div>
			<dl class="facts">
				<dt><?php esc_html_e( 'Date', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( ifs_festival_date_label( $festival->ID ) ); ?></dd>
				<dt><?php esc_html_e( 'Tithi', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( ifs_meta( 'tithi', $festival->ID ) ?: '—' ); ?></dd>
				<dt><?php esc_html_e( 'Deity', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( ifs_meta( 'deity', $festival->ID ) ?: '—' ); ?></dd>
				<dt><?php esc_html_e( 'Region', 'indian-festivals' ); ?></dt><dd><?php echo esc_html( implode( ', ', wp_list_pluck( get_the_terms( $festival->ID, 'ifs_region' ) ?: array(), 'name' ) ) ?: '—' ); ?></dd>
			</dl>
			<p class="quote">“<?php echo esc_html( ifs_opt( 'story_quote', '' ) ); ?>”</p>
		</div>
	</section>
	<?php
}

/**
 * Vrat and fasting scroll cards.
 */
function ifs_section_vrat() {
	$vrats = ifs_next_vrats( 6 );
	?>
	<section class="paper section-block" aria-labelledby="vrat-title">
		<h2 id="vrat-title" class="ribbon" style="font-size:17px"><?php esc_html_e( 'Vrat & Fasting Days', 'indian-festivals' ); ?></h2>
		<p class="section-sub"><?php esc_html_e( 'Next date for each vrat type', 'indian-festivals' ); ?></p>
		<?php if ( $vrats ) : ?>
			<div class="vrat-row" tabindex="0" aria-label="<?php esc_attr_e( 'Vrat cards, scroll sideways', 'indian-festivals' ); ?>">
				<?php foreach ( $vrats as $vrat ) : ?>
					<a class="vcard" href="<?php echo esc_url( get_permalink( $vrat ) ); ?>">
						<div class="vt"><?php echo esc_html( strtoupper( ifs_meta( 'vrat_type', $vrat->ID ) ) ); ?></div>
						<div class="vd"><?php echo esc_html( ifs_format_date( ifs_meta( 'date', $vrat->ID ) ) ); ?></div>
						<div class="vp"><?php echo esc_html( sprintf( __( 'Parana: %s', 'indian-festivals' ), ifs_meta( 'parana_time', $vrat->ID ) ?: '—' ) ); ?></div>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="small-note"><?php esc_html_e( 'Add vrat dates under Vrats in the dashboard.', 'indian-festivals' ); ?></p>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Tools grid. Each tool can be shown or hidden in the Customizer.
 */
function ifs_section_tools() {
	$icons = array(
		'panchang'       => '🕉',
		'panchang_date'  => '📅',
		'date_finder'    => '🔎',
		'vrat_calendar'  => '🌙',
		'choghadiya'     => '⏳',
		'rahu_kaal'      => '⚠',
		'abhijit'        => '☀',
		'muhurat_finder' => '💍',
		'moonrise'       => '🌕',
		'eclipse'        => '🌑',
		'holiday_list'   => '🏛',
		'wish_generator' => '✉',
		'countdown'      => '⏱',
		'add_calendar'   => '📆',
		'printable'      => '🖨',
		'reminder'       => '🔔',
		'embed'          => '🧩',
		'date_converter' => '🔄',
	);
	$tools = ifs_tools();
	?>
	<section class="paper section-block" aria-labelledby="tools-title">
		<h2 id="tools-title" class="ribbon" style="font-size:17px"><?php esc_html_e( 'Tools', 'indian-festivals' ); ?></h2>
		<p class="section-sub"><?php esc_html_e( 'Panchang, muhurat and calendar tools', 'indian-festivals' ); ?></p>
		<div class="tools-grid">
			<?php foreach ( $tools as $key => $label ) : ?>
				<?php if ( ! ifs_opt( 'tool_' . $key, true ) ) : continue; endif; ?>
				<a class="tool" href="<?php echo esc_url( home_url( '/tools/' . str_replace( '_', '-', $key ) . '/' ) ); ?>">
					<span class="ti" aria-hidden="true"><?php echo esc_html( $icons[ $key ] ); ?></span>
					<strong><?php echo esc_html( $label ); ?></strong>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}

/**
 * Navy band: mini calendar, upcoming festivals, quick links and a Sanskrit scroll.
 */
function ifs_section_calendar() {
	$tz    = new DateTimeZone( ifs_opt( 'default_timezone', 'Asia/Kolkata' ) );
	$now   = new DateTime( 'now', $tz );
	$year  = (int) $now->format( 'Y' );
	$month = (int) $now->format( 'n' );
	$today = (int) $now->format( 'j' );
	$first = new DateTime( sprintf( '%04d-%02d-01', $year, $month ), $tz );
	$days  = (int) $first->format( 't' );
	$dow   = (int) $first->format( 'w' );
	$start = (int) ifs_opt( 'calendar_start', 0 );

	$labels = $start ? array( 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ) : array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
	$offset = $start ? ( ( $dow + 6 ) % 7 ) : $dow;

	$month_start = sprintf( '%04d-%02d-01', $year, $month );
	$month_end   = sprintf( '%04d-%02d-%02d', $year, $month, $days );
	$in_month    = get_posts(
		array(
			'post_type'      => 'festival',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_ifs_date',
					'value'   => array( $month_start, $month_end ),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				),
			),
		)
	);
	$fest_days = array();
	foreach ( $in_month as $id ) {
		$fest_days[] = (int) substr( ifs_meta( 'date', $id ), 8, 2 );
	}

	$quick = array(
		array( '📅', __( 'Vrat Calendar', 'indian-festivals' ), home_url( '/vrat/' ) ),
		array( '🔎', __( 'Muhurat Finder', 'indian-festivals' ), home_url( '/muhurat/' ) ),
		array( '🧭', __( 'Festival Finder', 'indian-festivals' ), home_url( '/festivals/' ) ),
		array( '🖨', __( 'Printable Calendar', 'indian-festivals' ), home_url( '/calendar/' ) ),
		array( '💬', __( 'WhatsApp Alerts', 'indian-festivals' ), ifs_opt( 'whatsapp_channel', '' ) ? ifs_opt( 'whatsapp_channel', '' ) : home_url( '/subscribe/' ) ),
	);
	?>
	<section class="navy-band" aria-label="<?php esc_attr_e( 'Calendar, upcoming festivals and quick links', 'indian-festivals' ); ?>">
		<div class="navy-col">
			<h3><?php esc_html_e( 'FESTIVAL CALENDAR', 'indian-festivals' ); ?></h3>
			<div class="mini-cal">
				<div class="mhead"><?php echo esc_html( wp_date( 'F Y', $now->getTimestamp(), $tz ) ); ?></div>
				<div class="grid7">
					<?php foreach ( $labels as $label ) : ?>
						<span class="dow"><?php echo esc_html( $label ); ?></span>
					<?php endforeach; ?>
					<?php for ( $i = 0; $i < $offset; $i++ ) : ?>
						<span></span>
					<?php endfor; ?>
					<?php for ( $d = 1; $d <= $days; $d++ ) : ?>
						<?php
						$classes = array( 'day' );
						if ( $d === $today ) {
							$classes[] = 'today';
						}
						if ( in_array( $d, $fest_days, true ) ) {
							$classes[] = 'fest';
						}
						?>
						<span class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"><?php echo (int) $d; ?></span>
					<?php endfor; ?>
				</div>
			</div>
		</div>

		<div class="navy-col">
			<h3><?php esc_html_e( 'UPCOMING FESTIVALS', 'indian-festivals' ); ?></h3>
			<?php foreach ( ifs_upcoming_festivals( 4 ) as $post ) : ?>
				<?php $days_left = (int) floor( ( strtotime( ifs_meta( 'date', $post->ID ) . ' UTC' ) - strtotime( ifs_today() . ' UTC' ) ) / DAY_IN_SECONDS ); ?>
				<a class="ufest" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
					<span class="thumb" aria-hidden="true"><?php echo esc_html( ifs_festival_icon( $post->post_title ) ); ?></span>
					<span>
						<span class="t" style="display:block"><?php echo esc_html( $post->post_title ); ?></span>
						<span class="d" style="display:block"><?php echo esc_html( ifs_festival_date_label( $post->ID ) ); ?></span>
						<span class="pill"><?php echo esc_html( sprintf( _n( '%d day left', '%d days left', max( 0, $days_left ), 'indian-festivals' ), max( 0, $days_left ) ) ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>

		<div class="navy-col qlinks">
			<h3><?php esc_html_e( 'QUICK LINKS', 'indian-festivals' ); ?></h3>
			<?php foreach ( $quick as $q ) : ?>
				<a href="<?php echo esc_url( $q[2] ); ?>"><span class="i" aria-hidden="true"><?php echo esc_html( $q[0] ); ?></span><?php echo esc_html( $q[1] ); ?></a>
			<?php endforeach; ?>
		</div>

		<div class="navy-col scroll-col">
			<div class="scroll-note">
				<div style="font-family:'Noto Sans Devanagari',serif;font-size:18px;margin-bottom:6px"><?php echo esc_html( ifs_opt( 'footer_sanskrit', '' ) ); ?></div>
				<?php echo esc_html( ifs_opt( 'footer_sanskrit_en', '' ) ); ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Latest blog articles.
 */
function ifs_section_articles() {
	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'no_found_rows'  => true,
		)
	);
	?>
	<section class="paper section-block" aria-labelledby="articles-title">
		<h2 id="articles-title" class="ribbon" style="font-size:17px"><?php esc_html_e( 'Latest Articles', 'indian-festivals' ); ?></h2>
		<div class="grid-3" style="margin-top:16px">
			<?php foreach ( $posts as $i => $post ) : ?>
				<a class="ccard" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
					<div class="thumb" style="<?php echo esc_attr( ifs_art_attr( $post->ID, $i ) ); ?>"></div>
					<h3><?php echo esc_html( $post->post_title ); ?></h3>
					<p><?php echo esc_html( ifs_excerpt_words( $post, 18 ) ); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
		<?php if ( ! $posts ) : ?>
			<p class="small-note"><?php esc_html_e( 'Articles will appear here once published.', 'indian-festivals' ); ?></p>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Greetings gallery.
 */
function ifs_section_greetings() {
	$posts = get_posts(
		array(
			'post_type'      => 'greeting',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
		)
	);
	?>
	<section class="paper section-block" aria-labelledby="greet-title">
		<h2 id="greet-title" class="ribbon" style="font-size:17px"><?php esc_html_e( 'Greetings Gallery', 'indian-festivals' ); ?></h2>
		<div class="grid-6" style="margin-top:16px">
			<?php foreach ( $posts as $i => $post ) : ?>
				<a class="ccard" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
					<div class="thumb" style="<?php echo esc_attr( ifs_art_attr( $post->ID, $i ) ); ?>"></div>
					<h3><?php echo esc_html( $post->post_title ); ?></h3>
				</a>
			<?php endforeach; ?>
		</div>
		<p style="text-align:center;margin-top:14px"><a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/wishes/' ) ); ?>"><?php esc_html_e( 'Create a wish', 'indian-festivals' ); ?></a></p>
	</section>
	<?php
}

/**
 * Web stories row.
 */
function ifs_section_stories() {
	$posts = get_posts(
		array(
			'post_type'      => 'web_story',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
		)
	);
	?>
	<section class="paper section-block" aria-labelledby="stories-title">
		<h2 id="stories-title" class="ribbon" style="font-size:17px"><?php esc_html_e( 'Web Stories', 'indian-festivals' ); ?></h2>
		<div class="grid-6" style="margin-top:16px">
			<?php foreach ( $posts as $i => $post ) : ?>
				<a class="story-tile" href="<?php echo esc_url( get_permalink( $post ) ); ?>" style="<?php echo esc_attr( ifs_art_attr( $post->ID, $i ) ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}

/**
 * WhatsApp and email subscribe band.
 */
function ifs_section_subscribe() {
	$wa    = ifs_opt( 'whatsapp_channel', '' );
	$embed = (string) ifs_opt( 'email_form_embed', '' );
	?>
	<section class="subscribe" aria-labelledby="sub-title">
		<div>
			<h2 id="sub-title"><?php esc_html_e( 'Never miss a festival', 'indian-festivals' ); ?></h2>
			<p><?php esc_html_e( 'Get reminders for festivals, vrats and muhurats on WhatsApp or email.', 'indian-festivals' ); ?></p>
			<?php if ( $wa ) : ?>
				<a class="btn" href="<?php echo esc_url( $wa ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Join WhatsApp channel', 'indian-festivals' ); ?></a>
			<?php endif; ?>
		</div>
		<div>
			<?php if ( '' !== trim( $embed ) ) : ?>
				<?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="ifs_subscribe">
					<?php wp_nonce_field( 'ifs_subscribe', 'ifs_subscribe_nonce' ); ?>
					<label class="sr-only" for="ifs-email"><?php esc_html_e( 'Email address', 'indian-festivals' ); ?></label>
					<input id="ifs-email" type="email" name="email" placeholder="<?php esc_attr_e( 'Your email', 'indian-festivals' ); ?>" required>
					<button class="btn" type="submit"><?php esc_html_e( 'Subscribe', 'indian-festivals' ); ?></button>
				</form>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Save email subscriptions (admin-post, nonce protected).
 */
function ifs_handle_subscribe() {
	check_admin_referer( 'ifs_subscribe', 'ifs_subscribe_nonce' );
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( is_email( $email ) ) {
		$list = get_option( 'ifs_subscribers', array() );
		if ( ! in_array( $email, $list, true ) ) {
			$list[] = $email;
			update_option( 'ifs_subscribers', $list, false );
		}
	}
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : home_url( '/' ) );
	exit;
}
add_action( 'admin_post_nopriv_ifs_subscribe', 'ifs_handle_subscribe' );
add_action( 'admin_post_ifs_subscribe', 'ifs_handle_subscribe' );
