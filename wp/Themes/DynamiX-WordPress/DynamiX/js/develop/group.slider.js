
/* :: 	Group Slider							      
---------------------------------------------*/

		(function( $ ) {

			"use strict";

			function resize_container( gallery, height )
			{
				if( !height )
				{
					var init_slide_height = $( gallery + ' .groupslides-wrap').height();
				}
				else
				{
					var init_slide_height = height;
				}
						
				$( gallery +' .group-slider').animate(
				{
					height: init_slide_height
				}, 750, function() {
					// Animation complete.
				});	
			}		
		
			var group_gallery = function() {

				$('.gallery-wrap.group-slider').each(function(index, value) { 	
						
					var gallery = '#'+$(this).attr('id'),
						effect = $( gallery ).attr("data-groupslider-fx"),
						timeout = $( gallery + ' .timeout').val()*1000;

					// Cycle through iframe(s) > retrieve src and create data-src. 
					$( gallery +' iframe').each(function(index, value) { 
						var src = $(this).attr('src');
						$(this).attr('data-src',src);		
					});							
			
					$( gallery + ' .group-slider').cycle({ 
						fx: effect,
						timeout: timeout,
						speed: 1000,
						slideResize: 0,		
						slideExpr: '.groupslides-wrap',			
						cleartype:  true,
						cleartypeNoBg:  true,
						before:  onBefore,
						after:  onAfter,
						easing: 'easeInOutExpo',
						prev: gallery + ' .slidernav-left  a',
						next: gallery + ' .slidernav-right  a'
					});
	
					// Remove Active Class on Navigation Click
					$( gallery + ' .slidernav a').click(function() {
						$( gallery + ' .caption-wrap' ).removeClass('active', 700, "easeOutBounce" );
					});
								
					$( gallery ).touchwipe({
						preventDefaultEvents: false,
							wipeLeft: function() {
								$( gallery + ' .group-slider').cycle('next');
								return false;
							},
							wipeRight: function() {
								$( gallery + ' .group-slider').cycle("prev");
								return false;
							}
					});	
	
	
					$(window).resize(function()
					{
							var slide_height = jQuery( gallery + ' .group-slider').find('.groupslides-wrap.current').height();
							$( gallery + ' .group-slider').css('height', slide_height);	
					});	
		
		
					function onBefore()
					{ 		
						$( gallery + ' .group-slider .groupslides-wrap.current').removeClass('current');
						$( gallery + ' .caption-wrap').removeClass('active');
						$(this).addClass('current');	
						
						var slide_height = $(this).height();
						resize_container( gallery, slide_height );			
					}
		
		
					function onAfter(currElement, nextElement, opts, isForward) {

						// iFrame					
			
						$( gallery +' iframe').attr('src', '');
							
						$(this).find('iframe').each(function(index, value) { 
							// Apply data-src to iframe src attribute
							data_src = $(this).attr('data-src');

							if( data_src )
							{						
								console.log('data_src:before', data_src);
								$(this).attr('src', data_src);
							}
						});


						// Captions
						var caption_timeout = parseInt( timeout - 2000);
						
						if( !$(this).find('.caption-wrap').hasClass('caption-hover') )
						{						
							// None CSS3 animation
							if( $.support.transition === false )
							{
								$(this).find('.caption-wrap').addClass('active',700, 'easeOutSine').delay( caption_timeout ).queue(function(next){
									$(this).find('.caption-wrap').removeClass('active', 700, "easeOutBounce" );
									next();
								});							
							}
							else
							{		
								$(this).find('.caption-wrap').addClass('active' ).delay( caption_timeout ).queue(function(next){
									$(this).find('.caption-wrap').removeClass('active' );
									next();
								});		
							}
						}						

						var videoid = $(this).find('.jwplayer').attr("id");						
								
						$( gallery + ' .panel .jwplayer').each(function(index)
						{
							var obj = '';
							obj = $(this).attr("id");

							if( jwplayer(obj).getState() == 'PLAYING' )
							{
								jwplayer(obj).pause();
							}

							if( obj == videoid && ( $(this).hasClass("autostart") || $(this).parent('.jwplayer-wrapper').hasClass("autostart") ) )
							{
								if( jwplayer(obj).getState() == "IDLE" || jwplayer(obj).getState() == "PAUSED" )
								{
									jwplayer(obj).play();
								}
							}					 
						});
					} 
					

					$(window).load(function() {
						
						$( gallery ).animate({opacity:1});
						resize_container( gallery );
						
					});
			
				});	
			}	
		
			group_gallery();
		
		})(jQuery);