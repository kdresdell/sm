<?php
$output = $el_class = $bg_image = $bg_color = $bg_image_repeat = $font_color = $padding = $margin_bottom = '';
extract(shortcode_atts(array(
    'el_class'        => '',
    'bg_image'        => '',
    'bg_color'        => '',
    'bg_image_repeat' => '',
    'font_color'      => '',
    'padding'         => '',
    'margin_bottom'   => '',
	'wide_row' 		  => '',
	'parallax' 		  => ''
), $atts));

wp_enqueue_style( 'js_composer_front' );
wp_enqueue_script( 'wpb_composer_front_js' );
wp_enqueue_style('js_composer_custom_css');

$background_style = '';

$el_class = $this->getExtraClass($el_class);

if (strpos( $el_class, 'wide-row' ) !== false) {
    $wide_row = 'yes';
}

$css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_row '.get_row_css_class().$el_class, $this->settings['base']);


// Wide Row
if( $wide_row == 'yes' )
{
	$style = $this->buildStyle( '', '', '', $font_color, $padding, $margin_bottom);
	$background_style = $this->buildStyle($bg_image, $bg_color, $bg_image_repeat, '', '', '' );
	$css_class .= ' wide-row';
}
else
{
	$style = $this->buildStyle( $bg_image, $bg_color, $bg_image_repeat, $font_color, $padding, $margin_bottom);
}

// Parallax
if( !empty( $parallax ) ) 
{
	wp_deregister_script('jquery-parallax');
	wp_register_script('jquery-parallax',get_template_directory_uri().'/js/jquery.parallax-1.1.3.js',false,array('jquery'),true);
	wp_enqueue_script('jquery-parallax');
	
	if( empty( $wide_row ) )
	{
		$css_class .= ' custom-row parallax';
	}
}


$output .= '<div class="'.$css_class.'"'.$style.'>';

if( $wide_row == 'yes' )
{
	$output .= '<div class="wide-row-inner'. ( !empty( $parallax ) ? ' parallax' : '' ) .'" '. $background_style .'></div>';
}
	
$output .= wpb_js_remove_wpautop($content);
$output .= '</div>'.$this->endBlockComment('row');

echo $output;