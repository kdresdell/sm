<?php

/**
 * Generates the tabs that are used in the options menu
 */

function optionsframework_tabs() {

	$optionsframework_settings = get_option('optionsframework');
	$options = optionsframework_options();
	$menu = '';

	foreach ($options as $value) {
		// Heading for Navigation
		if ($value['type'] == "heading") {
			$jquery_click_hook = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['name']) );
			$jquery_click_hook = "of-option-" . $jquery_click_hook;
			$menu .= '<a id="'.  esc_attr( $jquery_click_hook ) . '-tab" class="nav-tab" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#'.  $jquery_click_hook ) . '">' . esc_html( $value['name'] ) . '</a>';
		}
	}

	return $menu;
}

/**
 * Generates the options fields that are used in the form.
 */

function optionsframework_fields() {

	global $allowedtags;
	$optionsframework_settings = get_option('optionsframework');

	// Gets the unique option id
	if ( isset( $optionsframework_settings['id'] ) ) {
		$option_name = $optionsframework_settings['id'];
	}
	else {
		$option_name = 'optionsframework';
	};

	$settings = get_option($option_name);
	$options = optionsframework_options();

	$counter = 0;
	$menu = '';
	
	foreach ( $options as $value ) {

		$counter++;
		$val = '';
		$select_value = '';
		$checked = '';
		$output = '';
		$tooltip = '';
		
		if( empty( $value['id'] ) ) $value['id'] = '';
		if( empty( $value['desc'] ) ) $value['desc'] = '';
		if( empty( $value['plac'] ) ) $value['plac'] = '';

		// Wrap all options
		if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) ) {

			// Keep all ids lowercase with no spaces
			$value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['id']) );

			$id = 'section-' . $value['id'];

			$class = 'section ';
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}

			$output .= '<li data-role="fieldcontain" id="' . esc_attr( $id ) .'" class="' . esc_attr( $class ) . '">'."\n";
			if ( isset( $value['name'] ) ) {
				if (  $value['type'] != 'radio' && $value['type'] != 'radio_inline' && $value['type'] != 'editor' && $value['type'] != 'checkbox' && $value['type'] != 'multicheck' && $value['type'] != 'typography' && $value['type'] != 'color' && $value['type'] != 'background' && $value['type'] != 'images'  ) {
					$output .= '<label for="'. $value['id']. '">' . esc_html( $value['name'] ) . '</label>' . "\n";
				}
			}
		
			$output .= "\n";
			
		}

		// Set default value to $val
		if ( isset( $value['std'] ) ) {
			$val = $value['std'];
		}

		// If the option is already saved, ovveride $val
		if ( ( $value['type'] != 'heading' ) && ( $value['type'] != 'info') ) {
			if ( isset( $settings[($value['id'])]) ) {
				$val = $settings[($value['id'])];
				// Striping slashes of non-array options
				if ( !is_array($val) ) {
					$val = stripslashes( $val );
				}
			}
		}

		// If there is a description save it for labels
		$explain_value = '';
		if ( isset( $value['desc'] ) ) {
			$explain_value = $value['desc'];
		}

		switch ( $value['type'] ) {

		// Basic text input
		case 'text':
			$output .= '<input data-mini="true" id="' . esc_attr( $value['id'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" placeholder="'. $value['plac'] .'" value="' . esc_attr( $val ) . '" />';
			break;

		// Textarea
		case 'textarea':
			$rows = '8';

			if ( isset( $value['settings']['rows'] ) ) {
				$custom_rows = $value['settings']['rows'];
				if ( is_numeric( $custom_rows ) ) {
					$rows = $custom_rows;
				}
			}

			$val = stripslashes( $val );
			$output .= '<textarea data-mini="true" id="' . esc_attr( $value['id'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '">' . esc_textarea( $val ) . '</textarea>';
			break;

		// Textarea
		case 'raw':
			$rows = '8';

			if ( isset( $value['settings']['rows'] ) ) {
				$custom_rows = $value['settings']['rows'];
				if ( is_numeric( $custom_rows ) ) {
					$rows = $custom_rows;
				}
			}

			$val = stripslashes( $val );
			$output .= '<textarea data-mini="true" id="' . esc_attr( $value['id'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '">' . $val . '</textarea>';
			break;			

		// Select Box
		case ($value['type'] == 'select'):
			$output .= '<select data-mini="true" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

			foreach ($value['options'] as $key => $option ) {
				$selected = '';
				if ( $val != '' ) {
					if ( $val == $key) { $selected = ' selected="selected"';}
				}
				$output .= '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
			}
			$output .= '</select>';
			break;

		// Flip Switch
		case ($value['type'] == 'switch'):
			$output .= '<select data-mini="true" data-role="slider" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

			foreach ($value['options'] as $key => $option ) {
				$selected = '';
				if ( $val != '' ) {
					if ( $val == $key) { $selected = ' selected="selected"';}
				}
				$output .= '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
			}
			$output .= '</select>';
			break;			

		// Slider
		case 'slider':
			$min = $value['min'];
			$max = $value['max'];
			$output .= '<input data-mini="true" id="' . esc_attr( $value['id'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="range"  min="' . esc_attr( $min ) . '" max="' . esc_attr( $max ) . '" value="' . esc_attr( $val ) . '" />';
			break;

		// Radio Inline Box
		case "radio_inline":
			$name = $option_name .'['. $value['id'] .']';
			$output .= '<fieldset data-role="controlgroup" data-type="horizontal" >' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";
			foreach ($value['options'] as $key => $option) {
				$id = $option_name . '-' . $value['id'] .'-'. $key;
				$output .= '<input data-mini="true" class="of-input of-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' />';
				$output .= '<label for="' . esc_attr( $id ) . '">' . esc_html( $option ) . '</label>';
			}
			$output .= '<fieldset>' . "\n";
			break;

		// Radio Box
		case "radio":
			$name = $option_name .'['. $value['id'] .']';
			$output .= '<fieldset data-role="controlgroup">' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";
			foreach ($value['options'] as $key => $option) {
				$id = $option_name . '-' . $value['id'] .'-'. $key;
				$output .= '<input data-mini="true" class="of-input of-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' /><label for="' . esc_attr( $id ) . '">' . esc_html( $option ) . '</label>';
			}
			$output .= '</fieldset>' . "\n";
			break;			


		// Image Selectors
		case "images":
			$name = $option_name .'['. $value['id'] .']';
			$output .= '<fieldset data-role="controlgroup" data-type="horizontal" >' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";
	
			foreach ($value['options'] as $key => $option) {
				$id = $option_name . '-' . $value['id'] .'-'. $key;
				$output .= '<input data-mini="true" class="of-input of-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' /><label for="' . esc_attr( $id ) . '"><img src="' . esc_url( $option['path'] ) . '" alt="' . $option['name'] .'" /><span class="image-text">' . $option['name'] .'</span></label>';
			}
			$output .= '<fieldset>' . "\n";
			break;
			

		// Checkbox
		case "checkbox":
			$output .= '<fieldset data-role="controlgroup">' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";
			$output .= '<input data-mini="true" id="' . esc_attr( $value['id'] ) . '" class="custom checkbox of-input" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" '. checked( $val, 1, false) .' />';
			$output .= '<label for="' . esc_attr( $value['id'] ) . '">'. $value['desc'] .'</label>';
			$output .= '</fieldset>' . "\n";
			break;

		// Multicheck
		case "multicheck":
			$output .= '<fieldset data-role="controlgroup">' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";
			foreach ($value['options'] as $key => $option) {
				$checked = '';
				$label = $option;
				$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));

				$id = $option_name . '-' . $value['id'] . '-'. $option;
				$name = $option_name . '[' . $value['id'] . '][' . $option .']';

				if ( isset($val[$option]) ) {
					$checked = checked($val[$option], 1, false);
				}

				$output .= '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label><input data-mini="true" id="' . esc_attr( $id ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $name ) . '" ' . $checked . ' />';
			}
			$output .= '</fieldset>' . "\n";
			break;

		// Color picker
		case "color":
			$output .= '<fieldset data-role="controlgroup">' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";		
			$output .= '<input data-mini="true" class="of-color" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '" type="text" value="' . esc_attr( $val ) . '" />';
			$output .= '<div id="' . esc_attr( $value['id'] . '_picker' ) . '" class="colorSelector"><div style="' . esc_attr( 'background-color:' . $val ) . '"></div></div>';
			$output .= '</fieldset>' . "\n";
			break;

		// Uploader
		case "upload":
			$output .= optionsframework_medialibrary_uploader( $value['id'], $val, null, '','','', $value['plac'] );
			break;

			// Typography
		case 'typography':
		
			$output .= '<fieldset data-role="controlgroup" data-type="horizontal">' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";
		
			unset( $font_size, $font_style, $font_face, $font_color );
			
			$typography_defaults = array(
				'size' => '',
				'face' => '',
				'style' => '',
				'color' => ''
			);
			
			$typography_stored = wp_parse_args( $val, $typography_defaults );
			
			$typography_options = array(
				'sizes' => of_recognized_font_sizes(),
				'faces' => of_recognized_font_faces(),
				'styles' => of_recognized_font_styles(),
				'color' => true
			);
			
			if ( isset( $value['options'] ) ) {
				$typography_options = wp_parse_args( $value['options'], $typography_options );
			}

			// Font Size
			if ( $typography_options['sizes'] ) {
				$font_size = '<label for="' . esc_attr( $value['id'] . '_size' ) . '">' . $value['name'] . '</label>';
				$font_size .= '<select data-mini="true" class="of-typography of-typography-size" name="' . esc_attr( $option_name . '[' . $value['id'] . '][size]' ) . '" id="' . esc_attr( $value['id'] . '_size' ) . '">';
				$sizes = $typography_options['sizes'];
				foreach ( $sizes as $i ) {
					$size = $i . 'px';
					$font_size .= '<option value="' . esc_attr( $size ) . '" ' . selected( $typography_stored['size'], $size, false ) . '>' . esc_html( $size ) . '</option>';
				}
				$font_size .= '</select>';
			}

			// Font Face
			if ( $typography_options['faces'] ) {
				$font_face = '<label for="' . esc_attr( $value['id'] . '_face' ) . '">' . $value['name'] . '</label>';
				$font_face .= '<select data-mini="true" class="of-typography of-typography-face" name="' . esc_attr( $option_name . '[' . $value['id'] . '][face]' ) . '" id="' . esc_attr( $value['id'] . '_face' ) . '">';
				$faces = $typography_options['faces'];
				foreach ( $faces as $key => $face ) {
					$font_face .= '<option data-mini="true" value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['face'], $key, false ) . '>' . esc_html( $face ) . '</option>';
				}
				$font_face .= '</select>';
			}

			// Font Styles
			if ( $typography_options['styles'] ) {
				$font_style = '<label for="' . esc_attr( $value['id'] . '_style' ) . '">' . $value['name'] . '</label>';
				$font_style .= '<select data-mini="true" class="of-typography of-typography-style" name="'.$option_name.'['.$value['id'].'][style]" id="'. $value['id'].'_style">';
				$styles = $typography_options['styles'];
				foreach ( $styles as $key => $style ) {
					$font_style .= '<option data-mini="true" value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>'. $style .'</option>';
				}
				$font_style .= '</select>';
			}

			// Font Color
			if ( $typography_options['color'] ) {
				$font_color = '<div id="' . esc_attr( $value['id'] ) . '_color_picker" class="colorSelector typo"><div style="' . esc_attr( 'background-color:' . $typography_stored['color'] ) . '"></div></div>';
				$font_color .= '<input data-mini="true" class="of-color of-typography of-typography-color" name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" type="text" value="' . esc_attr( $typography_stored['color'] ) . '" />';
			}
	
			// Allow modification/injection of typography fields
			$typography_fields = compact( 'font_size', 'font_face', 'font_style', 'font_color' );
			$typography_fields = apply_filters( 'of_typography_fields', $typography_fields, $typography_stored, $option_name, $value );
			$output .= implode( '', $typography_fields );
			$output .= '</fieldset>' . "\n";
			break;

		// Background
		case 'background':

			$background = $val;

			// Background Image - New AJAX Uploader using Media Library
			if (empty($background['image'])) {
				$background['image'] = '';
			}


			$output .= '<fieldset data-role="controlgroup" data-type="horizontal">' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";
						
			$output .= '<div class="upload-wrap">'.optionsframework_medialibrary_uploader( $value['id'], $background['image'], null, '',0,'image').'</div>';
						
			$class = 'of-background-properties';
			if ( '' == $background['image'] ) {
				$class .= ' hide';
			}
			$output .= '<div class="' . esc_attr( $class ) . '">';			

			// Background Repeat
			$output .= '<select data-mini="true" class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $value['id'] . '][repeat]'  ) . '" id="' . esc_attr( $value['id'] . '_repeat' ) . '">';
			$repeats = of_recognized_background_repeat();

			foreach ($repeats as $key => $repeat) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>'. esc_html( $repeat ) . '</option>';
			}
			$output .= '</select>';

			// Background Position
			$output .= '<select data-mini="true" class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position]' ) . '" id="' . esc_attr( $value['id'] . '_position' ) . '">';
			$positions = of_recognized_background_position();

			foreach ($positions as $key=>$position) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position'], $key, false ) . '>'. esc_html( $position ) . '</option>';
			}
			$output .= '</select>';

			// Background Attachment
			$output .= '<select data-mini="true" class="of-background of-background-attachment" name="' . esc_attr( $option_name . '[' . $value['id'] . '][attachment]' ) . '" id="' . esc_attr( $value['id'] . '_attachment' ) . '">';
			$attachments = of_recognized_background_attachment();

			foreach ($attachments as $key => $attachment) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['attachment'], $key, false ) . '>' . esc_html( $attachment ) . '</option>';
			}
			$output .= '</select>';
			
			// Background Color
			$output .= '<div id="' . esc_attr( $value['id'] ) . '_color_picker" class="colorSelector typo"><div style="' . esc_attr( 'background-color:' . $background['color'] ) . '"></div></div>';
			$output .= '<input data-mini="true" class="of-color of-background of-background-color" name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" type="text" value="' . esc_attr( $background['color'] ) . '" />';			
			
			$output .= '</div>';
			$output .= '</fieldset>' . "\n";
			break;

		// Editor
		case 'editor':

			$output .= '<fieldset data-role="controlgroup" data-type="horizontal">' . "\n";
			$output .= '<legend>'.$value['name'].'</legend>' . "\n";
			$output .= '<p class="explain cmb_metabox_description left">' . wp_kses( $explain_value, $allowedtags) . '</p>'."\n";
			echo $output;
			$textarea_name = esc_attr( $option_name . '[' . $value['id'] . ']' );
			$default_editor_settings = array(
				'textarea_name' => $textarea_name,
				'media_buttons' => false,
				'tinymce' => array( 'plugins' => 'wordpress' )
			);
			$editor_settings = array();
			if ( isset( $value['settings'] ) ) {
				$editor_settings = $value['settings'];
			}
			
			$editor_settings = array_merge( $default_editor_settings, $editor_settings ); // themeva_mod note: doesn't accept custom settings
			
			wp_editor( $val, $value['id'], $editor_settings );
			$output = '';
			$output = '</fieldset>' . "\n";
			echo $output;
			
			$output = '';
			break;

		// Info
		case "info":
			$id = '';
			$class = 'section';
			if ( isset( $value['id'] ) ) {
				$id = 'id="' . esc_attr( $value['id'] ) . '" ';
			}
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}

			$output .= '<li data-role="fieldcontain" ' . $id . 'class="' . esc_attr( $class ) . '">' . "\n";
			if ( isset($value['name']) ) {
				$output .= '<h5 class="cmb_metabox_title"><span class="title-text">' . esc_html( $value['name'] ) . '</span>' . "\n";
				
				if ( !empty($value['tooltip']) ) {
					$output .= '<span class="tooltip-icon">' . "\n";
					$output .= '<a href="#'. $value['id'] . '_popup" data-icon="info" data-theme="b" data-rel="popup" data-role="button" data-iconpos="notext">info</a>' . "\n";
					$output .= '</span>' . "\n";
					$output .= '<div data-role="popup" class="ui-content option-popup" data-theme="a" id="' . $value['id'] . '_popup" >' . "\n";
					$output .= '<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>' . "\n";
					$output .= '<p>' . $value['tooltip'] . '</p>' . "\n";
					$output .= '</div>' . "\n";
					
				}
				
				$output .= '<br class="clear" /></h5>' . "\n";	
			}
			if ( $value['desc'] ) {
				$output .=  apply_filters('of_sanitize_info', '<p class="explain cmb_metabox_description left">' . $value['desc'] . '</p>' )  . "\n";
			}
			$output .= '</li>' . "\n";
			break;

		// Heading for Navigation
		case "heading":
			if ($counter >= 2) {
				$output .= '</ul>'."\n";
			}
			$jquery_click_hook = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['name']) );
			$jquery_click_hook = "of-option-" . $jquery_click_hook;
			$menu .= '<a id="'.  esc_attr( $jquery_click_hook ) . '-tab" class="nav-tab" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#'.  $jquery_click_hook ) . '">' . esc_html( $value['name'] ) . '</a>'."\n";
			$output .= '<ul data-role="list-view" class="ui-listview group_meta_box" id="' . esc_attr( $jquery_click_hook ) . '">';
			//$output .= '<li class="title"><h2>' . esc_html( $value['name'] ) . '</h2></li>' . "\n";
			break;
		}

		if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) ) {
			
			if ( ( $value['type'] != "checkbox" ) && ( $value['type'] != "editor" ) ) {
				$output .= '<p class="explain cmb_metabox_description">' . wp_kses( $explain_value, $allowedtags) . '</p>'."\n";
			}
			$output .= '</li>' . "\n";
		}

		echo $output;
	}
	echo '</ul>';
}