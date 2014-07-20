<?php

	$NV_attachedmedia = explode(",",$NV_attachedmedia);

	$NV_slidearray = $NV_navimg = $slider_frame = '';
	
	// Get Slider Frame Path
	$slider_frame = get_slider_frame( $NV_show_slider );

	foreach ($NV_attachedmedia as $NV_attachedmedia_id) { // cycle through attachment ID's
	
	/* ------------------------------------
	
	:: POST / PAGE ATTACHMENT MEDIA DATA
	
	------------------------------------ */
	
		$postcount = $post_count = 0;
		if(empty($NV_shortcode_id)) $NV_shortcode_id=''; // if is shortcode assign ID.
		
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
		
		:: GRID ONLY
		
		------------------------------------*/
		
		if($NV_show_slider=='gridgallery')
		{
			echo'<div class="nv-sortable row">';
		}
		
		/* ------------------------------------
		
		:: GRID ONLY *END*
		
		------------------------------------*/
	
	
		if ( $images = get_children(array(
				'post_parent' => $NV_attachedmedia_id,
				'post_type' => 'attachment',
				'numberposts' => $numposts,
				'order' =>$orderby,
				'orderby' =>$sortby,
				'post_mime_type' => 'image,video',)))
			{
				
			if(isset($post_count)) $post_count = $post_count+count($images); else $post_count = count($images);  // count query
				
			foreach( $images as $image ) {
			
	/* ------------------------------------
	
	:: GET INDIVIDUAL ATTACHMENT DATA
	
	------------------------------------ */
			 
				$size="full";
				$image_src=wp_get_attachment_image_src($image->ID, $size,false);
				$image_url=$image_src[0];
				
				$image_type = explode("/", $image->post_mime_type , 2);
				$image_type = $image_type[0];
				
				if(empty($NV_stagegallery)) $NV_stagegallery='';
				if(empty($NV_displaytitle)) $NV_displaytitle='';	
				if(empty($NV_groupgridcontent)) $NV_groupgridcontent='';
				if(empty($NV_cssclasses))	$NV_cssclasses='';
				
				if($image_type=='image') {
					//$NV_previewimgurl	=	parse_url($image_url, PHP_URL_PATH); // Preview Image URL
					$NV_previewimgurl	=	$image_url; // Preview Image URL
					
					if(get_post_meta($image->ID, 'gallery-video-url', true)!='') {
						$NV_movieurl		=	get_post_meta($image->ID, 'gallery-video-url', true);
						if( empty( $NV_lightbox ) ) { 
							$NV_videotype	=	'jwp';
						} 
						$NV_videoautoplay	=	'1';
						$NV_slidetimeout	=	get_post_meta($image->ID, 'gallery-slide-timeout', true);	
						$video_id			=   $image->ID;
					} else {
						$NV_movieurl		=	'';
						$NV_videotype		=	'';
						$NV_slidetimeout	=	'';
						$NV_videoautoplay	=	'';			
					}
					
				} else {
					$NV_movieurl 		=	wp_get_attachment_url($image->ID);  // Movie File URL
					$NV_videotype		=	'jwp';
					$NV_previewimgurl	=	'none';
					$video_id			=   $image->ID;
					$NV_slidetimeout	=	get_post_meta($image->ID, 'gallery-slide-timeout', true);	
				}		
				
		
				$NV_galexturl=		get_post_meta($image->ID, 'gallery-link-url', true);
				
				if(empty( $NV_galexturl))  $NV_galexturl ='';
				
				if($NV_galexturl=='') $NV_disablegallink='yes'; else  $NV_disablegallink='';
				
				$NV_disablereadmore ='';
				
				$NV_posttitle=		$image->post_title;
				$NV_description= 	$image->post_content;
				
				if(isset($NV_imgzoomcrop)) $NV_imgzoomcrop = $NV_imgzoomcrop; else $NV_imgzoomcrop='';
				
				if($NV_imgzoomcrop=="zoom") {
					$NV_imgzoomcrop="1";
				} elseif($NV_imgzoomcrop=="zoom crop") {
					$NV_imgzoomcrop="3";
				} else {
					$NV_imgzoomcrop="1";
				}
				
				if($NV_videoautoplay) {
					$NV_videoautoplay = "1";
				} else {
					$NV_videoautoplay ="0";	
				}
	
	/* ------------------------------------
	
	:: GET INDIVIDUAL ATTACHMENT DATA *END*
	
	------------------------------------ */
	
	/* ------------------------------------
	
	:: 3D ONLY
	
	------------------------------------ */
	
	if(empty($NV_3dsegments)) 	$NV_3dsegments='';
	if(empty($NV_3dtween))  	$NV_3dtween='';
	if(empty($NV_3dtweentime)) 	$NV_3dtweentime='';
	if(empty($NV_3dtweendelay)) $NV_3dtweendelay='';
	if(empty($NV_3dzdistance)) 	$NV_3dzdistance='';
	if(empty($NV_3dexpand))		$NV_3dexpand='';
	
	$NV_3dsegments_slide	= $NV_3dsegments;
	$NV_3dtween_slide		= $NV_3dtween;
	$NV_3dtweentime_slide	= $NV_3dtweentime;
	$NV_3dtweendelay_slide	= $NV_3dtweendelay;
	$NV_3dzdistance_slide	= $NV_3dzdistance;
	$NV_3dexpand_slide		= $NV_3dexpand;
	
	$NV_transitions='';
	$NV_transitions = array($NV_transitions,'<Transition Pieces="'.$NV_3dsegments_slide.'" Time="'.$NV_3dtweentime_slide.'" Transition="'.$NV_3dtween_slide.'" Delay="'.$NV_3dtweendelay_slide.'"  DepthOffset="'.$NV_3dzdistance_slide.'" CubeDistance="'.$NV_3dexpand_slide.'"></Transition>');
	
	/* ------------------------------------
	
	:: 3D ONLY *END*
	
	------------------------------------ */
	
				$postcount++;
				
				if($NV_videotype !="" && $postcount!="1") { // Stop IE autoplaying hidden video onload. 
					$display_none ="";
					$display_none = "yes";
				}
				
				$slide_id='';
				$slide_id="slide-".get_the_ID();
				
				if( empty($NV_customlayer) ) $NV_customlayer='';
	
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
						elseif( $NV_show_slider == 'islider' || $NV_show_slider == 'galleryaccordion' )
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
		
		}
	}
	
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
	
	} 