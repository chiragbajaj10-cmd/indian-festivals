<?php
/**
 * Theme options: Appearance > Customize with live preview.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tools shown in the home tools grid. Each has a show/hide toggle.
 *
 * @return array key => label
 */
function ifs_tools() {
	return array(
		'panchang'       => "Today's Panchang",
		'panchang_date'  => 'Panchang by date and city',
		'date_finder'    => 'Festival date finder',
		'vrat_calendar'  => 'Vrat calendar',
		'choghadiya'     => 'Choghadiya',
		'rahu_kaal'      => 'Rahu Kaal, Yamaganda, Gulika',
		'abhijit'        => 'Abhijit and Brahma Muhurat',
		'muhurat_finder' => 'Muhurat finder',
		'moonrise'       => 'Moonrise tool',
		'eclipse'        => 'Eclipse and Panchak',
		'holiday_list'   => 'Holiday list by state',
		'wish_generator' => 'Wish generator',
		'countdown'      => 'Countdown widget',
		'add_calendar'   => 'Add to calendar (.ics)',
		'printable'      => 'Printable calendar',
		'reminder'       => 'Reminder sign-up',
		'embed'          => 'Embeddable widget',
		'date_converter' => 'Hindu date converter',
	);
}

/**
 * Default values for every theme option.
 *
 * @return array
 */
function ifs_defaults() {
	$defaults = array(
		'logo_dark'             => '',
		'logo_size'             => 120,
		'default_city'          => 'Varanasi',
		'lang_hi_label'         => 'हिंदी',
		'lang_en_label'         => 'English',
		'lang_switch_on'        => true,

		'preset'                => 'brown_desk',
		'color_primary'         => '#7e1c1c',
		'color_secondary'       => '#4a2a16',
		'color_accent'          => '#c69a43',
		'color_background'      => '#efe1bf',
		'color_text'            => '#3a2315',
		'color_link'            => '#7e1c1c',
		'color_header'          => '#1f120a',
		'color_footer'          => '#140a05',
		'color_button'          => '#a42b2b',

		'heading_font'          => 'Cinzel',
		'body_font'             => 'Crimson Text',
		'heading_weight'        => 700,
		'body_weight'           => 400,
		'base_font_size'        => 17,
		'line_height'           => 1.5,
		'self_host_fonts'       => false,

		'header_layout'         => 'centered',
		'topbar_on'             => true,
		'ticker_on'             => false,
		'search_on'             => true,
		'sticky_on'             => true,
		'mobile_menu_style'     => 'slide',
		'menu_hover_style'      => 'underline',

		'home_hero'             => true,
		'home_info_strip'       => true,
		'home_popular'          => true,
		'home_panchang'         => true,
		'home_journey'          => true,
		'home_deity'            => true,
		'home_story'            => true,
		'home_vrat'             => true,
		'home_tools'            => true,
		'home_calendar'         => true,
		'home_articles'         => true,
		'home_greetings'        => true,
		'home_stories'          => true,
		'home_subscribe'        => true,
		'home_ads'              => true,
		'home_order'            => 'hero,info_strip,popular,journey,deity,story,vrat,tools,calendar,articles,greetings,stories,subscribe',

		'show_breadcrumbs'      => true,
		'show_related'          => true,
		'show_share'            => true,
		'show_author_box'       => true,
		'show_prev_next'        => true,

		'container_width'       => 1280,
		'section_spacing'       => 22,
		'card_style'            => 'parchment',
		'border_style'          => 'double_gold',
		'radius'                => 6,
		'deco_mandala'          => true,
		'deco_toran'            => true,
		'deco_wood'             => true,
		'deco_grain'            => true,
		'deco_torn'             => true,
		'archive_columns'       => 4,
		'posts_per_page'        => 12,

		'footer_copyright'      => '© Indian Festivals. All rights reserved.',
		'footer_sanskrit'       => 'सर्वे भवन्तु सुखिनः',
		'footer_sanskrit_en'    => 'May all be happy, may all be free from illness',
		'footer_disclaimer'     => 'Dates are indicative. Verify dates with an authentic panchang before observing any festival.',

		'default_timezone'      => 'Asia/Kolkata',
		'date_format'           => 'j F Y',
		'calendar_start'        => 0,
		'data_source'           => 'manual',
		'panchang_api_url'      => '',
		'panchang_api_key'      => '',
		'festival_lang'         => 'en',
		'regional_calendar'     => 'north',
		'disclaimer_text'       => 'Festival and panchang dates vary by region and location. Verify with an authentic panchang.',
		'story_quote'           => 'Dharma is the path; devotion is the light that guides it.',

		'ad_header'             => '',
		'ad_in_content'         => '',
		'ad_sidebar'            => '',
		'ad_footer'             => '',
		'ad_rail_left'          => '',
		'ad_rail_right'         => '',
		'ad_top'                => '',
		'ad_mid'                => '',
		'tools_strapline'       => 'Eighteen tools for every auspicious moment',
		'ad_height'             => 250,
		'ga_id'                 => '',
		'adsense_publisher_id'  => '',
		'gsc_tag'               => '',
		'custom_header_code'    => '',
		'custom_footer_code'    => '',

		'schema_on'             => true,
		'schema_event'          => true,
		'schema_faq'            => true,
		'schema_article'        => true,
		'schema_breadcrumb'     => true,
		'schema_website'        => true,
		'default_social_image'  => '',

		'whatsapp_channel'      => '',
		'social_facebook'       => '',
		'social_instagram'      => '',
		'social_youtube'        => '',
		'social_x'              => '',
		'social_pinterest'      => '',
		'email_form_embed'      => '',
		'push_code'             => '',

		'custom_css'            => '',
		'motion_enabled'        => true,
		'intro_enabled'         => true,
		'perf_lazy'             => true,
		'perf_preload_fonts'    => true,
		'perf_remove_emoji'     => true,
	);

	foreach ( array_keys( ifs_tools() ) as $tool ) {
		$defaults[ 'tool_' . $tool ] = true;
	}
	return array_merge( $defaults, ifs_theme_option_defaults() );
}

