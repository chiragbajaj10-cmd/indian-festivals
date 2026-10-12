<?php
/**
 * Custom post types and taxonomies for the dashboard menu.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all content types.
 */
function ifs_register_content_types() {
	$cpts = array(
		'festival'  => array( 'Festivals', 'Festival', 'festivals', 'dashicons-calendar-alt', 21, true ),
		'vrat'      => array( 'Vrats', 'Vrat', 'vrats', 'dashicons-heart', 22, true ),
		'muhurat'   => array( 'Muhurats', 'Muhurat', 'muhurats', 'dashicons-clock', 23, true ),
		'katha'     => array( 'Katha & Aarti', 'Katha or Aarti', 'katha', 'dashicons-book-alt', 24, true ),
		'deity'     => array( 'Deities', 'Deity', 'deities', 'dashicons-superhero', 25, true ),
		'greeting'  => array( 'Greetings', 'Greeting', 'greetings', 'dashicons-format-image', 26, true ),
		'web_story' => array( 'Web Stories', 'Web Story', 'stories', 'dashicons-format-gallery', 27, true ),
		'holiday'   => array( 'Holidays', 'Holiday', 'holidays', 'dashicons-palmtree', 28, true ),
		'panchang'  => array( 'Panchang Entries', 'Panchang Entry', 'panchang', 'dashicons-admin-site-alt3', 29, false ),
		'city'      => array( 'Cities', 'City', 'cities', 'dashicons-location-alt', 30, false ),
	);

	foreach ( $cpts as $slug => $c ) {
		register_post_type(
			$slug,
			array(
				'labels'              => array(
					'name'          => $c[0],
					'singular_name' => $c[1],
					'menu_name'     => $c[0],
					'add_new_item'  => 'Add New ' . $c[1],
					'edit_item'     => 'Edit ' . $c[1],
					'all_items'     => 'All ' . $c[0],
				),
				'public'              => $c[5],
				'publicly_queryable'  => $c[5],
				'show_ui'             => true,
				'show_in_rest'        => true,
				'has_archive'         => $c[5],
				'menu_icon'           => $c[3],
				'menu_position'       => $c[4],
				'rewrite'             => array( 'slug' => $c[2], 'with_front' => false ),
				'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
				'show_in_nav_menus'   => $c[5],
				'exclude_from_search' => ! $c[5],
			)
		);
	}

	ifs_register_tax( 'ifs_deity', 'Deity', 'Deities', array( 'festival', 'katha', 'deity' ) );
	ifs_register_tax( 'ifs_tradition', 'Tradition', 'Traditions', array( 'festival' ) );
	ifs_register_tax( 'ifs_region', 'Region', 'Regions', array( 'festival', 'holiday' ) );
	ifs_register_tax( 'ifs_festival_type', 'Festival Type', 'Festival Types', array( 'festival' ) );
	ifs_register_tax( 'ifs_month', 'Month', 'Months', array( 'festival' ) );
	ifs_register_tax( 'ifs_vrat_type', 'Vrat Type', 'Vrat Types', array( 'vrat' ) );
	ifs_register_tax( 'ifs_muhurat_type', 'Muhurat Type', 'Muhurat Types', array( 'muhurat' ) );
	ifs_register_tax( 'ifs_language', 'Language', 'Languages', array( 'katha', 'greeting' ), false );
	ifs_register_tax( 'ifs_festival_name', 'Festival', 'Festival Tags', array( 'greeting', 'web_story' ), false );
	ifs_register_tax( 'ifs_state', 'State', 'States', array( 'holiday' ) );
	ifs_register_tax( 'ifs_holiday_type', 'Holiday Type', 'Holiday Types', array( 'holiday' ) );
	ifs_register_tax( 'ifs_city', 'City', 'Cities', array( 'panchang' ) );
}
add_action( 'init', 'ifs_register_content_types' );

/**
 * Register a taxonomy with consistent defaults.
 *
 * @param string   $slug       Taxonomy slug.
 * @param string   $singular   Singular label.
 * @param string   $plural     Plural label.
 * @param string[] $objects    Post types.
 * @param bool     $hierarchy  Hierarchical (category-like) or tag-like.
 */
function ifs_register_tax( $slug, $singular, $plural, $objects, $hierarchy = true ) {
	register_taxonomy(
		$slug,
		$objects,
		array(
			'labels'            => array(
				'name'          => $plural,
				'singular_name' => $singular,
				'search_items'  => 'Search ' . $plural,
				'all_items'     => 'All ' . $plural,
				'edit_item'     => 'Edit ' . $singular,
				'add_new_item'  => 'Add New ' . $singular,
				'menu_name'     => $plural,
			),
			'hierarchical'      => $hierarchy,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => str_replace( '_', '-', $slug ) ),
		)
	);
}

/**
 * Map of content type => list of dashboard columns.
 *
 * @return array
 */
function ifs_admin_column_map() {
	return array(
		'festival' => array(
			'ifs_date'  => array( 'Date', '_ifs_date' ),
			'ifs_deity' => array( 'Deity', '_ifs_deity' ),
		),
		'vrat'     => array(
			'ifs_date' => array( 'Date', '_ifs_date' ),
			'ifs_type' => array( 'Vrat Type', '_ifs_vrat_type' ),
		),
		'muhurat'  => array(
			'ifs_date'     => array( 'Date', '_ifs_date' ),
			'ifs_category' => array( 'Category', '_ifs_category' ),
		),
		'holiday'  => array(
			'ifs_date'  => array( 'Date', '_ifs_date' ),
			'ifs_state' => array( 'State', '_ifs_state' ),
		),
		'panchang' => array(
			'ifs_date' => array( 'Date', '_ifs_date' ),
		),
	);
}

/**
 * Add the custom columns to each list table.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function ifs_add_admin_columns( $columns ) {
	$screen = get_current_screen();
	if ( ! $screen || ! isset( ifs_admin_column_map()[ $screen->post_type ] ) ) {
		return $columns;
	}
	foreach ( ifs_admin_column_map()[ $screen->post_type ] as $key => $col ) {
		$columns[ $key ] = $col[0];
	}
	return $columns;
}
add_filter( 'manage_posts_columns', 'ifs_add_admin_columns' );

/**
 * Render custom column values.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function ifs_render_admin_column( $column, $post_id ) {
	$post_type = get_post_type( $post_id );
	$map       = ifs_admin_column_map();
	if ( ! isset( $map[ $post_type ][ $column ] ) ) {
		return;
	}
	$value = get_post_meta( $post_id, $map[ $post_type ][ $column ][1], true );
	echo esc_html( (string) $value ? $value : '—' );
}
add_action( 'manage_posts_custom_column', 'ifs_render_admin_column', 10, 2 );

/**
 * Make date columns sortable.
 *
 * @param array $columns Sortable columns.
 * @return array
 */
function ifs_sortable_columns( $columns ) {
	$columns['ifs_date'] = 'ifs_date';
	return $columns;
}
foreach ( array( 'festival', 'vrat', 'muhurat', 'holiday', 'panchang' ) as $ifs_pt ) {
	add_filter( "manage_edit-{$ifs_pt}_sortable_columns", 'ifs_sortable_columns' );
}

/**
 * Sort list tables by the stored date meta.
 *
 * @param WP_Query $query Admin query.
 */
function ifs_admin_sort_by_date( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( 'ifs_date' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', '_ifs_date' );
		$query->set( 'orderby', 'meta_value' );
	}
}
add_action( 'pre_get_posts', 'ifs_admin_sort_by_date' );
