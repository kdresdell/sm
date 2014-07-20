<?php

/* ------------------------------------

:: POST CATEGORY DATA

------------------------------------ */

	$postcount = $post_count = $data_id = 0;
	$cats = $NV_slidearray = $NV_navimg = $slider_frame = $pcats = '';
	
	// Make Page / Post Field Array
	$NV_pagepost_id = explode( ",", $NV_pagepost_id );

	// If no shortcode make empty
	if( empty( $NV_shortcode_id ) ) $NV_shortcode_id = ''; 
	
	// Get Slider Frame Path
	$slider_frame = get_slider_frame( $NV_show_slider );

/* ------------------------------------
:: BLACK AND WHITE EFFECT	
------------------------------------ */

	if( $NV_imageeffect == 'shadowblackwhite' || $NV_imageeffect == 'frameblackwhite' || $NV_imageeffect == 'blackwhite' )
	{
		$NV_blackwhite = 'blackwhite';
		
		if( $NV_imageeffect == 'shadowblackwhite' ) $NV_imageeffect = 'shadow';
		if( $NV_imageeffect == 'frameblackwhite' ) $NV_imageeffect = 'frame';
		if( $NV_imageeffect == 'blackwhite' ) $NV_imageeffect = 'none';

		// enqueue black and white script
		wp_deregister_script('jquery-blackandwhite');	
		wp_register_script('jquery-blackandwhite', get_template_directory_uri().'/js/jquery.blackandwhite.min.js',false,array('jquery'),true);
		wp_enqueue_script('jquery-blackandwhite');
	}
	else
	{
		$NV_blackwhite = '';
	}		


/* ------------------------------------
:: WPEC PRODUCT / TAGS
------------------------------------ */
	
	if($NV_productcat) {
		if(is_array($NV_productcat)) {
			foreach ($NV_productcat as $pcatlist) { // Get category ID Array
				$pcats = $pcats.",".$pcatlist;
			} 
		} else {
			$pcats = $NV_productcat;
		}
	}	

	if($NV_producttag) {
		if(is_array($NV_producttag)) {
			foreach ($NV_producttag as $taglist) { // Get category ID Array
				$tags = $tags.",".$taglist;
			}	
		} else {
			$tags = $NV_producttag;
		}
	}

	if(isset($pcats)) $pcats = lTrim($pcats,','); else $pcats='';
	$pcats = preg_replace( '/[^A-Za-z0-9() ,-]/', '', $pcats );
	
	if(isset($tags)) $tags = lTrim($tags,','); else $tags='';
	$tags = preg_replace( '/[^A-Za-z0-9() ,-]/', '', $tags );
	
/* ------------------------------------
:: GALLERY MEDIA CATEGORIES
------------------------------------ */

	if(is_array($NV_mediacat)) {
		$mediacats='';
		foreach ($NV_mediacat as $mediacatlist) { // Get category ID Array
			$mediacats = $mediacats.",".$mediacatlist;
		}
	} else {
		$mediacats = $NV_mediacat;
	}
	
	
	$mediacats = lTrim($mediacats,',');
	$mediacats = preg_replace( '/[^A-Za-z0-9() ,-]/', '', $mediacats );
	
	
/* ------------------------------------
:: POST CATEGORIES
------------------------------------ */

	if(is_array($NV_gallerycat)) {
		foreach ($NV_gallerycat as $catlist) { // Get category ID Array
			$cats = $cats.",".$catlist;
		}
	} else {
		$cats = $NV_gallerycat;
	}
	
	if($NV_gallerynumposts) { // Number of posts to display
		$numposts = $NV_gallerynumposts;
	} else {
		$numposts = -1;
	}
	
	if($NV_gallerysortby!='') { // Sort Posts by
		$sortby = $NV_gallerysortby;
	} else {
		$sortby = "menu_order";
	}
	
	if($NV_galleryorderby) {
		$orderby = $NV_galleryorderby;
	} else {
		$orderby = "ASC";
	}
	
	$cats = lTrim($cats,',');
	$cats = preg_replace( '/[^A-Za-z0-9() ,-]/', '', $cats );

	$cat_isnum = str_replace(",","", $cats); // join cats to check if numeric

	if (is_numeric ($cat_isnum)) { // backwards compatible with post id
		$cat_type= "cat";
	} else {
		$cat_type= "category_name"; // if not numeric, use category name
	}
	
	if(isset($paged)) $paged = $paged; else $paged='';

