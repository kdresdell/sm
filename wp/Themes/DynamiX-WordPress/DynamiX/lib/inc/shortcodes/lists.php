<?php

	/* ------------------------------------
	:: LISTS
	------------------------------------*/
	
	function list_shortcode( $atts, $content = null, $code ) {
	   extract( shortcode_atts( array(
		  'style' => '',
		  'color' => '',
	), $atts ) );
	
		return '<div class="list '. esc_attr($style) .' '. esc_attr($color) .'">'. remove_wpautop( $content ) .'</div>';
	
	}

	/* ------------------------------------
	:: LISTS MAP 	
	------------------------------------*/

	wpb_map( array(
		"name"		=> __("List", "js_composer"),
		"base"		=> "list",
		"class"		=> "wpb_controls_top_right",
		"icon"		=> "icon-list",
		"controls"	=> "full",
		"category"  => __('Content', 'js_composer'),
		"params"	=> array(
			array(
				"type" => "dropdown",
				"heading" => __("Style", "js_composer"),
				"param_name" => "style",
				"value" => array(
					__('Arrow', "js_composer") => 'arrow',
					__('Check', "js_composer") => 'check', 
					__('Bullet', "js_composer") => 'orb', 
					__('Cross', "js_composer") => 'cross', 
				),
			),				
			array(
				"type" => "dropdown",
				"heading" => __("Color", "js_composer"),
				"param_name" => "color",
				"value" => get_options_array('colors'),
				"description" => __("Select color of the toggle icon.", "js_composer")
			),							
		   array(
				"type" => "textarea_html",
				"holder" => "div",
				"class" => "",
				"heading" => __("Text", "js_composer"),
				"param_name" => "content",
				"value" => "<ul><li>List Item</li><li>List Item</li><li>List Item</li></ul>",
			),		
		),
	));
	
	add_shortcode('list', 'list_shortcode');