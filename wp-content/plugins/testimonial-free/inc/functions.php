<?php

// Post thumbnails
add_theme_support( 'post-thumbnails' );
add_image_size( 'tf-client-image-size', 100, 100, true );


function testimonial_free_register_post_type() {

	$labels = array(
		'name'               => __( 'Testimonials', 'shapedplugin' ),
		'singular_name'      => __( 'Testimonial', 'shapedplugin' ),
		'add_new'            => _x( 'Add New Testimonial', 'shapedplugin', 'shapedplugin' ),
		'add_new_item'       => __( 'Add New Testimonial', 'shapedplugin' ),
		'edit_item'          => __( 'Edit Testimonial', 'shapedplugin' ),
		'new_item'           => __( 'New Testimonial', 'shapedplugin' ),
		'view_item'          => __( 'View Testimonial', 'shapedplugin' ),
		'search_items'       => __( 'Search Testimonials', 'shapedplugin' ),
		'not_found'          => __( 'No Testimonials found', 'shapedplugin' ),
		'not_found_in_trash' => __( 'No Testimonials found in Trash', 'shapedplugin' ),
		'parent_item_colon'  => __( 'Parent Testimonial:', 'shapedplugin' ),
		'menu_name'          => __( 'Testimonials', 'shapedplugin' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => 'dashicons-format-quote',
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(
			'title',
			'editor',
			'thumbnail',
			'page-attributes'
		)
	);

	register_post_type( 'testimonial-free', $args );
}

add_action( 'init', 'testimonial_free_register_post_type' );


// Change title placeholder
function sp_testimonial_free_change_default_title($title) {
	$screen = get_current_screen();
	if('testimonial-free' == $screen->post_type) {
		$title = 'Type client name here';
	}
	return $title;
}
add_filter('enter_title_here','sp_testimonial_free_change_default_title');



/* Including files */
if(file_exists( SP_TF_PATH . 'inc/options/meta-box.php')){
	require_once(SP_TF_PATH . "inc/options/meta-box.php");
}
if(file_exists( SP_TF_PATH . 'inc/shortcodes.php')){
	require_once(SP_TF_PATH . "inc/shortcodes.php");
}
