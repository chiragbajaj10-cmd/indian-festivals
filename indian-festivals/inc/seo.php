<?php
/**
 * SEO output: meta description, Open Graph, JSON-LD schema and tracking codes.
 * Output is skipped for schema when Rank Math, Yoast or AIOSEO is active, to avoid duplicates.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a major SEO plugin is handling meta and schema.
 *
 * @return bool
 */
function ifs_seo_plugin_active() {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Meta description, Open Graph and schema.
 */
function ifs_seo_head() {
	if ( is_admin() || is_404() || is_search() ) {
		return;
	}
	$plugin = ifs_seo_plugin_active();

	if ( ! $plugin ) {
		$desc = '';
		if ( is_singular() ) {
			$desc = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_queried_object_id() ) ), 30 );
		} elseif ( is_front_page() ) {
			$desc = get_bloginfo( 'description' );
		}
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $desc ) ) . '">' . "\n";
		}

		$image = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : ifs_opt( 'default_social_image', '' );
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( is_singular() ? 'article' : 'website' ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( get_permalink() ?: home_url( '/' ) ) . '">' . "\n";
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		}
	}

	if ( $plugin || ! ifs_opt( 'schema_on', true ) ) {
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
		$date = ifs_meta( 'date' );
		$end  = ifs_meta( 'end_date' ) ?: $date;
		if ( $date ) {
			$graph[] = array(
				'@type'       => 'Event',
				'name'        => get_the_title(),
				'startDate'   => $date,
				'endDate'     => $end,
				'eventStatus' => 'https://schema.org/EventScheduled',
				'location'    => array(
					'@type'   => 'Place',
					'name'    => 'India',
					'address' => array( '@type' => 'PostalAddress', 'addressCountry' => 'IN' ),
				),
				'description' => wp_strip_all_tags( get_the_excerpt() ),
				'url'         => get_permalink(),
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
		$items = array(
			array( '@type' => 'ListItem', 'position' => 1, 'name' => __( 'Home', 'indian-festivals' ), 'item' => home_url( '/' ) ),
		);
		if ( is_singular() ) {
			$items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => get_the_title(), 'item' => get_permalink() );
		} else {
			$items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => wp_strip_all_tags( get_the_archive_title() ), 'item' => get_pagenum_link( 1 ) );
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	if ( ! $graph ) {
		return;
	}

	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	);
	echo '<script type="application/ld+json">' . str_replace( '</', '<\/', $json ) . '</script>' . "\n";
}
add_action( 'wp_head', 'ifs_seo_head', 5 );

/**
 * Analytics, Search Console and custom head code.
 */
function ifs_tracking_head() {
	$ga = trim( (string) ifs_opt( 'ga_id', '' ) );
	if ( $ga && preg_match( '/^G-[A-Z0-9]+$/i', $ga ) ) {
		echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga ) . '"></script>' . "\n";
		echo '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . esc_js( $ga ) . '");</script>' . "\n";
	}
	$gsc = (string) ifs_opt( 'gsc_tag', '' );
	if ( '' !== trim( $gsc ) ) {
		echo $gsc . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	$head = (string) ifs_opt( 'custom_header_code', '' );
	if ( '' !== trim( $head ) ) {
		echo $head . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'ifs_tracking_head', 1 );

/**
 * Custom footer code and push notification code.
 */
function ifs_tracking_footer() {
	$foot = (string) ifs_opt( 'custom_footer_code', '' );
	if ( '' !== trim( $foot ) ) {
		echo $foot . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	$push = (string) ifs_opt( 'push_code', '' );
	if ( '' !== trim( $push ) ) {
		echo $push . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_footer', 'ifs_tracking_footer', 20 );
