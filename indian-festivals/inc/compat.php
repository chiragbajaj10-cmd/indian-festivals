<?php
/**
 * Hosting compatibility and safe activation guard.
 *
 * This file loads before all feature modules. A host running an unsupported PHP
 * version gets a clear WordPress admin notice instead of a white "critical error"
 * screen. The theme's supported baseline is PHP 7.4 / WordPress 5.8.
 *
 * @package IndianFestivals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the host can run this theme.
 *
 * @return bool
 */
function ifs_host_is_compatible() {
	global $wp_version;
	$php_ok = version_compare( PHP_VERSION, '7.4.0', '>=' );
	$wp_ok  = ! isset( $wp_version ) || version_compare( $wp_version, '5.8', '>=' );
	return $php_ok && $wp_ok;
}

/**
 * Explain the compatibility problem in wp-admin.
 */
function ifs_compatibility_notice() {
	global $wp_version;
	$messages = array();
	if ( version_compare( PHP_VERSION, '7.4.0', '<' ) ) {
		/* translators: %s: active PHP version */
		$messages[] = sprintf( __( 'PHP %s is too old. Indian Festivals needs PHP 7.4 or newer.', 'indian-festivals' ), PHP_VERSION );
	}
	if ( isset( $wp_version ) && version_compare( $wp_version, '5.8', '<' ) ) {
		/* translators: %s: active WordPress version */
		$messages[] = sprintf( __( 'WordPress %s is too old. Indian Festivals needs WordPress 5.8 or newer.', 'indian-festivals' ), $wp_version );
	}
	if ( $messages ) {
		echo '<div class="notice notice-error"><p><strong>Indian Festivals:</strong> ' . esc_html( implode( ' ', $messages ) ) . '</p></div>';
	}
}

/**
 * Small compatibility shims for very old WordPress installs.
 */
if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component );
	}
}
