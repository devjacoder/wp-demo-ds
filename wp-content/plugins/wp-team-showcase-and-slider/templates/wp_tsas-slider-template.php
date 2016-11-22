<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
function get_wp_tsas_showcase_slider( $atts, $content = null ){
    // setup the query
            extract(shortcode_atts(array(
		"limit" => '',	
		"category" => '',		
		"design" => '',
		"slides_column" => '',
		"slides_scroll" => '',
		"dots" => '',
		"arrows" => '',
		"autoplay" => '',
		"autoplay_interval" => '',
		"speed" => '',
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
	
	if( $slides_column ) { 
		$slidesColumn = $slides_column; 
	} else {
		$slidesColumn = '3';
	}	
	
	if( $slides_scroll ) { 
		$slidesScroll = $slides_scroll; 
	} else {
		$slidesScroll = '1';
	}	
	if( $autoplay ) { 
		$slidesautoplay = $autoplay; 
	} else {
		$slidesautoplay = 'true';
	}	
	if( $dots ) { 
		$sliderdots = $dots; 
	} else {
		$sliderdots = 'true';
	}	
	if( $arrows ) { 
		$sliderarrows = $arrows; 
	} else {
		$sliderarrows = 'true';
	}	
	if( $autoplay_interval ) { 
		$autoplayInterval = $autoplay_interval; 
	} else {
		$autoplayInterval = '3000';
	}
	if( $speed ) { 
		$sliderspeed = $speed; 
	} else {
		$sliderspeed = '500';
	}	

if( $popup ) { 
		$teampopup = $popup; 
	} else {
		$teampopup = 'true';
	}		
	
	
	ob_start();
	
	$unique			= wp_tsas_get_unique();
	$post_type 		= 'team_showcase_post';
	$orderby 		= 'post_date';
	$order 			= 'DESC';
	$popup_html		= '';

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
	if ( $query->have_posts() ) {		?> 
		  
		  <div class="wp-tsas-slider-<?php echo $unique; ?> wp_teamshowcase_slider <?php echo $teamdesign; ?>">
		   
		  
			<?php  			 
		  
            while ( $query->have_posts() ) : $query->the_post();
            	
            	$popup_id = wp_tsas_get_unique();
            	$count++;
              	
                $css_class="team-slider";               
				$class = '';		
				
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
					
					// Creating Popup 
					if($teampopup == "true") {
						ob_start();
						include('popup/popup-design-1.php');
						$popup_html .= ob_get_clean();
					}
			
			$i++;
            endwhile; 
			 ?>
			</div><!-- end .wp_teamshowcase_slider -->
		<?php

		echo $popup_html; // Print Popup HTML

		} ?>
			
			 
			 <script type="text/javascript">
		jQuery(document).ready(function(){
		jQuery('.wp-tsas-slider-<?php echo $unique; ?>').slick({
			dots: <?php echo $sliderdots; ?>,
			infinite: true,
			arrows: <?php echo $sliderarrows; ?>,
			speed: <?php echo $sliderspeed; ?>,
			autoplay: <?php echo $slidesautoplay; ?>,						
			autoplaySpeed: <?php echo $autoplayInterval; ?>,
			slidesToShow: <?php echo $slidesColumn; ?>,
			slidesToScroll: <?php echo $slidesScroll; ?>,
			responsive: [
    {
      breakpoint: 769,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 641,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 481,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
  ]
		});
	});
	</script>
             <?php  
			  wp_reset_query(); 
             return ob_get_clean();
	}

add_shortcode( 'wp-team-slider', 'get_wp_tsas_showcase_slider' );
?>