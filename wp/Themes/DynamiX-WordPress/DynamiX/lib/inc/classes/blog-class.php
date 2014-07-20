<?php
/**
 * The template for retrieving Blog Variables
 *
 * @package WordPress
 */
 
	global $NV_gallery_postformat; // check is post type is displayed in gallery
	 
	if($NV_gallery_postformat=='yes') { // use page settings if a gallery
	
		if(!$NV_imgheight) $NV_imgheight='130';
	
	} else {
		
	
	if(is_single())
	{ 
		if( of_get_option('postimgheight') ) {
			$NV_imgheight=of_get_option('postimgheight'); // image height 
		} 	
		if( of_get_option('postimgwidth') ) {
			$NV_imgwidth = of_get_option('postimgwidth'); // image width 
		} 	
		
		if( of_get_option('postimgalign') ) {
			$NV_imgalign=of_get_option('postimgalign'); // image align
		}
		else
		{
			$NV_imgalign = 'aligncenter';
		}
		
	} else {
		if( of_get_option('arhimgwidth')) {
			$NV_imgwidth= of_get_option("arhimgwidth"); // Get Archive Image Width		
		}
		
		if( of_get_option('arhimgheight')) {
			$NV_imgheight = of_get_option("arhimgheight"); // Get Archive Image Height
		}					
	
		if( of_get_option('arhimgalign') ) {
			$NV_imgalign=of_get_option('arhimgalign'); // image align
		}	
		else
		{
			$NV_imgalign = 'aligncenter';
		}		
	}
	
	
	if(empty($NV_imgheight) && empty($NV_imgwidth)) {
		$NV_imgwidth='542';
		$NV_image_size = 'width="'. $NV_imgwidth.'"';
	} elseif(isset($NV_imgwidth) && empty($NV_imgheight)) {
		$NV_image_size = 'width="'. $NV_imgwidth .'"';	
	} elseif(isset($NV_imgheight) && isset($NV_imgwidth)) {
		$NV_image_size = 'width="'. $NV_imgwidth .'" height="'. $NV_imgheight .'"';	
	}
	
	
/* ------------------------------------
:: POST CONTENT
------------------------------------ */

	$NV_nolink = '';
	
	$NV_blogcontent = of_get_option("arhpostcontent"); // Post Content
	$format = get_post_format();
	
	if( is_single() || $NV_blogcontent == 'full_post' ) :
	
		$content = get_the_content();
		
		if( get_option( 'themeva_theme') == 'Copa' && $format == 'gallery')
		{
			$content = switch_gallery($content);
		}
	
		$content = apply_filters( 'the_content', $content);
	
		$NV_description = $content;
		$NV_nolink='yes';
	
	elseif (  $NV_blogcontent == '' || $NV_blogcontent == 'excerpt' || $NV_blogcontent == 'excerpt_image' ) : 
	
		if ( empty($post->post_excerpt) )
		{
			if( function_exists('the_advanced_excerpt') )
			{
				$NV_description = the_advanced_excerpt('',true);
			}
			else
			{
				$content = get_the_content();

				if( get_option( 'themeva_theme') == 'Copa' && $format == 'gallery')
				{
					$content = switch_gallery($content);
				}

				$content = apply_filters( 'the_content', $content);
				$NV_description = $content;				
			}
		}
		else
		{
			$NV_description = get_the_excerpt(); 
		}
	
	else : 
	
		$NV_description = '';
		$NV_nolink='yes';
	
	endif; 
	
	
	global $NV_postlayout;
	
	$NV_arhimgdisplay 	= of_get_option("arhimgdisplay"); // Lightbox on First / Custom Images
	$NV_arhpostpostmeta = of_get_option("arhpostpostmeta"); // Display Postmeta Data
	$NV_postmetaalign = ( $NV_postlayout == 'grid' ? 'post_title' : of_get_option("postmetaalign") ); // Display Postmeta Data
	
	if(empty($NV_arhimgwidth_param)) $NV_arhimgwidth_param='';
	if(empty($NV_imgzoomcrop)) $NV_imgzoomcrop='0';
	
	$postcount = 0;
	
	
	if(is_single()) 	$NV_imageeffect = of_get_option('postimgeffect'); else $NV_imageeffect = of_get_option('arhimgeffect'); // image effect
	if(!is_single()) 	$NV_imageeffect = of_get_option('arhimgeffect');
	
	if(!$NV_imageeffect) 	$NV_imageeffect = 'shadowreflection'; // set default image effect
	
	if( is_single() && of_get_option('postimgdisplay') == 'lightbox' ) $NV_showlightbox = 'lightbox="yes"'; elseif( !is_single() && of_get_option('arhimgdisplay') == 'lightbox' ) $NV_showlightbox = 'lightbox="yes"'; else $NV_showlightbox = '';
	
	// post link
	$post_link = ( get_post_meta( $post->ID, '_cmb_galexturl', true ) !='' )  ? get_post_meta( $post->ID, '_cmb_galexturl', true ) : get_permalink();
	
	if( $NV_showlightbox !='' ) $NV_permalink =''; else $NV_permalink = $post_link; // assign permalink if lightbox is disabled
	
	if($NV_imageeffect=='' || $NV_imageeffect=='shadow' || $NV_imageeffect=='shadowreflection') { $NV_vidshadow="yes"; } elseif($NV_imageeffect=='frame') {
		$NV_vidshadow="frame";
	}
	
	}
	
	// Check is Timthumb is Enabled or Disabled
	if( of_get_option('timthumb_disable') !='disable' )
	{  
		require_once NV_FILES . '/adm/functions/BFI_Thumb.php';
		
		$params = '';
		
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
			$params['width'] = 980;
		}		
		
		if( !empty( $NV_previewimgurl ) ) 
		{
			$NV_imagepath = bfi_thumb( dyn_getimagepath($NV_previewimgurl) , $params );
		}
	}
	else 
	{
		$NV_imagepath = ( !empty( $NV_previewimgurl ) ) ? dyn_getimagepath($NV_previewimgurl) : '';
	}