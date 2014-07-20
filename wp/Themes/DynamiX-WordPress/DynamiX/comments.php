<?php
/**
 * @package WordPress
 * @subpackage NorthVantage
 */

	/* ------------------------------------
	:: Comments Alignment
	------------------------------------ */
 
	$NV_arhpostpostmeta = of_get_option('arhpostpostmeta'); // get postmeta data configuration
	$NV_postmetaalign	= of_get_option("postmetaalign"); // Align Postmeta Data

	if(is_single() && ($NV_arhpostpostmeta=='post_only' || $NV_arhpostpostmeta=='')) : 			
		$NV_arhpostpostmeta='display';
	elseif(!is_single() && ($NV_arhpostpostmeta=='' || $NV_arhpostpostmeta=='archive_only')) : 	
		$NV_arhpostpostmeta='display';
	endif;
	 
	if(
	 $NV_arhpostpostmeta=='display' && $NV_postmetaalign=='') { 
		$columns='ten columns last clearfix offset-by-two';
	} else { 
		$columns='';
	} ?>

    <div class="comments-wrapper comments-area"  id="comments">
        <div class="comments-wrap <?php echo $columns; ?>">
        <?php if ( post_password_required() ) : ?>
            <p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'themeva' ); ?></p>
        <?php
                /* Stop the rest of comments.php from being processed,
                 * but don't kill the script entirely -- we still have
                 * to fully load the template.
                 */
                return;
            endif;
       
	   // You can start editing here -- including this comment! 
        
        if ( have_comments() ) : ?>
            <h4 id="comments-title">
                <?php
                    printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'themeva' ),
                        number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
                ?>
            </h4>
    
            <?php if ( get_comment_pages_count() > 1 && of_get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
            <nav id="comment-nav-above">
                <h3 class="assistive-text"><?php _e( 'Comment navigation', 'themeva' ); ?></h3>
                <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'themeva' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'themeva' ) ); ?></div>
            </nav>
            <?php endif; // check for comment navigation ?>
    
            <ol class="commentlist">
                <?php wp_list_comments('type=comment&callback=NorthVantage_comment'); ?>
            </ol>
    
            <?php if ( get_comment_pages_count() > 1 && of_get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
            <nav id="comment-nav-below">
                <h3 class="assistive-text"><?php _e( 'Comment navigation', 'themeva' ); ?></h3>
                <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'themeva' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'themeva' ) ); ?></div>
            </nav>
            <?php endif; // check for comment navigation ?>
    
        <?php
            /* If there are no comments and comments are closed, let's leave a little note, shall we?
             * But we don't want the note on pages or post types that do not support comments.
             */
            elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
        ?>
            <?php /* <p class="nocomments"><?php _e( 'Comments are closed.', 'themeva' ); ?></p> */ ?>
        <?php endif; ?>
    
        <?php comment_form(array(
            'comment_notes_after' => ' ',
        )); ?>
    	</div><!-- #comments-wrap -->
    </div><!-- #comments -->