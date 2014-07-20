<?php if (!is_admin()) if(!session_id()) session_start();

/* ------------------------------------
:: INITIATE JQUERY / STYLING
------------------------------------ */

	function init_nvscripts()
	{
		if ( !is_admin() )
		{
			wp_register_style('northvantage-style', get_bloginfo('stylesheet_url'),false,null);
			wp_enqueue_style('northvantage-style');
			
			if( of_get_option('enable_responsive') != 'disable' ) :
			
				wp_register_style('northvantage-responsive', get_template_directory_uri().'/stylesheets/responsive.css',false,null);
				wp_enqueue_style('northvantage-responsive');
			
			endif;
		
			wp_enqueue_script('jquery-ui-core',false,array('jquery'));
			wp_enqueue_script('jquery-effects-core',false,array('jquery'));
	
			wp_deregister_script('jquery-fancybox');	
			wp_register_script('jquery-fancybox', get_template_directory_uri().'/js/jquery.fancybox.min.js',false,array('jquery'),true);
			wp_enqueue_script('jquery-fancybox');
	
			$template_array = array(
				'template_url' 			 	 => get_template_directory_uri(),
				'branding_2x'  			 	 => of_get_option('branding_2x'),
				'branding_2x_dimensions' 	 => of_get_option('branding_2x_dimensions'),
				'branding_sec_2x' 		 	 => of_get_option('branding_sec_2x'),
				'branding_sec_2x_dimensions' => of_get_option('branding_sec_2x_dimensions'),			
			);
	
			wp_deregister_script('nv-script');	
			wp_register_script( 'nv-script', get_template_directory_uri().'/js/nv-script.pack.js',false,array('jquery'), true );
			wp_localize_script('nv-script', 'NV_SCRIPT', $template_array );
			wp_enqueue_script('nv-script');

			// Remove Visual Composer Style
			wp_dequeue_style( 'bootstrap' );
			wp_deregister_style( 'js_composer_front' );
			wp_deregister_style( 'ui-custom-theme' );
			wp_dequeue_style( 'ui-custom-theme' );
		
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			{
				wp_enqueue_script( 'comment-reply' );
			}
		}
	}    

	add_action('wp_enqueue_scripts', 'init_nvscripts',101);

	// remove script version
	function _remove_script_version( $src )
	{ 
		$parts = explode( '?', $src );
		return $parts[0];
	}
	
	//add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
	//add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );
	
	
	// add defer / async
	function strposa($haystack, $needles=array(), $offset=0)
	{
		$chr = array();
		foreach($needles as $needle)
		{
			$res = strpos($haystack, $needle, $offset);
			if ($res !== false) $chr[$needle] = $res;
		}
		if(empty($chr)) return false;
		return min($chr);
	}
	

	function add_defer_to_scripts( $url )
	{
		$scripts = array(
				'stage.slider.min',
				'group.slider.min',
				'jquery.fancybox.min',
				'content.animator.min',
				'jwplayer',
				'reflection',
				'jw-player.init.min',
				'jquery.tooltips',
				'contact.form',
				'jquery.themepunch.revolution.min.js',
				'jquery.themepunch.plugins.min.js'
			);
			
		if ( FALSE === strposa( $url, $scripts, 1) ) {
			return $url;
		} 
		
		return "$url' defer='defer' async='async";
	}
	
	//add_filter( 'clean_url', 'add_defer_to_scripts', 11, 1 );


/* ------------------------------------
:: DEFINE DIRECTORIES
------------------------------------ */

	define( 'NV_DIR', get_template_directory() );
	define( 'NV_FILES', NV_DIR . '/lib' );

/* ------------------------------------
:: THEME OPTIONS
------------------------------------ */

	require_once dirname( __FILE__ ) . '/lib/adm/functions/core.php';
	
	if ( !function_exists( 'optionsframework_init' ) )
	{
		define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/lib/adm/' );
		require_once dirname( __FILE__ ) . '/lib/adm/inc/options-framework.php';
	}
	
	require_once dirname( __FILE__ ) . '/lib/adm/inc/options-meta.php';
	require_once dirname( __FILE__ ) . '/lib/adm/inc/options-backup.php';
	
	require_once NV_FILES .'/inc/sub-functions.php';
	require_once NV_DIR .'/custom-functions.php';
	
	if( get_option('advancedexcerpt_no_custom') == '' )
	{
		update_option('advancedexcerpt_no_custom', 0 );
	}
	
	require_once NV_FILES .'/adm/inc/custom-widgets.php';	

