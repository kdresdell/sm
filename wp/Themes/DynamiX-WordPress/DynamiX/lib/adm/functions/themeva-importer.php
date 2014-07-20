<?php
function themeva_options_import_demo()
{
	// Check AJAX Referer
    check_ajax_referer('_theme_options', '_ajax_nonce');

	if (!class_exists('WP_Import')) {
		if (!defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true);
		$wp_import = get_template_directory() . '/lib/adm/functions/wordpress-importer.php';
		require_once($wp_import);
	}
	
	class themevaImport extends WP_Import {}

	//instantiate prime-importer
	$wp_import = new themevaImport();
	ob_start();


	//load demo.xml
	$wp_import->fetch_attachments = true;
	$xml_path = get_template_directory() . '/demo/demo.xml';

	$wp_import->import($xml_path);

	//load menus
	// automatically create menus and set their locations
	// add all pages to the Primary Navigation
	//Parameterize with the menu to look for when assigning the theme location a menu
	$primary_nav = wp_get_nav_menu_object('Main Menu');
    
	$primary_nav_term_id = (int)$primary_nav->term_id;
    
	//actually set the menu values
	$locations = get_theme_mod('nav_menu_locations');
	$locations['mainnav'] = $primary_nav_term_id; //$foo is term_id of menu

	set_theme_mod('nav_menu_locations', $locations);

	//setup Reading Settings
	update_option('show_on_front', 'page');
	$home_page = get_page_by_title('Home');
	if ($home_page->post_type == 'page') update_option('page_on_front', $home_page->ID);

	ob_end_clean();

	_e( 'Complete! The Demo content has been imported, <strong>please wait for page reload.</strong>', 'themeva-admin' );
	die();
}

add_action( 'wp_ajax_themeva_options_import_demo', 'themeva_options_import_demo' );