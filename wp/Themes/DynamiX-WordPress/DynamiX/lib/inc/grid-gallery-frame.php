<?php 

/* ------------------------------------

:: CONFIGURE SLIDE

------------------------------------*/

	if( !empty($NV_movieurl) && $NV_videotype=="" )
	{ 	
		$isplayer = strpos($NV_movieurl, "player.swf");
		 
		if ($isplayer !== false)
		{	
			if( $NV_videoautoplay == "1" )
			{
				$NV_movieurl .= "&amp;autostart=true";
			}
				
			if( of_get_option('jwplayer_skin') )
			{
				$NV_movieurl .="&amp;skin=".of_get_option('jwplayer_skin');
			}
				
			if( of_get_option('jwplayer_skinpos') )
			{
				$NV_movieurl .="&amp;controlbar.position=".of_get_option('jwplayer_skinpos');
			}				
		}
	}
	
	
	if($NV_imageeffect=='shadowreflection' && $NV_imgheight)
	{
		$effectheight=$NV_imgheight+$NV_imgheight/100*12;
		$NV_effectheight='style="height:'.$effectheight.'px"';
	}


	if( !empty($NV_imgwidth) )
	{
		$NV_maxwidth='style="max-width:'. $NV_imgwidth .'px"';
	}
	
	if( empty($NV_gallery_postformat) ) $NV_gallery_postformat=''; // check if postformat enabled

/* ------------------------------------

:: CONFIGURE SLIDE *END*

------------------------------------*/ ?>

<div class="panel block columns <?php echo $NV_gridcolumns_text."_column "; echo $categories; if($postcount==$NV_gridcolumns) { echo 'last'; } ?> " <?php if($NV_galleryheight) echo 'style="height:'.$NV_galleryheight.'px"'; ?> data-id="id-<?php echo $data_id; ?>">

