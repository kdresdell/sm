<?php /* Stop the theme from killing WordPress if BuddyPress is not enabled.*/
if ( !class_exists( 'BP_Core_User' ) )
	return false;

/* Load the default BuddyPress AJAX functions*/
/* We are going to load the ajax from the BuddyPress plugin directory so we always use the latest version*/
require_once( BP_PLUGIN_DIR . '/bp-themes/bp-default/_inc/ajax.php' );

/* Load the BuddyPress javascript and css*/
/* We use !bp_is_blog_page() here to only load the JS and CSS on BuddyPress pages to save on load time*/
/* We want to load the adminbar css on all pages so it has been taken out of the if statement*/
/* We are going to load the JS from the BuddyPress plugin directory so we always use the latest version*/

if ( !is_admin() ) {
		// Register buttons for the relevant component templates
		// Friends button
		if ( bp_is_active( 'friends' ) )
			add_action( 'bp_member_header_actions',    'bp_add_friend_button' );

		// Activity button
		if ( bp_is_active( 'activity' ) )
			add_action( 'bp_member_header_actions',    'bp_send_public_message_button' );

		// Messages button
		if ( bp_is_active( 'messages' ) )
			add_action( 'bp_member_header_actions',    'bp_send_private_message_button' );

		// Group buttons
		if ( bp_is_active( 'groups' ) ) {
			add_action( 'bp_group_header_actions',     'bp_group_join_button' );
			add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
			add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
		}

		// Blog button
		if ( bp_is_active( 'blogs' ) )
			add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
	}

/* Add required BuddyPress JavaScript vars.*/
/* Add words that we need to use in JS to the end of the page so they can be translated and still used.*/
function bp_dtheme_js_terms() { ?>
<script type="text/javascript">
<?php if(get_option('priority_loading')=='enable') { ?>
head.ready(function() {
<?php } ?>
	var bp_terms_my_favs = '<?php _e( "My Favorites", "buddypress" ) ?>';
	var bp_terms_accepted = '<?php _e( "Accepted", "buddypress" ) ?>';
	var bp_terms_rejected = '<?php _e( "Rejected", "buddypress" ) ?>';
	var bp_terms_show_all_comments = '<?php _e( "Show all comments for this thread", "buddypress" ) ?>';
	var bp_terms_show_all = '<?php _e( "Show all", "buddypress" ) ?>';
	var bp_terms_comments = '<?php _e( "comments", "buddypress" ) ?>';
	var bp_terms_close = '<?php _e( "Close", "buddypress" ) ?>';
	var bp_terms_mention_explain = '<?php printf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", "buddypress" ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname(bp_get_displayed_user_fullname()), bp_get_user_firstname(bp_get_displayed_user_fullname()) ); ?>';
<?php if(get_option('priority_loading')=='enable') { ?>
});
<?php } ?>
</script>
<?php
}
add_action( 'wp_footer', 'bp_dtheme_js_terms' );

/* Added so BuddyPress can take care of page titles on BuddyPress pages*/
if (!bp_is_blog_page() ) {
	add_action( 'wp_title', 'bp_get_page_title');
}

// CHANGE BP ADMIN BAR LOGO
function erocks_bp_adminbar_logo() {

global $bp;
echo '';
}
remove_action( 'bp_adminbar_logo', 'bp_adminbar_logo' );
add_action( 'bp_adminbar_logo', 'erocks_bp_adminbar_logo' );

define( 'BP_ENABLE_USERNAME_COMPATIBILITY_MODE', true );

/* enable shortcodes in budypress*/
//add_filter( 'bp_get_the_topic_post_content', 'do_shortcode' );
/* enable shortcodes in budypress*//*END*/

?>