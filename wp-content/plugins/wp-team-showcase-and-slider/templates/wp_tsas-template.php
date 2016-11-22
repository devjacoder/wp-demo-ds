<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function get_wp_tsas_showcase( $atts, $content = null ) {
       // setup the query
            extract(shortcode_atts(array(
		"limit" => '',	
		"category" => '',		
		"design" => '',
		"grid" => '2',
		"popup" => '',
		
	), $atts));
	
	if( $limit ) { 
		$posts_per_page = $limit; 
	} else {
		$posts_per_page = '-1';
	}
	if( $category ) { 
		$cat = $category; 
	} else {
		$cat = '';
	}
	
	if( $design ) { 
		$teamdesign = $design; 
	} else {
		$teamdesign = 'design-1';
	}	
	
	if( $grid ) { 
		$gridcol = $grid; 
	} else {
		$gridcol = '1';
	}
if( $popup ) { 
		$teampopup = $popup; 
	} else {
		$teampopup = 'true';
	}		
	
	$popup_html = '';
	
	ob_start();
	
	$post_type 		= 'team_showcase_post';
	$orderby 		= 'post_date';
	$order 			= 'DESC';
				 
        $args = array ( 
            'post_type'      => $post_type, 
            'orderby'        => $orderby, 
            'order'          => $order,
            'posts_per_page' => $posts_per_page,  
          
            );
	if($cat != ""){
            	$args['tax_query'] = array( array( 'taxonomy' => 'tsas-category', 'field' => 'term_id', 'terms' => $cat) );
            }        
      $query = new WP_Query($args);
	  global $post;
      $post_count = $query->post_count;
          $count = 0;		 
		  $i = 1;
		if ( $query->have_posts() ) { ?> 
		  
		  <div class="wp-tsas-wrp">
		  	<div class="wp_teamshowcase_grid <?php echo $teamdesign; ?>">

			<?php  			 
		  
             while ( $query->have_posts() ) : $query->the_post();
            	
            	$popup_id = wp_tsas_get_unique();
            	$count++;
              
                $css_class="team-grid";
                if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == ($count - 1) % $grid ) ) || 1 == $count ) { $css_class .= ' first'; }
                if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == $count % $grid ) ) || $post_count == $count ) { $css_class .= ' last'; }
				if ( is_numeric( $gridcol ) ) {					
					if($gridcol == 1){
						$per_row = 12;
					}
					else if($gridcol == 2){
						$per_row = 6;
					}
					else if($gridcol == 3){
						$per_row = 4;	
					}
					else if($gridcol == 4){
						$per_row = 3;
					}
					 else{
                        $per_row = $gridcol;
                    }
					$class = 'wp-tsas-medium-'.$per_row.' wp-tsas-columns';
				}
				
				switch ($teamdesign) {
				 case "design-1":
					include('designs/design-1.php');
					break;
				 case "design-2":
					include('designs/design-2.php');
					break;	
				 default:					 
						include('designs/design-1.php');
					}		
					
					if($teampopup == "true") {
						ob_start();
						include('popup/popup-design-1.php');
						$popup_html .= ob_get_clean();
					}
			
			$i++;
            endwhile; 
			?>
			</div><!-- end .wp_teamshowcase_grid -->
			
			<?php echo $popup_html; // Print Popup HTML ?>
			
		</div><!-- end .wp-tsas-wrp -->
<?php	}
        
        wp_reset_query(); 
		
		return ob_get_clean();	
}

add_shortcode('wp-team','get_wp_tsas_showcase');