<?php
/**
 * Tools section: journey banner style, every tool visible with large clear icons
 * and category filters (filtering is handled in assets/js/live-context.js).
 *
 * @package IndianFestivals
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ifs_icon_url = IFS_URI . '/assets/images/temple-icons.svg#';
// label, path, icon, blurb, group
$ifs_tools = array(
	array( "Today's Panchang", '/panchang/', 'yoga', 'Five limbs for today', 'panchang' ),
	array( 'Panchang by Date', '/panchang/', 'calendar', 'Any date, any city', 'panchang' ),
	array( 'Festival Finder', '/festivals/', 'star', 'Any festival, any year', 'festivals' ),
	array( 'Vrat Calendar', '/vrats/', 'moon', 'Next dates and parana', 'festivals' ),
	array( 'Choghadiya', '/panchang/', 'sunrise', 'Day and night slots', 'panchang' ),
	array( 'Rahu Kaal', '/panchang/', 'diya', 'Rahu, Yamaganda, Gulika', 'panchang' ),
	array( 'Abhijit Muhurat', '/panchang/', 'temple', 'Abhijit and Brahma', 'panchang' ),
	array( 'Muhurat Finder', '/muhurats/', 'kalash', 'Marriage to business', 'muhurat' ),
	array( 'Moonrise Tool', '/panchang/', 'moon', 'For Karwa Chauth', 'panchang' ),
	array( 'Eclipse & Panchak', '/panchang/', 'conch', 'With sutak timing', 'panchang' ),
	array( 'Holiday List', '/holidays/', 'calendar', 'By state, with download', 'festivals' ),
	array( 'Wish Generator', '/wishes/', 'lotus', 'Shareable greetings', 'share' ),
	array( 'Countdown', '/calendar/', 'diya', 'To the next festival', 'festivals' ),
	array( 'Add to Calendar', '/calendar/', 'calendar', 'ICS and Google link', 'share' ),
	array( 'Printable Calendar', '/calendar/', 'print', 'Monthly and yearly', 'share' ),
	array( 'Reminders', '/subscribe/', 'message', 'Push, WhatsApp, email', 'share' ),
	array( 'Embeddable Widget', '/calendar/', 'star', 'For other sites', 'share' ),
	array( 'Date Converter', '/panchang/', 'conch', 'Gregorian to Vikram', 'panchang' ),
);
$ifs_groups = array(
	'all'       => __( 'All tools', 'indian-festivals' ),
	'panchang'  => __( 'Panchang & timings', 'indian-festivals' ),
	'festivals' => __( 'Festivals & vrat', 'indian-festivals' ),
	'muhurat'   => __( 'Muhurat', 'indian-festivals' ),
	'share'     => __( 'Share & plan', 'indian-festivals' ),
);
?>
<section class="journey-banner tools-banner" aria-labelledby="tools-rail-title" data-tools>
	<h2 id="tools-rail-title" class="golden-ribbon journey-ribbon-pos"><?php esc_html_e( 'PANCHANG & FESTIVAL TOOLS', 'indian-festivals' ); ?></h2>
	<p class="ribbon-sub journey-sub"><?php echo esc_html( ifs_opt( 'tools_strapline', 'Eighteen tools for every auspicious moment' ) ); ?></p>

	<div class="tools-filters" role="tablist" aria-label="<?php esc_attr_e( 'Filter tools', 'indian-festivals' ); ?>">
		<?php foreach ( $ifs_groups as $key => $label ) : ?>
			<?php $count = 'all' === $key ? count( $ifs_tools ) : count( wp_list_filter( array_map( function ( $t ) { return array( 'g' => $t[4] ); }, $ifs_tools ), array( 'g' => $key ) ) ); ?>
			<button type="button" role="tab" class="tools-filter<?php echo 'all' === $key ? ' is-active' : ''; ?>" aria-selected="<?php echo 'all' === $key ? 'true' : 'false'; ?>" data-tools-filter="<?php echo esc_attr( $key ); ?>">
				<?php echo esc_html( $label ); ?> <span class="tools-filter-count"><?php echo (int) $count; ?></span>
			</button>
		<?php endforeach; ?>
	</div>

	<div class="tools-grid-big">
		<?php foreach ( $ifs_tools as $tool ) : ?>
			<a class="tool-tile-big" href="<?php echo esc_url( home_url( $tool[1] ) ); ?>" data-tool-group="<?php echo esc_attr( $tool[4] ); ?>">
				<span class="tool-tile-orb"><svg class="tool-tile-icon" aria-hidden="true"><use href="<?php echo esc_url( $ifs_icon_url . $tool[2] ); ?>"></use></svg></span>
				<span class="tool-tile-name"><?php echo esc_html( $tool[0] ); ?></span>
				<span class="tool-tile-blurb"><?php echo esc_html( $tool[3] ); ?></span>
				<span class="tool-tile-go" aria-hidden="true"><?php esc_html_e( 'Open →', 'indian-festivals' ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