/* ------------------------------------
:: ARCHIVE EXCERPT
------------------------------------ */

	function new_excerpt_more($more)
	{
		global $post;
		return '... <a href="'. get_permalink($post->ID) . '">' . __( 'Read More', 'themeva' )  . '</a>';
	}
	add_filter('excerpt_more', 'new_excerpt_more');

/* ------------------------------------
:: FRIENDLY PAGE TITLE
------------------------------------ */

	function themeva_wp_title( $title, $sep ) {
		global $paged, $page;
	
		if ( is_feed() )
			return $title;
	
		// Add the site name.
		$title .= get_bloginfo( 'name' );
	
		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";
	
		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 )
			$title = "$title $sep " . sprintf( __( 'Page %s', 'themeva' ), max( $paged, $page ) );
	
		return $title;
	}
	add_filter( 'wp_title', 'themeva_wp_title', 10, 2 );

/* ------------------------------------
:: MENU FILTER
------------------------------------ */

	function DYN_menupages()
	{
		add_filter('wp_list_pages', 'DYN_page_lists');
		$menupageslist = wp_list_pages('echo=0&title_li=&');
	
		remove_filter('wp_list_pages', 'DYN_page_lists'); // Remove filter to not affect all calls to wp_list_pages
		return $menupageslist;
	}

/* ------------------------------------
:: MENU DESCRIPTIONS
------------------------------------ */

	function DYN_page_lists($output)
	{	
		global $wpdb;
	
		$get_MenuDesc = mysql_query("SELECT p.ID, p.post_title, p.guid, p.post_parent, pm.meta_value FROM " . $wpdb->posts . " AS p LEFT JOIN (SELECT post_id, meta_value FROM " . $wpdb->postmeta . " AS ipm WHERE meta_key = 'pgopts') AS pm ON p.ID = pm.post_id WHERE p.post_type = 'page' AND p.post_status = 'publish' ORDER BY p.menu_order ASC");
		
		while ($row = mysql_fetch_assoc($get_MenuDesc))
		{
			extract($row);
			$post_title = wptexturize($post_title);
			$data = maybe_unserialize(get_post_meta( $ID, 'pgopts', true ));		
	
			$menudesc = ( !empty( $data["menudesc"] ) ) ? $data["menudesc"] : '';		
				
			if( $menudesc != "" && of_get_option('menu_subtitles') != 'disable' )
			{
				$output = str_replace('>' . $post_title .'</a>' , '>' . $post_title . '</a><span class="menudesc">' . $data["menudesc"] . '</span>', $output);
			}
				
		}	
	
		$parts = preg_split('/(<ul|<li|<\/ul>)/',$output,null,PREG_SPLIT_DELIM_CAPTURE);
		$newmenu = '';
		$level = 0;
		
		foreach ($parts as $part)
		{
			if ('<ul' == $part) { ++$level; }
			if ('</ul>' == $part) { --$level; }
			$newmenu .= $part;
		}
	
		return $newmenu;
	}
	
/* ------------------------------------
:: MENU DROPDOWN ICON
------------------------------------ */	

	function themeva_set_dropdown( $sorted_menu_items, $args )
	{
		$last_top = 0;
		foreach ( $sorted_menu_items as $key => $obj )
		{
			// it is a top lv item?
			if ( 0 == $obj->menu_item_parent )
			{
				// set the key of the parent
				$last_top = $key;
			}
			else
			{
				$sorted_menu_items[$last_top]->classes['dropdown'] = 'hasdropmenu';
			}
		}
		
		return $sorted_menu_items;
	}
	add_filter( 'wp_nav_menu_objects', 'themeva_set_dropdown', 10, 2 );	