/**
 * Read a theme option.
 *
 * @param string $key     Option key without the ifs_ prefix.
 * @param mixed  $fallback Optional fallback when no default is defined.
 * @return mixed
 */
function ifs_opt( $key, $fallback = '' ) {
	$defaults = ifs_defaults();
	$default  = array_key_exists( $key, $defaults ) ? $defaults[ $key ] : $fallback;
	return get_theme_mod( 'ifs_' . $key, $default );
}

/**
 * Colour presets. The "custom" preset uses the colour controls.
 *
 * @return array
 */
function ifs_presets() {
	return array(
		'brown_desk'     => array(
			'primary'    => '#7e1c1c',
			'secondary'  => '#4a2a16',
			'accent'     => '#c69a43',
			'background' => '#efe1bf',
			'text'       => '#3a2315',
			'link'       => '#7e1c1c',
			'header'     => '#1f120a',
			'footer'     => '#140a05',
			'button'     => '#a42b2b',
		),
		'saffron_temple' => array(
			'primary'    => '#c4511a',
			'secondary'  => '#7e1c1c',
			'accent'     => '#f2c14e',
			'background' => '#fff4e0',
			'text'       => '#3b1d0c',
			'link'       => '#a8430f',
			'header'     => '#3b1d0c',
			'footer'     => '#7e1c1c',
			'button'     => '#e0701f',
		),
		'royal_blue'     => array(
			'primary'    => '#1d3f7a',
			'secondary'  => '#0f1d3c',
			'accent'     => '#c69a43',
			'background' => '#eef2fa',
			'text'       => '#14213d',
			'link'       => '#1d3f7a',
			'header'     => '#0f1d3c',
			'footer'     => '#0a1430',
			'button'     => '#1d3f7a',
		),
	);
}

