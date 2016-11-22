<?php
/*
Plugin Name: Testimonial Free
Description: This plugin will enable Testimonial in your WordPress site.
Plugin URI: http://shapedplugin.com/plugin/testimonial-pro
Author: ShapedPlugin
Author URI: http://shapedplugin.com
Version: 1.2
*/


/* Define */
define( 'SP_TF_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
define( 'SP_TF_PATH', plugin_dir_path( __FILE__ ) );


/* Including files */
if(file_exists( SP_TF_PATH . 'inc/scripts.php')){
	require_once(SP_TF_PATH . "inc/scripts.php");
}
if(file_exists( SP_TF_PATH . 'inc/functions.php')){
	require_once(SP_TF_PATH . "inc/functions.php");
}