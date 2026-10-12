<?php
/**
 * SEO tools: canonical URLs, per-post meta, XML sitemap, robots rules and
 * breadcrumb schema. Skips meta output when Rank Math, Yoast or AIOSEO is
 * active so nothing is duplicated.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The SEO plugin, if any, that owns meta and schema output.
 *
 * @return string
 */
function ifs_seo_plugin_slug() {
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		return 'rankmath';
	}
	if ( defined( 'WPSEO_VERSION' ) ) {
		return 'yoast';
	}
	if ( defined( 'AIOSEO_VERSION' ) ) {
		return 'aioseo';
	}
	if ( defined( 'SEOPRESS_VERSION' ) ) {
		return 'seopress';
	}
	return '';
}

/* -------------------------------------------------------------------------
 * Per-post SEO meta fields
 * ---------------------------------------------------------------------- */

/**
 * SEO fields registered for every public post type.
 *
 * @param int $post_id Post ID.
 */
function ifs_seo_meta_box( $post ) {
	$post_id = $post->ID;
	wp_nonce_field( 'ifs_seo_save', 'ifs_seo_nonce' );
	$seo_title = get_post_meta( $post_id, '_ifs_seo_title', true );
	$seo_desc  = get_post_meta( $post_id, '_ifs_seo_desc', true );
	$canonical = get_post_meta( $post_id, '_ifs_seo_canonical', true );
	$noindex   = get_post_meta( $post_id, '_ifs_seo_noindex', true );
	$focus     = get_post_meta( $post_id, '_ifs_seo_keyword', true );
	?>
	<p class="description">
		<?php
		$plugin = ifs_seo_plugin_slug();
		if ( $plugin ) {
			/* translators: %s: SEO plugin name */
			echo esc_html( sprintf( __( 'Note: %s is active and will handle meta tags. The fields below are a fallback and will not override it.', 'indian-festivals' ), ucfirst( $plugin ) ) );
		} else {
			echo esc_html__( 'These fields control how this page looks in Google and on social media.', 'indian-festivals' );
		}
		?>
	</p>
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><label for="ifs_seo_title"><?php esc_html_e( 'SEO title', 'indian-festivals' ); ?></label></th>
				<td>
					<input type="text" id="ifs_seo_title" name="ifs_seo[title]" class="large-text" value="<?php echo esc_attr( $seo_title ); ?>">
					<p class="description"><?php esc_html_e( 'Shown as the blue link in Google. Leave empty to use the page title.', 'indian-festivals' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ifs_seo_desc"><?php esc_html_e( 'Meta description', 'indian-festivals' ); ?></label></th>
				<td>
					<textarea id="ifs_seo_desc" name="ifs_seo[desc]" rows="3" class="large-text"><?php echo esc_textarea( $seo_desc ); ?></textarea>
					<p class="description"><?php esc_html_e( 'The short description under the link in Google. Around 155 characters.', 'indian-festivals' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ifs_seo_keyword"><?php esc_html_e( 'Focus keyword', 'indian-festivals' ); ?></label></th>
				<td><input type="text" id="ifs_seo_keyword" name="ifs_seo[keyword]" class="large-text" value="<?php echo esc_attr( $focus ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="ifs_seo_canonical"><?php esc_html_e( 'Canonical URL', 'indian-festivals' ); ?></label></th>
				<td>
					<input type="url" id="ifs_seo_canonical" name="ifs_seo[canonical]" class="large-text" value="<?php echo esc_url( $canonical ); ?>">
					<p class="description"><?php esc_html_e( 'Optional. Tells Google which address is the main version of this page.', 'indian-festivals' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Search indexing', 'indian-festivals' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="ifs_seo[noindex]" value="1" <?php checked( $noindex, '1' ); ?>>
						<?php esc_html_e( 'Hide this page from search engines', 'indian-festivals' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Register the SEO meta box on the types that rank in search.
 */
function ifs_seo_add_meta_box() {
	foreach ( array( 'post', 'page', 'festival', 'vrat', 'muhurat', 'katha', 'deity', 'greeting', 'web_story', 'holiday' ) as $type ) {
		add_meta_box(
			'ifs_seo_box',
			__( 'Search Engine Optimization (SEO)', 'indian-festivals' ),
			'ifs_seo_meta_box',
			$type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'ifs_seo_add_meta_box' );

/**
 * Sanitize and store the SEO fields.
 *
 * @param int $post_id Post ID.
 */
function ifs_seo_save_meta( $post_id ) {
	if ( ! isset( $_POST['ifs_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ifs_seo_nonce'] ) ), 'ifs_seo_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$in = isset( $_POST['ifs_seo'] ) && is_array( $_POST['ifs_seo'] ) ? wp_unslash( $_POST['ifs_seo'] ) : array();

	$values = array(
		'title'     => sanitize_text_field( $in['title'] ?? '' ),
		'desc'      => sanitize_textarea_field( $in['desc'] ?? '' ),
		'keyword'   => sanitize_text_field( $in['keyword'] ?? '' ),
		'canonical' => esc_url_raw( $in['canonical'] ?? '' ),
		'noindex'   => ! empty( $in['noindex'] ) ? '1' : '',
	);
	foreach ( $values as $key => $value ) {
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_ifs_seo_' . $key );
		} else {
			update_post_meta( $post_id, '_ifs_seo_' . $key, $value );
		}
	}
}
add_action( 'save_post', 'ifs_seo_save_meta' );

/* -------------------------------------------------------------------------
 * Front-end output
 * ---------------------------------------------------------------------- */

/**
 * Fallback SEO title when no SEO plugin is active.
 */
function ifs_seo_document_title( $title_parts ) {
	if ( ifs_seo_plugin_slug() ) {
		return $title_parts;
	}
	if ( is_singular() ) {
		$custom = get_post_meta( get_queried_object_id(), '_ifs_seo_title', true );
		if ( $custom ) {
			$title_parts['title'] = $custom;
		}
	}
	return $title_parts;
}
add_filter( 'document_title_parts', 'ifs_seo_document_title' );

/**
 * Canonical URL, meta description, robots and Open Graph.
 */
function ifs_seo_head_enhanced() {
	if ( is_admin() || ifs_seo_plugin_slug() ) {
		return;
	}
	$post_id = is_singular() ? get_queried_object_id() : 0;

	// Canonical. Year and city variants point back to the base address.
	if ( $post_id ) {
		$custom = get_post_meta( $post_id, '_ifs_seo_canonical', true );
		$url    = $custom ? $custom : get_permalink( $post_id );
		// Strip campaign / view query args so one address owns the page.
		$url = remove_query_arg( array( 'city', 'view', 'utm_source', 'utm_medium', 'utm_campaign', 'ref' ), $url );
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	}

	// Robots.
	$noindex = $post_id ? get_post_meta( $post_id, '_ifs_seo_noindex', true ) : '';
	if ( $noindex || is_search() || is_404() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	} else {
		echo '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n";
	}

	// Meta description fallback.
	$desc = $post_id ? get_post_meta( $post_id, '_ifs_seo_desc', true ) : '';
	if ( ! $desc && $post_id ) {
		$desc = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 32 );
	}
	if ( ! $desc && is_front_page() ) {
		$desc = get_bloginfo( 'description' );
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $desc ) ) . '">' . "\n";
	}

	// Open Graph / Twitter. Gives rich previews in WhatsApp and Facebook.
	$image = $post_id && has_post_thumbnail( $post_id )
		? get_the_post_thumbnail_url( $post_id, 'large' )
		: ifs_opt( 'default_social_image', '' );
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( is_singular() ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $post_id ? get_permalink( $post_id ) : home_url( '/' ) ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( wp_strip_all_tags( $desc ) ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}

	// Language / hreflang for the two UI languages.
	echo '<link rel="alternate" hreflang="en-in" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
	echo '<link rel="alternate" hreflang="hi-in" href="' . esc_url( add_query_arg( 'lang', 'hi', home_url( '/' ) ) ) . '">' . "\n";
}
add_action( 'wp_head', 'ifs_seo_head_enhanced', 4 );

/**
 * Structured data for festivals, articles and site.
 */
function ifs_schema_jsonld() {
	if ( ! ifs_opt( 'schema_on', true ) ) {
		return;
	}
	$graph = array();

	if ( is_front_page() && ifs_opt( 'schema_website', true ) ) {
		$graph[] = array(
			'@type'           => 'WebSite',
			'name'            => get_bloginfo( 'name' ),
			'url'             => home_url( '/' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => home_url( '/?s={search_term_string}' ),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	if ( is_singular( 'festival' ) && ifs_opt( 'schema_event', true ) ) {
		$id   = get_queried_object_id();
		$date = ifs_meta( 'date', $id );
		$end  = ifs_meta( 'end_date', $id ) ?: $date;
		if ( $date ) {
			$graph[] = array(
				'@type'       => 'Event',
				'name'        => get_the_title( $id ),
				'description' => wp_strip_all_tags( get_the_excerpt( $id ) ),
				'startDate'   => $date,
				'endDate'     => $end,
				'eventStatus' => 'https://schema.org/EventScheduled',
				'location'    => array(
					'@type'   => 'Place',
					'name'    => 'India',
					'address' => array( '@type' => 'PostalAddress', 'addressCountry' => 'IN' ),
				),
				'organizer'   => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ),
				'url'         => get_permalink( $id ),
			);
		}
	}

	if ( is_singular( 'post' ) && ifs_opt( 'schema_article', true ) ) {
		$graph[] = array(
			'@type'         => 'Article',
			'headline'      => get_the_title(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'author'        => array( '@type' => 'Person', 'name' => get_the_author() ),
			'url'           => get_permalink(),
		);
	}

	if ( ! is_front_page() && ifs_opt( 'schema_breadcrumb', true ) && ( is_singular() || is_archive() ) ) {
		$items = array( array( '@type' => 'ListItem', 'position' => 1, 'name' => __( 'Home', 'indian-festivals' ), 'item' => home_url( '/' ) ) );
		if ( is_singular() ) {
			$items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => get_the_title(), 'item' => get_permalink() );
		} else {
			$items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => wp_strip_all_tags( get_the_archive_title() ), 'item' => get_pagenum_link( 1 ) );
		}
		$graph[] = array( '@type' => 'BreadcrumbList', 'itemListElement' => $items );
	}

	if ( ! $graph ) {
		return;
	}
	$json = wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo '<script type="application/ld+json">' . str_replace( '</', '<\/', $json ) . '</script>' . "\n";
}
add_action( 'wp_head', 'ifs_schema_jsonld', 5 );

/**
 * A visible breadcrumb trail (helps visitors and search engines).
 */
function ifs_breadcrumb_trail() {
	if ( ! ifs_opt( 'show_breadcrumbs', true ) ) {
		return;
	}
	$items = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'indian-festivals' ) . '</a>' );
	if ( is_singular() ) {
		$type = get_post_type();
		$obj  = get_post_type_object( $type );
		if ( $obj && $obj->has_archive ) {
			$items[] = '<a href="' . esc_url( get_post_type_archive_link( $type ) ) . '">' . esc_html( $obj->labels->name ) . '</a>';
		}
		$items[] = esc_html( get_the_title() );
	} elseif ( is_archive() ) {
		$items[] = esc_html( wp_strip_all_tags( get_the_archive_title() ) );
	} elseif ( is_search() ) {
		$items[] = esc_html__( 'Search results', 'indian-festivals' );
	}
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'indian-festivals' ) . '">' . implode( ' <span>›</span> ', $items ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/* -------------------------------------------------------------------------
 * XML sitemap (sitemaps help Google find every page)
 * ---------------------------------------------------------------------- */

/**
 * Serve /sitemap.xml and /sitemap-{type}.xml.
 */
function ifs_sitemap_route() {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ) : '';
	$req  = isset( $_SERVER['REQUEST_URI'] ) ? trim( (string) wp_unslash( $_SERVER['REQUEST_URI'] ), '/' ) : '';
	if ( 'sitemap.xml' !== $req && ! preg_match( '^sitemap-[a-z-]+\.xml$^', $req ) ) {
		return;
	}

	ifs_clear_sitemap_cache();
	header( 'Content-Type: application/xml; charset=utf-8' );
	header( 'X-Robots-Tag: index, follow' );

	if ( 'sitemap.xml' === $req ) {
		$types = array( 'post', 'page', 'festival', 'vrat', 'muhurat', 'katha', 'deity', 'holiday' );
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ( $types as $type ) {
			echo '  <sitemap><loc>' . esc_url( home_url( '/sitemap-' . $type . '.xml' ) ) . '</loc></sitemap>' . "\n";
		}
		echo '</sitemapindex>' . "\n";
		exit;
	}

	$type = str_replace( array( 'sitemap-', '.xml' ), '', $req );
	$posts = get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => 'publish',
			'posts_per_page' => 1000,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach ( $posts as $id ) {
		$noindex = get_post_meta( $id, '_ifs_seo_noindex', true );
		if ( $noindex ) {
			continue;
		}
		echo '  <url><loc>' . esc_url( get_permalink( $id ) ) . '</loc>';
		echo '<lastmod>' . esc_html( gmdate( 'c', strtotime( get_post_modified_time( 'c', true, $id ) ) ) ) . '</lastmod>';
		echo '<changefreq>' . esc_html( 'festival' === $type ? 'weekly' : 'monthly' ) . '</changefreq>';
		echo '<priority>' . esc_html( 'page' === $type ? '0.8' : '0.6' ) . '</priority></url>' . "\n";
	}
	echo '</urlset>' . "\n";
	exit;
}
add_action( 'init', 'ifs_sitemap_route', 0 );

/**
 * Clear the sitemap cache. WordPress caches get_posts per request only, so this
 * is a hook for object-cache users to invalidate their own cache.
 */
function ifs_clear_sitemap_cache() {
	if ( ! get_transient( 'ifs_sitemap_checked' ) ) {
		set_transient( 'ifs_sitemap_checked', 1, DAY_IN_SECONDS );
	}
}

/**
 * Ping search engines whenever content is published.
 *
 * @param int $post_id Post ID.
 */
function ifs_ping_search_engines( $post_id ) {
	if ( ifs_opt( 'schema_on', true ) && in_array( get_post_type( $post_id ), array( 'festival', 'vrat', 'post', 'page' ), true ) ) {
		wp_schedule_single_event( time() + 60, 'ifs_do_ping' );
	}
}
add_action( 'save_post', 'ifs_ping_search_engines' );

/**
 * Ping Google. Comment-ping is throttled by WordPress automatically.
 */
function ifs_do_ping() {
	$sitemap = home_url( '/sitemap.xml' );
	wp_remote_get(
		'https://www.google.com/ping?sitemap=' . rawurlencode( $sitemap ),
		array( 'timeout' => 8, 'blocking' => false )
	);
}
add_action( 'ifs_do_ping', 'ifs_do_ping' );
