<?php
	
    echo '<div class="videowrap ';
		if( !empty($ratio) ) echo $ratio;
	echo '">';
            
    $vidurl = $NV_movieurl;
    $file = basename($vidurl); 
    $parts = explode(".", $file); 
    
    $vidid = $parts[0]; //File name 
    if( $NV_videotype == "youtube" )
	{
    
            $vidid = strstr( $vidid , '=' ); // trim id after = 
            $params = strstr( $vidid , '&' ); // trim id after = 
            
    
            $splitter = '?'; // set url parameter	
            $isplaylist = strpos($vidurl ,"playlist?list="); // check if playlist
            $isredirect = strpos($vidurl ,"youtu.be"); // check if share url
                
            if($isredirect!=false) { // if share url retrieve video id
                $vidid=$parts[0];
                $splitter = '?'; // set url parameter	
            }				
                                
            if($isplaylist!=false) {
                $vidid = 'videoseries?list='.$vidid;
                $splitter = '&amp;';		
            }	
    
            
            if($isredirect==false) {
                $vidid = substr($vidid, 1); // remove = if not youtu.be address		
            }
            
            $vidid = str_replace($params,"",$vidid);
            $params = str_replace('?','',$params);
        
        
    }
	elseif( $NV_videotype == "wistia" )
	{
		$extras = $components = '';
		
		$components = parse_url( $vidid );

		$vidid = str_ireplace( array('/medias/', '/embed/iframe/'), '', $components['path'] );
		$extras = $components['query'];
		
		if( $extras == '' ) $extras = 'controlsVisibleOnLoad=true&amp;version=v1&amp;volumeControl=true';
		
		// autoplay
		if( $NV_videoautoplay == '1' ) $NV_videoautoplay = 'true'; else $NV_videoautoplay = 'false'; 

		$extras = $extras . '&amp;autoPlay='.  $NV_videoautoplay;
		
    }
	elseif( $NV_videotype == "swf" || $NV_videotype == "jwp" )
	{
        $vidid = $vidurl;
    }
    
    if( $NV_videotype == "youtube" )
	{ 
    
    /* ------------------------------------
    :: YOUTUBE
    ------------------------------------*/ ?>
    
    <iframe frameborder="0" marginheight="0" marginwidth="0" width="<?php echo $NV_imgwidth; ?>" height="<?php echo $NV_imgheight; ?>" src="http://www.youtube.com/embed/<?php echo $vidid.$splitter; ?>autoplay=<?php echo $NV_videoautoplay ?>&amp;loop=<?php echo $NV_loop; ?><?php echo $params; ?>&amp;wmode=opaque&amp;title=" allowfullscreen></iframe>
    
    <?php 
	}
	elseif( $NV_videotype == "vimeo" )
	{ 
    
    /* ------------------------------------
    :: VIMEO
    ------------------------------------*/ ?>
    
    <iframe frameborder="0" marginheight="0" marginwidth="0"  src="http://player.vimeo.com/video/<?php echo $vidid; ?>?autoplay=<?php echo $NV_videoautoplay ?>&amp;loop=<?php echo $NV_loop; ?>&amp;title=0&amp;byline=0&amp;portrait=0&amp;" width="<?php echo $NV_imgwidth; ?>" height="<?php echo $NV_imgheight; ?>" ></iframe>
    
    <?php 
	} 
	elseif( $NV_videotype == "wistia" )
	{ 
    
    /* ------------------------------------
    :: WISTIA
    ------------------------------------*/ ?>
    
	<iframe src="http://fast.wistia.net/embed/iframe/<?php echo $vidid .'?'. $extras; ?>" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" width="<?php echo $NV_imgwidth; ?>" height="<?php echo $NV_imgheight; ?>"></iframe>    
    
    <?php 
	} 	
	elseif( $NV_videotype == "swf" ) 
	{
        
	/* ------------------------------------	
	:: FLASH (SWF)
	------------------------------------*/
		
	if( empty($NV_imgheight) ) $NV_imgheight='100%';
	if( empty($NV_imgwidth) )  $NV_imgwidth='100%';	?>
				  
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="<?php echo $NV_imgwidth; ?>" height="<?php echo $NV_imgheight; ?>">
		<param name="movie" value="<?php echo $vidid; ?><?php if($NV_videotype!="swf") { ?>&amp;autoplay=<?php echo $NV_videoautoplay ?><?php } ?>" />
		<param name="wmode" value="transparent" />
		<param name="allowFullScreen" value="true" />
		<param name="allowScriptAccess" value="always" />
		<param name="scale" value="exactfit">
		<!--[if !IE]>-->
		<object type="application/x-shockwave-flash" data="<?php echo $vidid; ?><?php if($NV_videotype!="swf") { ?>&amp;autoplay=<?php echo $NV_videoautoplay ?><?php } ?>" width="<?php echo $NV_imgwidth; ?>" height="<?php echo $NV_imgheight; ?>">
		<param name="wmode" value="transparent" />
		<param name="allowFullScreen" value="true" />
		<param name="allowScriptAccess" value="always" />		
		<param name="scale" value="exactfit">		
		<!--<![endif]-->
		<!--[if !IE]>-->
		</object>
		<!--<![endif]-->
		</object>
		
		<?php 
    
    }
	elseif( $NV_videotype=="jwp" )
	{
        
	/* ------------------------------------
	:: JW PLAYER
	------------------------------------*/
        
		if( empty($NV_imgwidth) ) $NV_vidwidth = $NV_imgheight * "1.595"; else $NV_vidwidth = $NV_imgwidth; // 16:9 Ratio for Video
    
    
        if( empty($NV_imgheight) && !empty($NV_imgwidth) ) 
        { 
            $jwplayer_height = $jwplayer_height="". ceil( $NV_imgwidth / "1.595" ); $NV_imgheight=ceil( $NV_imgwidth / "1.595" ); 
        }
        elseif( !empty($NV_imgheight) )
        { 
            $jwplayer_height = $NV_imgheight;
        } 
    
        if( $NV_mediatype=='audio' && of_get_option('jwplayer_height') !='' )
        {
            $jwplayer_height = of_get_option('jwplayer_height');
        } 
    
        if( !empty ( $video_id ) ) $slide_id = 'slide-'.$video_id;

		// control bar position
		if( $NV_mediatype=='audio' ) 
		{
			$controlbar = 'bottom';
		}
		elseif( of_get_option('jwplayer_skinpos') )
		{
			$controlbar = of_get_option('jwplayer_skinpos');
		} else 
		{
			$controlbar = 'over';
		}

		// hide controls if background layer
		$icons = ( !empty($NV_customlayer) ? 'true' : 'false' );
		
		// skin
		$skin = ( of_get_option('jwplayer_skin') !='' ? of_get_option('jwplayer_skin') : '' );			
    
        echo '<div class="jwplayer-wrapper">';
        echo '<div 
		id="'. $slide_id .'" 
		class="jwplayer-container" 
		data-jw-width="'. $NV_vidwidth .'" 
		data-jw-height="'. $jwplayer_height .'" 
		data-jw-media="'. $NV_mediatype .'" 
		data-jw-mediaurl="'. $vidid .'" 
		data-jw-controlbar="'. $controlbar .'" 
		data-jw-loop="'. $NV_loop .'" 
		data-jw-icons="'. $icons .'" 
		data-jw-skin="'. $skin .'" 
		data-jw-swfsrc="'. of_get_option('jwplayer_swf') .'" 
		data-jw-image="'. dyn_getimagepath($NV_previewimgurl) .'"
		data-jw-autoplay="'. $NV_videoautoplay .'"></div>';
        echo '</div>';
		
	
		if ( is_plugin_active('jw-player-plugin-for-wordpress/jwplayermodule.php') )
		{
            wp_deregister_script( 'jw-player-init' );	
            wp_register_script( 'jw-player-init', get_template_directory_uri().'/js/jw-player.init.min.js',false,null,true );
            wp_enqueue_script( 'jw-player-init' );					
		}
		elseif( of_get_option('jwplayer_js') )
		{
        	// Check jw player javascript file is present
			$NV_jwplayer_js = of_get_option('jwplayer_js');
			wp_deregister_script( 'jw-player' );	
			wp_register_script( 'jw-player', $NV_jwplayer_js,false,null,true );
			wp_enqueue_script( 'jw-player' );	

			wp_deregister_script( 'jw-player-init' );	
            wp_register_script( 'jw-player-init', get_template_directory_uri().'/js/jw-player.init.min.js',false,null,true );
            wp_enqueue_script( 'jw-player-init' );					
		}	
    }
	
	echo '</div>';