<?php

	/* ------------------------------------
	:: GRID GALLERY
	------------------------------------*/	
	
	function postgallery_grid_shortcode( $atts, $content = null ) {
	   extract( shortcode_atts( array(
		  'content' => '',
		  'filtering' => '',
		  'columns' => '',
		  'categories' => '',
		  'post_format'=> '',
		  'product_categories' => '',
		  'product_tags' => '',		  
		  'slidesetid' => '',
		  'attached_id' => '', 
		  'pagepost_id' => '',
		  'media_categories' => '',
		  'portfolio_categories' => '',
		  'flickr_set' => '',  	  	  
		  'imageeffect' => '',
		  'height' => '',
		  'width' => '',
		  'title' => '',
		  'imgheight' => '',
		  'align' => '',
		  'imgwidth' => '',	  
		  'id' => '',
		  'lightbox' => '',	  
		  'shadow' => '',
		  'class' => '',
		  'limit' => '',
		  'excerpt' => '',
		  'orderby' => '',	  
		  'sortby' => '',
		  'content_type' => '',
		  'data_source' => '',  
		  ), $atts ) );
	
	
		$NV_title = esc_attr($title);
		$NV_gallerysortby =  esc_attr($sortby);
		$NV_galleryorderby =  esc_attr($orderby);
		$NV_gallerynumposts= esc_attr($limit);
		
		if( !empty( $content_type ) )
		{
			$NV_groupgridcontent = $content_type;
		}
		elseif( !empty( $content ) )
		{
			$NV_groupgridcontent = $content;
		}
		
		$NV_gridfilter = esc_attr($filtering);
		$NV_galleryheight = esc_attr($height);
		$NV_gallerywidth = esc_attr($width);
	
	
		if( empty($columns) )
		{
			$NV_gridcolumns = "3"; // Set default 3 Columns
		}
		else
		{
			$NV_gridcolumns = $columns;
		}
	
		$NV_gridcolumns_text = numberToWords( $NV_gridcolumns ); // convert number to word
		$NV_shadowsize = esc_attr($shadow);
		$NV_imageeffect = esc_attr($imageeffect);
		$NV_imgheight = esc_attr($imgheight);
		$NV_imgwidth = esc_attr($imgwidth);
		$NV_lightbox = esc_attr($lightbox);
	
		if( empty($NV_gallerywidth) )
		{ 
			$NV_panelwidth = 100/$NV_gridcolumns;
			$NV_widthtype='%'; 
		}
		else
		{ 
			$NV_panelwidth = $NV_gallerywidth/$NV_gridcolumns; $NV_widthtype='px';
		}
		
		$NV_panelformat='style="width:'.$NV_panelwidth.$NV_widthtype.';height:'.$NV_galleryheight.'px"'; // calc panel width/height
	
		/* ------------------------------------
		:: DEFAULT IMAGE SIZES
		------------------------------------*/
	
		// Set timthumb width / height values
		if( empty($NV_imgheight) && empty($NV_imgwidth) )
		{
			$NV_imgheight = '120';
			$NV_image_size = "h=". $NV_imgheight ."&amp;";	
		}
		elseif( !empty($NV_imgwidth) && empty($NV_imgheight) )
		{
			$NV_image_size = "w=". $NV_imgwidth ."&amp;";	
		} 
		elseif( !empty($NV_imgheight) && empty($NV_imgwidth) )
		{
			$NV_image_size = "h=". $NV_imgheight ."&amp;";	
		}
		elseif( !empty($NV_imgheight) && !empty($NV_imgwidth) )
		{
			$NV_image_size = "w=". $NV_imgwidth ."&amp;h=". $NV_imgheight ."&amp;";	
		}
	
		if( !empty($NV_gallerywidth) ) $NV_gridgallery_width = 'style="max-width:'.$NV_gallerywidth.'px"'; else $NV_gridgallery_width ='';
	
		// Excerpt
		if( !empty($excerpt) )
		{
			$NV_galleryexcerpt = esc_attr($excerpt);
		}
		else
		{
			$NV_galleryexcerpt = "55";
		}
	
		ob_start(); 
		
		/* ------------------------------------
		:: SET VARIABLES
		------------------------------------*/
		
		$NV_shortcode_id="gd".esc_attr($id);
		$NV_show_slider = 'gridgallery';
		$NV_gallerycat = esc_attr($categories);
		$NV_gallerypostformat = esc_attr($post_format);
		$NV_mediacat = esc_attr($media_categories);
		if( !empty( $portfolio_categories ) ) $NV_mediacat = esc_attr($portfolio_categories);
		$NV_slidesetid = esc_attr($slidesetid);
		$NV_attachedmedia = esc_attr($attached_id);
		$NV_flickrset = esc_attr($flickr_set);
		$NV_productcat = esc_attr($product_categories);
		$NV_producttag = esc_attr($product_tags);
		$NV_pagepost_id = esc_attr($pagepost_id);
		
		/* ------------------------------------
		:: SET VARIABLES *END*
		------------------------------------*/
	
		if($NV_title) echo '<div class="gallery-title"><h4>'.$NV_title.'</h4></div>'; // TITLE
		
		if( $NV_gridfilter == 'yes' ) $class = $class . ' filter';
		
		echo '<div id="grid-'. $NV_shortcode_id .'" data-grid-columns="'. $NV_gridcolumns .'" class="gallery-wrap grid-gallery fluid-gutter nv-skin '. $align .' '. $class .'" '. $NV_gridgallery_width .'>';
	
		$postcount = 0;
	
		/* ------------------------------------
		
		:: LOAD DATA SOURCE
		
		------------------------------------*/
		
		if( empty( $data_source ) )
		{
			if( !empty( $NV_attachedmedia ) ) $NV_datasource = 'data-1';
			if( !empty( $NV_gallerycat ) || !empty($NV_gallerypostformat ) ) $NV_datasource = 'data-2';
			if( !empty( $NV_flickrset ) )  $NV_datasource = 'data-3';
			if( !empty( $NV_slidesetid ) ) $NV_datasource = 'data-4';
			if( !empty( $NV_productcat ) || !empty( $NV_producttag ) ) $NV_datasource = 'data-5';
			if( !empty( $NV_mediacat ) ) $NV_datasource = 'data-6';
			if( !empty( $NV_pagepost_id ) ) $NV_datasource = 'data-8';
		}
		else
		{
			$NV_datasource = $data_source;
		}
	
		if( $NV_datasource == "data-1" ) 
		{
			include(NV_FILES .'/inc/classes/post-attachments-class.php');		
		}
		elseif( $NV_datasource == "data-2" ||  $NV_datasource == "data-5" || $NV_datasource == "data-6" || $NV_datasource == "data-8" ) 
		{
			include(NV_FILES .'/inc/classes/post-categories-class.php');		
		}
		elseif( $NV_datasource == "data-3" )
		{
			include(NV_FILES .'/inc/classes/flickr-class.php');			
		}
		elseif( $NV_datasource == "data-4" )
		{
			include(NV_FILES .'/inc/classes/slideset-class.php');		
		}
	
		/* ------------------------------------
		
		:: LOAD DATA SOURCE *END*
		
		------------------------------------*/ 
	
		echo '<div class="clear"></div>';
		echo '</div><!-- /gallery-wrap -->';
	
	
		if( $NV_gridfilter =='yes' ) 
		{
			wp_deregister_script('jquery-isotope');
			wp_register_script('jquery-isotope',get_template_directory_uri().'/js/jquery.isotope.min.js',false,array('jquery'),true);
			wp_enqueue_script('jquery-isotope');
		}
	
		$output_string=ob_get_contents();
		ob_end_clean();
	
		return $output_string;
	}

	/* ------------------------------------
	:: GRID GALLERY MAP
	------------------------------------*/

	wpb_map( array(
		"base"		=> "postgallery_grid",
		"name"		=> __("Grid Gallery", "js_composer"),
		"class"		=> "nv_options grid",
		"controls"	=> "edit_popup_delete",
		"icon"      => "icon-gridgallery",
		"category"  => __('Gallery', 'js_composer'),
		"params"	=> array(
			array(
				"type" => "textfield",
				"holder" => "h4",
				"class" => "",
				"heading" => __("Gallery ID", "js_composer"),
				"param_name" => "id",
				"value" => __("grid_", "js_composer"),
				"description" => __("Enter a unique ID grid_one.", "js_composer")
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Title", "js_composer"),
				"param_name" => "title",
				"value" => "",
				"description" => __("Add an optional title.", "js_composer")
			),						
			get_common_options( 'datasource' ),
			get_common_options( 'data-1' ),
			get_common_options( 'data-2' ),
			get_common_options( 'data-2-formats' ),
			get_common_options( 'data-8' ),
			get_common_options( 'orderby' ),
			get_common_options( 'sortby' ),
			get_common_options( 'excerpt' ),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Post Limit", "js_composer"),
				"param_name" => "limit",
				"value" => "",
				"dependency" => Array('element' => 'data_source' /*, 'not_empty' => true*/, 'value' => array('data-2','data-5','data-8','data-6')),
				"description" => __("Set a posts limit.", "js_composer")
			),				
			get_common_options( 'data-3' ),	
			get_common_options( 'data-4' ),	
			get_common_options( 'data-5' ),	
			get_common_options( 'data-5-tags' ),
			get_common_options( 'data-6' ),
			get_common_options( 'content' ),
			get_common_options( 'columns' ),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => __("Animated Filtering", "js_composer"),
				"param_name" => "filtering",
				"value" => array(
					'Enable' => 'yes',
				)
			),			
			get_common_options( 'width' ),
			get_common_options( 'height', 'grid' ),
			get_common_options( 'align', 'Gallery' ),
			get_common_options( 'imageeffect' ),
			get_common_options( 'imgwidth' ),	
			get_common_options( 'imgheight' ),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => __("Lightbox", "js_composer"),
				"param_name" => "lightbox",
				"value" => array(
					'Enable' => 'yes',
				)
			),			
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("CSS Classes", "js_composer"),
				"param_name" => "class",
				"value" => "",
				"description" => __("Add an optional CSS classes.", "js_composer")
			),					
		)		
	) );

add_shortcode('postgallery_grid', 'postgallery_grid_shortcode');

