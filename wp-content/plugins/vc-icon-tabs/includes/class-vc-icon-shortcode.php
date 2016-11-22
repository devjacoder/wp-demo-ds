<?php


// don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

class IcontabShortcode {

	function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'vcit_add_scripts_to_frontend' ) );

		add_action( 'init', array( $this, 'add_tabs_shortcode' ) );

		//add_action ( 'wp_head', array( $this, 'add_style' )  );

	}

	/**
	 * Add css and js to the page.
	 *
	 * @author Aman Saini
	 * @since  1.0
	 * @return [type] [description]
	 */
	public function vcit_add_scripts_to_frontend() {

		wp_enqueue_style( 'vcittabscss', VC_ICON_TABS_URL.'/css/vcit-frontend.css' );
		wp_enqueue_style( 'vcittabstyle', VC_ICON_TABS_URL.'/css/tabs-style.css' );

		wp_enqueue_style( 'font-awesome', VC_ICON_TABS_URL.'/css/font-awsome.css' );

		wp_enqueue_script( 'vcittabsjs', VC_ICON_TABS_URL.'/js/cbpFWTabs.js' );

	}

	/**
	 * Hook shortcodes
	 *
	 * @author Aman Saini
	 * @since  1.0
	 */
	public function add_tabs_shortcode() {

		add_shortcode( 'vc_icon_tabs', array( $this, 'vc_icon_tabs_shortcode' ) );

		add_shortcode( 'vc_icon_tab', array( $this, 'vc_icon_tab_shortcode' ) );

	}

	/**
	 * Add css style to the head of the page
	 *
	 * @author Aman Saini
	 * @since  1.0
	 */
	public function add_style() {

		global $post;

		$settings = get_post_meta( $post->ID, 'tab_settings', true );


		if ( !empty ( $settings ) ) {
			echo '<style>';
			foreach ( $settings as $key => $setting ) { ?>

			#tabs-<?php echo $key ?> nav {
			text-align:<?php echo $setting['tabs_position'] ?>;
			}
			#tabs-<?php echo $key ?> li {
			border-color:<?php echo $setting['border_color'] ?>;
			border-width: <?php echo $setting['border_width'] ?>px;
			-webkit-border-radius:  <?php echo $setting['border_radius'] ?>px <?php echo $setting['border_radius'] ?>px 0px 0px;
			 -moz-border-radius:  <?php echo $setting['border_radius'] ?>px <?php echo $setting['border_radius'] ?>px 0px 0px;
			 border-radius:  <?php echo $setting['border_radius'] ?>px <?php echo $setting['border_radius'] ?>px 0px 0px;

			}

			#tabs-<?php echo $key ?> nav li.tab-current:before, #tabs-<?php echo $key ?> nav li.tab-current:after{
			background-color:<?php echo $setting['border_color'] ?>;
			height: <?php echo $setting['border_width'] ?>px;
			}

			#tabs-<?php echo $key ?> li a {
			background-color:<?php echo $setting['background_color'] ?>;
			color:<?php echo $setting['font_color'] ?>;
			-webkit-border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;
			 -moz-border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;
			 border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;
			}


			#tabs-<?php echo $key ?> li.tab-current {
			box-shadow:inset 0 0px <?php echo $setting['border_color'] ?>;
			}

			#tabs-<?php echo $key ?> li.tab-current a {
			background-color:<?php echo $setting['active_tab_color'] ?> !important;
			color:<?php echo $setting['active_tab_font_color'] ?> !important;
			-webkit-border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;
			 -moz-border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;
			 border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;

			}

			#tabs-<?php echo $key ?> li a:hover {
			background-color:<?php echo $setting['hover_color'] ?>;
			color:<?php echo $setting['font_hover_color'] ?>;
			-webkit-border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;
			 -moz-border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;
			 border-radius:  <?php echo $setting['border_radius']-$setting['border_width']?>px <?php echo $setting['border_radius']-$setting['border_width']?>px 0px 0px;
			}


		<?php  }

			echo '</style>';
		}


	}

	/**
	 * Shortcode code to display tabs
	 *
	 * @author Aman Saini
	 * @since  1.0
	 * @param unknown $atts
	 * @param unknown $content
	 * @return shortcode html
	 */
	public function vc_icon_tabs_shortcode( $atts , $content='' ) {

		$output = $title = $interval = $el_class = '';
		$atts= shortcode_atts( array(
				'tab_contid' =>'',
				'tabs_position'=>'center',
				'background_color'=>'#fff',
				'font_color'=>'#768e9d',
				'font_hover_color'=>'#768e9d',
				'hover_color'=>'',
				'border_width'=>'1',
				'border_color'=>'#47a3da',
				'border_radius'=>'',
				'hover_color'=>'',
				'active_tab_color'=>'',
				'el_class' => '',
				'active_tab_font_color'=>'',
				'active_tab_color'=>''
			), $atts ) ;


		$cl = " " . str_replace( ".", "", $el_class );

		$el_class = $cl;

		$element = 'tabcontent';

		// Extract tab titles
		preg_match_all( '/vc_icon_tab([^\]]+)/i', $content, $matches, PREG_OFFSET_CAPTURE );
		$tab_titles = array();

		/**
		 * vc_icon_tabs
		 *
		 */
		if ( isset( $matches[1] ) ) {
			$tab_titles = $matches[1];
		}

		$key  = rand( 999, 9999999 );
		$tabs_nav = '<style>';

		$tabs_nav .= '#tabs-'.$key.' nav {';
		$tabs_nav .= $this->get_tab_styles( $atts, array( 'tabs_position' ) );
		//text-align:<?php echo $setting['tabs_position']
		$tabs_nav .= '} ';

		$tabs_nav .= '#tabs-'.$key.' li {';
		$tabs_nav .= $this->get_tab_styles( $atts, array( 'border_radius', 'border_color', 'border_width' ) );
		$tabs_nav .= '} ';

		$tabs_nav .= '#tabs-'.$key.' nav li.tab-current:before, #tabs-'.$key.' nav li.tab-current:after{';
		$tabs_nav .= $this->get_tab_styles( $atts, array( 'current_tab' ) );
		$tabs_nav .= '} ';

		$tabs_nav .= '#tabs-'.$key.' li a {';
		$tabs_nav .= $this->get_tab_styles( $atts, array( 'background_color', 'font_color', 'border_radius', 'border_diff' ) );
		$tabs_nav .= '} ';


		$tabs_nav .= '#tabs-'.$key.' li.tab-current {';
		$tabs_nav .= $this->get_tab_styles( $atts, array( 'border_radius', 'border_diff' ) );
		$tabs_nav .= '} ';

		$tabs_nav .= '#tabs-'.$key.' li.tab-current a {';
		$tabs_nav .= $this->get_tab_styles( $atts, array( 'border_radius', 'border_diff', 'active_tab_color', 'active_tab_font_color' ) );
		$tabs_nav .= '} ';

		$tabs_nav .= '#tabs-'.$key.'  li a:hover {';
		$tabs_nav .= $this->get_tab_styles( $atts, array( 'hover_color', 'font_hover_color','border_radius', 'border_diff',  ) );
		$tabs_nav .= '} ';



		$tabs_nav .= '</style>';
		//$tabs_nav .= $this->get_tab_styles( $atts, $tab_contid );
		$tabs_nav .= '
		<div id="tabs-'.$key.'" class="icontabs">
    	<nav>
    	<input class="tabs_id" type="hidden" value="'.$key.'" >
        <ul>';
		foreach ( $tab_titles as $tab ) {
			$tab_atts = shortcode_parse_atts( $tab[0] );
			$thumb_src ='';
			$tab_atts['icon_type'] = empty( $tab_atts['icon_type'] )?'':$tab_atts['icon_type'];
			if ( $tab_atts['icon_type'] == 'custom' ) {
				$thumb_src = wp_get_attachment_image_src( $tab_atts['custom_icon'], 'full' );
				if ( $thumb_src ) {
					$thumb_src = $thumb_src[0];

				}
			}else if ( empty( $tab_atts['icon'] ) ) {
					$tab_atts['icon']= '';
				}

			if ( !empty( $tab_atts['tab_back_color'] ) ) {

				$style='background-color:'.$tab_atts['tab_back_color'];
			}else {
				$style='';
			}


			if ( isset( $tab_atts['title'] ) ) {
				$tabs_nav .= '<li> <a style="'.$style.'" class="" href="#tab-' . ( isset( $tab_atts['tab_id'] ) ? $tab_atts['tab_id'] : sanitize_title( $tab_atts['title'] ) ) . '">';
				if ( !empty( $thumb_src ) ) {
					$tabs_nav .= '<i class="custom_icon_img"><img src="'.$thumb_src.'"></i>';
				}else {
					$tabs_nav .= '<i class="'. $tab_atts['icon'].'"></i>';
				}
				$tabs_nav .= '	<span>' . $tab_atts['title'] . '</span></a></li>';
			}
		}
		$tabs_nav .= '</ul>
    	</nav>' . "\n";

		if ( empty( $css_class ) ) {
			$css_class= '';
		}

		$output .= '<div class=" tabcontents ' .$el_class .' '. $css_class . '" >';
		$output .=  '<div class="wpb_wrapper wpb_tour_tabs_wrapper ui-tabs vc_clearfix">';
		$output .= $tabs_nav;
		$output .= ' <div class="tabcontent">';
		$output .= wpb_js_remove_wpautop( $content );
		$output .= ' </div>';
		$output .=  '</div> ' ;
		$output .= '</div> <script type="text/javascript">
			(function($){
			$("document").ready(function(){
			    new CBPFWTabs( document.getElementById( "tabs-'.$key.'" ) );
						})
			})(jQuery);

		</script>

		</div>' ;

		return $output;
	}


	function vc_icon_tab_shortcode( $atts , $content='' ) {
		extract( shortcode_atts( array(
					'tab_id' => '',
				), $atts ) );

		$output = '';

		if ( !empty( $tab_id ) ) {
			$output .= '<section class="tabsection" id="tab-'.$tab_id.'">';
			$output .= do_shortcode( $content );
			$output .= '</section>';
		}

		return $output;

	}

	public function get_tab_styles( $style_atts, $required_atts ) {
		if ( empty( $style_atts ) ) {
			return;
		}
		$req_attr = array_flip( $required_atts );
		$attr = array_intersect_key( $style_atts, $req_attr );

		$input_styles = '';
		$input_styles.= empty( $attr['color'] )?'':'color:'. $attr['color'].';';
		$input_styles.= empty( $attr['font_color'] )?'':'color:'. $attr['font_color'].';';
		$input_styles.= empty( $attr['tabs_position'] )?'':'text-align:'. $attr['tabs_position'].';';
		$input_styles.= empty( $attr['background_color'] )?'':'background-color:'. $attr['background_color'].';';
		//$input_styles.= empty( $attr['padding'] )?'':'padding:'. $attr['padding'].';';
		$input_styles.= empty( $attr['width'] )?'':'width:'. $attr['width'].';';
		$input_styles.= empty( $attr['height'] )?'':'height:'. $attr['height'].';';
		$input_styles.= empty( $attr['margin'] )?'':'margin:'. $attr['margin'].';';
		$input_styles.= empty( $attr['align'] )?'':'text-align:'. $attr['align'].';';

		$input_styles.= empty( $attr['border_color'] )?'':'border-color:'. $attr['border_color'].';';
		$input_styles.= empty( $attr['border_width'] )?'':'border-width:'. $attr['border_width'].'px;';
		$input_styles.= empty( $attr['width'] )?'':'width:'. $attr['width'].';';
		// Border Radius
		if ( !empty( $attr['border_radius'] ) && !isset( $req_attr['border_diff'] )  ) {
			$input_styles .= 'border-radius:'.$attr['border_radius'].'px '.$attr['border_radius'].'px 0px 0px;';
			$input_styles .= '-web-border-radius:'.$attr['border_radius'].'px '.$attr['border_radius'].'px 0px 0px;';
			$input_styles .= '-moz-border-radius:'.$attr['border_radius'].'px '.$attr['border_radius'].'px 0px 0px;';
		}else if ( !empty( $attr['border_radius'] ) && isset( $req_attr['border_diff'] ) ) {
				if ( !empty( $style_atts['border_width'] ) ) {
					$rad = $attr['border_radius']-$style_atts['border_width'];
				}else {
					$rad = $attr['border_radius'];
				}

				$input_styles .= '-webkit-border-radius:'.$rad.'px '.$rad.'px 0px 0px;';
				$input_styles .= '-moz-border-radius: '.$rad.'px '.$rad.'px 0px 0px;';
				$input_styles .= 'border-radius: '.$rad.'px '.$rad.'px 0px 0px;';

			}

		// Current Tab
		if ( isset( $req_attr['current_tab'] ) ) {
			$input_styles.= empty( $style_atts['border_color'] )?'':'background-color:'. $style_atts['border_color'].';';
			$input_styles.= empty( $style_atts['border_width'] )?'':'height:'. $style_atts['border_width'].'px;';
		}
		// Box Shadow
		if ( isset( $req_attr['box_shadow'] ) ) {
			$input_styles.= empty( $style_atts['border_color'] )?'':'box-shadow:inset 0 0px '. $style_atts['border_color'].';';
		}
		// Active Tab color
		if ( isset( $req_attr['active_tab_color'] ) ) {
			$input_styles.= empty( $style_atts['active_tab_color'] )?'':'background-color: '. $style_atts['active_tab_color'].' !important;';
		}

		// Active Tab font color
		if ( isset( $req_attr['active_tab_font_color'] ) ) {
			$input_styles.= empty( $style_atts['active_tab_font_color'] )?'':'color: '. $style_atts['active_tab_font_color'].' !important;';
		}

		// Active Tab font hover color
		if ( isset( $req_attr['hover_color'] ) ) {
			$input_styles.= empty( $style_atts['hover_color'] )?'':'background-color: '. $style_atts['hover_color'].';';
		}

		if ( isset( $req_attr['font_hover_color'] ) ) {
			$input_styles.= empty( $style_atts['font_hover_color'] )?'':'color: '. $style_atts['font_hover_color'].';';
		}




		if ( !empty( $attr['border'] ) ) {
			$border_array = explode( '|', $attr['border'] );
			if ( $border_array[0] == '0px' ) {
				$input_styles.= 'border:0px solid #fff;';
			}
			elseif ( !empty( $border_array[2] ) ) {
				$input_styles.= 'border:'. $border_array[0].' '. $border_array[1].' '.$border_array[2].';';
			}
		}

		return $input_styles;


	}

}

new IcontabShortcode();