/* ------------------------------------
:: SIDEBARS
------------------------------------ */

	global $wpdb;

	$sidebar_default 	= ( get_option( 'sidebars_num' ) != '' ) ? get_option( 'sidebars_num' ) : '2'; // upgrade purposes 
	$sidebars_num		= ( of_get_option('sidebars_num') !='' ) ? of_get_option('sidebars_num') : $sidebar_default;
	$get_droppanel_num 	= ( of_get_option('droppanel_columns_num') !='' ) ? of_get_option('droppanel_columns_num') : '4'; // If not set, default to 4 columns
	$get_footer_num 	= ( of_get_option('footer_columns_num') !='' ) ? of_get_option('footer_columns_num') : '4'; // If not set, default to 4 columns
	
	// Sidebar Columns
	$i=1;
    while( $i <= $sidebars_num )
	{
		if ( function_exists('register_sidebar') )
		{
			register_sidebar(
			array(
			'name'=>'sidebar'.$i,
			'id'=>'sidebar'.$i,
			'before_title' => '<h3>',
			'after_title' => '</h3>',
			));
		}
		$i++;
	}

	// Drop Panel Columns
	$i=1;
    while( $i <= $get_droppanel_num )
	{
		if ( function_exists('register_sidebar'))
		{
			register_sidebar(array(
			'name'=>'Drop Panel Column '.$i,
			'id'=>'droppanel'.$i,				
			'description' => 'Widgets in this area will be shown in Drop Panel column '.$i.'.',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
			));
		}
		$i++;
	}
	
	
	// Footer Columns
	$i=1;
    while( $i <= $get_footer_num )
	{
		if ( function_exists('register_sidebar'))
		{
			register_sidebar(
			array(
			'name'=>'Footer Column '.$i,
			'id'=>'footer'.$i,
			'description' => 'Widgets in this area will be shown in Footer column '.$i.'.',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
			));
		}
		$i++;
	}	


/* ------------------------------------
:: REGISTER POST TYPES
------------------------------------ */

	require_once NV_FILES .'/adm/inc/register-post-types.php';

/* ------------------------------------
:: BREADCRUMBS
------------------------------------ */

	require_once NV_FILES .'/inc/breadcrumbs.php';

/* ------------------------------------
:: PAGINATION
------------------------------------ */

	function pagination( $query, $baseURL ) {
		
		$page = $query->query_vars["paged"];
		if ( empty($page) ) $page = 1;
		$qs = $_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : "";
		// Only necessary if there's more posts than posts-per-page
		if ( $query->found_posts > $query->query_vars["posts_per_page"] ) {
			echo '<ul class="page_nav">';
			// Previous link?
			if ( $page > 1 ) {
				if(get_option("permalink_structure")) {
					echo '<li class="page-numbers previous"><a href="'.$baseURL.'page/'.($page-1).'/'.$qs.'">&laquo;</a></li>';
				} else {
					echo '<li class="page-numbers previous"><a href="'.$baseURL.'&amp;paged='.($page-1).'">&laquo;</a></li>';
				}			
				
			}
			// Loop through pages
			for ( $i=1; $i <= $query->max_num_pages; $i++ ) {
				// Current page or linked page?
				if ( $i == $page ) {
					echo '<li class="page-numbers active">'.$i.'</li>';
				} else {
				if(get_option("permalink_structure")) {
					echo '<li class="page-numbers"><a href="'.$baseURL.'page/'.$i.'/'.$qs.'">'.$i.'</a></li>';
				} else {
					echo '<li class="page-numbers"><a href="'.$baseURL.'&amp;paged='.$i.'">'.$i.'</a></li>';
				}
				}
			}
			// Next link?
			if ( $page < $query->max_num_pages ) {
				if(get_option("permalink_structure")) {
					echo '<li class="page-numbers next"><a href="'.$baseURL.'page/'.($page+1).'/'.$qs.'">&raquo;</a></li>';
				} else {
					echo '<li class="page-numbers next"><a href="'.$baseURL.'&amp;paged='.($page+1).'">&raquo;</a></li>';
				}				
			}
			echo '</ul>';
		}
	
	}


/* ------------------------------------
:: THUMBNAIL + CUSTOM BACKGROUNDS
------------------------------------ */

	add_theme_support( 'post-thumbnails' ); 

/* ------------------------------------
:: POST FORMATS SUPPORT
------------------------------------ */

	add_theme_support( 'post-formats', array( 'aside', 'link', 'status', 'quote', 'image' , 'video', 'audio', 'gallery' ));

/* ------------------------------------
:: AUTOMATIC FEED LINKS
------------------------------------ */

	add_theme_support( 'automatic-feed-links' );

/* ------------------------------------
:: DEFINE CONTENT WIDTH
------------------------------------ */

	$NV_layout = ( !empty( $NV_layout ) ) ? $NV_layout : '';

	if( $NV_layout != 'layout_one' )
	{
		if ( ! isset( $content_width ) ) $content_width = 611;
	}
	else
	{
		if ( ! isset( $content_width ) ) $content_width = 980;
	}
	