/**
 * Resolve the active palette (preset or custom colours).
 *
 * @return array
 */
function ifs_palette() {
	$presets = ifs_presets();
	$preset  = ifs_opt( 'preset', 'brown_desk' );
	$base    = $presets['brown_desk'];
	$out     = array();

	foreach ( $base as $key => $fallback ) {
		if ( isset( $presets[ $preset ] ) ) {
			$out[ $key ] = $presets[ $preset ][ $key ];
			continue;
		}
		$value       = sanitize_hex_color( (string) ifs_opt( 'color_' . $key, $fallback ) );
		$out[ $key ] = $value ? $value : $fallback;
	}
	return $out;
}

/**
 * Sanitizers.
 */
function ifs_sanitize_checkbox( $value ) {
	return (bool) $value;
}

function ifs_sanitize_number( $value ) {
	return is_numeric( $value ) ? 0 + $value : 0;
}

function ifs_sanitize_raw_code( $value ) {
	// Admin-only embed codes (ads, analytics, push, forms). Only users with customize capability can save these.
	return wp_unslash( (string) $value );
}

function ifs_sanitize_css( $value ) {
	return str_ireplace( '</style', '', wp_strip_all_tags( (string) $value ) );
}

/**
 * Pick a sanitizer by control type.
 *
 * @param string $type Control type.
 * @param string $key  Option key.
 * @return string callable name
 */
function ifs_sanitizer_for( $type, $key ) {
	if ( 'checkbox' === $type ) {
		return 'ifs_sanitize_checkbox';
	}
	if ( 'color' === $type ) {
		return 'sanitize_hex_color';
	}
	if ( 'url' === $type || 'image' === $type ) {
		return 'esc_url_raw';
	}
	if ( 'range' === $type || 'number' === $type ) {
		return 'ifs_sanitize_number';
	}
	if ( 'raw' === $type ) {
		return 'ifs_sanitize_raw_code';
	}
	if ( 'css' === $type ) {
		return 'ifs_sanitize_css';
	}
	if ( 'textarea' === $type ) {
		return 'sanitize_textarea_field';
	}
	return 'sanitize_text_field';
}

/**
 * Customizer field definitions.
 *
 * Each row: key, section, label, type, [choices or range args].
 *
 * @return array
 */
