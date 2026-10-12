<?php
/**
 * Tools page: CSV import and export for festivals, vrats and panchang entries,
 * an ICS calendar feed, and a cache clear button.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CSV column layout per import/export type.
 *
 * @return array
 */
function ifs_csv_columns() {
	return array(
		'festival' => array( 'title', 'date', 'end_date', 'tithi', 'puja_muhurat', 'deity', 'region' ),
		'vrat'     => array( 'title', 'vrat_type', 'date', 'start_time', 'end_time', 'parana_time' ),
		'panchang' => array( 'city', 'date', 'vara', 'tithi', 'nakshatra', 'yoga', 'karana', 'sunrise', 'sunset', 'moonrise' ),
	);
}

/**
 * Admin menu entry for the tools page.
 */
function ifs_tools_menu() {
	add_menu_page(
		__( 'IF Tools', 'indian-festivals' ),
		__( 'IF Tools', 'indian-festivals' ),
		'manage_options',
		'ifs-tools',
		'ifs_tools_page',
		'dashicons-admin-tools',
		31
	);
}
add_action( 'admin_menu', 'ifs_tools_menu' );

/**
 * Render the tools page.
 */
function ifs_tools_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$msg = isset( $_GET['ifs_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['ifs_msg'] ) ) : '';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Indian Festivals Tools', 'indian-festivals' ); ?></h1>
		<?php if ( $msg ) : ?>
			<div class="notice notice-info is-dismissible"><p><?php echo esc_html( $msg ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Export CSV', 'indian-festivals' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ifs_export">
			<?php wp_nonce_field( 'ifs_export', 'ifs_nonce' ); ?>
			<select name="type">
				<option value="festival"><?php esc_html_e( 'Festivals', 'indian-festivals' ); ?></option>
				<option value="vrat"><?php esc_html_e( 'Vrats', 'indian-festivals' ); ?></option>
				<option value="panchang"><?php esc_html_e( 'Panchang Entries', 'indian-festivals' ); ?></option>
			</select>
			<?php submit_button( __( 'Export CSV', 'indian-festivals' ), 'secondary', 'submit', false ); ?>
		</form>

		<h2><?php esc_html_e( 'Import CSV', 'indian-festivals' ); ?></h2>
		<p><?php esc_html_e( 'Use the same column names as the export. Festivals and vrats are matched by title. Panchang rows need an existing City title in the "city" column.', 'indian-festivals' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
			<input type="hidden" name="action" value="ifs_import">
			<?php wp_nonce_field( 'ifs_import', 'ifs_nonce' ); ?>
			<select name="type">
				<option value="festival"><?php esc_html_e( 'Festivals', 'indian-festivals' ); ?></option>
				<option value="vrat"><?php esc_html_e( 'Vrats', 'indian-festivals' ); ?></option>
				<option value="panchang"><?php esc_html_e( 'Panchang Entries', 'indian-festivals' ); ?></option>
			</select>
			<input type="file" name="csv" accept=".csv,text/csv" required>
			<?php submit_button( __( 'Import CSV', 'indian-festivals' ), 'primary', 'submit', false ); ?>
		</form>

		<h2><?php esc_html_e( 'ICS calendar feed', 'indian-festivals' ); ?></h2>
		<p>
			<a class="button" href="<?php echo esc_url( home_url( '/?ifs_ics=all' ) ); ?>"><?php esc_html_e( 'Download all festivals (.ics)', 'indian-festivals' ); ?></a>
		</p>

		<h2><?php esc_html_e( 'Cache', 'indian-festivals' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ifs_clear_cache">
			<?php wp_nonce_field( 'ifs_clear_cache', 'ifs_nonce' ); ?>
			<?php submit_button( __( 'Clear panchang cache', 'indian-festivals' ), 'secondary', 'submit', false ); ?>
		</form>
	</div>
	<?php
}

/**
 * Verify the nonce and capability for a tools form.
 *
 * @param string $action Nonce action.
 */
function ifs_tools_guard( $action ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'indian-festivals' ) );
	}
	check_admin_referer( $action, 'ifs_nonce' );
}

/**
 * Export CSV.
 */
