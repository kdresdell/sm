<?php
/*
Script Name: 	Custom Metaboxes and Fields (cmb)
Contributors: 	NorthVantage (_nv_mod)
				Andrew Norcross (@norcross / andrewnorcross.com)
				Jared Atchison (@jaredatch / jaredatchison.com)
				Bill Erickson (@billerickson / billerickson.net)
Description: 	Custom Metaboxes and Fields
Version: 		0.9a
*/

/**
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */


$meta_boxes = array();
$meta_boxes = apply_filters ( 'cmb_meta_boxes' , $meta_boxes );
foreach ( $meta_boxes as $meta_box ) {
	$my_box = new cmb_Meta_Box( $meta_box );
}

/**
 * Validate value of meta fields
 * Define ALL validation methods inside this class and use the names of these 
 * methods in the definition of meta boxes (key 'validate_func' of each field)
 */
class cmb_Meta_Box_Validate {
	function check_text( $text ) {
		if ($text != 'hello') {
			return false;
		}
		return true;
	}
}

/**
 * Defines the url to which is used to load local resources.
 * This may need to be filtered for local Window installations.
 * If resources do not load, please check the wiki for details.
 */
define( 'CMB_META_BOX_URL', apply_filters( 'cmb_meta_box_url', trailingslashit( str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( __FILE__ ) ) ) ) );

/**
 * Create meta boxes
 */
class cmb_Meta_Box {
	protected $_meta_box;

	function __construct( $meta_box ) {
		if ( !is_admin() ) return;

		$this->_meta_box = $meta_box;

		$upload = false;
		foreach ( $meta_box['fields'] as $field ) {
			if ( $field['type'] == 'file' || $field['type'] == 'file_list' ) {
				$upload = true;
				break;
			}
		}
		
		global $pagenow;		
		if ( $upload && in_array( $pagenow, array( 'page.php', 'page-new.php', 'post.php', 'post-new.php' ) ) ) {
			add_action( 'admin_head', array( &$this, 'add_post_enctype' ) );
		}

		add_action( 'admin_menu', array( &$this, 'add' ) );
		add_action( 'save_post', array( &$this, 'save' ) );
		

		add_filter( 'cmb_show_on', array( &$this, 'add_for_id' ), 10, 2 );
		add_filter( 'cmb_show_on', array( &$this, 'add_for_page_template' ), 10, 2 );
	}