<?php 

	if( $NV_gallery_postformat == 'yes' )
	{	
		global $NV_is_widget; $NV_is_widget = true; // stop comments displaying within gallery
		get_template_part( 'content', get_post_format() );	
	}
	else
	{ 
		if( $NV_groupgridcontent != "text" )
		{
			// Check "Preview Image" field is completed
			if($NV_videotype)
			{  
				$max_width = ( !empty( $NV_imgwidth ) ) ? 'style="max-width:'. $NV_imgwidth  .'px"' : '';
			?>    
	
			<div class="container videotype <?php echo $NV_shadowsize.' '.$NV_imageeffect.' '.$NV_cssclasses; ?>">
				<div class="gridimg-wrap" <?php echo $max_width; ?>>
					<div class="title-wrap">	                  					
					
						<?php include(NV_FILES .'/inc/classes/video-class.php');
						
						if(($NV_groupgridcontent=="titleoverlay" || $NV_groupgridcontent=="titletextoverlay"))
						{ ?>	
						<div class="title"><h3><?php if($NV_disablegallink!='yes') { ?><a href="<?php if($NV_galexturl) { echo $NV_galexturl; } ?>" title="<?php echo $NV_posttitle; ?>"><?php } ?><?php echo $NV_posttitle; ?><?php if($NV_disablegallink!='yes') { ?></a><?php } ?></h3>
							<?php 
							
							if($NV_groupgridcontent=="titletextoverlay") 
							{ ?>
							<div class="overlaytext">
							<?php echo do_shortcode($NV_description); ?>
							</div> 
							<?php 
							} ?>
						</div>	             
						<?php 
						} ?>	
						
					</div><!-- / title-wrap -->            	
				</div><!-- / gridimg-wrap -->
			</div><!-- / container -->		 
			
			<?php 
			
			}
			elseif( !empty( $NV_previewimgurl) )
			{ 
				// Set Max Width
				$max_width = ( !empty( $NV_imgwidth ) ) ? 'style="max-width:'. $NV_imgwidth  .'px"' : ''; ?>    
		
				<div class="container <?php echo $NV_shadowsize.' '.$NV_imageeffect.' '.$NV_cssclasses; ?>" >
					<div class="gridimg-wrap" <?php echo $max_width; ?>>
						<div class="title-wrap <?php if( $NV_lightbox != 'yes' && $NV_disablegallink == 'yes' ) echo $NV_blackwhite; ?>">
						
						<?php if(class_exists('WPSC_Query') || class_exists('Woocommerce')   && $NV_datasource=='data-5') { // Product Price  ?>
							<?php if( !empty( $NV_productprice ) ) : ?>	<span class="productprice"><?php echo $NV_productprice; ?></span> <?php endif; ?>	  
						<?php } 
						
						// Set Link / Lightbox
						if( $NV_lightbox == "yes" )
						{ 
							echo '<a href="';
							if( !empty($NV_movieurl) )
							{ 
								echo $NV_movieurl; 
							} 
							else
							{ 
								echo $NV_previewimgurl; 
							}
								
							echo '" title="'. $NV_posttitle.'" data-fancybox-group="gallery'. $NV_shortcode_id .'" style="width:'. $NV_imgwidth . 'px"';
								
							if( !empty($NV_movieurl) )
							{
								echo ' class="fancybox galleryvid '. $NV_blackwhite .'"';
							}
							else
							{ 
								echo ' class="fancybox galleryimg '. $NV_blackwhite .' "';
							} 
							echo '>';
						}
						elseif( $NV_disablegallink != 'yes' )
						{ 
							echo '<a href="'. $NV_galexturl .'"  title="'. $NV_posttitle .'" style="width:'. $NV_imgwidth . 'px" class="'. $NV_blackwhite .'">';
						}				
						
						if($NV_imageeffect=="reflection" || $NV_imageeffect=="shadowreflection") $class = 'gallery-img reflect'; else $class = 'gallery-img ';
								
						echo '<img class="'. $class .'" src="'. $NV_imagepath .'" alt="'. $NV_posttitle .'" width="'. $NV_imgwidth .'" height="'. $NV_imgheight .'" />';
								
						if( $NV_disablegallink != 'yes' || $NV_lightbox == "yes" )
						{
							echo '</a>';
						}
						
						if(($NV_groupgridcontent=="titleoverlay" || $NV_groupgridcontent=="titletextoverlay")) { ?>	
						<div class="title"><h3><?php if($NV_disablegallink!='yes') { ?><a href="<?php if($NV_galexturl) { echo $NV_galexturl; } ?>" title="<?php echo $NV_posttitle; ?>"><?php } ?><?php echo $NV_posttitle; ?><?php if($NV_disablegallink!='yes') { ?></a><?php } ?></h3>
							
							<?php if($NV_groupgridcontent=="titletextoverlay") { ?>
							<div class="overlaytext">
							<?php echo do_shortcode($NV_description); ?>
							</div>      
							<?php } ?>                              
						</div>	             
						<?php } ?>	
						</div><!-- / title-wrap -->
					</div><!-- / gridimg-wrap -->
				</div><!-- / container -->
			<?php 
			
			} 
		} 
	
		if(($NV_groupgridcontent!="image" && $NV_groupgridcontent!="titleoverlay" && $NV_groupgridcontent!="titletextoverlay" ))
		{ ?>  
		
			<div class="panelcontent content <?php echo $NV_cssclasses. ' '. $NV_imageeffect; ?>"  <?php echo $NV_maxwidth; ?>>
				
				<h3><?php if($NV_disablegallink!='yes') { ?>
				<a href="<?php if($NV_galexturl) { echo $NV_galexturl; } ?>" title="<?php echo $NV_posttitle; ?>"><?php } ?><?php echo $NV_posttitle; ?><?php if($NV_disablegallink!='yes') { ?></a>
				<?php } ?></h3>	
		
				<?php if($NV_groupgridcontent!="titleimage")
				{ 
					echo do_shortcode($NV_description);
					
					if( $NV_disablegallink != 'yes' && $NV_disablereadmore != 'yes' )
					{
						echo themeva_readmore( $NV_galexturl );	 
					}  
				} ?>
		
			</div><!-- /panelcontent --> 
		<?php 
		} 
	} ?>    

</div><!--  / panel -->


<?php if($postcount==$NV_gridcolumns) { $postcount="0"; ?> <div class="clear"></div> <?php }