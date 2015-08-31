<?php
/*
 * Plugin Name: WP SUAPI
 * Plugin URI:  https://github.com/WP-SUHV/wp-suapi
 * Description: Wordpress plugin to connect with the swiss unihockey API v2.
 * Version:     0.1.0
 * Author:      Philipp Meier
 * Author URI:  https://github.com/meip
 * Text Domain: suapi
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: wp-suapi
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Philipp Meier
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require __DIR__ . '/vendor/autoload.php';
// Load plugin class files
require_once( 'includes/class-wp-suapi.php' );
require_once( 'includes/class-wp-suapi-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-wp-suapi-admin-api.php' );
require_once( 'includes/lib/class-wp-suapi-post-type.php' );
require_once( 'includes/lib/class-wp-suapi-taxonomy.php' );

/**
 * Returns the main instance of WP_SUAPI to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WP_SUAPI
 */
function WP_SUAPI () {
	$instance = WP_SUAPI::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WP_SUAPI_Settings::instance( $instance );
	}

	return $instance;
}

WP_SUAPI();
