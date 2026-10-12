<?php
/**
 * Indian Festivals footer with toran border, editable links and Sanskrit line.
 *
 * @package IndianFestivals
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$ifs_footer_tagline = get_bloginfo( 'description' );
if ( ! $ifs_footer_tagline || false !== stripos( $ifs_footer_tagline, 'Just another WordPress site' ) ) {
	$ifs_footer_tagline = 'FESTIVALS · PANCHANG · VRAT';
}
$ifs_socials = array(
	array( 'social_youtube', '▶', 'YouTube' ),
	array( 'social_instagram', '◎', 'Instagram' ),
	array( 'social_facebook', 'f', 'Facebook' ),
	array( 'social_x', '𝕏', 'X' ),
	array( 'social_pinterest', '❂', 'Pinterest' ),
);
?>
	<?php if ( function_exists( 'ifs_ad_slot_markup' ) ) { ifs_render_ad('in_content'); ifs_render_ad('footer'); } ?>
	<?php if ( ifs_opt( 'deco_toran', true ) ) : ?><div class="toran-divider" aria-hidden="true"></div><?php endif; ?>
	<footer class="site-footer-exact" role="contentinfo">
		<div class="footer-brand">
			<span class="om-circle footer-om" aria-hidden="true">ॐ</span>
			<div><div class="footer-brand-name"><?php echo esc_html( strtoupper( get_bloginfo( 'name' ) ) ); ?></div><div class="footer-brand-sub"><?php echo esc_html( strtoupper( $ifs_footer_tagline ) ); ?></div></div>
		</div>
		<div class="footer-shloka"><div class="footer-shloka-main"><?php echo esc_html( ifs_opt( 'footer_sanskrit', 'सर्वे भवन्तु सुखिनः' ) ); ?></div><div class="footer-shloka-sub"><?php echo esc_html( ifs_opt( 'footer_sanskrit_en', 'May all be happy, may all be free from illness' ) ); ?></div></div>
		<nav class="footer-links-col" aria-label="<?php esc_attr_e( 'Footer links', 'indian-festivals' ); ?>">
			<div>
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'items_wrap' => '<ul class="footer-menu-list">%3$s</ul>', 'depth' => 1 ) ); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a>
					<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy</a>
					<a href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>">Disclaimer</a>
					<a href="<?php echo esc_url( home_url( '/sitemap/' ) ); ?>">Sitemap</a>
				<?php endif; ?>
			</div>
			<div class="footer-social-icons">
				<?php foreach ( $ifs_socials as $item ) : ?>
					<?php $url = ifs_opt( $item[0], '' ); ?>
					<?php if ( $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener me" aria-label="<?php echo esc_attr( $item[2] ); ?>"><?php echo esc_html( $item[1] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</nav>
	</footer>
	<p class="preview-disclaimer"><?php echo esc_html( ifs_opt( 'footer_disclaimer', '' ) ); ?></p>
</div><!-- .wood-canvas -->
<?php if ( function_exists( 'ifs_adblock_modal' ) ) { ifs_adblock_modal(); } ?>
<?php wp_footer(); ?>
</body>
</html>