	function add_post_enctype() {
		echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#post").attr("enctype", "multipart/form-data");
			jQuery("#post").attr("encoding", "multipart/form-data");
		});
		</script>';
	}

	// Add metaboxes
	function add() {
		$this->_meta_box['context'] = empty($this->_meta_box['context']) ? 'normal' : $this->_meta_box['context'];
		$this->_meta_box['priority'] = empty($this->_meta_box['priority']) ? 'high' : $this->_meta_box['priority'];
		$this->_meta_box['show_on'] = empty( $this->_meta_box['show_on'] ) ? array('key' => false, 'value' => false) : $this->_meta_box['show_on'];
		
		foreach ( $this->_meta_box['pages'] as $page ) {
			if( apply_filters( 'cmb_show_on', true, $this->_meta_box ) )
				add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']) ;
		}
	}
	
	/**
	 * Show On Filters
	 * Use the 'cmb_show_on' filter to further refine the conditions under which a metabox is displayed.
	 * Below you can limit it by ID and page template
	 */
	 
	// Add for ID 
	function add_for_id( $display, $meta_box ) {
		if ( 'id' !== $meta_box['show_on']['key'] )
			return $display;

		// If we're showing it based on ID, get the current ID					
		if( isset( $_GET['post'] ) ) $post_id = $_GET['post'];
		elseif( isset( $_POST['post_ID'] ) ) $post_id = $_POST['post_ID'];
		if( !isset( $post_id ) )
			return false;
		
		// If value isn't an array, turn it into one	
		$meta_box['show_on']['value'] = !is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];
		
		// If current page id is in the included array, display the metabox

		if ( in_array( $post_id, $meta_box['show_on']['value'] ) )
			return true;
		else
			return false;
	}
	
	// Add for Page Template
	function add_for_page_template( $display, $meta_box ) {
		if( 'page-template' !== $meta_box['show_on']['key'] )
			return $display;
			
		// Get the current ID
		if( isset( $_GET['post'] ) ) $post_id = $_GET['post'];
		elseif( isset( $_POST['post_ID'] ) ) $post_id = $_POST['post_ID'];
		if( !( isset( $post_id ) || is_page() ) ) return false;
			
		// Get current template
		$current_template = get_post_meta( $post_id, '_wp_page_template', true );
		
		// If value isn't an array, turn it into one	
		$meta_box['show_on']['value'] = !is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];

		// See if there's a match
		if( in_array( $current_template, $meta_box['show_on']['value'] ) )
			return true;
		else
			return false;
	}

	
	// Show fields
	function show() {

		global $post;
			
		// Use nonce for verification
		//_nv_mod
			echo '<input type="hidden" name="wp_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';
			echo '<ul data-role="list-view" class="cmb_metabox ui-listview">';
	
			foreach ( $this->_meta_box['fields'] as $field ) {
						
				// Set up blank or default values for empty ones
				if ( empty( $field['name'] ) ) 		$field['name'] = '';
				if ( empty( $field['desc'] ) ) 		$field['desc'] = '';
				if ( empty( $field['std'] ) ) 		$field['std'] = '';	
				if ( empty( $field['plac'] ) ) 		$field['plac'] = '';	
				if ( empty( $field['size'] ) ) 		$field['size'] = '';	
				if ( empty( $field['class'] ) ) 	$field['class'] = '';	
				if ( empty( $field['data'] ) ) 		$field['data'] = '';	
				
				if ( 'file' == $field['type'] && empty( $field['allow'] ) ) $field['allow'] = array( 'url', 'attachment' );
				if ( 'file' == $field['type'] && empty( $field['save_id'] ) )  $field['save_id']  = false;
				if ( 'multicheck' == $field['type'] ) $field['multiple'] = true;  
				
				$meta = get_post_meta( $post->ID, $field['id'], 'multicheck' != $field['type'] /* If multicheck this can be multiple values */ );
	
				echo '<li data-role="fieldcontain" class="' ,$field['size'], $field['class'], '">';
		
				
				if( $this->_meta_box['show_names'] == true ) {
					if( $field['type'] != 'radio_inline' && $field['type'] != 'radio' &&  $field['type'] != 'info' && $field['type'] != 'checkbox' && $field['type'] != 'media_picker' && $field['type'] != 'multicheck' && $field['type'] != 'wysiwyg' && $field['type'] != 'taxonomy_radio' && $field['type'] != 'title' && $field['type'] != 'images' ) {
						echo '<label for="', $field['id'], '">', $field['name'], '</label>';
					}
				}
	
						
				switch ( $field['type'] ) {
	
					case 'text':
						echo '<input data-mini="true" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" placeholder="',$field['plac'],'" />','<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'info':
						echo '<h5 class="cmb_metabox_title">', $field['name'], '</h5>';
						break;						
					case 'text_small':
						echo '<input data-mini="true" class="cmb_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
						break;
					case 'text_medium':
						echo '<input data-mini="true" class="cmb_text_medium" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
						break;
					case 'text_date':
						echo '<input data-mini="true" type="date" class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
						break;
					case 'text_date_timestamp':
						echo '<input data-mini="true" class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? date( 'm\/d\/Y', $meta ) : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
						break;
	
					case 'text_datetime_timestamp':
						echo '<input data-mini="true" class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '[date]" id="', $field['id'], '_date" value="', '' !== $meta ? date( 'm\/d\/Y', $meta ) : $field['std'], '" />';
						echo '<input data-mini="true" class="cmb_timepicker text_time" type="text" name="', $field['id'], '[time]" id="', $field['id'], '_time" value="', '' !== $meta ? date( 'h:i A', $meta ) : $field['std'], '" /><span class="cmb_metabox_description" >', $field['desc'], '</span>';
						break;
					case 'text_time':
						echo '<input data-mini="true" class="cmb_timepicker text_time" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
						break;					
					case 'text_money':
						echo '$ <input data-mini="true" class="cmb_text_money" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
						break;
					case 'colorpicker':
						$meta = '' !== $meta ? $meta : $field['std'];
						$hex_color = '(([a-fA-F0-9]){3}){1,2}$';
						if ( preg_match( '/^' . $hex_color . '/i', $meta ) ) // Value is just 123abc, so prepend #.
							$meta = '#' . $meta;
						elseif ( ! preg_match( '/^#' . $hex_color . '/i', $meta ) ) // Value doesn't match #123abc, so sanitize to just #.
							$meta = "#";
						echo '<input data-mini="true" class="cmb_colorpicker cmb_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta, '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
						break;
					case 'textarea':
						echo '<textarea data-mini="true" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10">', '' !== $meta ? $meta : $field['std'], '</textarea>','<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'textarea_small':
						echo '<textarea data-mini="true" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4">', '' !== $meta ? $meta : $field['std'], '</textarea>','<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'textarea_code':
						echo '<textarea data-mini="true" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10" class="cmb_textarea_code">', '' !== $meta ? $meta : $field['std'], '</textarea>','<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;					
					case 'select':
						if( empty( $meta ) && !empty( $field['std'] ) ) $meta = $field['std'];
						if( !empty( $field['theme'] ) ) $theme = 'data-theme="'. $field['theme'] .'"'; else $theme = '';
						echo '<select data-mini="true" '. $theme .' name="', $field['id'], '" id="', $field['id'], '">';
						foreach ($field['options'] as $option) {
							echo '<option value="', $option['value'], '"', $meta == $option['value'] ? ' selected="selected"' : '', '>', $option['name'], '</option>';
						}
						echo '</select>';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'switch':
						if( empty( $meta ) && !empty( $field['std'] ) ) $meta = $field['std'];
						echo '<select data-mini="true" data-role="slider" name="', $field['id'], '" id="', $field['id'], '">';
						foreach ($field['options'] as $option) {
							echo '<option value="', $option['value'], '"', $meta == $option['value'] ? ' selected="selected"' : '', '>', $option['name'], '</option>';
						}
						echo '</select>';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;		
					case 'slider':
						$min = $field['min'];
						$max = $field['max'];
						echo '<input data-mini="true" type="range" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" placeholder="',$field['plac'],'"  min="' . esc_attr( $min ) . '" max="' . esc_attr( $max ) . '" />','<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;			
					case 'images':
						if( empty( $meta ) && !empty( $field['std'] ) ) $meta = $field['std'];
						echo '<fieldset data-role="controlgroup" data-type="horizontal" >';
						echo '<legend>'.$field['name'].'</legend>';
						$i = 1;
						foreach ($field['options'] as $key => $option) {

							if( !isset( $option['data'] ) )
							{
								$option['data'] = '';	
							}
							
							echo '<input data-mini="true" type="radio" data-' ,$field['data'], '="' ,$option['data'], '" data-control="' ,$field['data'], '" name="', $field['id'], '" id="', $field['id'], $i, '" value="', $key, '"', $meta == $key ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '"><img src="', esc_url( $option['path'] ) , '" alt="' , $option['name'] ,'" /><span class="image-text">' , $option['name'] ,'</span></label>';
							$i++;
						}
						echo '</fieldset>';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;	
					case 'radio_inline':
						if( empty( $meta ) && !empty( $field['std'] ) ) $meta = $field['std'];
						echo '<fieldset data-role="controlgroup" data-type="horizontal" >';
						echo '<legend>'.$field['name'].'</legend>';
						$i = 1;
						foreach ($field['options'] as $option) {
							echo '<input data-mini="true" type="radio" name="', $field['id'], '" id="', $field['id'], $i, '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '">', $option['name'], '</label>';
							$i++;
						}
						echo '</fieldset>';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'radio':
						if( empty( $meta ) && !empty( $field['std'] ) ) $meta = $field['std'];
						echo '<fieldset data-role="controlgroup">';
						echo '<legend>'.$field['name'].'</legend>';
						$i = 1;
						foreach ($field['options'] as $option) {
							echo '<input data-mini="true" type="radio" name="', $field['id'], '" id="', $field['id'], $i,'" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '">', $option['name'].'</label>';
							$i++;
						}
						echo '</fieldset>';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'checkbox':
						echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
						echo '<span class="cmb_metabox_description">', $field['desc'], '</span>';
						break;
					case 'multicheck':
						echo '<fieldset data-role="controlgroup">';
						echo '<legend>'.$field['name'].'</legend>';
						$i = 1;
						foreach ( $field['options'] as $value => $name ) {
							// Append `[]` to the name to get multiple values
							// Use in_array() to check whether the current option should be checked
							echo '<input data-mini="true" type="checkbox" name="', $field['id'], '[]" id="', $field['id'], $i, '" value="', $value, '"', in_array( $value, $meta ) ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '">', $name, '</label>';	
							$i++;
						}
						echo '</fieldset>';
						echo '<span class="cmb_metabox_description">', $field['desc'], '</span>';					
						break;		
					case 'title':
						echo '<h5 class="cmb_metabox_title">', $field['name'], '</h5>';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'wysiwyg':
						echo '<fieldset data-role="controlgroup">';
						echo '<legend>'.$field['name'].'</legend>';					
						wp_editor( $meta ? $meta : $field['std'], $field['id'], isset( $field['options'] ) ? $field['options'] : array() );
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						echo '</fieldset>';
						break;
					case 'taxonomy_select':
						echo '<select name="', $field['id'], '" id="', $field['id'], '">';
						$names= wp_get_object_terms( $post->ID, $field['taxonomy'] );
						$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
						foreach ( $terms as $term ) {
							if (!is_wp_error( $names ) && !empty( $names ) && !strcmp( $term->slug, $names[0]->slug ) ) {
								echo '<option value="' . $term->slug . '" selected>' . $term->name . '</option>';
							} else {
								echo '<option value="' . $term->slug . '  ' , $meta == $term->slug ? $meta : ' ' ,'  ">' . $term->name . '</option>';
							}
						}
						echo '</select>';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'taxonomy_radio':
						$names= wp_get_object_terms( $post->ID, $field['taxonomy'] );
						$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
						echo '<fieldset data-role="controlgroup">';
						echo '<legend>'.$field['name'].'</legend>';
						foreach ( $terms as $term ) {
							if ( !is_wp_error( $names ) && !empty( $names ) && !strcmp( $term->slug, $names[0]->slug ) ) {
								echo '<input data-mini="true" type="radio" name="', $field['id'], '" value="'. $term->slug . '" checked>' . $term->name;
							} else {
								echo '<input data-mini="true" type="radio" name="', $field['id'], '" value="' . $term->slug . '  ' , $meta == $term->slug ? $meta : ' ' ,'  ">' . $term->name;
							}
						}
						echo '</fieldset>';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
					case 'taxonomy_multicheck':
						echo '<fieldset data-role="controlgroup">';
						echo '<legend>'.$field['name'].'</legend>';					
						$names = wp_get_object_terms( $post->ID, $field['taxonomy'] );
						$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
						foreach ($terms as $term) {
							echo '<input data-mini="true" type="checkbox" name="', $field['id'], '[]" id="', $field['id'], '" value="', $term->name , '"'; 
							foreach ($names as $name) {
								if ( $term->slug == $name->slug ){ echo ' checked="checked" ';};
							}
							echo' /><label>', $term->name , '</label>';
						}
						echo'</fieldset>';
						break;
					case 'file_list':
						echo '<input data-mini="true" class="cmb_upload_file" type="text" size="36" name="', $field['id'], '" value="" />';
						echo '<input data-mini="true" type="button" value="Upload File" />';
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
							$args = array(
									'post_type' => 'attachment',
									'numberposts' => null,
									'post_status' => null,
									'post_parent' => $post->ID
								);
								$attachments = get_posts($args);
								if ($attachments) {
									echo '<ul class="attach_list">';
									foreach ($attachments as $attachment) {
										echo '<li>'.wp_get_attachment_link($attachment->ID, 'thumbnail', 0, 0, 'Download');
										echo '<span>';
										echo apply_filters('the_title', '&nbsp;'.$attachment->post_title);
										echo '</span></li>';
									}
									echo '</ul>';
								}
							break;
					case 'file':
					$input_type_url = "hidden";
					if ( 'url' == $field['allow'] || ( is_array( $field['allow'] ) && in_array( 'url', $field['allow'] ) ) )
						$input_type_url="text";
					echo '<div class="cmb_upload_button_wrap"><input data-mini="true" class="cmb_upload_button" type="button" value="Upload File" /></div>';					
					echo '<input class="cmb_upload_file" data-mini="true" type="' . $input_type_url . '" size="45" id="', $field['id'], '" name="', $field['id'], '" value="', $meta, '" />';
					echo '<input class="cmb_upload_file_id" type="hidden" id="', $field['id'], '_id" name="', $field['id'], '_id" value="', get_post_meta( $post->ID, $field['id'] . "_id",true), '" />';					
					echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
					echo '<div id="', $field['id'], '_status" class="cmb_upload_status">';	
						if ( $meta != '' ) { 
							$check_image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $meta );
							if ( $check_image ) {
								echo '<div class="img_status">';
								echo '<img src="', $meta, '" alt="" />';
								echo '<p><a href="#" class="cmb_remove_file_button" rel="', $field['id'], '">Remove Image</a></p>';
								echo '</div>';
							} else {
								$parts = explode( '/', $meta );
								for( $i = 0; $i < count( $parts ); ++$i ) {
									$title = $parts[$i];
								} 
								echo 'File: <strong>', $title, '</strong>&nbsp;&nbsp;&nbsp; (<a href="', $meta, '" target="_blank" rel="external">Download</a> / <a href="#" class="cmb_remove_file_button" rel="', $field['id'], '">Remove</a>)';
							}	
						}
					echo '</div>'; 
						break;							
					case 'media_picker':
						
						wp_enqueue_script("jquery-effects-core",false,array('jquery'));	
						wp_enqueue_script("jquery-effects-slide",false,array('jquery'));	


						function get_slide_options($state,$xml_name,$slider)
						{			
							// 3d Gallery Tween //_nv_mod
							$gallery3d_tween = array(
								array( 'name' => 'linear', 'value' => 'linear' ),
								array( 'name' => 'easeInSine', 'value' => 'easeInSine' ),
								array( 'name' => 'easeOutSine', 'value' => 'easeOutSine' ),
								array( 'name' => 'easeInOutSine', 'value' => 'easeInOutSine' ),
								array( 'name' => 'easeInCubic', 'value' => 'easeInCubic' ),
								array( 'name' => 'easeOutCubic', 'value' => 'easeOutCubic' ),
								array( 'name' => 'easeInOutCubic', 'value' => 'easeInOutCubic' ),
								array( 'name' => 'easeInQuint', 'value' => 'easeInQuint' ),
								array( 'name' => 'easeOutQuint', 'value' => 'easeOutQuint' ),
								array( 'name' => 'easeInOutQuint', 'value' => 'easeInOutQuint' ),
								array( 'name' => 'easeInCirc', 'value' => 'easeInCirc' ),
								array( 'name' => 'easeOutCirc', 'value' => 'easeOutCirc' ),
								array( 'name' => 'easeInOutCirc', 'value' => 'easeInOutCirc' ),
								array( 'name' => 'easeInBack', 'value' => 'easeInBack' ),
								array( 'name' => 'easeOutBack', 'value' => 'easeOutBack' ),
								array( 'name' => 'easeInOutBack', 'value' => 'easeInOutBack' ),
								array( 'name' => 'easeInQuad', 'value' => 'easeInQuad' ),
								array( 'name' => 'easeOutQuad', 'value' => 'easeOutQuad' ),
								array( 'name' => 'easeInOutQuad', 'value' => 'easeInOutQuad' ),
								array( 'name' => 'easeInQuart', 'value' => 'easeInQuart' ),
								array( 'name' => 'easeOutQuart', 'value' => 'easeOutQuart' ),
								array( 'name' => 'easeInOutQuart', 'value' => 'easeInOutQuart' ),
								array( 'name' => 'easeInExpo', 'value' => 'easeInExpo' ),
								array( 'name' => 'easeOutExpo', 'value' => 'easeOutExpo' ),
								array( 'name' => 'easeInOutExpo', 'value' => 'easeInOutExpo' ),
								array( 'name' => 'easeInElastic', 'value' => 'easeInElastic' ),
								array( 'name' => 'easeOutElastic', 'value' => 'easeOutElastic' ),
								array( 'name' => 'easeInOutElastic', 'value' => 'easeInOutElastic' ),
								array( 'name' => 'easeInBounce', 'value' => 'easeInBounce' ),
								array( 'name' => 'easeOutBounce', 'value' => 'easeOutBounce' ),
								array( 'name' => 'easeInOutBounce', 'value' => 'easeInOutBounce' ),
							);

							// Embed Type Array //_nv_mod
							$embed_type = array(
								array( 'name' => 'Disabled', 'value' => '' ),
								array( 'name' => 'Vimeo', 'value' => 'vimeo' ),
								array( 'name' => 'YouTube', 'value' => 'youtube' ),
								array( 'name' => 'Flash', 'value' => 'swf' ),
								array( 'name' => 'Video (3d Gallery Only)', 'value' => '3dvid' ),
								array( 'name' => 'JW Player', 'value' => 'jwp' ),
								array( 'name' => 'Wistia', 'value' => 'wistia' ),
							);

							// Stage Content Array //_nv_mod
							$stage_content = array(
								array( 'name' => 'Image', 'value' => 'image' ),
								array( 'name' => 'Image / Text Overlay (Left)', 'value' => 'textimageleft' ),
								array( 'name' => 'Image / Text Overlay (Right)', 'value' => 'textimageright' ),
								array( 'name' => 'Image / Title Overlay (Hover)', 'value' => 'titleoverlay' ),
								array( 'name' => 'Image / Text Overlay (Hover)', 'value' => 'titletextoverlay' ),
								array( 'name' => 'Image / Text Overlay', 'value' => 'textoverlay' ),
								array( 'name' => 'Text', 'value' => 'textonly' ),
							);

							// Title Overlay Array //_nv_mod
							$title_overlay = array(
								array( 'name' => 'Disabled', 'value' => 'disabled' ),
								array( 'name' => 'Center Left Light', 'value' => 'center left light' ),
								array( 'name' => 'Center Right Light', 'value' => 'center right light' ),
								array( 'name' => 'Center Middle Light', 'value' => 'center middle light' ),
								array( 'name' => 'Center Left Dark', 'value' => 'center left dark' ),
								array( 'name' => 'Center Right Dark', 'value' => 'center right dark' ),
								array( 'name' => 'Center Middle Dark', 'value' => 'center middle dark' ),
								array( 'name' => 'Top Left Light', 'value' => 'top left light' ),
								array( 'name' => 'Top Right Light', 'value' => 'top right light' ),
								array( 'name' => 'Top Middle Light', 'value' => 'top middle light' ),
								array( 'name' => 'Top Left Dark', 'value' => 'top left dark' ),
								array( 'name' => 'Top Right Dark', 'value' => 'top right dark' ),
								array( 'name' => 'Top Middle Dark', 'value' => 'top middle dark' ),
								array( 'name' => 'Bottom Left Light', 'value' => 'bottom left light' ),
								array( 'name' => 'Bottom Right Light', 'value' => 'bottom right light' ),
								array( 'name' => 'Bottom Middle Light', 'value' => 'bottom middle light' ),
								array( 'name' => 'Bottom Left Dark', 'value' => 'bottom left dark' ),
								array( 'name' => 'Bottom Right Dark', 'value' => 'bottom right dark' ),
								array( 'name' => 'Bottom Middle Dark', 'value' => 'bottom middle dark' ),																				
							);

							// On / Off Array //_nv_mod
							$on_off = array(
								array( 'name' => 'On', 'value' => 'on' ),
								array( 'name' => 'Off', 'value' => 'off' ),
							);
							
							// Set field as array
							if( $state == 'saved' ) $ext = '[]'; else $ext = '';																														
							
							// Image URL
							echo '<label for="' . $xml_name['image_url'] . '">'. __('Image URL', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" type="text" name="' . $xml_name['image_url'] . $ext . '" id="' . $xml_name['image_url'] . '"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'image_url') .'"';
							
							echo ' />';	
						
							// Link URL
							echo '<label for="' . $xml_name['link_url'] . '">'. __('Link URL', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" type="text" name="' . $xml_name['link_url'] . $ext . '" id="' . $xml_name['link_url'] . $ext . '"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'link_url') .'"';
							
							echo ' />';	
							
							// Title	
							echo '<label for="' . $xml_name['title'] . '">'. __('Title', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" type="text" name="' . $xml_name['title'] . $ext . '" id="' . $xml_name['title'] . $ext . '"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'title') .'"';
							
							echo ' />';	
	
							// Description
							echo '<label for="' . $xml_name['description'] . '">'. __('Description', 'options_framework_theme') .'</label>';
							echo '<textarea data-theme="c" data-mini="true" type="text" name="' . $xml_name['description'] . $ext . '" id="' . $xml_name['description'] . $ext . '" />';
							
								if( $state == 'saved' ) echo find_xml_value($slider, 'description');
							
							echo '</textarea>';						
	
							echo '<br class="clear" />';
	
							echo '<div data-role="collapsible" data-theme="c" data-mini="true">';
							echo '<h3>Media</h3>';
							
							// Media URL
							echo '<label for="' . $xml_name['media_url'] . '">'. __('Media URL', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" type="text" name="' . $xml_name['media_url'] . $ext . '" id="' . $xml_name['media_url'] . $ext . '"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'media_url') .'"';
							
							echo ' />';		
							
							// Embed Type
							echo '<label for="' . $xml_name['embed_type'] . '">'. __('Embed Type', 'options_framework_theme') .'</label>';
							echo '<select data-theme="c" data-mini="true" id="' . $xml_name['embed_type'] . $ext . '" name="' . $xml_name['embed_type'] . $ext . '">';
					
								foreach ( $embed_type as $key => $effect )
								{
									echo '<option value="'. $effect['value'] .'"';
									
									if( $state == 'saved' )
									{
										if( find_xml_value($slider, 'embed_type') == $effect['value'] )
										{
											echo ' selected="selected" ';
										}
									}
									
									echo '>'. $effect['name'] .'</option>';
								}							
					
							echo '</select>';
	
							// Timeout
							echo '<label for="' . $xml_name['timeout'] . '">'. __('Timeout', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" placeholder="'. __('Seconds', 'options_framework_theme') .'" type="text" name="' . $xml_name['timeout'] . $ext . '" id="' . $xml_name['timeout'] . $ext . '"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'timeout') .'"';
							
							echo ' />';	
	
							// Autoplay
							echo '<label for="'. $xml_name['autoplay'] .'">'. __('Autoplay', 'options_framework_theme') .'</label>';
							echo '<select data-theme="c" data-mini="true" name="'. $xml_name['autoplay'] . $ext .'" id="' . $xml_name['autoplay'] .  $ext .'">';

								foreach ( $on_off as $key => $effect )
								{
									echo '<option value="'. $effect['value'] .'"';
									
									if( $state == 'saved' )
									{
										if( find_xml_value($slider, 'autoplay') == $effect['value'] )
										{
											echo ' selected="selected" ';
										}
									}
									
									echo '>'. $effect['name'] .'</option>';
								}

							echo '</select>';
							
							echo '</div>';
	
							echo '<div data-role="collapsible" data-theme="c" data-mini="true">';
							echo '<h3>Stage Gallery</h3>';
							
							// Stage Content
							echo '<label for="' . $xml_name['stage_content'] . '">'. __('Stage Content', 'options_framework_theme') .'</label>';
							echo '<select data-theme="c" data-mini="true" name="'. $xml_name['stage_content'] . $ext .'" id="' . $xml_name['stage_content'] .  $ext .'">';				
						
								foreach ( $stage_content as $key => $effect )
								{
									echo '<option value="'. $effect['value'] .'"';
									
									if( $state == 'saved' )
									{
										if( find_xml_value($slider, 'stage_content') == $effect['value'] )
										{
											echo ' selected="selected" ';
										}
									}
									
									echo '>'. $effect['name'] .'</option>';
								}							
						
							echo '</select>';
	
							// Title Overlay
							echo '<label for="' . $xml_name['title_overlay'] . '">'. __('Title Overlay', 'options_framework_theme') .'</label>';
							echo '<select data-theme="c" data-mini="true" name="'. $xml_name['title_overlay'] . $ext .'" id="' . $xml_name['title_overlay'] .  $ext .'">';

								foreach ( $title_overlay as $key => $effect )
								{
									echo '<option value="'. $effect['value'] .'"';
									
									if( $state == 'saved' )
									{
										if( find_xml_value($slider, 'title_overlay') == $effect['value'] )
										{
											echo ' selected="selected" ';
										}
									}
									
									echo '>'. $effect['name'] .'</option>';
								}	
        
							echo '</select>';
	
							echo '</div>';
	
							echo '<div data-role="collapsible" data-theme="c" data-mini="true">';
							echo '<h3>3d Gallery</h3>';
	
							// Pieces (3d)
							echo '<label for="' . $xml_name['gallery3d_pieces'] . '">'. __('Pieces', 'options_framework_theme') .'</label>';
							echo '<input type="range" data-mini="true" name="'. $xml_name['gallery3d_pieces'] . $ext .'" id="' . $xml_name['gallery3d_pieces'] . $ext .'" min="1" max="50"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'gallery3d_pieces') .'"';
							
							echo ' />';	
	
							// Depth Offset (3d)
							echo '<label for="' . $xml_name['gallery3d_depthoffset'] . '">'. __('Depth Offset', 'options_framework_theme') .'</label>';
							echo '<input type="range" data-mini="true" name="'. $xml_name['gallery3d_depthoffset'] . $ext .'" id="' . $xml_name['gallery3d_depthoffset'] .  $ext .'" min="-200" max="700"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'gallery3d_depthoffset') .'"';
							
							echo ' />';		
	
							// Cube Distance (3d)
							echo '<label for="' . $xml_name['gallery3d_cubedist'] . '">'. __('Cube Distance', 'options_framework_theme') .'</label>';
							echo '<input type="range" data-mini="true" name="'. $xml_name['gallery3d_cubedist'] . $ext .'" id="' . $xml_name['gallery3d_cubedist'] . $ext .'" min="5" max="50"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'gallery3d_cubedist') .'"';
							
							echo ' />';													
							
							// Tween (3d)
							echo '<label for="' . $xml_name['gallery3d_tween'] . '">'. __('Transition', 'options_framework_theme') .'</label>';
							echo '<select data-theme="c" data-mini="true" name="'. $xml_name['gallery3d_tween'] . $ext .'" id="' . $xml_name['gallery3d_tween'] .  $ext .'">';				
	
								foreach ( $gallery3d_tween as $key => $effect )
								{
									echo '<option value="'. $effect['value'] .'"';
									
									if( $state == 'saved' )
									{
										if( find_xml_value($slider, 'gallery3d_tween') == $effect['value'] )
										{
											echo ' selected="selected" ';
										}
									}
									
									echo '>'. $effect['name'] .'</option>';
								}	
	
			
							echo '</select>';
	
							// Transition Time (3d)
							echo '<label for="' . $xml_name['gallery3d_transtime'] . '">'. __('Transition Time', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" placeholder="'. __('Seconds', 'options_framework_theme') .'" type="text" name="' . $xml_name['gallery3d_transtime'] . $ext .'" id="' . $xml_name['gallery3d_transtime'] . $ext .'"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'gallery3d_transtime') .'"';
							
							echo ' />';
	
							// Delay Time (3d)
							echo '<label for="' . $xml_name['gallery3d_seconds'] . '">'. __('Delay Time', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" placeholder="'. __('Seconds', 'options_framework_theme') .'" type="text" name="' . $xml_name['gallery3d_seconds'] . $ext . '" id="' . $xml_name['gallery3d_seconds'] . $ext . '"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'gallery3d_seconds') .'"';
							
							echo ' />';												
	
							echo '</div>';
	
							echo '<div data-role="collapsible" data-theme="c" data-mini="true">';
							echo '<h3>Extras</h3>';
	
							// CSS Classes
							echo '<label for="' . $xml_name['css_classes'] . '">'. __('CSS Classes', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" placeholder="'. __('Separate by spaces', 'options_framework_theme') .'" type="text" name="' . $xml_name['css_classes'] . $ext .'" id="' . $xml_name['css_classes']  . $ext .'"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'css_classes') .'"';
							
							echo ' />';
	
							// Filter Tags
							echo '<label for="' . $xml_name['filter_tags'] . '">'. __('Filter Tags', 'options_framework_theme') .'</label>';
							echo '<input data-theme="c" data-mini="true" placeholder="'. __('Separate by commas', 'options_framework_theme') .'" type="text" name="' . $xml_name['filter_tags'] . $ext .'" id="' . $xml_name['filter_tags']  .  $ext .'"';
							
								if( $state == 'saved' ) echo ' value="'. find_xml_value($slider, 'filter_tags') .'"';
							
							echo ' />';							
							
	
							// Read More Text
							echo '<label for="'. $xml_name['readmore_link'] .'">'. __('Read More Text', 'options_framework_theme') .'</label>';
							echo '<select data-theme="c" data-mini="true" name="'. $xml_name['readmore_link'] . $ext .'" id="' . $xml_name['readmore_link'] . $ext .'" >';

								foreach ( $on_off as $key => $effect )
								{
									echo '<option value="'. $effect['value'] .'"';
									
									if( $state == 'saved' )
									{
										if( find_xml_value($slider, 'readmore_link') == $effect['value'] )
										{
											echo ' selected="selected" ';
										}
									}
									
									echo '>'. $effect['name'] .'</option>';
								}

							echo '</select>';
							echo '</div>';
						}
						
						// Slide Manager
						function nv_slide_manager($args) {
						
						extract($args);		
						
						echo '<div class="meta-input-slider">';
						echo '<div class="slide-manager" id="slide-manager">';
	
						echo '<div class="selected-slide" id="selected-slide">';
						echo '<div id="selected-slide-none"></div>';
						echo '<ul>';
						
						echo '<li id="default-slide" class="default-slide">';
						
						echo '<div class="selected-slide-wrapper">';
						echo '<img src="'. get_template_directory_uri() .'/lib/adm/images/no-image.png"/>';
						echo '<a href="#" data-role="button" data-theme="b" data-icon="gear" data-iconpos="notext" class="edit-slide">Close</a>';
						echo '<a href="#" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" title="'. __('Are you sure?', 'options_framework_theme') .'" class="remove-slide">Close</a>';
						echo '</div>';
						
						echo '<input type="hidden" class="slide-media-url" id="'. $xml_name['image'] .'" />';
						
						echo '<div data-enhance="false" class="slide-options-wrapper" data-title="'. __('Slide Options', 'options_framework_theme') .'">';
						echo '<div id="slide-options" class="slide-options">';
						
						get_slide_options('new',$xml_name,'');			

						echo '<br />';
						echo '<a href="#" rel="close" data-role="button" data-theme="b" data-iconpos="right" data-mini="true" data-icon="check">'. __('Done', 'options_framework_theme') .'</a>';
						
						echo '</div>';
						echo '</div>';
						
						echo '</li>';
						
						if( !empty($value) )
						{
							foreach ($value->childNodes as $slider)
							{				
								$thumb_src_preview = wp_get_attachment_image_src( find_xml_value($slider, 'image'), 'thumbnail' );
								$thumbnail = $thumb_src_preview[0];
								
								$image_url_preview = find_xml_value($slider, 'image_url');
								
								if( empty( $thumb_src_preview ) && !empty( $image_url_preview ) )
								{
									$thumbnail = $image_url_preview;
								}

								echo '<li class="slide-init">';
								
								echo '<div class="selected-slide-wrapper">';
								echo '<img src="' . $thumbnail . '"/>';
								echo '<a href="#" data-theme="b" data-icon="gear" class="edit-slide ui-btn ui-shadow ui-btn-corner-all ui-btn-icon-notext ui-btn-hover-b" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" title="Close"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">Close</span><span class="ui-icon ui-icon-gear ui-icon-shadow">&nbsp;</span></span></a>';
								echo '<a href="#" data-theme="a" data-icon="delete" title="'. __('Are you sure?', 'options_framework_theme') .'" class="remove-slide ui-btn ui-btn-up-a ui-shadow ui-btn-corner-all ui-btn-icon-notext" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">Close</span><span class="ui-icon ui-icon-delete ui-icon-shadow">&nbsp;</span></span></a>';
								echo '</div>';

								echo '<input type="hidden" class="slide-media-url" id="'. $xml_name['image'] .'[]" name="'. $xml_name['image'] .'[]" value="'. find_xml_value($slider, 'image') .'" />';

								echo '<div data-enhance="false" class="slide-options-wrapper" data-title="'. __('Slide Options', 'options_framework_theme') .'">';
								echo '<div id="slide-options" class="slide-options">';						
								
								get_slide_options('saved',$xml_name, $slider);
								
								echo '<br />';
								echo '<a href="#" rel="close" data-role="button" data-theme="b" data-iconpos="right" data-mini="true" data-icon="check">'. __('Done', 'options_framework_theme') .'</a>';
								echo '</div>';
								echo '</div>';
								
								echo '</li>';
							}
						}
						
						echo '</ul>';
						echo '<br class="clear" />';
						echo '</div>';
						
						}
						
						$xml_string = get_post_meta($post->ID, $field['xml'], true);
						
						if( !empty($xml_string) ){
	
							$xml_val = new DOMDocument();
							$xml_val->loadXML( $xml_string );
							$field['value'] = $xml_val->documentElement;
							
						}
		
						nv_slide_manager($field);
						
						echo '<h5 class="cmb_metabox_title">', $field['name'], '</h5>';		
						echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';		
						echo '<div class="media-image-gallery" id="media-image-gallery">';
						
						get_media_library();
						
					break;

					
					default:
						do_action('cmb_render_' . $field['type'] , $field, $meta);
				}
				
				echo '</li>';
			}
			echo '</ul>';
	}
	

	// Save data from metabox
	function save( $post_id)  {

		// verify nonce
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename(__FILE__) ) ) {
			return $post_id;
		}

		// check autosave
		if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		foreach ( $this->_meta_box['fields'] as $field ) {
			$name = $field['id'];			

			if ( ! isset( $field['multiple'] ) )
				$field['multiple'] = ( 'multicheck' == $field['type'] ) ? true : false;    
				  
			$old = get_post_meta( $post_id, $name, !$field['multiple'] /* If multicheck this can be multiple values */ );
			$new = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : null;
			
			if ( in_array( $field['type'], array( 'taxonomy_select', 'taxonomy_radio', 'taxonomy_multicheck' ) ) )  {	
				$new = wp_set_object_terms( $post_id, $new, $field['taxonomy'] );	
			}
			
			if ( ($field['type'] == 'textarea') || ($field['type'] == 'textarea_small') ) {
				$new = htmlspecialchars( $new );
			}

			if ( ($field['type'] == 'textarea_code') ) {
				$new = htmlspecialchars_decode( $new );
			}
			
			if ( $field['type'] == 'text_date_timestamp' ) {
				$new = strtotime( $new );
			}

			if ( $field['type'] == 'text_datetime_timestamp' ) {
				$string = $new['date'] . ' ' . $new['time'];
				$new = strtotime( $string );
			}
			
	
			$new = apply_filters('cmb_validate_' . $field['type'], $new, $post_id, $field);			
			
			// validate meta value
			if ( isset( $field['validate_func']) ) {
				$ok = call_user_func( array( 'cmb_Meta_Box_Validate', $field['validate_func']), $new );
				if ( $ok === false ) { // pass away when meta value is invalid
					continue;
				}
			} elseif ( $field['multiple'] ) {
				delete_post_meta( $post_id, $name );	
				if ( !empty( $new ) ) {
					foreach ( $new as $add_new ) {
						add_post_meta( $post_id, $name, $add_new, false );
					}
				}			
			} elseif ( 'media_picker' == $field['type'] ) {
				
			
				if( isset($_POST[$field['xml_name']['image']]))
				{
					$num = sizeof($_POST[$field['xml_name']['image']]) - 1;
				} 
				else
				{
					$num = -1;
				}
				
				
				$old = get_post_meta( $post_id,$field['xml'],true );
				
				$slider_xml = "<slide-set>";
				
				for( $i=0; $i<=$num; $i++ )
				{
					$slider_xml = $slider_xml. "<slide>";

					$image = stripslashes($_POST[$field['xml_name']['image']][$i]);
					$slider_xml = $slider_xml. create_xml_tag('image',$image);					

					$image_url = stripslashes($_POST[$field['xml_name']['image_url']][$i]);
					$slider_xml = $slider_xml. create_xml_tag('image_url',$image_url);
					
					$link_url = stripslashes($_POST[$field['xml_name']['link_url']][$i]);
					$slider_xml = $slider_xml. create_xml_tag('link_url',$link_url);
					
					$title = stripslashes(htmlspecialchars($_POST[$field['xml_name']['title']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('title',$title);

					$description = stripslashes(htmlspecialchars($_POST[$field['xml_name']['description']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('description',$description);

					$media_url = stripslashes(htmlspecialchars($_POST[$field['xml_name']['media_url']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('media_url',$media_url);	

					$embed_type = stripslashes(htmlspecialchars($_POST[$field['xml_name']['embed_type']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('embed_type',$embed_type);	

					$timeout = stripslashes(htmlspecialchars($_POST[$field['xml_name']['timeout']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('timeout',$timeout);

					$autoplay = stripslashes(htmlspecialchars($_POST[$field['xml_name']['autoplay']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('autoplay',$autoplay);

					$stage_content = stripslashes(htmlspecialchars($_POST[$field['xml_name']['stage_content']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('stage_content',$stage_content);	

					$title_overlay = stripslashes(htmlspecialchars($_POST[$field['xml_name']['title_overlay']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('title_overlay',$title_overlay);

					$gallery3d_pieces = stripslashes(htmlspecialchars($_POST[$field['xml_name']['gallery3d_pieces']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('gallery3d_pieces',$gallery3d_pieces);

					$gallery3d_depthoffset = stripslashes(htmlspecialchars($_POST[$field['xml_name']['gallery3d_depthoffset']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('gallery3d_depthoffset',$gallery3d_depthoffset);

					$gallery3d_cubedist = stripslashes(htmlspecialchars($_POST[$field['xml_name']['gallery3d_cubedist']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('gallery3d_cubedist',$gallery3d_cubedist);

					$gallery3d_tween = stripslashes(htmlspecialchars($_POST[$field['xml_name']['gallery3d_tween']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('gallery3d_tween',$gallery3d_tween);	

					$gallery3d_transtime = stripslashes(htmlspecialchars($_POST[$field['xml_name']['gallery3d_transtime']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('gallery3d_transtime',$gallery3d_transtime);

					$gallery3d_seconds = stripslashes(htmlspecialchars($_POST[$field['xml_name']['gallery3d_seconds']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('gallery3d_seconds',$gallery3d_seconds);

					$css_classes = stripslashes(htmlspecialchars($_POST[$field['xml_name']['css_classes']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('css_classes',$css_classes);

					$filter_tags = stripslashes(htmlspecialchars($_POST[$field['xml_name']['filter_tags']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('filter_tags',$filter_tags);
					
					$readmore_link = stripslashes(htmlspecialchars($_POST[$field['xml_name']['readmore_link']][$i]));
					$slider_xml = $slider_xml. create_xml_tag('readmore_link',$readmore_link);																														
					
					$slider_xml = $slider_xml . "</slide>";
				}
				
				$new = $slider_xml . "</slide-set>";
				
				
				
				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $field['xml'], $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $field['xml'], $old );
				}			
				
			} elseif ( '' !== $new && $new != $old  ) {
				update_post_meta( $post_id, $name, $new );
			} elseif ( '' == $new ) {
				delete_post_meta( $post_id, $name );
			}
	

			if ( 'file' == $field['type'] ) {
				$name = $field['id'] . "_id";
				$old = get_post_meta( $post_id, $name, !$field['multiple'] /* If multicheck this can be multiple values */ );
				if ( isset( $field['save_id'] ) && $field['save_id'] ) {
					$new = isset( $_POST[$name] ) ? $_POST[$name] : null;
				} else {
					$new = "";
				}

				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $name, $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $name, $old );
				}
			}			
		}
	}
}

/**
 * Adding scripts and styles
 */
 
function cmb_scripts( $hook ) {
  	if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
		
		global $post_type;
		$meta_boxes = array();
		$meta_boxes = apply_filters ( 'cmb_meta_boxes' , $meta_boxes );
		$count = 0;
		$menu = $meta_box_ids = '';
			
		foreach ( $meta_boxes as $meta_box )
		{
			foreach ($meta_box['pages'] as $type )
			{
				if( $type == $post_type )
				{
					// Heading for Navigation
					$menu .= '<a id="'.  $meta_box['id'] . '-tab" class="nav-tab" title="' . esc_attr( $meta_box['title'] ) . '" href="' . esc_attr( '#'.  $meta_box['id'] ) . '">' . esc_html( $meta_box['title'] ) . '</a>';
					// Collect Metabox ID's
					$meta_box_ids .='#'.$meta_box['id'].',';
					// Count Meta Boxes for Post Type
					$count++;
				}
				
			}
		}
			
		$meta_box_ids = rtrim($meta_box_ids,',');
	
		$meta_box_array = array(
			'meta_box_ids' => $meta_box_ids,
			'meta_box_menu' => $menu,
			'meta_box_count' => $count,
		);

		wp_register_script( 'cmb-timepicker',  get_template_directory_uri() . '/lib/adm/js/jquery.timePicker.min.js' );
		wp_register_script( 'cmb-scripts',  get_template_directory_uri() . '/lib/adm/js/cmb.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'media-upload', 'thickbox', 'farbtastic' ) );
		wp_localize_script('cmb-scripts', 'META_BOX', $meta_box_array );
		wp_enqueue_script( 'cmb-timepicker' );
		wp_enqueue_script( 'cmb-scripts' );
		wp_enqueue_style( 'cmb-styles' );

		wp_enqueue_script('jquery-ui-core');
		wp_deregister_script('options-custom');	
		wp_register_script('options-custom', get_template_directory_uri() . '/lib/adm/js/options-custom.js', array('jquery'));
		wp_enqueue_script('options-custom');
  	}
}
add_action( 'admin_enqueue_scripts', 'cmb_scripts', 10 );



function cmb_editor_footer_scripts() { ?>
	<?php
	if ( isset( $_GET['cmb_force_send'] ) && 'true' == $_GET['cmb_force_send'] ) { 
		$label = $_GET['cmb_send_label']; 
		if ( empty( $label ) ) $label="Select File";
		?>	
		<script type="text/javascript">
		jQuery(function($) {
			$('td.savesend input').val('<?php echo $label; ?>');
		});
		</script>
		<?php 
	}
}
add_action( 'admin_print_footer_scripts', 'cmb_editor_footer_scripts', 99 );


function add_scripts() { //_nv_mod
		
    global $post;
    if (is_admin()) {
        switch (basename($_SERVER['SCRIPT_FILENAME'])) {
            case "post.php":
            case "post-new.php":
            case "page.php":
            case "page-new":
                break;
            default:
                return;
        }
    } 
	
	global $post_type;
	if( 'page' == $post_type || 'portfolio' == $post_type || 'post' == $post_type )
	{	
		//wp_deregister_script( 'jquery-ui-widget' );
		wp_enqueue_style('nv_theme_settings_css', get_template_directory_uri() . '/lib/adm/css/nv-theme-settings.css');
		wp_enqueue_style('jquery-mobile-css', 'https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css');
		wp_enqueue_script( 'jquery-mobile', get_template_directory_uri() . '/lib/adm/js/jquery.mobile.custom-1.2.1.min.js', array('jquery') );
	}
	elseif( 'slide-sets' == $post_type )
	{
		wp_enqueue_style('nv_theme_settings_css', get_template_directory_uri() . '/lib/adm/css/nv-theme-settings.css');
		wp_enqueue_style('jquery-mobile-css', 'https://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css');
		wp_enqueue_script( 'jquery-mobile', get_template_directory_uri() . '/lib/adm/js/jquery.mobile.custom.min.js', array('jquery') );
		wp_register_script( 'themeva-slide-manager',  get_template_directory_uri() . '/lib/adm/js/slide-manager.js' );
		wp_enqueue_script( 'themeva-slide-manager' );
		wp_register_script( 'simple-dialog',  get_template_directory_uri() . '/lib/adm/js/simpledialog.js' );
		wp_enqueue_script( 'simple-dialog' );
	}
}

add_action( 'admin_head','add_scripts', 9999 ); //_nv_mod


// Force 'Insert into Post' button from Media Library 
add_filter( 'get_media_item_args', 'cmb_force_send' );
function cmb_force_send( $args ) {
		
	// if the Gallery tab is opened from a custom meta box field, add Insert Into Post button	
	if ( isset( $_GET['cmb_force_send'] ) && 'true' == $_GET['cmb_force_send'] )
		$args['send'] = true;
	
	// if the From Computer tab is opened AT ALL, add Insert Into Post button after an image is uploaded	
	if ( isset( $_POST['attachment_id'] ) && '' != $_POST["attachment_id"] ) {
		
		$args['send'] = true;		

		// TO DO: Are there any conditions in which we don't want the Insert Into Post 
		// button added? For example, if a post type supports thumbnails, does not support
		// the editor, and does not have any cmb file inputs? If so, here's the first
		// bits of code needed to check all that.
		// $attachment_ancestors = get_post_ancestors( $_POST["attachment_id"] );
		// $attachment_parent_post_type = get_post_type( $attachment_ancestors[0] );
		// $post_type_object = get_post_type_object( $attachment_parent_post_type );
	}		
	
	// change the label of the button on the From Computer tab
	if ( isset( $_POST['attachment_id'] ) && '' != $_POST["attachment_id"] ) {

		echo '
			<script type="text/javascript">
				function cmbGetParameterByNameInline(name) {
					name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
					var regexS = "[\\?&]" + name + "=([^&#]*)";
					var regex = new RegExp(regexS);
					var results = regex.exec(window.location.href);
					if(results == null)
						return "";
					else
						return decodeURIComponent(results[1].replace(/\+/g, " "));
				}
							
				jQuery(function($) {
					if (cmbGetParameterByNameInline("cmb_force_send")=="true") {
						var cmb_send_label = cmbGetParameterByNameInline("cmb_send_label");
						$("td.savesend input").val(cmb_send_label);
					}
				});
			</script>
		';
	}
	 
    return $args;

}

// End. That's it, folks! //