function ifs_customizer_fields() {
	$heading_fonts = array(
		'Cinzel'               => 'Cinzel (classic serif, default)',
		'Playfair Display'     => 'Playfair Display',
		'Yatra One'            => 'Yatra One (Devanagari friendly)',
		'Noto Serif Devanagari' => 'Noto Serif Devanagari',
	);
	$body_fonts    = array(
		'Crimson Text'          => 'Crimson Text (default)',
		'Lora'                  => 'Lora',
		'Noto Sans Devanagari'  => 'Noto Sans Devanagari',
		'Tiro Devanagari Hindi' => 'Tiro Devanagari Hindi',
	);
	$weights       = array( 400 => '400 Regular', 600 => '600 Semi-bold', 700 => '700 Bold' );

	$fields = array(
		// General and branding.
		array( 'logo_dark', 'ifs_general', 'Logo for dark backgrounds', 'image' ),
		array( 'logo_size', 'ifs_general', 'Logo size (px)', 'range', array( 40, 220, 2 ) ),
		array( 'default_city', 'ifs_general', 'Default city', 'text' ),
		array( 'lang_hi_label', 'ifs_general', 'Hindi label', 'text' ),
		array( 'lang_en_label', 'ifs_general', 'English label', 'text' ),
		array( 'lang_switch_on', 'ifs_general', 'Show Hindi / English switch', 'checkbox' ),

		// Colours.
		array( 'preset', 'ifs_colors', 'Colour preset', 'select', array(
			'brown_desk'     => 'Brown Desk (default)',
			'saffron_temple' => 'Saffron Temple',
			'royal_blue'     => 'Royal Blue',
			'custom'         => 'Custom colours below',
		) ),
		array( 'color_primary', 'ifs_colors', 'Primary colour', 'color' ),
		array( 'color_secondary', 'ifs_colors', 'Secondary colour', 'color' ),
		array( 'color_accent', 'ifs_colors', 'Accent colour (gold)', 'color' ),
		array( 'color_background', 'ifs_colors', 'Background colour', 'color' ),
		array( 'color_text', 'ifs_colors', 'Text colour', 'color' ),
		array( 'color_link', 'ifs_colors', 'Link colour', 'color' ),
		array( 'color_header', 'ifs_colors', 'Header colour', 'color' ),
		array( 'color_footer', 'ifs_colors', 'Footer colour', 'color' ),
		array( 'color_button', 'ifs_colors', 'Button colour', 'color' ),

		// Typography.
		array( 'heading_font', 'ifs_typography', 'Heading font', 'select', $heading_fonts ),
		array( 'body_font', 'ifs_typography', 'Body font', 'select', $body_fonts ),
		array( 'heading_weight', 'ifs_typography', 'Heading weight', 'select', $weights ),
		array( 'body_weight', 'ifs_typography', 'Body weight', 'select', $weights ),
		array( 'base_font_size', 'ifs_typography', 'Base font size (px)', 'range', array( 14, 22, 1 ) ),
		array( 'line_height', 'ifs_typography', 'Line height', 'range', array( 1.2, 2, 0.05 ) ),
		array( 'self_host_fonts', 'ifs_typography', 'Self-host fonts (skip Google Fonts; place files in assets/fonts)', 'checkbox' ),

		// Header and menu.
		array( 'header_layout', 'ifs_header', 'Header layout', 'select', array(
			'centered' => 'Centered logo',
			'left'     => 'Left logo',
			'compact'  => 'Compact',
		) ),
		array( 'topbar_on', 'ifs_header', 'Show top bar', 'checkbox' ),
		array( 'ticker_on', 'ifs_header', 'Show festival ticker', 'checkbox' ),
		array( 'search_on', 'ifs_header', 'Show search', 'checkbox' ),
		array( 'sticky_on', 'ifs_header', 'Sticky header', 'checkbox' ),
		array( 'mobile_menu_style', 'ifs_header', 'Mobile menu style', 'select', array(
			'slide'      => 'Slide down',
			'dropdown'   => 'Dropdown',
			'fullscreen' => 'Full screen',
		) ),
		array( 'menu_hover_style', 'ifs_header', 'Menu active style', 'select', array(
			'underline' => 'Gold underline',
			'pill'      => 'Pill highlight',
			'gold'      => 'Gold text',
		) ),

		// Home sections (show or hide).
		array( 'home_hero', 'ifs_home', 'Hero with next festival', 'checkbox' ),
		array( 'home_info_strip', 'ifs_home', 'Info strip (today, tithi, next vrat, Rahu Kaal, city)', 'checkbox' ),
		array( 'home_popular', 'ifs_home', 'Popular festivals', 'checkbox' ),
		array( 'home_panchang', 'ifs_home', "Today's Panchang panel", 'checkbox' ),
		array( 'home_journey', 'ifs_home', 'Festival Journey', 'checkbox' ),
		array( 'home_deity', 'ifs_home', 'Browse by deity', 'checkbox' ),
		array( 'home_story', 'ifs_home', "Today's story", 'checkbox' ),
		array( 'home_vrat', 'ifs_home', 'Vrat and fasting scroll cards', 'checkbox' ),
		array( 'home_tools', 'ifs_home', 'Tools grid', 'checkbox' ),
		array( 'home_calendar', 'ifs_home', 'Calendar, upcoming and quick links band', 'checkbox' ),
		array( 'home_articles', 'ifs_home', 'Latest articles', 'checkbox' ),
		array( 'home_greetings', 'ifs_home', 'Greetings gallery', 'checkbox' ),
		array( 'home_stories', 'ifs_home', 'Web stories row', 'checkbox' ),
		array( 'home_subscribe', 'ifs_home', 'WhatsApp and email subscribe band', 'checkbox' ),
		array( 'home_ads', 'ifs_home', 'Ad slots between sections', 'checkbox' ),
		array( 'home_order', 'ifs_home', 'Section order (comma separated keys: hero, info_strip, popular, journey, deity, story, vrat, tools, calendar, articles, greetings, stories, subscribe)', 'text' ),

		// Display toggles.
		array( 'show_breadcrumbs', 'ifs_display', 'Breadcrumbs', 'checkbox' ),
		array( 'show_related', 'ifs_display', 'Related posts', 'checkbox' ),
		array( 'show_share', 'ifs_display', 'Share buttons', 'checkbox' ),
		array( 'show_author_box', 'ifs_display', 'Author box', 'checkbox' ),
		array( 'show_prev_next', 'ifs_display', 'Next and previous links', 'checkbox' ),

		// Layout and decorations.
		array( 'container_width', 'ifs_layout', 'Container width (px)', 'range', array( 960, 1440, 10 ) ),
		array( 'section_spacing', 'ifs_layout', 'Section spacing (px)', 'range', array( 10, 60, 2 ) ),
		array( 'card_style', 'ifs_layout', 'Card style', 'select', array(
			'parchment' => 'Parchment paper',
			'flat'      => 'Flat card',
			'frame'     => 'Framed card',
		) ),
		array( 'border_style', 'ifs_layout', 'Border style', 'select', array(
			'double_gold' => 'Double gold',
			'plain'       => 'Plain',
		) ),
		array( 'radius', 'ifs_layout', 'Corner radius (px)', 'range', array( 0, 20, 1 ) ),
		array( 'deco_mandala', 'ifs_layout', 'Mandala decoration', 'checkbox' ),
		array( 'deco_toran', 'ifs_layout', 'Toran / zigzag band', 'checkbox' ),
		array( 'deco_wood', 'ifs_layout', 'Wood texture background', 'checkbox' ),
		array( 'deco_grain', 'ifs_layout', 'Paper grain', 'checkbox' ),
		array( 'deco_torn', 'ifs_layout', 'Torn paper edges', 'checkbox' ),
		array( 'archive_columns', 'ifs_layout', 'Archive columns (2 to 5)', 'select', array( 2 => '2', 3 => '3', 4 => '4', 5 => '5' ) ),
		array( 'posts_per_page', 'ifs_layout', 'Posts per page', 'number' ),

		// Footer.
		array( 'footer_copyright', 'ifs_footer', 'Copyright text', 'text' ),
		array( 'footer_sanskrit', 'ifs_footer', 'Sanskrit line', 'text' ),
		array( 'footer_sanskrit_en', 'ifs_footer', 'Sanskrit line translation', 'text' ),
		array( 'footer_disclaimer', 'ifs_footer', 'Footer disclaimer', 'textarea' ),

		// Festival and panchang.
		array( 'default_timezone', 'ifs_festival', 'Timezone (PHP identifier)', 'text' ),
		array( 'date_format', 'ifs_festival', 'Date format (PHP date format)', 'select', array(
			'j F Y'  => '5 April 2026',
			'd/m/Y'  => '05/04/2026',
			'Y-m-d'  => '2026-04-05',
		) ),
		array( 'calendar_start', 'ifs_festival', 'Calendar starts on', 'select', array( 0 => 'Sunday', 1 => 'Monday' ) ),
		array( 'data_source', 'ifs_festival', 'Panchang data source', 'select', array(
			'manual' => 'Manual entries (Panchang Entries)',
			'csv'    => 'CSV import (stored as entries)',
			'api'    => 'Licensed API (URL and key below)',
		) ),
		array( 'panchang_api_url', 'ifs_festival', 'Panchang API URL', 'url' ),
		array( 'panchang_api_key', 'ifs_festival', 'Panchang API key', 'text' ),
		array( 'festival_lang', 'ifs_festival', 'Festival name language', 'select', array( 'en' => 'English', 'hi' => 'Hindi' ) ),
		array( 'regional_calendar', 'ifs_festival', 'Regional calendar', 'select', array(
			'north'    => 'North Indian (Purnimanta)',
			'south'    => 'South Indian (Amanta)',
			'bengali'  => 'Bengali',
			'gujarati' => 'Gujarati',
		) ),
		array( 'disclaimer_text', 'ifs_festival', 'Disclaimer shown on every date', 'textarea' ),
		array( 'story_quote', 'ifs_festival', "Quote for today's story", 'textarea' ),

		// Ads and tracking.
		array( 'ad_header', 'ifs_ads', 'AdSense code: header', 'raw' ),
		array( 'ad_in_content', 'ifs_ads', 'AdSense code: in content', 'raw' ),
		array( 'ad_sidebar', 'ifs_ads', 'AdSense code: sidebar', 'raw' ),
		array( 'ad_footer', 'ifs_ads', 'AdSense code: footer', 'raw' ),
		array( 'ad_rail_left', 'ifs_ads', 'AdSense code: left vertical rail (160x600)', 'raw' ),
		array( 'ad_rail_right', 'ifs_ads', 'AdSense code: right vertical rail (160x600)', 'raw' ),
		array( 'ad_top', 'ifs_ads', 'AdSense code: top leaderboard (970x90)', 'raw' ),
		array( 'ad_mid', 'ifs_ads', 'AdSense code: mid content (728x90)', 'raw' ),
		array( 'tools_strapline', 'ifs_ads', 'Strapline for the tools rail', 'text' ),
		array( 'ad_height', 'ifs_ads', 'Reserved ad height (px)', 'range', array( 60, 600, 10 ) ),
		array( 'adsense_publisher_id', 'ifs_ads', 'AdSense publisher ID (ca-pub-XXXXXXXXXXXXXXXX)', 'text' ),
		array( 'ga_id', 'ifs_ads', 'Google Analytics ID (G-XXXX)', 'text' ),
		array( 'gsc_tag', 'ifs_ads', 'Search Console meta tag', 'raw' ),
		array( 'custom_header_code', 'ifs_ads', 'Custom code in head', 'raw' ),
		array( 'custom_footer_code', 'ifs_ads', 'Custom code before body end', 'raw' ),

		// SEO.
		array( 'schema_on', 'ifs_seo', 'Structured data (schema) on', 'checkbox' ),
		array( 'schema_event', 'ifs_seo', 'Event schema for festivals', 'checkbox' ),
		array( 'schema_faq', 'ifs_seo', 'FAQ schema', 'checkbox' ),
		array( 'schema_article', 'ifs_seo', 'Article schema for posts', 'checkbox' ),
		array( 'schema_breadcrumb', 'ifs_seo', 'Breadcrumb schema', 'checkbox' ),
		array( 'schema_website', 'ifs_seo', 'WebSite schema on home', 'checkbox' ),
		array( 'default_social_image', 'ifs_seo', 'Default social share image', 'image' ),

		// Social and subscribe.
		array( 'whatsapp_channel', 'ifs_social', 'WhatsApp channel link', 'url' ),
		array( 'social_facebook', 'ifs_social', 'Facebook URL', 'url' ),
		array( 'social_instagram', 'ifs_social', 'Instagram URL', 'url' ),
		array( 'social_youtube', 'ifs_social', 'YouTube URL', 'url' ),
		array( 'social_x', 'ifs_social', 'X (Twitter) URL', 'url' ),
		array( 'social_pinterest', 'ifs_social', 'Pinterest URL', 'url' ),
		array( 'email_form_embed', 'ifs_social', 'Email form embed code', 'raw' ),
		array( 'push_code', 'ifs_social', 'Web push notification code', 'raw' ),

		// Advanced.
		array( 'custom_css', 'ifs_advanced', 'Custom CSS', 'css' ),
		array( 'motion_enabled', 'ifs_advanced', 'Enable 3D motion by default (visitors can toggle)', 'checkbox' ),
		array( 'intro_enabled', 'ifs_advanced', 'Show the scroll intro on the home page (gate, map, India, globe)', 'checkbox' ),
		array( 'perf_lazy', 'ifs_advanced', 'Lazy load images', 'checkbox' ),
		array( 'perf_preload_fonts', 'ifs_advanced', 'Preload fonts', 'checkbox' ),
		array( 'perf_remove_emoji', 'ifs_advanced', 'Remove emoji scripts', 'checkbox' ),
	);

	// Tool visibility toggles.
	foreach ( ifs_tools() as $tool => $label ) {
		$fields[] = array( 'tool_' . $tool, 'ifs_tools', 'Show tool: ' . $label, 'checkbox' );
	}

	return $fields;
}

