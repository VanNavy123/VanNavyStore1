<?php
/*
Plugin Name: Media Center Extensions
Plugin URI: http://demo.transvelo.com/media-center-wp/
Description: Extensions for Media Center Wordpress Theme. Supplied as a separate plugin so that the customer does not find empty shortcodes on changing the theme.
Version: 2.6.3
Author: Transvelo
Author URI: http://transvelo.com/
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );

global $mc_plugin_dir;

$mc_plugin_dir = plugin_dir_path( __FILE__ );

//Load Modules

#-----------------------------------------------------------------
# Theme Shortcodes
#-----------------------------------------------------------------

require_once 'modules/theme-shortcodes/theme-shortcodes.php';

#-----------------------------------------------------------------
# Static Block Post Type for Megamenu Item
#-----------------------------------------------------------------

require_once 'modules/post-type-static-block/post-type-static-block.php';

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	#-----------------------------------------------------------------
	# Product Taxonomies
	#-----------------------------------------------------------------

	require_once 'modules/product-taxonomies/class-mc-product-taxonomies.php';

	#-----------------------------------------------------------------
	# Brands Short Code
	#-----------------------------------------------------------------

	require_once 'modules/theme-shortcodes/class-mc-brand-shortcodes.php';

}

#-----------------------------------------------------------------
# Visual Composer Extensions
#-----------------------------------------------------------------

if ( defined( 'WPB_VC_VERSION' ) ) {
	require_once 'modules/js_composer/js_composer.php';
}

#-----------------------------------------------------------------
# Ecwid
#-----------------------------------------------------------------

if( defined( 'ECWID_DEMO_STORE_ID' ) ) {
	require_once 'modules/ecwid/functions.php';
}

add_action( 'plugins_loaded', 'mediacenter_extensions_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.2.2
 */
function mediacenter_extensions_load_textdomain() {
	load_plugin_textdomain( 'mc-ext', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}