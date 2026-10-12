<?php
/**
 * Advertising slots: vertical skyscraper rails on wide screens plus horizontal
 * slots between sections. Paste AdSense code into the matching Customizer fields.
 * Each slot reserves its height so the page does not jump when an ad loads.
 *
 * @package IndianFestivals
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ifs_ad_slot_markup' ) ) {
	/**
	 * Render one labelled ad slot.
	 *
	 * @param string $slot   Slot key and Customizer field suffix.
	 * @param string $format Reserve dimensions, e.g. 160x600.
	 * @param string $class  Extra classes.
	 */
	function ifs_ad_slot_markup( $slot, $format, $class = '' ) {
		$code = trim( (string) ifs_opt( 'ad_' . $slot, '' ) );
		printf(
			'<aside class="ad-slot ad-slot--%1$s %2$s" data-ad-slot="%1$s" data-ad-format="%3$s" aria-label="%4$s">',
			esc_attr( $slot ),
			esc_attr( $class ),
			esc_attr( $format ),
			esc_attr__( 'Advertisement', 'indian-festivals' )
		);
		if ( '' !== $code ) {
			echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin-supplied ad code.
		} else {
			printf(
				'<span class="ad-slot-label"><span class="ad-slot-word">%1$s</span><span class="ad-slot-size">%2$s</span></span>',
				esc_html__( 'Advertisement', 'indian-festivals' ),
				esc_html( $format )
			);
		}
		echo '</aside>';
	}
}
?>
<?php if ( ifs_opt( 'ad_rail_enabled', true ) ) : ?>
<aside class="ad-rail ad-rail--left"><?php ifs_render_ad('rail_left'); ?></aside>
<aside class="ad-rail ad-rail--right"><?php ifs_render_ad('rail_right'); ?></aside>
<?php endif; ?>
