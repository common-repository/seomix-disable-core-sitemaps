<?php
/**
 * Plugin Name:         SX Disable WordPress XML sitemaps
 * Plugin URI:          https://www.seomix.fr
 * Description:         Disable core sitemap functionality
 * Author:              Daniel Roch - SeoMix
 * Author URI:          https://daniel-roch.fr/
 * Contributors:        confridin, seokey, seomix
 * Text Domain: 		seomix-disable-core-sitemaps
 * Version:             1.0
 * Requires at least:   5.5
 * Tested up to:        6.6
 * Requires PHP:        5.6
 * License:             GPLv2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright (C) 2020, SeoMix - contact@seomix.fr
 *
 */


/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Doing it wrong' );
}


/**
 * Disable Core sitemap functionnality
 *
 * @since   1.0
 * @author  Daniel Roch
 *
 * @return  void
 */
add_filter( 	'wp_sitemaps_enabled', '__return_false' );
remove_action( 	'init', 'wp_sitemaps_get_server' );


/**
 * Plugin activation : add option to flush rules later
 */
function seomix_core_sitemaps_plugin_activate() { 
    // Add option to propertly flush rules when sitemap would be really deactivated (it's too soon to flush now)
    if ( ! get_option( 'seomix_core_sitemaps_plugin_flush' ) ) {
        add_option( 'seomix_core_sitemaps_plugin_flush', true );
    }
}
register_activation_hook( __FILE__, 'seomix_core_sitemaps_plugin_activate' );


/**
 * Flush rewrite rules if necessary
 */
function seomix_core_sitemaps_plugin_flush() {
    // Do we need to flush ?
    if ( get_option( 'seomix_core_sitemaps_plugin_flush' ) ) {
        // Flush now
        flush_rewrite_rules();
        // Delete useless option
        delete_option( 'seomix_core_sitemaps_plugin_flush' );
    }
}
add_action( 'init', 'seomix_core_sitemaps_plugin_flush', 20 );


/**
 * Plugin deactivation.
 */
function seomix_core_sitemaps_plugin_deactivate() { 
	// Deactivation hooks are run after init, so when we deactivate our plugin, sitemap are still deactivated.
	// We need to activate them again before flushing rewrites rules
	add_filter( 	'wp_sitemaps_enabled', '__return_true');
	global $wp_sitemaps;
	// If there isn't a global instance, set and bootstrap the sitemaps system.
	if ( empty( $wp_sitemaps ) ) {
		$wp_sitemaps = new WP_Sitemaps();
		$wp_sitemaps->init();

		/**
		 * Fires when initializing the Sitemaps object.
		 *
		 * Additional sitemaps should be registered on this hook.
		 *
		 * @since 5.5.0
		 *
		 * @param WP_Sitemaps $wp_sitemaps Sitemaps object.
		 */
		do_action( 'wp_sitemaps_init', $wp_sitemaps );
	}
    // Flush rules
    flush_rewrite_rules(); 
}
register_deactivation_hook( __FILE__, 'seomix_core_sitemaps_plugin_deactivate' );


/**
 * Redirect 404 for old sitemaps URL
 *
 * @since   1.0
 * @author  Daniel Roch
 *
 * @return  void
 */
add_action ( 'template_redirect', 'seomix_core_sitemaps_redirect', 1 );
function seomix_core_sitemaps_redirect() {
	if ( is_404() ) {
		// Home URL
		$home = trailingslashit ( home_url() );
		// Core Sitemap URL start
		$homesitemap 		= $home . 'wp-sitemap';
		// Get current URL
		$current_url 		= seomix_core_sitemaps_get_current_url();
		// Does current URL begins with wp-sitemap ?
		if ( true === seomix_core_sitemaps_stringbeginswith( $current_url , $homesitemap ) ) {
			// Does it ends with .xml
			if ( true === seomix_core_sitemaps_stringendswith( $current_url , 'xml' ) ) {
				// Do you need another URL ?
				$redirecturl = apply_filters( 'seomix_core_sitemaps_redirect_target', $home, $current_url );
				// Redirect
				wp_safe_redirect( esc_url( $redirecturl ), 301 );
				die;
			}
		}
	}
}


/**
 * Get Current user URL
 *
 * @since   1.0
 * @author  Daniel Roch, seokey
 *
 * @see     https://wordpress.org/plugins/sf-move-login/ (some code here is inspired from "Move Login" WordPress plugin : it's a plugin worth checking)
 * @see     is_ssl()
 *
 * @return  (string) $url User current URL
 */
function seomix_core_sitemaps_get_current_url() {
	// Get port Data if necessary
	$portused = isset( $_SERVER['SERVER_PORT'] ) ? intval( $_SERVER['SERVER_PORT'] ) : '';
	$port     = ( 80 !== $portused && 443 !== $portused ) ? ( ':' . $portused ) : '';
	// Get Request URI
	$uri = ! empty( $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] ) ? $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] : '';
	$uri = empty( $uri ) ? $_SERVER['REQUEST_URI'] : '';
	// Get final URL
	$currenturl = 'http' . ( is_ssl() ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'] . $port . $uri;
	// return final URL
	return esc_url( $currenturl );
}


/**
 * Helper function : dos this begins with ?
 *
 * @since   1.0
 * @author  Daniel Roch
 *
 * @return  (string) true or false
 */
function seomix_core_sitemaps_stringbeginswith( $s1, $s2 ){
    return substr( $s1, 0, strlen( $s2 ) ) === $s2 ? true : false;
}


/**
 * Helper function : dos this end with ?
 *
 * @since   1.0
 * @author  Daniel Roch
 *
 * @return  (string) true or false
 */
function seomix_core_sitemaps_stringendswith( $s1, $s2 ){
    return substr( $s1, -strlen( $s2 ) ) == $s2 ? true : false;
}
