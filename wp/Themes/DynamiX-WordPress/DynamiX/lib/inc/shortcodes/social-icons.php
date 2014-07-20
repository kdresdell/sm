<?php

/* ------------------------------------
:: SOCIAL ICONS
------------------------------------*/

	class WPBakeryShortCode_Socialicon extends WPBakeryShortCode {
		protected  $predefined_atts = array(
					'url' => '',
					'name' => '',
					);
		public function content( $atts, $content = null ) {
			$title = $tab_id = '';
			extract(shortcode_atts(array(
					'url' => '',
					'name' => '',
			), $atts));
	
			$output = '';

			require NV_FILES .'/adm/inc/social-media-urls.php'; // get social media button links

			// Get Social Icons
			$get_social_icons = social_icon_data();
			
			foreach( $get_social_icons as $social_icon => $value )
			{
				$icon_id = str_replace( 'sociallink_','',$social_icon );
				
				if( $icon_id == $name )
				{
					if( $url !='' )
					{
						$sociallink = $url;
					}
					else
					{
						$sociallink = getsociallink( ${'sociallink_'.$name} );
					}
					
					$icon_name = '';
					$icon_name = strtolower( str_replace('.','',$value['name'] ) );
				
					
					if( $icon_name == 'vimeo' ) $icon_name = 'vimeo-square';
					if( $icon_name == 'email' ) $icon_name = 'envelope';
					if( $icon_name == 'google' ) $icon_name = 'google-plus';
						
					$output .= "\n\t\t". '<li class="dock-tab social-'. strtolower( str_replace('.','',$value['name'] ) ) .'">';
					$output .= "\n\t\t\t". '<a href="'. str_replace(' ', '%20', $sociallink) .'" title="'. $value['name'] .'" target="_blank"><i class="social-icon fa fa-lg fa-'. $icon_name .'"></i></a>';
					$output .= "\n\t\t". '</li>';
					
				}
			}
	
			return $output;
		}

		public function mainHtmlBlockParams($width, $i) {
			return 'data-element_type="'.$this->settings["base"].'" class=" wpb_'.$this->settings['base'].'"'.$this->customAdminBlockParams();
		}
		public function containerHtmlBlockParams($width, $i) {
			return 'class="wpb_column_container vc_container_for_children"';
		}
		protected function outputTitle($title) {
			return  '';
		}
	
		public function customAdminBlockParams() {
			return '';
		}
	
	}

	class WPBakeryShortCode_Socialwrap extends WPBakeryShortCode {
	
		public function __construct($settings) {
			parent::__construct($settings);
		}
	
		protected function content( $atts, $content = null ) {

			$align = $share_icon = $el_position = $el_class = '';
			//
			extract(shortcode_atts(array(
				'align' => '',
				'share_icon' => '',
				'el_class' => '',
			), $atts));
			
			$output = '';
	
			$el_class = $this->getExtraClass($el_class);

			if( $share_icon == 'yes' )
			{
				$output .= "\n\t".'<div class="socialicons init '. $align .' '. $el_class .' clearfix">';
				$output .= "\n\t\t".'<ul>';
				$output .= "\n\t\t\t".'<li class="dock-tab"><a class="socialinit" href="#"><i class="fa fa-share-square-o fa-lg"></i></a></li>';
				$output .= "\n\t\t".'</ul>';
				
				$output .= "\n\t".'<div class="socialicons '.$align.' '. $el_class .' toggle">';
				$output .= "\n\t\t".'<ul>';
				$output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
				$output .= "\n\t\t".'</ul>';
				$output .= "\n\t".'</div>';
				$output .= "\n\t".'</div>';
			}
			else
			{
				$output .= "\n\t".'<div class="socialicons display '.$align.' '. $el_class .'">';
				$output .= "\n\t\t".'<ul>';
				$output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
				$output .= "\n\t\t".'</ul>';
				$output .= "\n\t".'</div>';
				$output .= "\n\t".'<div class="clear"></div>';				
			}

			$output = $this->startRow($el_position) . $output . $this->endRow($el_position);
			return $output;
		}
	
	   public function contentAdmin( $atts, $content ) {
			$width = $custom_markup = '';
			$shortcode_attributes = array('width' => '1/1');
			foreach ( $this->settings['params'] as $param ) {
				if ( $param['param_name'] != 'content' ) {
					if (isset($param['value']) && is_string($param['value']) ) {
						$shortcode_attributes[$param['param_name']] = $param['value'];
					} elseif(isset($param['value'])) {
						$shortcode_attributes[$param['param_name']] = $param['value'];
					}
				} else if ( $param['param_name'] == 'content' && $content == NULL ) {
					$content = $param['value'];
				}
			}
			extract(shortcode_atts(
				$shortcode_attributes
				, $atts));
	
			$output = '';
	
			$elem = $this->getElementHolder($width);
	
			$inner = '';
			foreach ($this->settings['params'] as $param) {
				$param_value = '';
				$param_value = isset($$param['param_name']) ? $$param['param_name'] : '';
				if ( is_array($param_value)) {
					// Get first element from the array
					reset($param_value);
					$first_key = key($param_value);
					$param_value = $param_value[$first_key];
				}
				$inner .= $this->singleParamHtmlHolder($param, $param_value);
			}
			
			$inner .= '<input type="hidden" class="wpb_vc_param_value columns textfield " name="columns" value="">';
	 
			$tmp = '';
	
			if ( isset($this->settings["custom_markup"]) && $this->settings["custom_markup"] != '' ) {
				if ( $content != '' ) {
					$custom_markup = str_ireplace("%content%", $tmp.$content, $this->settings["custom_markup"]);
				} else if ( $content == '' && isset($this->settings["default_content_in_template"]) && $this->settings["default_content_in_template"] != '' ) {
					$custom_markup = str_ireplace("%content%", $this->settings["default_content_in_template"], $this->settings["custom_markup"]);
				} else {
					$custom_markup =  str_ireplace("%content%", '', $this->settings["custom_markup"]);
				}
	 
				$inner .= do_shortcode($custom_markup);
			}
			$elem = str_ireplace('%wpb_element_content%', $inner, $elem);
			$output = $elem;
	
			return $output;
		}
	}

	/* ------------------------------------
	:: SOCIAL ICONS MAP
	------------------------------------*/	

	wpb_map( array(
		"name"		=> __("Social Icons", "js_composer"),
		"base"		=> "socialwrap",
		"show_settings_on_create" => false,
		"is_container" => true,
		"icon"		=> "icon-wpb-tweetme",
		"category"  => __('Social', 'js_composer'),
		"wrapper_class" => "clearfix nv_options social_wrap",
		"params"	=> array(
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => __("Share Icon", "js_composer"),
				"param_name" => "share_icon",
				"value" =>  array(
					__('Enable', "js_composer") => "yes", 
				)
			),	
			array(
				"type" => "dropdown",
				"heading" => __("Align", "js_composer"),
				"param_name" => "align",
				"value" => array(
					__('Left', "js_composer") => 'left',
					__('Center', "js_composer") => 'center', 
					__('Right', "js_composer") => 'right', 

				),
			),				
			array(
				"type" => "textfield",
				"heading" => __("Extra class name", "js_composer"),
				"param_name" => "el_class",
				"value" => "",
				"description" => __("Add custom CSS classes to the above field: <br /><br />
				<strong>color</strong> = Color Social Icons <br />", "js_composer")
			)
		),
	  "custom_markup" => '
	  <h4 class="wpb_element_title">'. __("Social Icons", "js_composer") .'</h4>
	  <div class="wpb_social_icon_holder wpb_holder clearfix vc_container_for_children">
	  %content%
	  </div>
	  <div class="tab_controls">
	  <button class="add_social" title="'.__("Add Icon", "js_composer").'">'.__("Add Icon", "js_composer").'</button>
	  </div>
	  ',
	  'default_content' => '
	  [socialicon name="Edit Me"]
	  ',
	  'js_view' => 'SocialWrapView',		
	) );
	
	
	// Get Social Icons
	$get_social_icons = social_icon_data();
	
	foreach( $get_social_icons as $social_icon => $value )
	{
		$social_icons[ $value['name'] ] = str_replace('sociallink_','',$social_icon);
	}
	
	wpb_map( array(
		"name"		=> __("Social Icon", "js_composer"),
		"base"		=> "socialicon",
  	  	"content_element" => false,
  	  	"is_container" => true,	
		"params"	=> array(
			array(
				"type" => "dropdown",
				"heading" => __("Social Icon", "js_composer"),
				"holder" => "h4",
				"param_name" => "name",
				"value" => $social_icons,
				"description" => __("Select color of the toggle icon.", "js_composer")
			),				
			array(
				"type" => "textfield",
				"heading" => __("Link URL", "js_composer"),
				"param_name" => "url",
				"value" => "",
				"description" => __("Optional Link URL", "js_composer")
			),	
		)
	) );
	
	add_shortcode('socialwrap', 'socialicons_shortcode');
	add_shortcode('socialicon', 'socialicons_shortcode');