function ifs_handle_export() {
	ifs_tools_guard( 'ifs_export' );
	$type    = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'festival';
	$columns = ifs_csv_columns();
	if ( ! isset( $columns[ $type ] ) ) {
		$type = 'festival';
	}

	$post_type = array( 'festival' => 'festival', 'vrat' => 'vrat', 'panchang' => 'panchang' );
	$posts     = get_posts(
		array(
			'post_type'      => $post_type[ $type ],
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="ifs-' . $type . '-' . gmdate( 'Ymd' ) . '.csv"' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
	fputcsv( $out, $columns[ $type ] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fputcsv

	foreach ( $posts as $post ) {
		$row = array();
		foreach ( $columns[ $type ] as $col ) {
			if ( 'title' === $col ) {
				$row[] = $post->post_title;
			} elseif ( 'city' === $col ) {
				$cid   = (int) ifs_meta( 'city_id', $post->ID );
				$row[] = $cid ? get_the_title( $cid ) : '';
			} elseif ( 'region' === $col ) {
				$row[] = implode( '|', wp_list_pluck( get_the_terms( $post->ID, 'ifs_region' ) ?: array(), 'name' ) );
			} else {
				$row[] = ifs_meta( $col, $post->ID );
			}
		}
		fputcsv( $out, $row ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fputcsv
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_ifs_export', 'ifs_handle_export' );

/**
 * Import CSV.
 */
function ifs_handle_import() {
	ifs_tools_guard( 'ifs_import' );
	$type    = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';
	$columns = ifs_csv_columns();

	if ( ! isset( $columns[ $type ] ) || empty( $_FILES['csv']['tmp_name'] ) || ! is_uploaded_file( $_FILES['csv']['tmp_name'] ) ) {
		ifs_tools_redirect( __( 'Import failed: choose a CSV file and type.', 'indian-festivals' ) );
	}
	if ( ! empty( $_FILES['csv']['size'] ) && (int) $_FILES['csv']['size'] > 2 * MB_IN_BYTES ) {
		ifs_tools_redirect( __( 'Import failed: file is larger than 2 MB.', 'indian-festivals' ) );
	}

	$handle = fopen( $_FILES['csv']['tmp_name'], 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	if ( ! $handle ) {
		ifs_tools_redirect( __( 'Import failed: could not read file.', 'indian-festivals' ) );
	}

	$header = fgetcsv( $handle );
	if ( ! $header ) {
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		ifs_tools_redirect( __( 'Import failed: empty file.', 'indian-festivals' ) );
	}
	$header = array_map( 'sanitize_key', array_map( 'trim', preg_replace( '/^\xEF\xBB\xBF/', '', $header ) ) );

	$created = 0;
	$skipped = 0;
	while ( ( $row = fgetcsv( $handle ) ) !== false ) {
		if ( count( $row ) < 2 ) {
			continue;
		}
		$data = array();
		foreach ( $header as $i => $key ) {
			$data[ $key ] = isset( $row[ $i ] ) ? sanitize_text_field( $row[ $i ] ) : '';
		}
		$ok = 'festival' === $type ? ifs_import_festival( $data ) : ( 'vrat' === $type ? ifs_import_vrat( $data ) : ifs_import_panchang( $data ) );
		if ( $ok ) {
			++$created;
		} else {
			++$skipped;
		}
	}
	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

	ifs_clear_panchang_cache();
	/* translators: 1: created count, 2: skipped count */
	ifs_tools_redirect( sprintf( __( 'Import finished: %1$d saved, %2$d skipped.', 'indian-festivals' ), $created, $skipped ) );
}
add_action( 'admin_post_ifs_import', 'ifs_handle_import' );

/**
 * Write meta from an imported row, sanitized per field type.
 *
 * @param int   $post_id Post ID.
 * @param array $data    Row data.
 * @param array $fields  key => type.
 */
function ifs_import_write_meta( $post_id, $data, $fields ) {
	foreach ( $fields as $key => $type ) {
		$value = ifs_sanitize_meta_value( $type, isset( $data[ $key ] ) ? $data[ $key ] : '' );
		if ( '' === $value || 0 === $value ) {
			delete_post_meta( $post_id, '_ifs_' . $key );
		} else {
			update_post_meta( $post_id, '_ifs_' . $key, $value );
		}
	}
}

/**
 * Find a post by title or create it.
 *
 * @param string $post_type Post type.
 * @param string $title     Title.
 * @return int Post ID, or 0 on failure.
 */
function ifs_import_find_or_create( $post_type, $title ) {
	if ( '' === $title ) {
		return 0;
	}
	$existing = get_page_by_title( $title, OBJECT, $post_type );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	return (int) wp_insert_post(
		array(
			'post_type'   => $post_type,
			'post_title'  => $title,
			'post_status' => 'publish',
		)
	);
}

/**
 * @param array $data Row.
 * @return bool
 */
function ifs_import_festival( $data ) {
	$id = ifs_import_find_or_create( 'festival', $data['title'] ?? '' );
	if ( ! $id ) {
		return false;
	}
	ifs_import_write_meta(
		$id,
		$data,
		array(
			'date'         => 'date',
			'end_date'     => 'date',
			'tithi'        => 'text',
			'puja_muhurat' => 'text',
			'deity'        => 'text',
		)
	);
	if ( ! empty( $data['date'] ) ) {
		update_post_meta( $id, '_ifs_year', (int) substr( $data['date'], 0, 4 ) );
	}
	if ( ! empty( $data['region'] ) ) {
		wp_set_object_terms( $id, array_map( 'trim', explode( '|', $data['region'] ) ), 'ifs_region' );
	}
	return true;
}

/**
 * @param array $data Row.
 * @return bool
 */
function ifs_import_vrat( $data ) {
	$id = ifs_import_find_or_create( 'vrat', $data['title'] ?? '' );
	if ( ! $id ) {
		return false;
	}
	ifs_import_write_meta(
		$id,
		$data,
		array(
			'vrat_type'   => 'select',
			'date'        => 'date',
			'start_time'  => 'time',
			'end_time'    => 'time',
			'parana_time' => 'time',
		)
	);
	return true;
}

/**
 * @param array $data Row.
 * @return bool
 */
function ifs_import_panchang( $data ) {
	$city_id = ifs_city_id_by_name( $data['city'] ?? '' );
	if ( ! $city_id || empty( $data['date'] ) ) {
		return false;
	}
	$id = (int) wp_insert_post(
		array(
			'post_type'   => 'panchang',
			'post_title'  => sprintf( '%s · %s', $data['city'], $data['date'] ),
			'post_status' => 'publish',
		)
	);
	if ( ! $id ) {
		return false;
	}
	update_post_meta( $id, '_ifs_city_id', $city_id );
	ifs_import_write_meta(
		$id,
		$data,
		array(
			'date'      => 'date',
			'vara'      => 'text',
			'tithi'     => 'text',
			'nakshatra' => 'text',
			'yoga'      => 'text',
			'karana'    => 'text',
			'sunrise'   => 'time',
			'sunset'    => 'time',
			'moonrise'  => 'time',
		)
	);
	return true;
}

/**
 * Clear panchang cache.
 */
function ifs_handle_clear_cache() {
	ifs_tools_guard( 'ifs_clear_cache' );
	ifs_clear_panchang_cache();
	ifs_tools_redirect( __( 'Panchang cache cleared.', 'indian-festivals' ) );
}
add_action( 'admin_post_ifs_clear_cache', 'ifs_handle_clear_cache' );

/**
 * Back to the tools page with a message.
 *
 * @param string $message Message.
 */
function ifs_tools_redirect( $message ) {
	wp_safe_redirect( add_query_arg( array( 'page' => 'ifs-tools', 'ifs_msg' => rawurlencode( $message ) ), admin_url( 'admin.php' ) ) );
	exit;
}

/**
 * ICS feed: /?ifs_ics=all or /?ifs_ics=<festival ID>.
 */
function ifs_ics_feed() {
	if ( ! isset( $_GET['ifs_ics'] ) ) {
		return;
	}
	$which = sanitize_text_field( wp_unslash( $_GET['ifs_ics'] ) );
	$args  = array(
		'post_type'      => 'festival',
		'post_status'    => 'publish',
		'posts_per_page' => 500,
		'no_found_rows'  => true,
		'meta_key'       => '_ifs_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
	);
	if ( 'all' !== $which ) {
		$args['p'] = absint( $which );
	}
	$posts = get_posts( $args );

	nocache_headers();
	header( 'Content-Type: text/calendar; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="indian-festivals.ics"' );

	$lines   = array( 'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//Indian Festivals//EN', 'CALSCALE:GREGORIAN' );
	$stamp   = gmdate( 'Ymd\THis\Z' );
	foreach ( $posts as $post ) {
		$date = ifs_meta( 'date', $post->ID );
		if ( ! $date || '0' === ifs_meta( 'enable_ics', $post->ID ) ) {
			continue;
		}
		$end  = ifs_meta( 'end_date', $post->ID ) ?: $date;
		$end_ts = strtotime( $end . ' UTC' ) + DAY_IN_SECONDS;
		$lines[] = 'BEGIN:VEVENT';
		$lines[] = 'UID:' . $post->post_name . '@' . wp_parse_url( home_url(), PHP_URL_HOST );
		$lines[] = 'DTSTAMP:' . $stamp;
		$lines[] = 'DTSTART;VALUE=DATE:' . str_replace( '-', '', $date );
		$lines[] = 'DTEND;VALUE=DATE:' . gmdate( 'Ymd', $end_ts );
		$lines[] = 'SUMMARY:' . ifs_ics_escape( $post->post_title );
		$lines[] = 'DESCRIPTION:' . ifs_ics_escape( ifs_excerpt_words( $post, 40 ) . ' ' . __( 'Verify dates with an authentic panchang.', 'indian-festivals' ) );
		$lines[] = 'URL:' . esc_url_raw( get_permalink( $post ) );
		$lines[] = 'END:VEVENT';
	}
	$lines[] = 'END:VCALENDAR';

	echo implode( "\r\n", $lines ) . "\r\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'template_redirect', 'ifs_ics_feed' );

/**
 * Escape text for ICS.
 *
 * @param string $text Text.
 * @return string
 */
function ifs_ics_escape( $text ) {
	$text = str_replace( array( '\\', ';', ',', "\n", "\r" ), array( '\\\\', '\;', '\,', '\n', '' ), wp_strip_all_tags( (string) $text ) );
	return $text;
}
