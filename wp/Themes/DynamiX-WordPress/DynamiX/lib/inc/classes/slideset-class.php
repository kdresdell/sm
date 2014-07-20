<?php

	/* ------------------------------------
	
	:: Slide Mananger
	
	------------------------------------ */

	$postcount = $post_count = $data_id = $z = $video_id = 0;
	$cats = $NV_slidearray = $NV_navimg = $slider_frame = '';
	
	if( empty($NV_shortcode_id) ) $NV_shortcode_id = ''; // if is shortcode assign ID.

	if( !is_array( $NV_slidesetid ) )
	{
		$NV_slidesetid = rtrim( $NV_slidesetid , ',' );
		$NV_slide_sets = explode(",", $NV_slidesetid );
	}
	else
	{
		$NV_slidesetid = implode( ",", $NV_slidesetid ); // needed to upgrades of older versions
		$NV_slide_sets = explode( ",", $NV_slidesetid );
	}

	get_page_by_title( $NV_slide_sets , 'slide-sets' );


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
	
	:: GRID ONLY
	
	------------------------------------ */

	if( $NV_show_slider == 'gridgallery' )
	{
		foreach ( $NV_slide_sets as $slide_set )
		{ 
			if( is_numeric( $slide_set ) )
			{
				$post_id = $slide_set;
			}
			else
			{
				$name = get_page_by_title( $slide_set, 'OBJECT', "slide-sets" );
				$post_id = $name->ID;
			}
			
			$slide_xml = get_post_meta( $post_id, 'slide_manager_xml', true );
			$slide_data = new DOMDocument();
			$slide_data->loadXML( $slide_xml );
			$slide_set = $slide_data->documentElement;
	
			// Attain Filter Tags
			foreach( $slide_set->childNodes as $slide )
			{
				if ($slide->hasChildNodes())
				{
					$filter_tags = str_replace(", ", ",", find_xml_value( $slide, 'filter_tags' ) );
					
					$filter_tags = explode(",", $filter_tags );
						
					foreach( $filter_tags as $filter_tag )
					{
						$category_array[] = $filter_tag; // Enter Categories into an Array
					}
				}
			}				
		}		
		
		$category_array = array_unique( $category_array );
		asort( $category_array );
		
		
		if( !empty( $category_array ) && !empty( $NV_gridfilter) ) { ?>
			<div class="splitter-wrap">
				<ul class="splitter <?php if( $NV_shortcode_id ) echo "id-".$NV_shortcode_id; ?>">
					<li>
                    	<span class="filter-text"><?php _e('Filter By: ', 'themeva' ); ?></span>
						<ul>
							<li class="segment-1 selected-1 active"><a href="#" data-value="all"><?php _e('All', 'themeva' ); ?></a></li>
							<?php 
							$catcount=2;
							
							foreach ($category_array as $catname) { // Get category ID Array ?>
							<?php if($catname) { ?>
							<li class="segment-<?php echo $catcount; ?>"><a href="#" data-value="<?php echo str_replace(" ","",$catname).$NV_shortcode_id; ?>"><?php echo $catname; ?></a></li>                    <?php }
							$catcount++; } ?>
						</ul>
					</li>
				</ul>
			</div>
		<?php 
		} 
		
		if( $NV_show_slider=='gridgallery' )
		{
			echo'<div class="nv-sortable row">';
		}
	}


	/* ------------------------------------
	
	:: GET INDIVIDUAL SLIDE DATA
	
	------------------------------------ */
	

	// Get Total Slide Count if multiple slides selected
	if( is_array( $NV_slide_sets ) && count( $NV_slide_sets ) > 1 )
	{
		foreach( $NV_slide_sets as $NV_slide_set )
		{
			// Check if Name or ID
			if( is_numeric( $NV_slide_set ) )
			{
				$post_id = $NV_slide_set;
			}
			else
			{
				$name = get_page_by_title( $NV_slide_set, 'OBJECT', "slide-sets" );
				$post_id = $name->ID;
			}
			
			$slide_xml = get_post_meta( $post_id, 'slide_manager_xml', true );
			
			$slide_data = new DOMDocument();
			$slide_data->loadXML( $slide_xml );
			$slide_set = $slide_data->documentElement;
			
			$post_count = $post_count + $slide_set->getElementsByTagName('slide')->length;	
		}
	}
	
	// Slide Set ID Array Check
	foreach( $NV_slide_sets as $NV_slide_set )
	{
		// Check if Name or ID
		if( is_numeric( $NV_slide_set ) )
		{
			$post_id = $NV_slide_set;
		}
		else
		{
			$name = get_page_by_title( $NV_slide_set, 'OBJECT', "slide-sets" );
			$post_id = $name->ID;
		}
		
		$slide_xml = get_post_meta( $post_id, 'slide_manager_xml', true );
		
		$slide_data = new DOMDocument();
		$slide_data->loadXML( $slide_xml );
		$slide_set = $slide_data->documentElement;
		
		// Count
		if( count( $NV_slide_sets ) == 1 ) 
		{
			$post_count = $slide_set->getElementsByTagName('slide')->length;	
		}
	
		foreach( $slide_set->childNodes as $slide )
		{
			$NV_disablegallink=
			$NV_movieurl=
			$NV_previewimgurl=
			$NV_imgzoomcrop=
			$NV_stagegallery=
			$NV_cssclasses=
			$NV_displaytitle=
			$NV_disablegallink=
			$NV_disablereadmore=
			$NV_galexturl=
			$NV_videotype=
			$NV_videoautoplay=
			$NV_posttitle=
			$NV_description=
			$NV_slidetimeout= '';

			$NV_3dsegments 	 = ( !empty( $NV_3dsegments ) ) ? $NV_3dsegments : '';
			$NV_3dtween	 	 = ( !empty( $NV_3dtween ) ) ? $NV_3dtween : '';
			$NV_3dtweentime  = ( !empty( $NV_3dtweentime ) ) ? $NV_3dtweentime : '';
			$NV_3dtweendelay = ( !empty( $NV_3dtweendelay ) ) ? $NV_3dtweendelay : '';
			$NV_3dzdistance  = ( !empty( $NV_3dzdistance ) ) ? $NV_3dzdistance : '';
			$NV_3dexpand 	 = ( !empty( $NV_3dexpand ) ) ? $NV_3dexpand : '';
			$NV_transitions	 = ( !empty( $NV_transitions ) ) ? $NV_transitions : '';
			$NV_stagetimeout = ( !empty( $NV_stagetimeout ) ) ? $NV_stagetimeout : '';			
			
			// Get Image SRC from Attachment ID
			$get_image_src = wp_get_attachment_image_src( find_xml_value( $slide, 'image' ), 'full');
			
			$attachment_meta = themeva_attachment_data( find_xml_value( $slide, 'image' ) );
			
			$NV_previewimgurl = $get_image_src[0];
			
			if( empty( $NV_previewimgurl ) )
			{
				$NV_previewimgurl = find_xml_value( $slide, 'image_url' );
			}
			
			$NV_movieurl 		= find_xml_value( $slide, 'media_url' );
			$NV_videotype 		= find_xml_value( $slide, 'embed_type' );
			$NV_videoautoplay 	= find_xml_value( $slide, 'autoplay' );
			
			$NV_posttitle 		= ( find_xml_value( $slide, 'title' ) !='' ) ? find_xml_value( $slide, 'title' ) : $attachment_meta['title'];
			$NV_description 	= ( find_xml_value( $slide, 'description' ) !='' ) ? find_xml_value( $slide, 'description' ) : $attachment_meta['description'];
			$NV_galexturl 		= find_xml_value( $slide, 'link_url' );
			
			// Assign unique video ID
			$video_id = $post_id + $data_id;
			
			if( empty( $NV_galexturl ) )
			{
				$NV_disablegallink = "yes";
			} 		
			
			$NV_stagegallery 	= find_xml_value( $slide, 'stage_content' );
			$NV_cssclasses 		= find_xml_value( $slide, 'css_classes' );
			$NV_displaytitle 	= find_xml_value( $slide, 'title_overlay' );
			$NV_disablereadmore = find_xml_value( $slide, 'readmore_link' );
			$NV_slidetimeout 	= find_xml_value( $slide, 'timeout' );
			$tags_array		 	= find_xml_value( $slide, 'filter_tags' ); 
			
			// disable readmore
			if( $NV_disablereadmore == 'off' ) $NV_disablereadmore = 'yes';
			
			if( $NV_videoautoplay == 'on' )
			{
				$NV_videoautoplay = "1";
			} 
			else
			{
				$NV_videoautoplay = "0";	
			}	
			
			$postcount++;
			$data_id++;
			
			// Stop IE autoplaying hidden video onload. 
			if( $NV_videotype !="" && $postcount !="1" )
			{ 
				$display_none = "yes";
			}
			
			$slide_id = '';
			$slide_id = "slide". get_the_ID();
			
			/* ------------------------------------
			
			:: GRID ONLY
			
			------------------------------------ */
			
			$categories = '';
			
			// Enter Categories into an Array
			if( !empty( $tags_array ) )
			{
				$tags_array = str_replace(" ", "", $tags_array );
				
				$tags_array = explode(',', $tags_array);
				
				foreach($tags_array as $tag)
				{
					$categories .= $tag.$NV_shortcode_id.',';
				}
				
				$replace_arr = array(' ',',');
				$replace_with= array('_',' '); 
				
				$categories = str_replace( $replace_arr, $replace_with, $categories );
			}
			
			
			/* ------------------------------------
			
			:: 3D ONLY
			
			------------------------------------ */

			
			$NV_3dsegments_slide	= ( find_xml_value( $slide, 'gallery3d_pieces' ) !='' ) 	 ? find_xml_value( $slide, 'gallery3d_pieces' )		: $NV_3dsegments;
			$NV_3dtween_slide		= ( find_xml_value( $slide, 'gallery3d_tween' )	!='' ) 		 ? find_xml_value( $slide, 'gallery3d_tween' )		: $NV_3dtween;
			$NV_3dtweentime_slide	= ( find_xml_value( $slide, 'gallery3d_transtime' ) !='' ) 	 ? find_xml_value( $slide, 'gallery3d_transtime' )	: $NV_3dtweentime;
			$NV_3dtweendelay_slide	= ( find_xml_value( $slide, 'gallery3d_seconds' ) !='' ) 	 ? find_xml_value( $slide, 'gallery3d_seconds' )	: $NV_3dtweendelay;
			$NV_3dzdistance_slide	= ( find_xml_value( $slide, 'gallery3d_depthoffset' ) !='' ) ? find_xml_value( $slide, 'gallery3d_depthoffset' ): $NV_3dzdistance;
			$NV_3dexpand_slide		= ( find_xml_value( $slide, 'gallery3d_cubedist' ) !='' )  	 ? find_xml_value( $slide, 'gallery3d_cubedist' )	: $NV_3dexpand;
			
			if( !empty($NV_transitions) )
			{
				array_push($NV_transitions,'<Transition Pieces="'.$NV_3dsegments_slide.'" Time="'.$NV_3dtweentime_slide.'" Transition="'.$NV_3dtween_slide.'" Delay="'.$NV_3dtweendelay_slide.'"  DepthOffset="'.$NV_3dzdistance_slide.'" CubeDistance="'.$NV_3dexpand_slide.'"></Transition>');
			}
			else
			{
				$NV_transitions = array($NV_transitions,'<Transition Pieces="'.$NV_3dsegments_slide.'" Time="'.$NV_3dtweentime_slide.'" Transition="'.$NV_3dtween_slide.'" Delay="'.$NV_3dtweendelay_slide.'"  DepthOffset="'.$NV_3dzdistance_slide.'" CubeDistance="'.$NV_3dexpand_slide.'"></Transition>');
			}
			
			
			/* ------------------------------------
			
			:: GET INDIVIDUAL SLIDE DATA *END*
			
			------------------------------------ */
			
			
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
				
				if( $NV_imageeffect == 'circular' ) $params['height'] = $params['width'];
				
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

			
			if( $NV_slidetimeout )
			{
				$NV_slidearray = $NV_slidearray . $NV_slidetimeout .","; 
			}
			elseif($NV_stagetimeout)
			{
				$NV_slidearray = $NV_slidearray . $NV_stagetimeout .","; 
			} 
			else
			{
				$NV_slidearray = $NV_slidearray . "10,";
			}
			
			$z++;
			
			if( $NV_show_slider == 'islider' )
			{
				if( !empty($NV_previewimgurl) )
				{ 
					$NV_navimg .= $NV_previewimgurl.','; 
				}
				elseif( $image )
				{ 
					$NV_navimg .= $image.',';
				}
			}
		}
	}
	
	/* ------------------------------------
	
	:: GROUP SLIDER ONLY 
	
	------------------------------------ */
	
	if( $NV_show_slider == 'groupslider' )
	{
		if( $postcount != "0" ) 
		{
			$postcount="0"; // CHECK NEEDS END TAG 
			echo '</div><!--  / row -->';
		} 
	}
	

	/* ------------------------------------
	
	:: GRID ONLY 
	
	------------------------------------ */

	if( $NV_show_slider == 'gridgallery' )
	{
		echo '<div class="clear"></div>';
		echo '</div>';
	}