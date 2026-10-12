<?php
/**
 * Indian Festivals header: devotional desk design with links flanking the centred
 * Om brand, as in the reference design.
 *
 * @package IndianFestivals
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

try {
	$ifs_timezone = new DateTimeZone( (string) ifs_opt( 'default_timezone', 'Asia/Kolkata' ) );
} catch ( Exception $error ) {
	$ifs_timezone = new DateTimeZone( 'Asia/Kolkata' );
}
$ifs_now     = new DateTime( 'now', $ifs_timezone );
$ifs_samvat  = ifs_vikram_samvat( (int) $ifs_now->format( 'Y' ), (int) $ifs_now->format( 'n' ) );
$ifs_logo    = (string) ifs_opt( 'logo_dark', '' );
$ifs_tagline = get_bloginfo( 'description' );
if ( ! $ifs_tagline || false !== stripos( $ifs_tagline, 'Just another WordPress site' ) ) {
	$ifs_tagline = 'FESTIVALS · PANCHANG · VRAT';
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'indian-festivals' ); ?></a>

<div class="wood-canvas" data-motion-default="<?php echo ifs_opt( 'motion_enabled', true ) ? 'on' : 'off'; ?>">
	<?php get_template_part( 'template-parts/ad-rails' ); ?>
	<?php if ( ifs_opt( 'topbar_on', true ) ) : ?>
		<div class="top-strip">
			<div class="top-strip-left">
				<span>▦ &nbsp;<span data-live="date-long"><?php echo esc_html( wp_date( 'j F Y', $ifs_now->getTimestamp(), $ifs_timezone ) ); ?></span></span>
				<span class="top-divider">|</span>
				<span data-live="samvat"><?php echo esc_html( sprintf( __( 'Vikram Samvat %d', 'indian-festivals' ), $ifs_samvat ) ); ?></span>
				<span class="top-divider">|</span>
				<button type="button" class="location-chip" data-live-locate title="<?php esc_attr_e( 'Use my precise location', 'indian-festivals' ); ?>">♧ &nbsp;<span data-live="city"><?php echo esc_html( ifs_opt( 'default_city', 'Varanasi' ) ); ?></span> ⌄</button>
			</div>
			<div class="top-strip-right">
				<?php if ( ifs_opt( 'search_on', true ) ) : ?>
					<form role="search" method="get" class="search-box" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label class="sr-only" for="ifs-search-top"><?php esc_html_e( 'Search festivals', 'indian-festivals' ); ?></label>
						<input id="ifs-search-top" type="search" name="s" placeholder="<?php esc_attr_e( 'Search festivals, panchang...', 'indian-festivals' ); ?>">
						<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'indian-festivals' ); ?>"><svg class="ornament-svg" aria-hidden="true"><use href="<?php echo esc_url( IFS_URI . '/assets/images/temple-icons.svg#search' ); ?>"></use></svg></button>
					</form>
				<?php endif; ?>
				<button type="button" class="motion-toggle" data-motion-toggle aria-pressed="true" aria-label="<?php esc_attr_e( 'Turn off 3D motion', 'indian-festivals' ); ?>">✦ <span data-motion-label>3D ON</span></button>
				<?php if ( ifs_opt( 'lang_switch_on', true ) ) : ?>
					<span class="language-label"><?php echo esc_html( ifs_opt( 'lang_hi_label', 'हिंदी' ) ); ?></span>
					<span class="top-divider language-label">|</span>
					<span class="language-label language-current"><?php echo esc_html( ifs_opt( 'lang_en_label', 'English' ) ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<header class="main-header">
		<nav class="nav-links" aria-label="<?php esc_attr_e( 'Primary left', 'indian-festivals' ); ?>">
			<?php if ( has_nav_menu( 'primary-left' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'primary-left', 'container' => false, 'items_wrap' => '<ul class="nav-menu">%3$s</ul>', 'depth' => 1 ) ); ?>
			<?php else : ?>
				<a class="nav-link <?php echo is_front_page() ? 'active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">HOME</a>
				<a class="nav-link" href="<?php echo esc_url( get_post_type_archive_link( 'festival' ) ?: home_url( '/festivals/' ) ); ?>">FESTIVALS</a>
				<a class="nav-link" href="<?php echo esc_url( home_url( '/panchang/' ) ); ?>">PANCHANG</a>
				<a class="nav-link" href="<?php echo esc_url( get_post_type_archive_link( 'vrat' ) ?: home_url( '/vrats/' ) ); ?>">VRAT &amp; FASTS</a>
			<?php endif; ?>
		</nav>

		<a class="center-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php if ( $ifs_logo ) : ?>
				<img src="<?php echo esc_url( $ifs_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="brand-image" width="280" height="90">
			<?php else : ?>
				<span class="om-circle">ॐ</span>
				<span class="brand-title"><span class="brand-title-flourish">❦</span><span>INDIAN FESTIVALS</span><span class="brand-title-flourish">❧</span></span>
				<span class="brand-subtitle"><?php echo esc_html( strtoupper( $ifs_tagline ) ); ?></span>
			<?php endif; ?>
		</a>

		<nav class="nav-links" aria-label="<?php esc_attr_e( 'Primary right', 'indian-festivals' ); ?>">
			<?php if ( has_nav_menu( 'primary-right' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'primary-right', 'container' => false, 'items_wrap' => '<ul class="nav-menu">%3$s</ul>', 'depth' => 1 ) ); ?>
			<?php else : ?>
				<a class="nav-link" href="<?php echo esc_url( get_post_type_archive_link( 'muhurat' ) ?: home_url( '/muhurats/' ) ); ?>">MUHURAT</a>
				<a class="nav-link" href="<?php echo esc_url( home_url( '/wishes/' ) ); ?>">WISHES</a>
				<a class="nav-link" href="<?php echo esc_url( get_post_type_archive_link( 'katha' ) ?: home_url( '/katha/' ) ); ?>">KATHA</a>
				<a class="nav-link" href="<?php echo esc_url( get_post_type_archive_link( 'web_story' ) ?: home_url( '/stories/' ) ); ?>">WEB STORIES</a>
			<?php endif; ?>
		</nav>

		<button class="mobile-menu-button" type="button" data-mobile-menu aria-controls="mobile-scene-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'indian-festivals' ); ?>">☰</button>
		<nav id="mobile-scene-nav" class="mobile-menu-panel" data-mobile-panel aria-label="<?php esc_attr_e( 'Mobile menu', 'indian-festivals' ); ?>">
			<?php if ( has_nav_menu( 'mobile' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'mobile', 'container' => false, 'items_wrap' => '<ul class="mobile-menu-list">%3$s</ul>', 'depth' => 1 ) ); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">HOME</a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'festival' ) ?: home_url( '/festivals/' ) ); ?>">FESTIVALS</a>
				<a href="<?php echo esc_url( home_url( '/panchang/' ) ); ?>">PANCHANG</a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'vrat' ) ?: home_url( '/vrats/' ) ); ?>">VRAT &amp; FASTS</a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'muhurat' ) ?: home_url( '/muhurats/' ) ); ?>">MUHURAT</a>
				<a href="<?php echo esc_url( home_url( '/wishes/' ) ); ?>">WISHES</a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'katha' ) ?: home_url( '/katha/' ) ); ?>">KATHA</a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'web_story' ) ?: home_url( '/stories/' ) ); ?>">WEB STORIES</a>
			<?php endif; ?>
		</nav>
	</header>

	<div class="ornate-chain-divider" aria-hidden="true"><div class="chain-line"></div><span class="chain-center">❖ ✦ ❖</span><div class="chain-line"></div></div>
