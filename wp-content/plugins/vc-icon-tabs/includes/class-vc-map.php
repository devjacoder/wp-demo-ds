<?php


// don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

class VCExtendIcontabs {
	function __construct() {

		// We safely integrate with VC with this hook
		add_action( 'vc_before_init', array( $this, 'integrateWithVC' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'vcit_add_scripts_to_admin' ) );

		//add_action( 'wp_ajax_vcit_save_post_data',  array( $this, 'vcit_save_post_data' ) );
		add_action( 'wp_ajax_vcit_get_img_url',  array( $this, 'vcit_get_img_url' ) );


	}


	public function vcit_add_scripts_to_admin() {

		$required_vc = '4.3';
		if ( defined( 'WPB_VC_VERSION' ) ) {
			if ( version_compare( WPB_VC_VERSION, $required_vc, '>=' ) ) {
				wp_enqueue_style( 'vcitadmincss', VC_ICON_TABS_URL.'/css/vc_extend-admin.css' );

			} else {
				wp_enqueue_style( 'vcitadmincss', VC_ICON_TABS_URL.'/deprecated/vc_extend-admin.css' );
			}
		}

		wp_enqueue_style( 'vciticoncss', VC_ICON_TABS_URL.'/css/font-awsome.css' );

	}




	public function integrateWithVC() {
		// Check if Visual Composer is installed
		if ( ! defined( 'WPB_VC_VERSION' ) || ! function_exists( 'wpb_map' ) ) {

			// Display notice that Visual Compser is required
			add_action( 'admin_notices', array( $this, 'showVcVersionNotice' ) );
			return;

		}
		add_shortcode_param( 'fa_vcit_icon', array( $this, 'icons' ), VC_ICON_TABS_URL.'/js/script-vc.js' );
		add_shortcode_param( 'inp_hidden', array( $this, 'create_hidden_input' ) );


		$required_vc = '4.3';
		if ( defined( 'WPB_VC_VERSION' ) ) {
			if ( version_compare( WPB_VC_VERSION, $required_vc, '>=' ) ) {
				$adminjs =  VC_ICON_TABS_URL.'/js/admin.js';

			} else {
				$adminjs =  VC_ICON_TABS_URL.'/deprecated/admin.js';

			}
		}

		$tab_id_1 = time() . '-1-' . rand( 0, 100 );
		$tab_id_2 = time() . '-2-' . rand( 0, 100 );
		vc_map( array(
				"name" => __( 'Icon Tabs', 'vcit' ),
				'base' => 'vc_icon_tabs',
				'show_settings_on_create' => true,
				'is_container' => true,
				'content_element' => true,
				'controls' => "full",
				'admin_enqueue_js'=> $adminjs,
				'front_enqueue_js'=> VC_ICON_TABS_URL.'/js/CBPFWTabs.js',
				'icon' => 'icon-vc_icon_tabs',
				'category' => __( 'Content', 'vcit' ),
				'description' => __( 'Tabbed content with Icons', 'vcit' ),
				"admin_enqueue_css" => array( VC_ICON_TABS_URL.'/css/vc_extend-admin.css' ),
				'params' => array(
					array(
						'type' => 'inp_hidden',
						'heading' => __( 'Hidden Tabs Container Id', 'vcit' ),
						'param_name' => 'tab_contid',
						'description' => __( 'Stores the tab container Id', 'vcit' ),
					),

					array(
						'type' => 'dropdown',
						'heading' => __( 'Tabs Position', 'vcit' ),
						'param_name' => 'tabs_position',
						'value' => array(
							__( 'Center', 'vcit' ) => 'center',
							__( 'Left', 'vcit' ) => 'left',
							__( 'Right', 'vcit' ) => "right"
						),
						'description' => __( 'Select Tabs Position.', 'vcit' )
					),

					array(
						'type' => 'colorpicker',
						'heading' => __( 'Tabs Background Color', 'vcit' ),
						'param_name' => 'background_color',
						'description' => __( 'Select background color for the tab', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",

					),

					array(
						'type' => 'colorpicker',
						'heading' => __( 'Text Color', 'wvcitpb' ),
						'param_name' => 'font_color',
						'description' => __( 'Select text color for tab headings', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",

					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Text Color on mouse hover', 'vcit' ),
						'param_name' => 'font_hover_color',
						'description' => __( 'Select the text color when mouse is on tab', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",

					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Tab Hover Color', 'vcit' ),
						'param_name' => 'hover_color',
						'description' => __( 'Select background color when mouse is on tab , leave it empty for no color', 'wpb' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",
					),


					array(
						'type' => 'colorpicker',
						'heading' => __( 'Active Tab Background Color', 'wpb' ),
						'param_name' => 'active_tab_color',
						'description' => __( 'Select current tab background color', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",

					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Active Tab Text Color', 'wpb' ),
						'param_name' => 'active_tab_font_color',
						'description' => __( 'Select current tab text color', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",

					),

					array(
						'type' => 'colorpicker',
						'heading' => __( 'Border Color', 'vcit' ),
						'param_name' => 'border_color',
						'description' => __( 'Select border color for the tabs', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Border Width', 'vcit' ),
						'param_name' => 'border_width',
						'description' => __( 'Add border width for the tabs.Only use numeric value e.g 8', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Margin', 'vcit' ),
						'param_name' => 'margin',
						'value' => '0px 0.25em',
						'description' => __( 'Margin between tabs e.g 10px or 1em.Default is 0px 0.25em(top-bottom left-right)', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",
					),

					array(
						'type' => 'textfield',
						'heading' => __( 'Padding', 'vcit' ),
						'param_name' => 'padding',
						'value' => '0px 1.25em',
						'description' => __( 'Padding around tab heading e.g 10px or 1em.Default is 0px 1.25em(top-bottom left-right)', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",
					),

					array(
						'type' => 'textfield',
						'heading' => __( 'Border Radius', 'vcit' ),
						'param_name' => 'border_radius',
						'description' => __( 'Add value for border radius to make your tabs round at top corners. Only use numeric value e.g 8', 'vcit' ),
						"edit_field_class" => "vc_col-sm-6 vc_column",
					),


					// array(
					//  'type' => 'textfield',
					//  'heading' => __( 'Extra class name', 'vcit' ),
					//  'param_name' => 'el_class',
					//  'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'vcit' )
					// )
				),

				'custom_markup' => '
				<div class="wpb_tabs_holder wpb_holder vc_container_for_children">
				<ul class="tabs_controls">
				</ul>
				%content%
				</div>'
				,
				'default_content' => '
				[vc_icon_tab title="' . __( 'Tab 1', 'vcit' ) . '" tab_id="' . $tab_id_1 . '"][/vc_icon_tab]
				[vc_icon_tab title="' . __( 'Tab 2', 'vcit' ) . '" tab_id="' . $tab_id_2 . '"][/vc_icon_tab]
				',
				'js_view' => 'VcIconTabsView'
			) );


		vc_map( array(
				'name' => __( 'Tab', 'vcit' ),
				'base' => 'vc_icon_tab',
				'allowed_container_element' => 'vc_row',
				'is_container' => true,
				'content_element' => false,
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Title', 'vcit' ),
						'param_name' => 'title',
						'description' => __( 'Tab title.', 'vcit' )
					),
					array(
						'type' => 'tab_id',
						'heading' => __( 'Tab ID', 'vcit' ),
						'param_name' => "tab_id"
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Chose Icon type', 'vcit' ),
						'param_name' => 'icon_type',
						'value' => array(
							__( 'None', 'vcit' ) => 'none',
							__( 'Font Awesome', 'vcit' ) => 'fontawesome',
							__( 'Custom', 'vcit' ) => "custom"
						),

					),
					array(
						'type' => 'fa_vcit_icon',
						// 'holder' => 'div',
						'heading' => __( 'Chose Icon for tab', 'vcit' ),
						'param_name' => 'icon',
						"dependency" => array(
							'element' => 'icon_type',
							'value' => array( 'fontawesome' )
						)

					),
					array(
						'type' => 'attach_image',
						'heading' => __( 'Custom Icon for tab', 'vcit' ),
						'param_name' => 'custom_icon',
						"dependency" => array(
							'element' => 'icon_type',
							'value' => array( 'custom' )
						)

					),



					array(
						'type' => 'colorpicker',
						'heading' => __( 'Tab Background Color', 'vcit' ),
						'param_name' => "tab_back_color",
						'description' => __( 'Set the tab backround color. It will override the color set for all tabs. Leave epmty to use color set for all tabs', 'vcit' ),
					),
				),
				'js_view' =>  'VcIconTabView'
			) );


	}

	function create_hidden_input( $settings, $value ) {
		$dependency = vc_generate_dependencies_attributes( $settings );

		if ( empty( $value ) ) {
			$value = mt_rand();

		}
		return '<div class="my_param_block">'
			.'<input name="'.$settings['param_name']
			.'" class="wpb_vc_param_value wpb-textinput '
			.$settings['param_name'].' '.$settings['type'].'_field" type="text" value="'
			.$value.'" ' . $dependency . '/>'
			.'</div>';
	}



	public function icons( $settings, $value ) {
		$dependency = vc_generate_dependencies_attributes( $settings );
		return '<div class="my_param_block">'
			.'<div class="vcit_icon_preview" style="display: inline-block;
					margin-right: 10px;
					height: 60px;
					width: 90px;
					text-align: center;
					background: #FAFAFA;
					font-size: 60px;
					padding: 15px 0;
					margin-bottom: 10px;
					border: 1px solid #DDD;
					float: left;
					box-sizing: content-box;"><i class="bk-ice-cream"></i></div>'
			.'<input placeholder="' . __( "Search icon or pick one below...", 'vcit' ) . '" name="' . $settings['param_name'] . '"'
			. ' data-param-name="' . $settings['param_name'] . '"'
			. ' data-icon-css-path="' . VC_ICON_TABS_URL.'/' . '"'
			.'class="wpb_vc_param_value wpb-textinput'
			.$settings['param_name'].' '.$settings['type'].'_field" type="text" value="'
			.$value.'" ' . $dependency . ' style="width: 230px; margin-right: 10px; vertical-align: top; float: left; margin-bottom: 10px"/>'

			. '<div class="vcit_select_font_window" style="display: none; font-size: 40px; width: 100%; padding: 8px;
					box-sizing: border-box;
					-moz-box-sizing: border-box;
					background: #FAFAFA;
					height: 250px;
					overflow-y: scroll;
					border: 1px solid #DDD;
					clear: both"></div>'
			.'</div>';
	}

	function vcit_save_post_data() {

		if ( isset(  $_POST['settings']  ) ) {

			$current_value = get_post_meta( $_POST['post_id'], 'tab_settings', true );
			$current_value[$_POST['settings']['tab_contid']] = $_POST['settings'];
			update_post_meta( $_POST['post_id'], 'tab_settings', $current_value );
		}
		die;

	}
	/**
	 * Return Image icon
	 *
	 * @author Aman Saini
	 * @since  1.2.3
	 * @return [type]
	 */
	function vcit_get_img_url() {
		$thumb_src ='';
		if ( !empty(  $_POST['img_id']  ) ) {
			$thumb_src = wp_get_attachment_image_src( $_POST['img_id'] , 'full' );
			if ( $thumb_src ) {
				$thumb_src = $thumb_src[0];

			}
		}

		echo  $thumb_src;
		die;

	}


	/*
	Show notice if your plugin is activated but Visual Composer is not
	*/
	public function showVcVersionNotice() {
		$plugin_data = get_plugin_data( __FILE__ );
		echo '
		<div class="updated">
		  <p>'.sprintf( __( '<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend' ), 'Visual Composer Icon Tabs' ).'</p>
		</div>';
	}


}
// Finally initialize code
new VCExtendIcontabs();
