<?php
/**
 * Custom fields for every content type, with nonce and sanitization.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions per post type. Stored as post meta with the _ifs_ prefix.
 *
 * @return array
 */
function ifs_meta_fields() {
	$muhurat_categories = array(
		'marriage'       => 'Marriage',
		'griha_pravesh'  => 'Griha Pravesh',
		'vehicle'        => 'Vehicle',
		'mundan'         => 'Mundan',
		'naamkaran'      => 'Naamkaran',
		'business'       => 'Business start',
		'other'          => 'Other',
	);
	$vrat_types         = array(
		'ekadashi'   => 'Ekadashi',
		'pradosh'    => 'Pradosh',
		'sankashti'  => 'Sankashti',
		'purnima'    => 'Purnima',
		'amavasya'   => 'Amavasya',
		'shivaratri' => 'Shivaratri',
		'other'      => 'Other',
	);

	return array(
		'festival'  => array(
			'date'          => array( 'Festival date', 'date' ),
			'end_date'      => array( 'End date', 'date' ),
			'tithi'         => array( 'Tithi', 'text' ),
			'puja_muhurat'  => array( 'Puja muhurat', 'text' ),
			'muhurat_start' => array( 'Muhurat start (e.g. 07:08 PM)', 'text' ),
			'muhurat_end'   => array( 'Muhurat end (e.g. 08:19 PM)', 'text' ),
			'avoid_label'   => array( 'Avoid label (e.g. Rahukalam to avoid)', 'text' ),
			'avoid_start'   => array( 'Avoid start', 'text' ),
			'avoid_end'     => array( 'Avoid end', 'text' ),
			'tithi_start'   => array( 'Tithi begins (e.g. 03:44 PM on Oct 19, 2025)', 'text' ),
			'tithi_end'     => array( 'Tithi ends (e.g. 05:12 PM on Oct 20, 2025)', 'text' ),
			'summary'       => array( 'Summary (short paragraph)', 'textarea' ),
			'significance'  => array( 'Significance', 'textarea' ),
			'rituals'       => array( 'Rituals (separate with semicolons)', 'textarea' ),
			'katha_title'   => array( 'Katha title', 'text' ),
			'katha_text'    => array( 'Katha text', 'textarea' ),
			'deity'         => array( 'Deity', 'text' ),
			'year'          => array( 'Year', 'number' ),
			'enable_ics'    => array( 'Include in .ics download', 'checkbox' ),
		),
		'vrat'      => array(
			'vrat_type'   => array( 'Vrat type', 'select', $vrat_types ),
			'date'        => array( 'Date', 'date' ),
			'start_time'  => array( 'Start time', 'time' ),
			'end_time'    => array( 'End time', 'time' ),
			'parana_time' => array( 'Parana time', 'time' ),
		),
		'muhurat'   => array(
			'category'   => array( 'Category', 'select', $muhurat_categories ),
			'date'       => array( 'Date', 'date' ),
			'start_time' => array( 'Start time', 'time' ),
			'end_time'   => array( 'End time', 'time' ),
			'notes'      => array( 'Notes', 'textarea' ),
		),
		'katha'     => array(
			'audio_url' => array( 'Audio URL', 'url' ),
			'language'  => array( 'Language', 'text' ),
			'deity'     => array( 'Deity', 'text' ),
		),
		'deity'     => array(
			'epithets'          => array( 'Epithets (comma separated)', 'text' ),
			'symbols'           => array( 'Symbols', 'textarea' ),
			'related_festivals' => array( 'Related festival slugs (comma separated)', 'text' ),
		),
		'greeting'  => array(
			'festival'      => array( 'Festival', 'text' ),
			'language'      => array( 'Language', 'text' ),
			'greeting_text' => array( 'Greeting text', 'textarea' ),
		),
		'web_story' => array(
			'festival' => array( 'Festival', 'text' ),
			'slides'   => array( 'Slides (one per line: heading | text | image URL)', 'textarea' ),
		),
		'holiday'   => array(
			'date'  => array( 'Date', 'date' ),
			'state' => array( 'State', 'text' ),
			'type'  => array( 'Type', 'select', array( 'government' => 'Government', 'bank' => 'Bank', 'optional' => 'Optional' ) ),
		),
		'panchang'  => array(
			'date'     => array( 'Date', 'date' ),
			'city_id'  => array( 'City', 'city' ),
			'vara'     => array( 'Weekday (vara)', 'text' ),
			'tithi'    => array( 'Tithi', 'text' ),
			'nakshatra'=> array( 'Nakshatra', 'text' ),
			'yoga'     => array( 'Yoga', 'text' ),
			'karana'   => array( 'Karana', 'text' ),
			'sunrise'  => array( 'Sunrise', 'time' ),
			'sunset'   => array( 'Sunset', 'time' ),
			'moonrise' => array( 'Moonrise', 'time' ),
		),
		'city'      => array(
			'latitude'  => array( 'Latitude', 'text' ),
			'longitude' => array( 'Longitude', 'text' ),
			'timezone'  => array( 'Timezone', 'text' ),
		),
	);
}