/* ------------------------------------
:: WP CUSTOM MENU SHORTCODE
------------------------------------ */

	function list_menu($atts, $content = null) {
		extract(shortcode_atts(array(  
			'menu'            => '', 
			'container'       => 'div', 
			'container_class' => '', 
			'container_id'    => '', 
			'menu_class'      => 'menu', 
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => 'wp_page_menu',
			'before'          => '',
			'after'           => '',
			'link_before'     => '',
			'link_after'      => '',
			'depth'           => 0,
			'walker'          => '',
			'theme_location'  => ''), 
			$atts));
	 
	 
		return wp_nav_menu( array( 
			'menu'            => $menu, 
			'container'       => $container, 
			'container_class' => $container_class, 
			'container_id'    => $container_id, 
			'menu_class'      => $menu_class, 
			'menu_id'         => $menu_id,
			'echo'            => false,
			'fallback_cb'     => $fallback_cb,
			'before'          => $before,
			'after'           => $after,
			'link_before'     => $link_before,
			'link_after'      => $link_after,
			'depth'           => $depth,
			'walker'          => $walker,
			'theme_location'  => $theme_location));
	}
	
	add_shortcode("listmenu", "list_menu"); //Create the shortcode



/* ------------------------------------
:: WP CUSTOM MENU SUPPORT
------------------------------------ */

	add_theme_support( 'nav-menus' );
		register_nav_menus( array(
			'mainnav' => __( 'Main Navigation', 'themeva' ),
			'mobilenav' => __( 'Mobile Navigation', 'themeva' ),
		) );
	

	class Walker_Nav_Menu_Dropdown extends Walker_Nav_Menu {
		var $to_depth = -1;
	
		function start_lvl( &$output, $depth=0,  $args=array() )
		{
		  $output .= '</option>';
		}
	
	
		function end_lvl(&$output, $depth=0,  $args=array() )
		{
		  $indent = str_repeat("\t", $depth); // don't output children closing tag
		}
	
	
		function start_el( &$output, $item, $depth=0, $args=array(), $current_object_id=0 )
		{
			$indent = ( $depth ) ? str_repeat( "&nbsp;", $depth * 4 ) : '';
	
			$class_names = $value = '';
	
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;
		
	
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names = ' class="' . esc_attr( $class_names ) . '"';
	
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
	
			$value = ' value="'. $item->url .'"';
			$output .= '<option'.$id.$value.$class_names.'>';
	
	
			$item_output = $args->before;
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		
			$output .= $indent.$item_output;
	
		}
	
	
		function end_el( &$output, $item, $depth=0,  $args=array() )
		{
			$output = str_replace('id="menu-', 'id="select-menu-', $output);
			if(substr($output, -9) != '</option>')
				$output .= "</option>"; // replace closing </li> with the option tag
		}
	}


	class dyn_walker extends Walker_Nav_Menu {
			
		function start_lvl( &$output, $depth=0,  $args=array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "\n$indent<ul class=\"sub-menu skinset-menu nv-skin\">\n";
		}
	
		
		function start_el( &$output, $item, $depth=0, $args=array(), $current_object_id=0 ) {
			global $wp_query;
	
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
			$class_names = $value = '';
	
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
	
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = ' class="' . esc_attr( $class_names ) . '"';
	
			if($depth=="0") {
			$output .= $indent . '<li class="menubreak"></li><li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
			} else {
			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';		
			}
			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
	
			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= '<span class="menutitle">'.$args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after.'</span>';
			if( $item->attr_title && of_get_option('menu_subtitles') != 'disable' ) {
			$item_output .= '<span class="menudesc">' . $item->attr_title  . '</span>';
			}
			$item_output .= '</a>';
			if( $item->description && of_get_option('wpcustommdesc_enable')=='enable' ) {
			$item_output .= '<div class="menudesc">' . do_shortcode($item->description) . '</div>';
			}		
			$item_output .= $args->after;
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	} 


/* ------------------------------------
:: MULTI-SITE PATH
------------------------------------ */

	function dyn_getimagepath($img_src)
	{
		global $blog_id;
		if (isset($blog_id) && $blog_id > 0) {
			$imageParts = explode('/files/', $img_src);
			if (isset($imageParts[1])) {
				$img_src = '/wp-content/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
			}
		}
		return $img_src;
	}


/* ------------------------------------
:: BBPRESS
------------------------------------ */

	add_theme_support( 'bbpress' );
	
	if (class_exists('bbPress'))
	{
		wp_enqueue_style( 'bbp-nv-bbpress', get_stylesheet_directory_uri() . '/stylesheets/bbpress.css',false,null);
	}
	
	function filter_bbPress_breadcrumb_separator()
	{
	//$sep = ' » ';
	$sep = ' <span class="subbreak">/</span> ';
	return $sep;
	}
	
	add_filter('bbp_breadcrumb_separator', 'filter_bbPress_breadcrumb_separator');


/* ------------------------------------
:: BUDDYPRESS
------------------------------------ */

	if ( function_exists('bp_is_blog_page') && !is_admin())
	{
		wp_enqueue_style( 'bp-legacy-css', get_stylesheet_directory_uri() . '/stylesheets/style-buddypress.css',false,null);
	}

/* ------------------------------------
:: WP E-COMMERCE
------------------------------------ */

	function wpsc_product_rater_nv()
	{
		global $wpsc_query;
		$product_id = get_the_ID();
		$output = '';
		if ( get_option( 'product_ratings' ) == 1 )
		{
			$output .= "<div class='product_footer'>";
			$output .= "<div class='product_average_vote'>";
			$output .= "<strong>" . __( 'Avg. Customer Rating', 'wpsc' ) . ":</strong>";
			$output .= wpsc_product_existing_rating_nv( $product_id );
			$output .= "</div>";
			$output .= "<div class='product_user_vote'>";
			$output .= "<strong><span id='rating_" . $product_id . "_text'>" . __( 'Your Rating', 'wpsc' ) . ":</span>";
			$output .= "<span class='rating_saved' id='saved_" . $product_id . "_text'> " . __( 'Saved', 'wpsc' ) . "</span>";
			$output .= "</strong>";
			$output .= wpsc_product_new_rating( $product_id );
			$output .= "</div>";
			$output .= "</div>";
		}
		return $output;
	}
	
	function wpsc_product_existing_rating_nv( $product_id )
	{
		global $wpdb;
		$get_average = $wpdb->get_results( "SELECT AVG(`rated`) AS `average`, COUNT(*) AS `count` FROM `" . WPSC_TABLE_PRODUCT_RATING . "` WHERE `productid`='" . $product_id . "'", ARRAY_A );
		$average = floor( $get_average[0]['average'] );
		$count = $get_average[0]['count'];
		$output  = "  <span class='votetext'>";
		for ( $l = 1; $l <= $average; ++$l )
		{
			$output .= "<img class='goldstar' src='" .get_template_directory_uri() . "/images/gold-star.png' alt='$l' title='$l' />";
		}
		$remainder = 5 - $average;
		for ( $l = 1; $l <= $remainder; ++$l )
		{
			$output .= "<img class='goldstar' src='" .get_template_directory_uri() . "/images/grey-star.png' alt='$l' title='$l' />";
		}
	
		$output .= "</span> \r\n";
		return $output;
	}
	
	function remove_wpsc_style()
	{
		global $wp_styles;
		wp_deregister_style( 'wpsc-gold-cart-grid-view' );
		wp_deregister_style( 'wpsc-gold-cart' );
	}
	
	add_action('wp_print_styles', 'remove_wpsc_style', 100);

/* ------------------------------------
:: WOOCOMMERCE
------------------------------------ */

	add_theme_support( 'woocommerce' );	

	add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
	
	function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;
		
		ob_start();	?>
	 
		<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'themeva'); ?>">
			<span class="shop-cart-itemnum">
				<?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'themeva'), $woocommerce->cart->cart_contents_count);?> - 
			</span>
			 <?php echo $woocommerce->cart->get_cart_total(); ?>
			
		</a>
		<?php
	
		$fragments['a.cart-contents'] = ob_get_clean();
	
		return $fragments;
	}