/* ------------------------------------

:: GRID ONLY

------------------------------------ */
	
	// Show number of posts on a page 
	if( empty($NV_gridshowposts) && empty($NV_gridfilter) && empty($NV_gallerynumposts) && $NV_show_slider == 'gridgallery' ) 
	{
		$numposts = "6";
	} 
	elseif( !empty($NV_gallerynumposts) && empty($NV_gridfilter) )
	{
		$numposts = $NV_gallerynumposts;
	}
	elseif( !empty($NV_gridshowposts) && empty($NV_gridfilter) )
	{
		$numposts = $NV_gridshowposts;
	}
	elseif( !empty($NV_gridfilter) )
	{
		$numposts = "-1";	
	} 
	

/* ------------------------------------

:: GRID ONLY *END*

------------------------------------ */

	global $NV_gallery_postformat,$post;
	$NV_gallery_postformat='';
	
	if($NV_datasource=='data-5') {
	
		if($sortby == 'meta_value') $sortby = '';
		
		
		if( class_exists('Woocommerce') ) {
			
		   $args=array(
			'post_type' => 'product',
			'product_cat' => $pcats,
			'product_tag' => $tags,
			'post_status' => 'publish',
			'paged' => $paged,
			'order' => $orderby,
			'orderby' => $sortby,
			'posts_per_page' => $numposts,
			'ignore_sticky_posts'=> 1
			);	
			
		} elseif ( class_exists('WPSC_Query') ) {
		
		   $args=array(
			'post_type' => 'wpsc-product',
			'wpsc_product_category' => $pcats,
			'product_tag' => $tags,
			'post_status' => 'publish',
			'paged' => $paged,
			'order' => $orderby,
			'orderby' => $sortby,
			'posts_per_page' => $numposts,
			'ignore_sticky_posts'=> 1
			);
		
		}
		
		$featured_query = new wp_query($args);
		
	} elseif($NV_datasource=='data-2') {
		
		if($NV_gallerypostformat!='') { // set post format
		
			$args=array(
			'post_type' => 'post',
			'post_status' => 'publish',
			$cat_type => $cats,
			'paged' => $paged,
			'orderby' => $sortby,
			'order' => $orderby,
			'posts_per_page' => $numposts,
			'ignore_sticky_posts'=> 1,
			'tax_query' => array(
						array( 'taxonomy' => 'post_format',
							  'field' => 'slug',
							  'terms' => 'post-format-'.$NV_gallerypostformat
							  )
						)
			);	
		
			$NV_gallery_postformat='yes';
						
		} else {
			
			$args=array(
			'post_type' => 'post',
			'post_status' => 'publish',
			$cat_type => $cats,
			'paged' => $paged,
			'orderby' => $sortby,
			'order' => $orderby,
			'posts_per_page' => $numposts,
			'ignore_sticky_posts'=> 1
			);		
			
		}
	
		$featured_query = new wp_query($args);  
	
	} elseif($NV_datasource=='data-6') {
	
	
	   $args=array(
		'post_type' => 'portfolio',
		'post_status' => 'publish',
		'portfolio-category' => $mediacats,
		'paged' => $paged,
		'orderby' => $sortby,
		'order' => $orderby,
		'posts_per_page' => $numposts,
		'ignore_sticky_posts'=> 1
		);
	
		$featured_query = new wp_query($args);  
	
	} elseif( $NV_datasource == 'data-8' ) {
	
	   $args=array(
	    'post__in' => $NV_pagepost_id,
		'post_type' => array( 'post', 'page', 'portfolio', 'event' ),
		'post_status' => 'publish',
		'paged' => $paged,
		'orderby' => $sortby,
		'order' => $orderby,
		'posts_per_page' => $numposts,
		'ignore_sticky_posts'=> 1
		);
	
		$featured_query = new wp_query($args);  
	
	} else { // If no options select display all categories
	
		$args=array(
		  'post_type' => 'post',
		  'post_status' => 'publish',
		  'paged' => $paged,
		  'orderby' => $sortby,
		  'order' => $orderby,
		  'posts_per_page' => $numposts,
		  'ignore_sticky_posts'=> 1
		  );
	
		$featured_query = new wp_query($args);
	} 
	
	/* ------------------------------------
	
	:: GRID ONLY
	
	------------------------------------ */
	
	if($NV_show_slider=='gridgallery') {
		if($NV_gallerycat || $NV_productcat || $NV_producttag || $NV_mediacat) {
			if( !empty( $NV_gridfilter) ) { ?>
				<div class="splitter-wrap">
					<ul class="splitter <?php if($NV_shortcode_id) echo "id-".$NV_shortcode_id; ?>">
						<li>
                        	<span class="filter-text"><?php _e('Filter By: ', 'themeva' ); ?></span>
							<ul>
								<li class="segment-1 selected-1 active"><a href="#" data-value="all"><?php _e('All', 'themeva' ); ?></a></li>
								<?php 								
								$catcount=2;
								if(!empty($NV_gallerycat)) :
			
								if( is_array( $NV_gallerycat ) ) $NV_gallerycat = implode(",", $NV_gallerycat); // create string
								$NV_gallerycat = explode(",", $NV_gallerycat); // make array if not already
							
								foreach ($NV_gallerycat as $catlist) { // Get category ID Array 
							
									if (is_numeric ($catlist)) { // backwards compatible with post id
										$catname = get_cat_name($catlist);
									} else {
										$catname = $catlist; // if not numeric, use category name
									}					
								
									$strip_html = preg_replace("/&#?[a-z0-9]+;/i","",$catname);	?>
									
									<li class="segment-<?php echo $catcount; ?>"><a href="#" data-value="<?php echo str_replace( " ","_", $strip_html ); echo $NV_shortcode_id; ?>"><?php echo $catname; ?></a></li>                    
								<?php 
								$catcount++; 
								} 
	
								elseif(!empty($NV_mediacat)) :
								$catcount=2;
								
								if( is_array( $NV_mediacat ) ) $NV_mediacat = implode(",", $NV_mediacat); // create string
								$NV_mediacat = explode(",", $NV_mediacat); // make array if not already
								
								foreach ($NV_mediacat as $catlist) { // Get category ID Array 
							
									if (is_numeric ($catlist)) { // backwards compatible with post id
										$catname = get_cat_name($catlist);
									} else {
										$catname = $catlist; // if not numeric, use category name
									}					
								
									$strip_html = preg_replace("/&#?[a-z0-9]+;/i","",$catname);	?>
									
									<li class="segment-<?php echo $catcount; ?>"><a href="#" data-value="<?php echo str_replace(" ","_",$strip_html); echo $NV_shortcode_id; ?>"><?php echo $catname; ?></a></li>                    
								<?php 
								$catcount++; 
								} 							
								
								elseif($NV_productcat || $NV_producttag) :
								$catcount=2;
								
								if($pcats && $tags) {
									$NV_productcattag = 	$pcats .','. $tags;
								} elseif($pcats) {
									$NV_productcattag = 	$pcats;
								} elseif($tags) {
									$NV_productcattag = 	$tags;
								}
								
								$NV_productcattag = explode(",", $NV_productcattag);
								
								// Get category ID Array 
								foreach ($NV_productcattag as $catlist)
								{ 
									$catname = $catlist; // if not numeric, use category name
									$strip_html = preg_replace("/&#?[a-z0-9]+;/i","",$catname);	?>
									
									<li class="segment-<?php echo $catcount; ?>"><a href="#" data-value="<?php echo str_replace(" ","_",$strip_html); echo $NV_shortcode_id; ?>"><?php echo $catname; ?></a></li>                    
								<?php 
								$catcount++; 
								} 
						
								endif; ?>
							</ul>
						</li>
					</ul>
				</div>
			<?php } 
		
			if($NV_show_slider=='gridgallery')
			{
				echo'<div class="nv-sortable row">';
			}
		}
	}
	
	/* ------------------------------------
	
	:: GRID ONLY *END*
	
	------------------------------------ */
	
	$post_count = $featured_query->post_count; // Check how many posts in query.
	
	while ($featured_query->have_posts()) : $featured_query->the_post(); 
	
		/* ------------------------------------
		
		:: CUSTOM FIELD DATA
		
		------------------------------------ */
		
		$categories='';
		
		if($NV_gallerycat) :
		
		foreach((get_the_category($post->ID)) as $category) {
				$categories .= str_replace(" ","_",$category->cat_name).$NV_shortcode_id. ' ';
				$categories = preg_replace("/&#?[a-z0-9]+;/i","",$categories);
		} 
		
		elseif($NV_mediacat) :
		$terms = get_the_terms($post->ID,'portfolio-category');
		foreach($terms as $category) {
				$categories .= str_replace(" ","_",$category->name).$NV_shortcode_id. ' ';
				$categories = preg_replace("/&#?[a-z0-9]+;/i","",$categories);
		} 
		
		elseif($NV_productcat || $NV_producttag) :
		
		if( class_exists('Woocommerce') ) :
		
			$product_categories = get_the_terms($post->ID,'product_cat');
			if(!empty($product_categories)) {
				foreach ($product_categories as $product_category) {
					$categories .= str_replace(" ","_", $product_category->name).$NV_shortcode_id. ' ';
				}
			}
		
			$product_tags = get_the_terms($post->ID,'product_tag');
			if($product_tags) {
				foreach ($product_tags as $product_tag) {
					$categories .= str_replace(" ","_", $product_tag->name).$NV_shortcode_id. ' ';
				}
			}	
			$categories = preg_replace("/&#?[a-z0-9]+;/i","",$categories);
			
		endif;
		
		if( class_exists('WPSC_Query') ) :	
		
			$wpsc_product_categories = get_the_product_category( wpsc_the_product_id() );
			if($wpsc_product_categories) {
				foreach ($wpsc_product_categories as $wpsc_product_category) {
					$categories .= str_replace(" ","_", $wpsc_product_category->name).$NV_shortcode_id. ' ';
				}
			}
		
			$wpsc_product_tags = wp_get_product_tags( wpsc_the_product_id() );
			if($wpsc_product_tags) {
				foreach ($wpsc_product_tags as $wpsc_product_tag) {
					$categories .= str_replace(" ","_", $wpsc_product_tag->name).$NV_shortcode_id. ' ';
				}
			}	
			$categories = preg_replace("/&#?[a-z0-9]+;/i","",$categories);
		
		
		endif;
		
		
		
		endif;
		
		/* ------------------------------------
		
		:: GET INDIVIDUAL SLIDE DATA
		
		------------------------------------ */
		
		if( $NV_datasource == 'data-5' ) :
		
		if( class_exists('Woocommerce') ) :
		
			global $product,$woocommerce;
			
			$thumbnail_id = get_post_thumbnail_id($post->ID);
		
			$size="large";
			$image_src = wp_get_attachment_image_src($thumbnail_id, $size,false); // Get attached image URL
			
			$NV_previewimgurl = $image_src[0];
			
			if( empty($NV_previewimgurl) ) $NV_previewimgurl = $woocommerce->plugin_url().'/assets/images/placeholder.png';
			
			$NV_productprice	= $product->get_price_html(); // Product Price
		
		endif;
		
		if ( class_exists('WPSC_Query') ) : 
		
			$NV_previewimgurl	= wpsc_the_product_image(); // Product Image
			$NV_productprice	= wpsc_the_product_price(); // Product Price
		
		endif;
		
			$NV_previewimgurl	=	parse_url($NV_previewimgurl, PHP_URL_PATH); // make relative Image URL
		
		else :
		
			$NV_previewimgurl = get_post_meta( $post->ID, '_cmb_previewimgurl', true ); // Preview Image URL
		
		endif;
		
		$image = catch_image(); // Check for images within post

		// check what image to use, custom, featured, image within post. 
		if( empty( $NV_previewimgurl ) )
		{  
			$post_image_id = get_post_thumbnail_id($post->ID);
			if ( !empty($post_image_id) )
			{
				
				$thumbnail = wp_get_attachment_image_src( $post_image_id, false);
				$NV_previewimgurl = $thumbnail[0];
				
				$NV_previewimgurl =	parse_url($NV_previewimgurl, PHP_URL_PATH); // make relative Image URL
			} 
			elseif( !empty($image) ) 
			{
				$NV_previewimgurl=$image;
			}
		}		
		
		$NV_movieurl 		= ( get_post_meta( $post->ID, '_cmb_movieurl', true ) !='' ) 		? get_post_meta( $post->ID, '_cmb_movieurl', true ) : '';
		$NV_stagegallery 	= ( get_post_meta( $post->ID, '_cmb_stagegallery', true ) !='' ) 	? get_post_meta( $post->ID, '_cmb_stagegallery', true ) : '';
		$NV_disablegallink 	= ( get_post_meta( $post->ID, '_cmb_disablegallink', true ) !='' )  ? get_post_meta( $post->ID, '_cmb_disablegallink', true ) : '';
		$NV_disablereadmore = ( get_post_meta( $post->ID, '_cmb_disablereadmore', true ) !='' ) ? get_post_meta( $post->ID, '_cmb_disablereadmore', true ) : '';
		$NV_imgzoomcrop 	= ( get_post_meta( $post->ID, '_cmb_imgzoomcrop', true ) !='' ) 	? get_post_meta( $post->ID, '_cmb_imgzoomcrop', true ) : '';
		$NV_displaytitle 	= ( get_post_meta( $post->ID, '_cmb_displaytitle', true ) !='' ) 	? get_post_meta( $post->ID, '_cmb_displaytitle', true ) : '';
		$NV_postsubtitle 	= ( get_post_meta( $post->ID, '_cmb_pagesubtitle', true ) !='' ) 	? get_post_meta( $post->ID, '_cmb_pagesubtitle', true ) : '';
		$NV_videotype 		= ( get_post_meta( $post->ID, '_cmb_videotype', true ) !='' ) 		? get_post_meta( $post->ID, '_cmb_videotype', true ) : '';
		$ratio 				= ( get_post_meta( $post->ID, '_cmb_videoratio', true ) !='' ) 		? get_post_meta( $post->ID, '_cmb_videoratio', true ) : '';
		$NV_videoautoplay	= ( get_post_meta( $post->ID, '_cmb_videoautoplay', true ) !='' ) 	? get_post_meta( $post->ID, '_cmb_videoautoplay', true ) : '';
		$NV_slidetimeout	= ( get_post_meta( $post->ID, '_cmb_slidetimeout', true ) !='' ) 	? get_post_meta( $post->ID, '_cmb_slidetimeout', true ) : '';
		$NV_cssclasses		= ( get_post_meta( $post->ID, '_cmb_cssclasses', true ) !='' ) 		? get_post_meta( $post->ID, '_cmb_cssclasses', true ) : '';
		
		
		// Set the Permalink
		if( get_post_meta( $post->ID, '_cmb_galexturl', true ) !='' )
		{
			$NV_galexturl = get_post_meta( $post->ID, '_cmb_galexturl', true );
		}
		else
		{
			$NV_galexturl = get_permalink();
		}
		
		// Set the title
		if( get_post_meta( $post->ID, '_cmb_pagetitle', true ) )
		{
			$NV_posttitle = get_post_meta( $post->ID, '_cmb_pagetitle', true );
		}
		else
		{
			$NV_posttitle = the_title('', '', false);	
		}
		
		global $more; $more = FALSE; 
		
		if ( empty($post->post_excerpt) )
		{
			
			if( $NV_stagegallery == "textoverlay" || !function_exists('the_advanced_excerpt') )
			{
				$NV_description = get_the_content();
			}
			else
			{
				$NV_description = the_advanced_excerpt('length='.$NV_galleryexcerpt.'&ellipsis=',true);
			}
		} 
		else
		{
			$NV_description = get_the_excerpt(); 
		}
		
		/* ------------------------------------
		
		:: 3D ONLY
		
		------------------------------------ */
		
		if( empty($NV_3dsegments) ) 	$NV_3dsegments = '';
		if( empty($NV_3dtween) )  		$NV_3dtween = '';
		if( empty($NV_3dtweentime) ) 	$NV_3dtweentime = '';
		if( empty($NV_3dtweendelay) )	$NV_3dtweendelay = '';
		if( empty($NV_3dzdistance) ) 	$NV_3dzdistance = '';
		if( empty($NV_3dexpand) )		$NV_3dexpand = '';
		
		
		$NV_3dsegments_slide   = ( get_post_meta( $post->ID, '_cmb_gallery3dsegments', true ) !='' )  ? get_post_meta( $post->ID, '_cmb_gallery3dsegments', true ) : $NV_3dsegments;
		$NV_3dtween_slide	   = ( get_post_meta( $post->ID, '_cmb_gallery3dtween', true ) !='' ) 	  ? get_post_meta( $post->ID, '_cmb_gallery3dtween', true )	: $NV_3dtween;
		$NV_3dtweentime_slide  = ( get_post_meta( $post->ID, '_cmb_gallery3dtweentime', true ) !='' ) ? get_post_meta( $post->ID, '_cmb_gallery3dtweentime', true )	: $NV_3dtweentime;
		$NV_3dtweendelay_slide = ( get_post_meta( $post->ID, '_cmb_gallery3dtweendelay', true ) !='' ) ? get_post_meta( $post->ID, '_cmb_gallery3dtweendelay', true ) : $NV_3dtweendelay;
		$NV_3dzdistance_slide  = ( get_post_meta( $post->ID, '_cmb_gallery3dzdistance', true ) !='' ) ? get_post_meta( $post->ID, '_cmb_gallery3dzdistance', true ) : $NV_3dzdistance;
		$NV_3dexpand_slide     = ( get_post_meta( $post->ID, '_cmb_gallery3dexpand', true ) !='' ) ? get_post_meta( $post->ID, '_cmb_gallery3dexpand', true ) : $NV_3dexpand;
		
		if(isset($NV_transitions)) {
		array_push($NV_transitions,'<Transition Pieces="'.$NV_3dsegments_slide.'" Time="'.$NV_3dtweentime_slide.'" Transition="'.$NV_3dtween_slide.'" Delay="'.$NV_3dtweendelay_slide.'"  DepthOffset="'.$NV_3dzdistance_slide.'" CubeDistance="'.$NV_3dexpand_slide.'"></Transition>');
		} else {
		$NV_transitions='';
		$NV_transitions = array($NV_transitions,'<Transition Pieces="'.$NV_3dsegments_slide.'" Time="'.$NV_3dtweentime_slide.'" Transition="'.$NV_3dtween_slide.'" Delay="'.$NV_3dtweendelay_slide.'"  DepthOffset="'.$NV_3dzdistance_slide.'" CubeDistance="'.$NV_3dexpand_slide.'"></Transition>');
		}
		
		/* ------------------------------------
		
		:: 3D ONLY *END*
		
		------------------------------------ */
		
		if($NV_imgzoomcrop=="zoom") {
			$NV_imgzoomcrop="1";
		} elseif($NV_imgzoomcrop=="zoom crop") {
			$NV_imgzoomcrop="3";
		} else {
			$NV_imgzoomcrop="0";
		}
		
		if($NV_videoautoplay) {
			$NV_videoautoplay = "1";
		} else {
			$NV_videoautoplay ="0";	
		}
		
		/* ------------------------------------
		
		:: CUSTOM FIELD DATA *END*
		
		------------------------------------ */   
		
		$do_not_duplicate[] = get_the_ID();
		
		$postcount++;
		$data_id++;
		
		if($NV_videotype !="" && $postcount!="1") { // Stop IE autoplaying hidden video onload. 
			$display_none ="";
			$display_none = "yes";
		}
		
		$slide_id='';
		$slide_id="slide-".$NV_shortcode_id.get_the_ID();
		
		if($NV_shortcode_id) $slide_id.'-'.$NV_shortcode_id;
		
		if(empty($NV_customlayer)) $NV_customlayer='';
		
		// Check is Timthumb is Enabled or Disabled
		if( of_get_option('timthumb_disable') !='disable' && empty( $NV_customlayer ) )
		{  
			require_once NV_FILES . '/adm/functions/BFI_Thumb.php';
			
			if( !empty( $NV_imgwidth ) )
			{
				$params['width'] = $NV_imgwidth;	
			}
	
			if( !empty( $NV_imgheight ) )
			{
				$params['height'] = $NV_imgheight;	
			}		
			
			if( $NV_imgzoomcrop == '0' )
			{
				$params['crop'] = true;	
			}

			if( empty( $NV_imgwidth ) )
			{
				if( $NV_show_slider == 'stageslider' || $NV_show_slider == 'gallery3d' || $NV_show_slider == 'nivo' )
				{
					if( get_option('themeva_theme') == 'ePix' || get_option('themeva_theme') == 'Copa' )
					{
						$params['width'] = 1050;
					}
					else
					{
						$params['width'] = 980;
					}
				}
				elseif( $NV_show_slider == 'islider' )
				{
					$params['width'] = 720;
				}
				else
				{
					$params['width'] = 300;
				}
			}			
			
			$NV_imagepath = bfi_thumb( dyn_getimagepath($NV_previewimgurl) , $params );
		}
		else 
		{
			$NV_imagepath = dyn_getimagepath($NV_previewimgurl);
		}
		
		/* ------------------------------------
		:: GET SLIDER FRAME
		------------------------------------ */			
			
		require $slider_frame;

		/* ------------------------------------
		:: / GET SLIDER FRAME
		------------------------------------ */		
		
		if(empty($NV_slidearray)) $NV_slidearray='';
		if(empty($NV_stagetimeout)) $NV_stagetimeout='';
		if(empty($NV_slidetimeout)) $NV_slidetimeout='';
		
		if($NV_slidetimeout!='') {
			$NV_slidearray = $NV_slidearray . $NV_slidetimeout .","; 
		} elseif($NV_stagetimeout!='') {
			$NV_slidearray = $NV_slidearray . $NV_stagetimeout .","; 
		} else {
			$NV_slidearray = $NV_slidearray . "10,";
		} 
		
		if($NV_show_slider=='islider') {
			if($NV_previewimgurl) { $NV_navimg.=$NV_previewimgurl.','; } elseif($image) { $NV_navimg.=$image.','; }
		}
	endwhile; ?>
	
	<?php
	
	/* ------------------------------------
	
	:: GROUP SLIDER ONLY 
	
	------------------------------------ */
	
	if($NV_show_slider=='groupslider') {
		if($postcount!="0") { $postcount="0"; // CHECK NEEDS END TAG ?>
			</div><!--  / row -->
		<?php } 
	}
	
	/* ------------------------------------
	
	:: GROUP SLIDER ONLY *END*
	
	------------------------------------ */
	
	/* ------------------------------------
	
	:: GRID ONLY 
	
	------------------------------------ */
	
	if($NV_show_slider=='gridgallery') { ?>
	<div class="clear"></div>
	</div>
	<?php }
	
	/* ------------------------------------
	
	:: GRID ONLY *END*
	
	------------------------------------ */ 
	
	wp_reset_query();