/**
 * Register one meta box per content type.
 */
function ifs_add_meta_boxes() {
	foreach ( array_keys( ifs_meta_fields() ) as $post_type ) {
		add_meta_box(
			'ifs_fields',
			__( 'Details', 'indian-festivals' ),
			'ifs_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'ifs_add_meta_boxes' );

/**
 * Render the fields.
 *
 * @param WP_Post $post Current post.
 */
function ifs_render_meta_box( $post ) {
	$fields = ifs_meta_fields()[ $post->post_type ] ?? array();
	wp_nonce_field( 'ifs_save_meta', 'ifs_meta_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';

	foreach ( $fields as $key => $def ) {
		$name  = 'ifs_meta[' . $key . ']';
		$id    = 'ifs_' . $key;
		$value = get_post_meta( $post->ID, '_ifs_' . $key, true );
		$type  = $def[1];

		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $def[0] ) . '</label></th><td>';

		switch ( $type ) {
			case 'textarea':
				printf( '<textarea id="%1$s" name="%2$s" rows="4" class="large-text">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
				break;

			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
				foreach ( $def[2] as $opt_value => $opt_label ) {
					printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $opt_value ), selected( $value, $opt_value, false ), esc_html( $opt_label ) );
				}
				echo '</select>';
				break;

			case 'checkbox':
				printf( '<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>', esc_attr( $id ), esc_attr( $name ), checked( $value, '1', false ), esc_html__( 'Yes', 'indian-festivals' ) );
				break;

			case 'city':
				$cities = get_posts(
					array(
						'post_type'      => 'city',
						'posts_per_page' => -1,
						'orderby'        => 'title',
						'order'          => 'ASC',
						'fields'         => 'ids',
					)
				);
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"><option value="0">—</option>';
				foreach ( $cities as $city_id ) {
					printf( '<option value="%1$d" %2$s>%3$s</option>', (int) $city_id, selected( (int) $value, (int) $city_id, false ), esc_html( get_the_title( $city_id ) ) );
				}
				echo '</select>';
				break;

			case 'date':
				printf( '<input type="date" id="%1$s" name="%2$s" value="%3$s">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
				break;

			case 'time':
				printf( '<input type="time" id="%1$s" name="%2$s" value="%3$s">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
				break;

			case 'number':
				printf( '<input type="number" id="%1$s" name="%2$s" value="%3$s" min="1900" max="2200">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
				break;

			case 'url':
				printf( '<input type="url" id="%1$s" name="%2$s" value="%3$s" class="regular-text">', esc_attr( $id ), esc_attr( $name ), esc_url( $value ) );
				break;

			default:
				printf( '<input type="text" id="%1$s" name="%2$s" value="%3$s" class="regular-text">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>';
}

/**
 * Sanitize one value according to its field type.
 *
 * @param string $type  Field type.
 * @param mixed  $value Raw value.
 * @param array  $def   Field definition.
 * @return string|int
 */
function ifs_sanitize_meta_value( $type, $value, $def = array() ) {
	switch ( $type ) {
		case 'date':
			return preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $value ) ? (string) $value : '';
		case 'time':
			return preg_match( '/^\d{2}:\d{2}$/', (string) $value ) ? (string) $value : '';
		case 'number':
			return absint( $value );
		case 'city':
			return absint( $value );
		case 'checkbox':
			return '1' === (string) $value ? '1' : '';
		case 'url':
			return esc_url_raw( (string) $value );
		case 'textarea':
			return sanitize_textarea_field( (string) $value );
		case 'select':
			$key = sanitize_key( (string) $value );
			return isset( $def[2][ $key ] ) ? $key : '';
		default:
			return sanitize_text_field( (string) $value );
	}
}

/**
 * Save meta on post save.
 *
 * @param int $post_id Post ID.
 */
function ifs_save_meta( $post_id ) {
	if ( ! isset( $_POST['ifs_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ifs_meta_nonce'] ) ), 'ifs_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$fields    = ifs_meta_fields()[ $post_type ] ?? array();
	$input     = isset( $_POST['ifs_meta'] ) && is_array( $_POST['ifs_meta'] ) ? wp_unslash( $_POST['ifs_meta'] ) : array();

	foreach ( $fields as $key => $def ) {
		$raw   = isset( $input[ $key ] ) ? $input[ $key ] : '';
		$clean = ifs_sanitize_meta_value( $def[1], $raw, $def );
		if ( '' === $clean || 0 === $clean ) {
			delete_post_meta( $post_id, '_ifs_' . $key );
		} else {
			update_post_meta( $post_id, '_ifs_' . $key, $clean );
		}
	}

	// Keep the year field in sync with the festival date.
	if ( 'festival' === $post_type ) {
		$date = get_post_meta( $post_id, '_ifs_date', true );
		if ( $date ) {
			update_post_meta( $post_id, '_ifs_year', (int) substr( $date, 0, 4 ) );
		}
	}
}
add_action( 'save_post', 'ifs_save_meta' );
