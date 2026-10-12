<?php
/**
 * Panchang data access and muhurat timing helpers.
 *
 * Astronomy is NOT calculated inside the theme. Panchang values come from:
 *  1. Panchang Entries (manual or CSV import, generated from an authentic panchang), or
 *  2. A licensed API (for example one built on Swiss Ephemeris) set in Theme: Festival and Panchang.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parse "HH:MM" into minutes after midnight.
 *
 * @param string $hm Time string.
 * @return int|null
 */
function ifs_hm_to_min( $hm ) {
	if ( ! preg_match( '/^(\d{1,2}):(\d{2})$/', (string) $hm, $m ) ) {
		return null;
	}
	return ( (int) $m[1] * 60 ) + (int) $m[2];
}

/**
 * Format minutes after midnight as "HH:MM".
 *
 * @param float $minutes Minutes.
 * @return string
 */
function ifs_min_to_hm( $minutes ) {
	$m = (int) round( $minutes );
	$m = ( ( $m % 1440 ) + 1440 ) % 1440;
	return sprintf( '%02d:%02d', intdiv( $m, 60 ), $m % 60 );
}

/**
 * Rahu Kaal, Yamaganda, Gulika, Abhijit and Brahma Muhurat for a day.
 *
 * The day is split into eight equal parts between sunrise and sunset. Rahu, Yamaganda
 * and Gulika use the standard weekday part numbers. Brahma Muhurat is 96 to 48 minutes
 * before sunrise and Abhijit is 48 minutes centred on local noon.
 *
 * @param string $sunrise "HH:MM"
 * @param string $sunset  "HH:MM"
 * @param int    $weekday 0 = Sunday ... 6 = Saturday.
 * @return array
 */
function ifs_day_timings( $sunrise, $sunset, $weekday ) {
	$sr = ifs_hm_to_min( $sunrise );
	$ss = ifs_hm_to_min( $sunset );
	if ( null === $sr || null === $ss || $ss <= $sr ) {
		return array();
	}

	$part   = ( $ss - $sr ) / 8;
	$w      = max( 0, min( 6, (int) $weekday ) );
	$rahu   = array( 8, 2, 7, 5, 6, 4, 3 );
	$yama   = array( 5, 4, 3, 2, 1, 7, 6 );
	$gulika = array( 7, 6, 5, 4, 3, 2, 1 );
	$noon   = ( $sr + $ss ) / 2;

	$slot = function ( $index ) use ( $sr, $part ) {
		$start = $sr + ( ( $index - 1 ) * $part );
		return array( ifs_min_to_hm( $start ), ifs_min_to_hm( $start + $part ) );
	};

	return array(
		'rahu_kaal'      => $slot( $rahu[ $w ] ),
		'yamaganda'      => $slot( $yama[ $w ] ),
		'gulika'         => $slot( $gulika[ $w ] ),
		'abhijit'        => array( ifs_min_to_hm( $noon - 24 ), ifs_min_to_hm( $noon + 24 ) ),
		'brahma_muhurat' => array( ifs_min_to_hm( $sr - 96 ), ifs_min_to_hm( $sr - 48 ) ),
	);
}

/**
 * Find the City post ID by its title.
 *
 * @param string $name City name.
 * @return int
 */
function ifs_city_id_by_name( $name ) {
	$post = get_page_by_title( $name, OBJECT, 'city' );
	return $post ? (int) $post->ID : 0;
}

/**
 * Weekday number (0 = Sunday) for a Y-m-d date in the site timezone.
 *
 * @param string $date Y-m-d.
 * @return int
 */
function ifs_weekday( $date ) {
	$tz = new DateTimeZone( ifs_opt( 'default_timezone', 'Asia/Kolkata' ) );
	return (int) ( new DateTime( $date . ' 12:00:00', $tz ) )->format( 'w' );
}

/**
 * Panchang values for a date and city.
 *
 * @param string $date    Y-m-d.
 * @param int    $city_id City post ID (0 for any city).
 * @return array|null Keys: vara, tithi, nakshatra, yoga, karana, sunrise, sunset, moonrise.
 */
function ifs_get_panchang( $date, $city_id = 0 ) {
	$source = ifs_opt( 'data_source', 'manual' );
	$key    = 'ifs_pc_' . md5( $date . '|' . (int) $city_id . '|' . $source );
	$cached = get_transient( $key );
	if ( false !== $cached ) {
		return $cached ? $cached : null;
	}

	$fields = array( 'vara', 'tithi', 'nakshatra', 'yoga', 'karana', 'sunrise', 'sunset', 'moonrise' );
	$data   = null;

	$ids = get_posts(
		array(
			'post_type'      => 'panchang',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => '_ifs_date',
					'value' => $date,
				),
				array(
					'key'   => '_ifs_city_id',
					'value' => (int) $city_id,
					'type'  => 'NUMERIC',
				),
			),
		)
	);

	if ( $ids ) {
		$data = array();
		foreach ( $fields as $field ) {
			$data[ $field ] = (string) get_post_meta( $ids[0], '_ifs_' . $field, true );
		}
	}

	if ( ! $data && 'api' === $source ) {
		$data = ifs_fetch_panchang_api( $date, $city_id );
	}

	set_transient( $key, $data ? $data : 0, 12 * HOUR_IN_SECONDS );
	return $data ? $data : null;
}

/**
 * Fetch panchang values from a licensed API.
 *
 * The API must return JSON with keys: vara, tithi, nakshatra, yoga, karana, sunrise, sunset, moonrise.
 *
 * @param string $date    Y-m-d.
 * @param int    $city_id City post ID.
 * @return array|null
 */
function ifs_fetch_panchang_api( $date, $city_id ) {
	$url = ifs_opt( 'panchang_api_url', '' );
	$key = ifs_opt( 'panchang_api_key', '' );
	if ( ! $url ) {
		return null;
	}

	$lat = $city_id ? get_post_meta( $city_id, '_ifs_latitude', true ) : '';
	$lon = $city_id ? get_post_meta( $city_id, '_ifs_longitude', true ) : '';

	$args = array(
		'date' => $date,
		'lat'  => $lat,
		'lon'  => $lon,
		'tz'   => ifs_opt( 'default_timezone', 'Asia/Kolkata' ),
		'lang' => ifs_opt( 'festival_lang', 'en' ),
	);

	$headers = array( 'Accept' => 'application/json' );
	if ( $key ) {
		$headers['Authorization'] = 'Bearer ' . $key;
	}

	$response = wp_remote_get(
		add_query_arg( $args, $url ),
		array(
			'timeout' => 10,
			'headers' => $headers,
		)
	);
	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}

	$json = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $json ) ) {
		return null;
	}

	$out = array();
	foreach ( array( 'vara', 'tithi', 'nakshatra', 'yoga', 'karana', 'sunrise', 'sunset', 'moonrise' ) as $field ) {
		$out[ $field ] = isset( $json[ $field ] ) ? sanitize_text_field( (string) $json[ $field ] ) : '';
	}
	return $out;
}

/**
 * Clear cached panchang values.
 */
function ifs_clear_panchang_cache() {
	global $wpdb;
	$like = $wpdb->esc_like( '_transient_ifs_pc_' ) . '%';
	$like_timeout = $wpdb->esc_like( '_transient_timeout_ifs_pc_' ) . '%';
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", $like, $like_timeout ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}
