<?php
/*
Plugin Name: Visual Composer Icon Tabs
Plugin URI: http://webholics.in
Description: Create beautiful tabs using Icons.
Version: 1.2.6
Author: Aman Saini
Author URI:  http://amansaini.me
*/



// don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

define( "VC_ICON_TABS_DIR", WP_PLUGIN_DIR."/".basename( dirname( __FILE__ ) ) );
define( "VC_ICON_TABS_URL", plugins_url()."/".basename( dirname( __FILE__ ) ) );


add_action( 'admin_init', 'vcit_init_addons' );
function vcit_init_addons() {

	$required_vc = '4.3';
	if ( defined( 'WPB_VC_VERSION' ) ) {
		if ( version_compare( WPB_VC_VERSION, $required_vc, '>=' ) ) {
			require_once VC_ICON_TABS_DIR . "/includes/class-vc-icon-tabs.php";
		} else {
			require_once VC_ICON_TABS_DIR . "/deprecated/class-vc-icon-tabs.php";
		}
	}

}// end init_addons

/* Require Core Files */

require_once VC_ICON_TABS_DIR . "/includes/class-vc-map.php";
require_once VC_ICON_TABS_DIR . "/includes/class-vc-icon-shortcode.php";