/* ------------------------------------
:: TRANSLATION
------------------------------------ */

	load_theme_textdomain( 'themeva', NV_DIR . '/languages' );

	$locale = get_locale();
	$locale_file = NV_DIR . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );


/* ------------------------------------
:: WP MINIFY FIX FOR GOOGLE JQUERY CDN
------------------------------------ */

	function add_minify_location()
	{
		if (class_exists('WPMinify'))
		{
			echo '<!-- WP-Minify JS -->';
			echo '<!-- WP-Minify CSS -->';
		}
	}
	add_action('wp_head','add_minify_location',99);


/* ------------------------------------
:: VISUAL COMPOSER EXTENDED
------------------------------------ */
	
	require_once NV_FILES .'/inc/shortcodes.php';	


/* ------------------------------------
:: ADMIN.CSS + ACTIVATION
------------------------------------ */

	// Admin CSS
	function themeva_admin_styles()
	{
		wp_enqueue_style('themeva-admin-styles', get_template_directory_uri().'/lib/adm/css/wp-admin.css' );
	}
	
	add_action('admin_enqueue_scripts', 'themeva_admin_styles');

	
	// This handles the re-direct to the theme options page after activation.
	if (is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" )
	{
		header( 'Location: '.admin_url().'themes.php?page=options-framework');
	}	