/**
 * Register Customizer sections and settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function ifs_customize_register( $wp_customize ) {
	$sections = array(
		'ifs_general'    => 'Theme: General and Branding',
		'ifs_colors'     => 'Theme: Colors',
		'ifs_typography' => 'Theme: Typography',
		'ifs_header'     => 'Theme: Header and Menu',
		'ifs_home'       => 'Theme: Home Sections',
		'ifs_tools'      => 'Theme: Tools Grid',
		'ifs_display'    => 'Theme: Show and Hide',
		'ifs_layout'     => 'Theme: Layout and Style',
		'ifs_footer'     => 'Theme: Footer',
		'ifs_festival'   => 'Theme: Festival and Panchang',
		'ifs_ads'        => 'Theme: Ads and Tracking',
		'ifs_seo'        => 'Theme: SEO',
		'ifs_social'     => 'Theme: Social and Subscribe',
		'ifs_advanced'   => 'Theme: Advanced',
	);

	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'priority' => 40,
			)
		);
	}

	$defaults = ifs_defaults();

	foreach ( ifs_customizer_fields() as $field ) {
		list( $key, $section, $label, $type ) = $field;
		$extra   = isset( $field[4] ) ? $field[4] : null;
		$setting = 'ifs_' . $key;

		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => ifs_sanitizer_for( $type, $key ),
				'transport'         => 'refresh',
				'capability'        => 'edit_theme_options',
			)
		);

		if ( 'image' === $type ) {
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					$setting,
					array(
						'label'   => $label,
						'section' => $section,
					)
				)
			);
			continue;
		}

		$control = array(
			'label'   => $label,
			'section' => $section,
		);

		if ( 'checkbox' === $type ) {
			$control['type'] = 'checkbox';
		} elseif ( 'select' === $type ) {
			$control['type']    = 'select';
			$control['choices'] = $extra;
		} elseif ( 'range' === $type ) {
			$control['type']        = 'range';
			$control['input_attrs'] = array(
				'min'  => $extra[0],
				'max'  => $extra[1],
				'step' => $extra[2],
			);
		} elseif ( 'color' === $type ) {
			$control['type'] = 'color';
		} elseif ( 'number' === $type ) {
			$control['type'] = 'number';
		} elseif ( 'url' === $type ) {
			$control['type'] = 'url';
		} elseif ( in_array( $type, array( 'textarea', 'raw', 'css' ), true ) ) {
			$control['type'] = 'textarea';
		} else {
			$control['type'] = 'text';
		}

		$wp_customize->add_control( $setting, $control );
	}
}
add_action( 'customize_register', 'ifs_customize_register' );

/**
 * Live preview: no extra JS needed because settings use refresh transport.
 * This hook prints the active preset name so the preview tab shows it.
 */
function ifs_customize_preview_js() {
	wp_enqueue_script( 'ifs-theme', IFS_URI . '/assets/js/theme.js', array(), IFS_VERSION, true );
}
add_action( 'customize_preview_init', 'ifs_customize_preview_js' );
