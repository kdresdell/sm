<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet
	$optionsframework_settings = get_option( 'optionsframework' ); 
	$optionsframework_settings['id'] = 'themeva'; // _themeva_mod
	update_option( 'optionsframework', $optionsframework_settings );
}


function optionsframework_options() {

	// WP Editor data
	$wp_editor_settings = array(
		'wpautop' => true, // Default
		'textarea_rows' => 5,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/lib/adm/images/';	
	
	$theme = wp_get_theme();
	$theme_name = $theme->Name;
	
	// Page Layout
	$page_layout_array = array(
		'layout_one' => array(
			'path' => $imagepath . 'layout-1.png',
			'name' => ''
		),
		'layout_two' => array(
			'path' => $imagepath . 'layout-2.png',
			'name' => ''
		),	
		'layout_three' => array(
			'path' => $imagepath . 'layout-3.png',
			'name' => ''
		),	
		'layout_four' => array(
			'path' => $imagepath . 'layout-4.png',
			'name' => ''
		),	
		'layout_five' => array(
			'path' => $imagepath . 'layout-5.png',
			'name' => ''
		),	
		'layout_six' => array(
			'path' => $imagepath . 'layout-6.png',
			'name' => ''
		),				 			 			 			 			 		 
	);		

	// Columns data
	$columns_array = array(
		'1' => __('One', 'themeva-admin'),
		'2' => __('Two', 'themeva-admin'),
		'3' => __('Three', 'themeva-admin'),
		'4' => __('Four', 'themeva-admin'),
	);

	// Sidebar data
	$sidebars = ( of_get_option('sidebars_num') !='' ? of_get_option('sidebars_num') : 2 );
	
	for ( $i=1; $i <= $sidebars; $i++ )
	{
		 $sidebar_array['Sidebar'.$i] = 'Sidebar '.$i;
	}
	
	$post_formats = get_theme_support( 'post-formats' );

	foreach( $post_formats[0] as $format )
	{
		 $format_array[$format] = $format;
	}	

	// Author Bio
	$author_bio_array = array(
		'posts' => __('Posts', 'themeva-admin'),
		'enable' => __('Posts &amp; Pages', 'themeva-admin'),
		'disable' => __('Disable', 'themeva-admin'),
	);

	// Twitter Fedd
	$twitter_feed_array = array(
		'none' => __('Disabled', 'themeva-admin'),
		'pagetop' => __('Top', 'themeva-admin'),
		'pagebot' => __('Bottom', 'themeva-admin'),
	);	

	// Social Icon data
	$social_icon_array = social_icon_data();

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one' => '1',
		'five' => '1'
	);

	// Background Defaults
	$background_defaults = array(
		'color' => '',
		'image' => '',
		'repeat' => 'repeat',
		'position' => 'top center',
		'attachment'=>'scroll' );

	// Typography Defaults
	$typography_defaults = array(
		'size' => '15px',
		'face' => 'georgia',
		'style' => 'bold',
		'color' => '#bada55' );
		
	// Typography Options
	$typography_options = array(
		'sizes' => array( '6','12','14','16','20' ),
		'faces' => array( 'Helvetica Neue' => 'Helvetica Neue','Arial' => 'Arial' ),
		'styles' => array( 'normal' => 'Normal','bold' => 'Bold' ),
		'color' => false
	);

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category){
		$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}


	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}


	$options = array();

	$options[] = array(
		'name' => __('General', 'themeva-admin'),
		'type' => 'heading'
	);	

	$options[] = array(
		'name' => __('Layout', 'themeva-admin'),
		'type' => 'info'
	);

	$options[] = array(
		'name' => __('Responsive Design', 'themeva-admin'),
		'desc' => '',
		'id' => 'enable_responsive',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);	

	$options[] = array(
		'name' => 'Page Layout',
		'desc' => '',
		'id' => 'pagelayout',
		'std' => 'layout_four',
		'type' => "images",
		'options' => $page_layout_array
	);		

	$options[] = array(
		'name' => __('Breadcrumbs', 'themeva-admin'),
		'desc' => '',
		'id' => 'breadcrumb',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);

	$options[] = array(
		'name' => __('Page Comments', 'themeva-admin'),
		'desc' => '',
		'id' => 'pagecomments',
		'std' => 'disable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);

	$options[] = array(
		'name' => __('Show Author Bio', 'themeva-admin'),
		'desc' => __('Enable this option to display author bio information.', 'themeva-admin'),
		'id' => 'author_bio',
		'std' => 'disable',
		'type' => 'radio_inline',
		'options' => $author_bio_array
	);	

	$options[] = array(
		'name' => __('Number of Sidebars', 'themeva-admin'),
		'plac' => __('Default is 2 sidebars', 'themeva-admin'),
		'id' => 'sidebars_num',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Typography', 'themeva-admin'),
		'type' => 'info',
		'id' => 'typopgraphy_info',
		'tooltip' => 'See documentation for how to setup custom <a target="_blank" href="http://themeva.com/docs/'. strtolower($theme_name) .'/google-cufon-font-settings/">Google / Cuf&oacute;n</a> Fonts.',		
	);	

	$options[] = array(
		'name' => __('Font Type', 'themeva-admin'),
		'desc' => '',
		'id' => 'nv_font_type',
		'std' => 'enable_google',
		'type' => 'radio_inline',
		'options' => array(
			'enable' => __('Cuf&oacute;n', 'themeva-admin'),
			'enable_google' => __('Google', 'themeva-admin'),
			'disable' => __('Standard', 'themeva-admin')
		)	
	);		
	
	$options[] = array(
		'name' => __('Custom Cuf&oacute;n Font', 'themeva-admin'),
		'desc' => '',
		'id' => 'cufon_font',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __('Custom Google Font One', 'themeva-admin'),
		'desc' => '',
		'id' => 'googlefont_url_1',
		'plac' => 'URL Name',
		'std' => '',
		'type' => 'text'
	);	

	$options[] = array(
		'name' => '',
		'desc' => '',
		'id' => 'googlefont_css_1',
		'plac' => 'CSS Name',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Custom Google Font Two', 'themeva-admin'),
		'desc' => '',
		'id' => 'googlefont_url_2',
		'plac' => 'URL Name',
		'std' => '',
		'type' => 'text'
	);	

	$options[] = array(
		'name' => '',
		'desc' => '',
		'id' => 'googlefont_css_2',
		'plac' => 'CSS Name',
		'std' => '',
		'type' => 'text'
	);			

	$options[] = array(
		'name' => __('Image Resizing + First Image Detection', 'themeva-admin'),
		'type' => 'info',
		'id' => 'image_resize_info',
	);	

	$options[] = array(
		'name' => __('Image Resizer', 'themeva-admin'),
		'desc' => '',
		'id' => 'timthumb_disable',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);	

	$options[] = array(
		'name' => __('First Image Detection', 'themeva-admin'),
		'desc' => '',
		'id' => 'firstimage_detect',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);		

	$options[] = array(
		'name' => __('JW Player', 'themeva-admin'),
		'type' => 'info',
		'desc' => __('See documentation for how to setup JW Player', 'themeva-admin'),			
	);	

	$options[] = array(
		'name' => __('Javascript File ( jwplayer.js )', 'themeva-admin'),
		'id' => 'jwplayer_js',
		'plac' => 'Upload jwplayer.js',
		'type' => 'upload'
	);	

	$options[] = array(
		'name' => __('Flash File ( player.swf )', 'themeva-admin'),
		'id' => 'jwplayer_swf',
		'plac' => 'Upload player.swf',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __('Plugins', 'themeva-admin'),
		'id' => 'jwplayer_plugins',
		'plac' => 'Comma separated',
		'type' => 'upload'
	);	

	$options[] = array(
		'name' => __('Skin', 'themeva-admin'),
		'id' => 'jwplayer_skin',
		'plac' => '.zip file',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __('Controlbar Height', 'themeva-admin'),
		'desc' => '',
		'id' => 'jwplayer_height',
		'plac' => '24 is the default',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Controlbar Position', 'themeva-admin'),
		'desc' => '',
		'id' => 'jwplayer_skinpos',
		'std' => 'over',
		'type' => 'radio_inline',
		'options' => array(
			'over' => __('Over', 'themeva-admin'),
			'top' => __('Top', 'themeva-admin'),
			'bottom' => __('Bottom', 'themeva-admin')
		)	
	);

	$options[] = array(
		'name' => __('Flickr', 'themeva-admin'),
		'type' => 'info',
		'desc' => __('See documentation for how to setup Flickr', 'themeva-admin'),		
	);		

	$options[] = array(
		'name' => __('Flickr User ID', 'themeva-admin'),
		'desc' => '',
		'id' => 'flickr_userid',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Header', 'themeva-admin'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __('Primary Branding / Logo', 'themeva-admin'),
		'type' => 'info',
	);	

	$options[] = array(
		'name' => __('Primary Logo', 'themeva-admin'),
		'desc' => '',
		'id' => 'branding_url',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __('Primary Logo ( Retina )', 'themeva-admin'),
		'desc' => '',
		'id' => 'branding_2x',
		'type' => 'upload'
	);	

	$options[] = array(
		'name' => __('Primary Logo ( Retina ) Width x Height', 'themeva-admin'),
		'desc' => '',
		'id' => 'branding_2x_dimensions',
		'std' => '',
		'plac' => 'e.g. 200x100 ( See description below )',
		'desc' => 'Enter a value half the size of the actual image size e.g. 400px ( width ) x 200px ( height ): value = <strong>200x100</strong>',
		'type' => 'text'
	);			

	$options[] = array(
		'name' => __('Secondary Branding / Logo', 'themeva-admin'),
		'type' => 'info',
	);	

	$options[] = array(
		'name' => __('Secondary Logo', 'themeva-admin'),
		'desc' => '',
		'id' => 'branding_url_sec',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __('Secondary Logo ( Retina )', 'themeva-admin'),
		'desc' => '',
		'id' => 'branding_sec_2x',
		'type' => 'upload'
	);	

	$options[] = array(
		'name' => __('Secondary Logo ( Retina ) Width x Height', 'themeva-admin'),
		'desc' => '',
		'id' => 'branding_sec_2x_dimensions',
		'std' => '',
		'plac' => 'e.g. 200x100 ( See description below )',
		'desc' => 'Enter a value half the size of the actual image size e.g. 400px ( width ) x 200px ( height ): value = <strong>200x100</strong>',
		'type' => 'text'
	);		

	$options[] = array(
		'name' => __('Main Menu', 'themeva-admin'),
		'type' => 'info'
	);

	$options[] = array(
		'name' => __('WP Custom Menu', 'themeva-admin'),
		'id' => 'wpcustomm_enable',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);
	
	$options[] = array(
		'name' => __('Menu Subtitles', 'themeva-admin'),
		'id' => 'menu_subtitles',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);	

	$options[] = array(
		'name' => __('Menu Descriptions', 'themeva-admin'),
		'id' => 'wpcustommdesc_enable',
		'std' => 'disable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);	
	
	$options[] = array(
		'name' => __('Drop Panel / Search', 'themeva-admin'),
		'type' => 'info',
	);

	$options[] = array(
		'name' => __('Display Drop Panel', 'themeva-admin'),
		'id' => 'enable_droppanel',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);

	$options[] = array(
		'name' => __('Display Search', 'themeva-admin'),
		'id' => 'enable_search',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);	

	$options[] = array(
		'name' => __('Trigger Button Align', 'themeva-admin'),
		'id' => 'droppanel_button_align',
		'std' => 'center',
		'type' => 'radio_inline',
		'options' => array(
			'left' => __('Left', 'themeva-admin'),
			'center' => __('Center', 'themeva-admin')
		)	
	);

	$options[] = array(
		'name' => __('Drop Panel Columns', 'themeva-admin'),
		'id' => 'droppanel_columns_num',
		'std' => '4',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $columns_array
	);	

	$options[] = array(
		'name' => __('Extras', 'themeva-admin'),
		'type' => 'info',
	);

	$options[] = array(
		'name' => __('Sticky Menu', 'themeva-admin'),
		'id' => 'sticky_menu',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);	

	$options[] = array(
		'name' => __('Mobile Menu', 'themeva-admin'),
		'id' => 'mobile_menu',
		'std' => 'floating',
		'type' => 'radio_inline',
		'options' => array(
			'floating' => __('Floating Menu', 'themeva-admin'),
			'select' => __('Select Menu', 'themeva-admin')
		)	
	);		

	$options[] = array(
		'name' => __('Favicon', 'themeva-admin'),
		'desc' => '',
		'id' => 'header_favicon',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __('Custom Header HTML / Shortcode', 'themeva-admin'),
		'id' => 'header_html',
		'type' => 'textarea',
		'settings' => array(
			'rows' => '20'
		)
	);	

	$options[] = array(
		'name' => __('Header Custom Field', 'themeva-admin'),
		'id' => 'header_customfield',
		'type' => 'editor',
		'settings' => $wp_editor_settings 
	);

	$options[] = array(
		'name' => __('Header Infobar Message', 'themeva-admin'),
		'id' => 'header_infobar',
		'type' => 'editor',
		'settings' => $wp_editor_settings 
	);

	$options[] = array(
		'name' => __('Tracking Code', 'themeva-admin'),
		'id' => 'tracking_code',
		'type' => 'textarea',
		'desc' => __('Add Google Analytics / tracking code within this field.', 'themeva-admin'),
		'settings' => array(
			'rows' => '20'
		)		
	);		
		
	$options[] = array(
		'name' => __('Footer', 'themeva-admin'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __('Main Footer', 'themeva-admin'),
		'type' => 'info',
	);		

	$options[] = array(
		'name' => __('Display Footer', 'themeva-admin'),
		'id' => 'mainfooter',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);	

	$options[] = array(
		'name' => __('Footer Columns', 'themeva-admin'),
		'id' => 'footer_columns_num',
		'std' => '4',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $columns_array
	);

	$options[] = array(
		'name' => __('Lower Footer', 'themeva-admin'),
		'type' => 'info',
	);

	$options[] = array(
		'name' => __('Display Lower Footer', 'themeva-admin'),
		'id' => 'lowerfooter',
		'std' => 'enable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'enable' => __('On', 'themeva-admin')
		)	
	);

	$options[] = array(
		'name' => __('Left Column', 'themeva-admin'),
		'id' => 'lowfooterleft',
		'type' => 'textarea',
		'settings' => $wp_editor_settings 
	);

	$options[] = array(
		'name' => __('Right Column', 'themeva-admin'),
		'id' => 'lowfooterright',
		'type' => 'textarea',
		'settings' => $wp_editor_settings 
	);


	$options[] = array(
		'name' => __('Blog', 'themeva-admin'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __('Layout', 'themeva-admin'),
		'type' => 'info',
	);	

	$options[] = array(
		'name' => 'Page Layout',
		'desc' => '',
		'id' => 'arhlayout',
		'std' => 'layout_four',
		'type' => "images",
		'options' => $page_layout_array
	);

	$options[] = array(
		'name' => __('Column 1 Sidebar', 'themeva-admin'),
		'id' => 'archcolone',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $sidebar_array
	);

	$options[] = array(
		'name' => __('Column 2 Sidebar', 'themeva-admin'),
		'id' => 'archcoltwo',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $sidebar_array
	);	

	$options[] = array(
		'name' => __('Layout Format', 'themeva-admin'),
		'desc' => '',
		'id' => 'arhpostdisplay',
		'std' => 'normal',
		'type' => 'radio_inline',
		'options' => array(
			'normal' => __('Normal', 'themeva-admin'),
			'grid' => __('Grid', 'themeva-admin')
		)	
	);

	$options[] = array(
		'name' => __('Grid Columns', 'themeva-admin'),
		'desc' => '',
		'id' => 'arhpostcolumns',
		'std' => 'normal',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => array (
			'2' => 'Two',
			'3' => 'Three',
			'4' => 'Four',
			'5' => 'Five',
			'6' => 'Six',
		)
	);	

	$options[] = array(
		'name' => __('Post Content', 'themeva-admin'),
		'id' => 'arhpostcontent',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => array (
			'excerpt_image' => 'Excerpt + First / Custom Image',
			'excerpt' => 'Excerpt Only',
			'image_only' => 'Image Only',
			'full_post' => 'Full Post',
		)
	);

	$options[] = array(
		'name' => __('Display Post Metadata', 'themeva-admin'),
		'id' => 'arhpostpostmeta',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => array (
			'' => 'Archive / Single Post',
			'archive_only' => 'Archive Only',
			'post_only' => 'Single Post Only',
			'disabled' => 'Disabled',
		)
	);

	$options[] = array(
		'name' => __('Post Metadata Align', 'themeva-admin'),
		'desc' => '',
		'id' => 'postmetaalign',
		'std' => 'default',
		'type' => 'radio_inline',
		'options' => array(
			'default' => __('Left', 'themeva-admin'),
			'post_title' => __('Below Title', 'themeva-admin')
		)	
	);	

	$options[] = array(
		'name' => __('Blog Page Images', 'themeva-admin'),
		'type' => 'info',
	);	

	$options[] = array(
		'name' => __('Image Alignment', 'themeva-admin'),
		'id' => 'arhimgalign',
		'std' => 'aligncenter',
		'type' => 'radio_inline',
		'options' => array(
			'alignleft' => __('Left', 'themeva-admin'),
			'aligncenter' => __('Center', 'themeva-admin'),
			'alignright' => __('Right', 'themeva-admin')
		)	
	);
	
	$options[] = array(
		'name' => __('Image Lightbox', 'themeva-admin'),
		'id' => 'arhimgdisplay',
		'std' => 'disable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'lightbox' => __('On', 'themeva-admin')
		)	
	);	
	
	$options[] = array(
		'name' => __('Image Effect', 'themeva-admin'),
		'id' => 'arhimgeffect',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => array (
			'' => 'Shadow + Reflection',
			'reflect' => 'Reflection',
			'shadow' => 'Shadow',
			'frame' => 'Frame',
			'blackwhite' => 'Black & White',
			'frameblackwhite' => 'Frame + Black & White',
			'shadowblackwhite' => 'Shadow + Black & White',
			'none' => 'None',
		)
	);

	$options[] = array(
		'name' => __('Image Height', 'themeva-admin'),
		'id' => 'arhimgheight',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Image Width', 'themeva-admin'),
		'id' => 'arhimgwidth',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Single Post Images', 'themeva-admin'),
		'type' => 'info',
	);	

	$options[] = array(
		'name' => __('Image Alignment', 'themeva-admin'),
		'id' => 'postimgalign',
		'std' => 'aligncenter',
		'type' => 'radio_inline',
		'options' => array(
			'alignleft' => __('Left', 'themeva-admin'),
			'aligncenter' => __('Center', 'themeva-admin'),
			'alignright' => __('Right', 'themeva-admin')
		)	
	);
	
	$options[] = array(
		'name' => __('Image Lightbox', 'themeva-admin'),
		'id' => 'postimgdisplay',
		'std' => 'disable',
		'type' => 'radio_inline',
		'options' => array(
			'disable' => __('Off', 'themeva-admin'),
			'lightbox' => __('On', 'themeva-admin')
		)	
	);	
	
	$options[] = array(
		'name' => __('Image Effect', 'themeva-admin'),
		'id' => 'postimgeffect',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => array (
			'' => 'Shadow + Reflection',
			'reflection' => 'Reflection',
			'shadow' => 'Shadow',
			'frame' => 'Frame',
			'none' => 'None',
		)
	);

	$options[] = array(
		'name' => __('Image Height', 'themeva-admin'),
		'id' => 'postimgheight',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Image Width', 'themeva-admin'),
		'id' => 'postimgwidth',
		'std' => '',
		'type' => 'text'
	);	

	/*
	$options[] = array(
		'name' => __('Filter Blog Posts', 'themeva-admin'),
		'type' => 'info',
	);	

	$options[] = array(
		'name' => __('Disable Post Format(s)', 'themeva-admin'),
		'id' => 'filter_formats',
		'std' => '',
		'type' => 'multicheck',
		'options' => $format_array		
	);		
	*/

	if (class_exists( 'BP_Core_User' ) || class_exists( 'bbPress' ) ) { 

		$options[] = array(
			'name' => __('BuddyPress / BBPress', 'themeva-admin'),
			'type' => 'heading'
		);
	
		$options[] = array(
			'name' => __('Layout', 'themeva-admin'),
			'type' => 'info',
		);	
	
		$options[] = array(
			'name' => 'Page Layout',
			'desc' => '',
			'id' => 'buddylayout',
			'std' => 'layout_four',
			'type' => "images",
			'options' => $page_layout_array
		);
	
		$options[] = array(
			'name' => __('Column 1 Sidebar', 'themeva-admin'),
			'id' => 'buddycolone',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'options' => $sidebar_array
		);
	
		$options[] = array(
			'name' => __('Column 2 Sidebar', 'themeva-admin'),
			'id' => 'buddycoltwo',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'options' => $sidebar_array
		);

		$options[] = array(
			'name' => __('Content Border', 'themeva-admin'),
			'desc' => '',
			'id' => 'buddycontentborder',
			'std' => 'default',
			'type' => 'radio_inline',
			'options' => array(
				'default' => __('Default', 'themeva-admin'),
				'disabled' => __('Disable', 'themeva-admin')
			)	
		);				
	
	}

	if (class_exists( 'woocommerce' ) ) { 

		$options[] = array(
			'name' => __('Woocommerce', 'themeva-admin'),
			'type' => 'heading'
		);
	
		$options[] = array(
			'name' => __('Layout', 'themeva-admin'),
			'type' => 'info',
		);	
	
		$options[] = array(
			'name' => 'Page Layout',
			'desc' => '',
			'id' => 'woocomlayout',
			'std' => 'layout_four',
			'type' => "images",
			'options' => $page_layout_array
		);
	
		$options[] = array(
			'name' => __('Column 1 Sidebar', 'themeva-admin'),
			'id' => 'woocomcolone',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'options' => $sidebar_array
		);
	
		$options[] = array(
			'name' => __('Column 2 Sidebar', 'themeva-admin'),
			'id' => 'woocomcoltwo',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'options' => $sidebar_array
		);
	}	
	
	
	$options[] = array(
		'name' => __('Social Media', 'themeva-admin'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __('Social Icons', 'themeva-admin'),
		'type' => 'info',
		'id' => 'social_info',
		'tooltip' => 'Switch Social Icons "On" if you want to enable social icons on every Page / Post, it can be disabled on individual pages if required.',		
	);	

	$options[] = array(
		'name' => __('Social Icons', 'themeva-admin'),
		'desc' => '',
		'id' => 'display_socialicons',
		'std' => 'off',
		'type' => 'radio_inline',
		'options' => array(
			'off' => __('Off', 'themeva-admin'),
			'yes' => __('On', 'themeva-admin')
		)	
	);	

	$options[] = array(
		'name' => __('Share Icon', 'themeva-admin'),
		'desc' => '',
		'id' => 'socialicons_share',
		'std' => 'on',
		'type' => 'radio_inline',
		'options' => array(
			'yes' => __('Off', 'themeva-admin'),
			'on' => __('On', 'themeva-admin')
		)	
	);		

	$options[] = array(
		'name' => __('Social Icons Color', 'themeva-admin'),
		'id' => 'socialicons_color',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => array (
			'' => 'Default',
			'light' => 'Light',
			'dark' => 'Dark',
			'color' => 'Color',
		)
	);	

	foreach ( $social_icon_array as $key => $socialicon )
	{
		$social_array[ strtolower( str_replace('.','',$socialicon['name'] ) ) ] = $socialicon['name'];
	}

	$options[] = array(
		'name' => __('Enable Social Icons', 'themeva-admin'),
		'id' => 'socialicons',
		'std' => '',
		'type' => 'multicheck',
		'options' => $social_array		
	);	
	

	$options[] = array(
		'name' => __('Social Icon URL\'s', 'themeva-admin'),
		'type' => 'info',
		'id' => 'socialurl_info',		
	);			
	
	foreach ( $social_icon_array as $key => $socialicon )
	{
		$options[] = array(
			'name' => $socialicon['name'],
			'id' => $key,
			'std' => $socialicon['path'],
			'type' => 'text'
		);	
	}

	$options[] = array(
		'name' => __('Twitter Feed', 'themeva-admin'),
		'type' => 'info',
		'id' => 'twitter_info',
		'tooltip' => 'Enter your Twitter details, if you wish to enable Twitter Feed globally, set the Twitter Display, this can be overriden on individual pages.',
	);

	$options[] = array(
		'name' => __('Twitter Username', 'themeva-admin'),
		'id' => 'twitter_usrname',
		'type' => 'text'
	);	

	$options[] = array(
		'name' => __('Consumer Key', 'themeva-admin'),
		'id' => 'twitter_conkey',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Consumer Secret', 'themeva-admin'),
		'id' => 'twitter_consecret',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Access Token', 'themeva-admin'),
		'id' => 'twitter_acctoken',
		'type' => 'text'
	);	

	$options[] = array(
		'name' => __('Access Token Secret', 'themeva-admin'),
		'id' => 'twitter_acctokensecret',
		'type' => 'text'
	);				
	
	$options[] = array(
		'name' => __('Number of Tweets', 'themeva-admin'),
		'id' => 'twitter_feednum',
		'plac' => 'Enter the number of tweets to display',
		'type' => 'text'
	);	

	$options[] = array(
		'name' => __('Twitter Display', 'themeva-admin'),
		'id' => 'twitter_display',
		'std' => 'none',
		'type' => 'radio_inline',
		'options' => $twitter_feed_array
	);		
	
	$options[] = array(
		'name' => __('Main RSS Feed', 'themeva-admin'),
		'type' => 'info',
	);	
		
	$options[] = array(
		'name' => __('Main RSS Title', 'themeva-admin'),
		'id' => 'rss_title',
		'std' => 'Blog',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Main RSS Feed URL', 'themeva-admin'),
		'id' => 'rss_feed',
		'plac' => 'e.g. /category/YOUR-CATEGORY-NAME/feed/',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Customize', 'themeva-admin'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __('Custom CSS', 'themeva-admin'),
		'id' => 'header_css',
		'type' => 'raw',
		'settings' => array(
			'rows' => '20'
		)	
	);	

	$options[] = array(
		'name' => __('Mobile CSS', 'themeva-admin'),
		'id' => 'responsive_css',
		'desc' => __('CSS for Mobile / Responsive mode e.g. ( hides sidebars ) <strong>.content-wrap .sidebar {display:none !important;}</strong>', 'themeva-admin'),
		'type' => 'raw',
		'settings' => array(
			'rows' => '20'
		)	
	);

	$options[] = array(
		'name' => __('JavaScript', 'themeva-admin'),
		'id' => 'footer_javascript',
		'desc' => __('Add your scripts here, loads at the end of the page. E.g. <strong>&lt;script type="text/javascript"&gt; YOUR CODE &lt;/script&gt;</strong>', 'themeva-admin'),
		'type' => 'raw',
		'settings' => array(
			'rows' => '20'
		)
	);	

	$options[] = array(
		'name' => __('Getting Started', 'themeva-admin'),
		'type' => 'heading'
	);	

	$options[] = array(
		'name' => __('1. Import Demo Content', 'themeva-admin'),
		'type' => 'info',
		'desc' => __('
		<p>Click the button below to Import the demo content. Please note, some of the demo images have been removed due to copyright.</p>
		<p><strong>DO NOT</strong> install if you have existing content, the demo content may interfere.</p>
		<a href="#" data-role="button" data-theme="b" data-mini="true" class="import-demo ui-btn-left ui-btn-b ui-shadow ui-btn-corner-all ui-mini ui-btn-up-b" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">'. __( 'Import Demo Content', 'optionsframework' ) .'</span></span></a></span>
		
		<p><div class="ajax-message"></div></p>
		', 'themeva-admin'),			
	);	

	$options[] = array(
		'name' => __('2. Documentation', 'themeva-admin'),
		'type' => 'info',
		'desc' => __('<p>See the Getting Started section of the documentation below.</p>
	
		<a target="_blank" href="http://themeva.com/docs/'. strtolower($theme_name) .'/category/getting-started/" data-role="button" data-theme="b" data-mini="true" class="documentation_link ui-btn-left ui-btn-b ui-shadow ui-btn-corner-all ui-mini ui-btn-up-b" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">'. __( 'Documentation', 'optionsframework' ) .'</span></span></a></span>
		
		', 'themeva-admin'),			
	);	

	$options[] = array(
		'name' => __('3. Customize Skin', 'themeva-admin'),
		'type' => 'info',
		'desc' => __('<p>Click here to Customize a Skin, once the customization screen has loaded - <strong>Select a Skin</strong> to edit from the <strong>Edit + Set Default Skin</strong> section.</p>
	
		<a href="/wp-admin/customize.php" target="_blank" data-role="button" data-theme="b" data-mini="true" class="documentation_link ui-btn-left ui-btn-b ui-shadow ui-btn-corner-all ui-mini ui-btn-up-b" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">'. __( 'Customize', 'optionsframework' ) .'</span></span></a></span>
		
		', 'themeva-admin'),			
	);	
	

	return $options;
}