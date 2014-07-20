
/* Themeva Javascript Combination File
---------------------------------------------*/


/*
* hoverFlow - A Solution to Animation Queue Buildup in jQuery
* Version 1.00
*
* Copyright (c) 2009 Ralf Stoltze, http://www.2meter3.de/code/hoverFlow/
* Dual-licensed under the MIT and GPL licenses.
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*/
(function($){$.fn.hoverFlow=function(c,d,e,f,g){if($.inArray(c,['mouseover','mouseenter','mouseout','mouseleave'])==-1){return this}var h=typeof e==='object'?e:{complete:g||!g&&f||$.isFunction(e)&&e,duration:e,easing:g&&f||f&&!$.isFunction(f)&&f};h.queue=false;var i=h.complete;h.complete=function(){$(this).dequeue();if($.isFunction(i)){i.call(this)}};return this.each(function(){var b=$(this);if(c=='mouseover'||c=='mouseenter'){b.data('jQuery.hoverFlow',true)}else{b.removeData('jQuery.hoverFlow')}b.queue(function(){var a=(c=='mouseover'||c=='mouseenter')?b.data('jQuery.hoverFlow')!==undefined:b.data('jQuery.hoverFlow')===undefined;if(a){b.animate(d,h)}else{b.queue([])}})})}})(jQuery);




/* :: 	Gallery Image Shadow 									      
---------------------------------------------*/

function nv_shadow() {

	var element='.shadowreflection .gridimg-wrap,.shadow .gridimg-wrap, div.stage-slider-wrap.islider.shadowreflection, div.stage-slider-wrap.islider.shadow,div.stage-slider-wrap.nivo.shadowreflection .slider-inner-wrap, div.stage-slider-wrap.nivo.shadow .slider-inner-wrap, div.accordion-gallery-wrap.shadow,div.accordion-gallery-wrap.shadowreflection,div.accordion-gallery-wrap.shadowblackwhite, div.post-gallery-wrap.islider.shadow, div.post-gallery-wrap.islider.shadowreflection,div.post-gallery-wrap.nivo.shadow, div.post-gallery-wrap.nivo.shadowreflection, #item-header-avatar span.avatar';
	
	jQuery(element).not('.nivo .gridimg-wrap,.islider .gridimg-wrap, .gridimg-wrap.none').each(function(index) {
	
		jQuery(this).append('<div class="shadow-wrap"><img src="'+ NV_SCRIPT.template_url +'/images/shadow-a.png" /></div>');
		
	});

}


/* :: 	Lightbox Hover Images							      
---------------------------------------------*/

function nv_lightboxhover() {

	jQuery('.post-grid.archive .galleryimg, .accordion-gallery .galleryimg, .container .galleryimg').append('<div class="hoverimg"><img src="'+ NV_SCRIPT.template_url +'/images/image-hover.png" width="54" height="55" alt="&nbsp;" /></div>');	
	jQuery('.post-grid.archive .galleryvid,.accordion-gallery .galleryvid, .container .galleryvid').append('<div class="hovervid"><img src="'+ NV_SCRIPT.template_url +'/images/video-hover.png" width="54" height="55" alt="&nbsp;" /></div>');	

}


/* :: 	Preload Images											      
---------------------------------------------*/

(function( $ )
{

	$.fn.preloadImages = function(options,f) {
	
		if(!$.browser.msie) {
			
			var defaults = {
			showSpeed: 800,
			easing: 'easeOutQuad'
			};
		
			var options = $.extend(defaults, options);
		
			return this.each(function(){
			var container = $(this);
			var image = container.find('img').not('.hovervid img,.hoverimg img');
		
			$(image).css({ "visibility": "hidden", "opacity": "0" });
			$(image).bind('load error', function(){
				$(this).css({ "visibility": "visible" }).animate({ opacity:"1" }, {duration:options.showSpeed, easing:options.easing}).closest(container).removeClass('preload');
			}).each(function(){
					if(this.complete || ($.browser.msie && parseInt($.browser.version) == 6)) { $(this).trigger('load'); }
			});
			});
		
		}
		
	};

})( jQuery );


/* :: Add Shadows
---------------------------------------------*/

if( jQuery.browser.msie )
{
	jQuery(window).load( function() {
		nv_shadow();
	});	
} 
else {
	jQuery(document).ready( function() {
		nv_shadow();
	});	
}


/* :: themeva functions
---------------------------------------------*/

jQuery(document).ready( function($) {
	
	// Add Search Character
	$('#primary-wrapper #forums_search_submit, \
	   #primary-wrapper #groups_search_submit, \
	   #primary-wrapper #members_search_submit, \
	   #primary-wrapper #messages_search_submit, \
	   #primary-wrapper #bbp_search_submit').val('\uF002');

	if(!$.browser.msie)
	{
		$('.container .gridimg-wrap, .custom-layer .fullimage').not('.container.videotype .gridimg-wrap, .reflection .gridimg-wrap, .shadowreflection .gridimg-wrap').addClass('preload');	
	}
	
	$('.preload').preloadImages();
	nv_lightboxhover();
	

	/* :: Accordion Icon
	---------------------------------------------*/
	
	$('.ui-accordion-header').prepend('<i class="fa fa-plus"></i>');	
	

	/* :: Retina Logo
	---------------------------------------------*/


	if( NV_SCRIPT.branding_2x !='' || NV_SCRIPT.branding_sec_2x !='' )
	{
		var retina = window.devicePixelRatio > 1 ? true : false;

		if( retina )
		{
			// Change Primary SRC
			$('#header-logo img.primary').attr('src', NV_SCRIPT.branding_2x );
			
			// Set Primary Width / Height
			if( NV_SCRIPT.branding_2x_dimensions !='' )
			{
				var dim = NV_SCRIPT.branding_2x_dimensions.split('x');
				$('#header-logo img.primary').attr('width', dim[0] ).css('width',dim[0] +'px');
				$('#header-logo img.primary').attr('height', dim[1] ).css('height', dim[1] +'px');
			}
			
			// Change Secondary SRC
			$('#header-logo img.secondary').attr('src', NV_SCRIPT.branding_sec_2x );

			// Set Secondary Width / Height
			if( NV_SCRIPT.branding_sec_2x_dimensions !='' )
			{
				var dim = NV_SCRIPT.branding_sec_2x_dimensions.split('x');
				$('#header-logo img.secondary').attr('width', dim[0] ).css('width',dim[0] +'px');
				$('#header-logo img.secondary').attr('height', dim[1] ).css('height', dim[1] +'px');
			}			
		}
	}

	/* :: Header Search											      
	---------------------------------------------*/
	
	$('#panelsearchsubmit').click(function()
	{
		if( $("#panelsearchform").hasClass("disabled") )
		{
			$('#panelsearchform').animate({
				width: 140
				}, 400, function()
				{
					$(this).find('#drops').animate({
					opacity: 1
					}, 400, function()
				{
					$( "#panelsearchform" ).switchClass( "disabled","active");
				});	
			});	
		} 
		else if( $("#panelsearchform").hasClass("active") )
		{
			if($("#panelsearchform #drops").val()!='')
			{
				$("#panelsearchform").submit();
			}
			else
			{
				$('#panelsearchform #drops').animate({
					opacity: 0
				  }, 400, function()
				  {
					$('#panelsearchform').animate({
						width: 22
					  }, 400, function() {
						$( "#panelsearchform" ).switchClass( "active", "disabled");		
					});	
				});		 
			}
		}
	});

	
	/* :: Navigation												      
	---------------------------------------------*/

	$('#nv_selectmenu select').change(
		function()
		{
			var selected_item =  $('#nv_selectmenu select>option:selected');
			
			if ( $(this).val()!='' )
			{
				if( $(this).hasClass('wp-page-nav') )
				{
					window.location.href='?p='+$(this).val();
				}
				else if( $(selected_item).hasClass('droppaneltrigger') )
				{
					if( !$.browser.msie ) $(this).trigger_droppanel();
				}
				else
				{
					window.location.href=$(this).val();
				}
			}
		}
	);

	$('#nv-tabs ul.sub-menu,#nv-tabs ul.children').parent().prepend('<span class="dropmenu-icon"></span>');

	$('#nv-tabs ul li.hasdropmenu').not('#nv-tabs ul li ul li.hasdropmenu, #nv-tabs #megaMenu ul li.hasdropmenu').find('.dropmenu-icon').delay(500).animate({ opacity:1 });
	
	$('#nv-tabs ul ul').hide();
	
	$('#nv-tabs ul li a').attr('aria-haspopup','true');
	
	$('#nv-tabs ul li').not('#nv-tabs #megaMenu ul li, #nv-tabs ul li.extended-menu ul li,#nv-tabs ul li .dropmenu-icon').hover(
		function(e)
		{	
			$(this).find('ul:first').hoverFlow(e.type,
			{ 
				height: "show",
				opacity:0.97
			}, 400, "easeOutCubic");				

		}, 
		function(e)
		{
			$(this).find('ul:first').hoverFlow(e.type,
			{ 
				height: "hide",
				opacity:0 
			}, 150, "easeInCubic", function()
			{
				$(this).hide();	
			});				
		}
	);

	
	$('#nv-tabs li a').not('#nv-tabs li li a, #nv-tabs #megaMenu ul li a').prepend('<span class="menu-highlight"></span>');
							 
	$('#nv-tabs li').not('#nv-tabs li.hasdropmenu,#nv-tabs li.current_page_item,#nv-tabs.static li').hover(
		function(g)
		{
			$(this).find('.menu-highlight').hoverFlow(g.type,
				{
					width: '20px',
					opacity: 1
				}, 250, "easeInOutCubic", function()
				{
					// Animation complete.
				}
			);
		},
		function(g)
		{
			$(this).find('.menu-highlight').hoverFlow(g.type,
				{
					width: '0',
					opacity: 0
				}, 110, "easeOutQuad",
				function()
				{
					// Animation complete.
				}
	  		);
		}
	);


	if( $.browser.msie && $.browser.version == 7 )
	{
		var menuwidth=$('#nv-tabs.center').width();
		
		menuwidth=menuwidth/2;
		
		$('#nv-tabs.center').css(
			{
				'left':'50%',
				'margin-left':'-'+menuwidth+'px',
				'float':'left'
			}
		);
	}
	

	$(".mobilemenu-init a").click(function(event)
	{	
		event.preventDefault();
		
		$('html, body').animate({ scrollTop: '0px' }, 200,"easeInOutCubic");
		
		// Hide / Show Menu
		$( "#mobile-tabs" ).delay(200).toggleClass( 'show', 500, function() {
			
			if( $(this).hasClass('show') ) 
			{
				var menu_height = $( '#mobile-tabs' ).outerHeight();
				
				$('#primary-wrapper').css('height', menu_height + 'px');
			}
			else
			{
				$('#primary-wrapper').css('height', 'auto');
			}
			
		});
	});
	


/* :: Text Resizer									      
---------------------------------------------*/	
	
  // Increase Font Size
  $(".increaseFont").click(
  	function()
	{
    	var currentFontSize = $('.content-wrap').css('font-size');
    	var currentFontSizeNum = parseFloat(currentFontSize, 10);
    	var newFontSize = currentFontSizeNum*1.1;
    	$('.content-wrap').css('font-size', newFontSize);	
    	return false;
  	}
  );
  
  // Decrease Font Size
  $(".decreaseFont").click(
  	function()
	{
    	var currentFontSize = $('.content-wrap').css('font-size');
    	var currentFontSizeNum = parseFloat(currentFontSize, 10);
    	var newFontSize = currentFontSizeNum*0.9;
    	$('.content-wrap').css('font-size', newFontSize);
    	return false;
  	}
  );
	


/* :: WPEC Modifications										      
---------------------------------------------*/

	$(".product_form").live('submit',
		function()
			{ 
				if($(this).parents('form:first').find('select.wpsc_select_variation[value=0]:first').length)
				return false;
    			var cartCount = $('.shop-cart .shop-cart-itemnum').text();
    			var cartInt = parseInt(cartCount);
    			var quantity = parseInt($('.cartcount').val());
    			
				if (quantity > 1)
    				cartInt += quantity;
    			else
    				cartInt++;
 				$('.shop-cart .shop-cart-itemnum').text(cartInt);
    		}
		);
		
    	$('a.emptycart').click(
			function()
			{
    			$('.shop-cart .shop-cart-itemnum').text("0");
    		}
		);

	$('.shop-cart').hover(function(e)
		{
			$(this).find('.shop-cart-contents').hoverFlow(e.type, { height: "show" }, 150, "easeInOutCubic");
		},
		function(e)
		{
			$(this).find('.shop-cart-contents').hoverFlow(e.type, { height: "hide" }, 250, "easeInBack");
		}
	);
	
	
	$('.wpcart_gallery a').each(
		function()
		{
			$('.wpcart_gallery a.thickbox').unbind('click');	
			$(this).removeClass('thickbox').addClass('galleryimg fancybox');
			var rel = $(this).attr("rel");
			rel = rel.replace(" ","_");
			$(this).attr('title', rel);
			$(this).attr('data-fancybox-group', 'image-'+rel);
			$(this).removeAttr('rev');
		}
	);	
	
	
/* :: 	Add target blank										      
---------------------------------------------*/

	$('.target_blank a').each(function()
		{
			$(this).click(
				function(event)
				{
					event.preventDefault();
			   		event.stopPropagation();
			   		window.open(this.href, '_blank');
				}
		   );
		}
	);


/* :: 	Back to top Animation									      
---------------------------------------------*/

	$('.hozbreak-top a,.autototop a').click(
		function()
		{
			 $('html, body').animate({ scrollTop: '0px' }, 400,"easeInOutCubic");
			 return false;
		}
	);
	
	$(function () { // run this code on page load (AKA DOM load)
	 
		/* set variables locally for increased performance*/
		var scroll_timer;
		var displayed = false;
		var $message = $('div.autototop a');
		var $window = $(window);
		var top = $(document.body).children(0).position().top;
	 
		/* react to scroll event on window*/
		$window.scroll(
			function ()
			{
				window.clearTimeout(scroll_timer);
				scroll_timer = window.setTimeout(function () { // use a timer for performance
					if($window.scrollTop() <= top) // hide if at the top of the page
					{
						$('.mobilemenu-init').removeClass('scroll');
						displayed = false;
						$message.fadeOut(500);
					}
					else if(displayed == false) // show if scrolling down
					{
						$('.mobilemenu-init').addClass('scroll');
						displayed = true;
						$message.stop(true, true).show().click(function () { $message.fadeOut(500); });
					}
				}, 100,"easeInOutCubic");
			}
		);
	});



/* :: 	Drop Panel												      
---------------------------------------------*/
	
	// Expand Panel
	$("#open").click(function(){
		$("div#panel").animate({height: "show"}, 900, "easeInOutCubic").addClass('open');
	});	
	
	$(".contacttrigger").click(function(){
		$("div#panel").animate({height: "show"}, 900, "easeInOutCubic").addClass('open');
		$("#toggle a.toggle").toggle();
	
	});	

	$(".droppaneltrigger a, a.droppaneltrigger").click(function() {
		$(this).trigger_droppanel();
	});
	
	// Collapse Panel
	$("#close").click(function(){
		$("div#panel").animate({height: "hide"}, 900, "easeInBack").removeClass('open');
	});		
	
	// Switch buttons on click
	$("#toggle a.toggle").click(function () {
		$("#toggle a.toggle").toggle();
	});		



/* :: 	Header Infobar											      
---------------------------------------------*/

	$("span.infobar-close a").click(function () {
		$("div.header-infobar").animate({height:0,opacity:0});
	});		


/* :: 	Social Icons Animate									      
---------------------------------------------*/
	
	// Show Social Icons
	$("a.socialinit").on("click", function(event)
	{		
		if( $(this).hasClass('socialhide') )
		{
			$("div.socialicons").not('div.socialicons.display,div.socialicons.init').fadeOut('slow');
		}
		else
		{
			$("div.socialicons").not('div.socialicons.display,div.socialicons.init').fadeIn('slow');
		}
		
		$(this).toggleClass('socialhide');
		
		return false;
		e.preventDefault(); // same thing as above
	});	

	$('.socialicons ul li div.social-icon,.socialinit .socialinithide,.socialhide .socialinithide, .textresize li').hover(function () {
		
		if( $.browser.msie && $.browser.version < 9 ) {
			$(this).animate({ marginTop:2 },100,'easeOutQuad');
		} else {
			$(this).animate({ opacity:0.6,marginTop:2 },100,'easeOutQuad');
		}
		
	});

	$('.socialicons ul li div.social-icon,.socialinit .socialinithide,.socialhide .socialinithide, .textresize li').mouseout(function () {
		
		if( $.browser.msie && $.browser.version < 9 ) {
			$(this).animate({ marginTop:0 },170,'easeOutQuad');
		} else {
			$(this).animate({ opacity:1,marginTop:0 },170,'easeOutQuad');
		}
		
		
	});	
	

	$('#searchsubmit,#panelsearchsubmit').hover(function () {
		if( $.browser.msie && $.browser.version < 9 ) {
			//
		} else {
			$(this).animate({opacity:0.6},100,'easeOutQuad');
		}
	});

	$('#searchsubmit,#panelsearchsubmit').mouseout(function () {
		
		if( $.browser.msie && $.browser.version < 9 ) {
			//
		} else {		
			$(this).animate({opacity:1},170,'easeOutQuad');
		}
	});		


/* :: 	Gallery Image Hover 									      
---------------------------------------------*/

	
	$('.galleryimg,.shortcodeimg,.shortcodevid,.galleryvid').hover(
		
		// Mouseover, fadeIn the hidden hover class	
		function()
		{
			$(this).children('div.hoverimg,div.hovervid').css('display', 'block').fadeTo("slow",1);
		}, 
	
		//Mouseout, fadeOut the hover class
		function()
		{
			$(this).children('div.hoverimg,div.hovervid').fadeTo("fast",0,
				function()
				{
					$(this).css('display', 'none'); // FIX IE BUG	
				}
			);
		}
	);

/* :: 	Gallery Navigation Hover 									      
---------------------------------------------*/

		
	$('.stage-slider-wrap.islider,.post-gallery-wrap,.group-slider.shortcode,.gallery-wrap.vertical').hover(
		function()
		{
			$(this).find('.islider-nav-wrap .nvcolor-wrap,.slidernav-left,.slidernav-right').fadeTo(500,1);	
		},
		function()
		{
			$(this).find('.islider-nav-wrap .nvcolor-wrap,.slidernav-left,.slidernav-right').fadeTo(200,0);	
		}
	);		
		
		
/* :: 	Contact Form										      	  
---------------------------------------------*/

	$('form#contactForm').submit(function() {
		$('form#contactForm .error').remove();
		var hasError = false;
		$('.requiredField').each(function() {
			if($.trim($(this).val()) == '') {
				var labelText = $(this).prev('label').text();
				$(this).parent().append('<span class="error">You forgot to enter your '+labelText+'.</span>');
				hasError = true;
			} else if($(this).hasClass('email')) {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(!emailReg.test($.trim($(this).val()))) {
					var labelText = $(this).prev('label').text();
					$(this).parent().append('<span class="error">You entered an invalid '+labelText+'.</span>');
					hasError = true;
				}
			}
		});
		if(!hasError) {
			var formInput = $(this).serialize();
			$.post($(this).attr('action'),formInput, function(data){
				$('form#contactForm').slideUp("fast", function() {				   
					$(this).before('<p class="thanks"><strong>Thanks!</strong> Your email was successfully sent. I check my email all the time, so I should be in touch soon.</p>');
				});
			});
		}
		
		return false;
		
	});



/* :: 	Tabs												      	  
---------------------------------------------*/

	$('.nv-tabs').each(
		function(index)
		{
			$(this).tabs({ fx: { opacity:'toggle', duration:200 }  });
		}
	);

/* :: 	Reveal Content 										      	  
---------------------------------------------*/


	$("h4.reveal").click(function(e)
	{
		if ( $(this).hasClass('ui-state-active') )
		{
			$(this).removeClass('ui-state-active').next().slideUp(500);
		}
		else
		{
			$(this).addClass('ui-state-active').next().slideDown(500);
		}
	});
	

	
/* :: 	Gallery Overlay												  
---------------------------------------------*/

	$('.gridimg-wrap').hover(function(e)
		{
			$(this).find('.title').hoverFlow(e.type, { height: "show" }, 400, "easeInOutCubic");
		},
		function(e)
		{
			$(this).find('.title').hoverFlow(e.type, { height: "hide" }, 400, "easeInBack");
		}
	);


/* :: Trigger Drop Panel
---------------------------------------------*/

(function( $ ){
	
	$.fn.trigger_droppanel = function() {
		
		if($("div#panel").hasClass('open')) {
			
			$("div#panel").removeClass('open');
			$("#toggle a.toggle").toggle();
			
			$('html, body').animate({scrollTop: '0px'}, 800,"easeInOutCubic",function() {
				$("div#panel").animate({height: "hide"}, 900, "easeInBack");	
			});
			return false;
			
			
		} else {
			
			$("div#panel").addClass('open');
			$("#toggle a.toggle").toggle();
			

			$('html, body').animate({scrollTop: '0px'}, 800,"easeInOutCubic",function() {
				$("div#panel").animate({height: "show"}, 900, "easeInOutCubic");
			});
			return false;

		}
	}
	
})( jQuery );


/* :: Reflection Canvas Reszier
---------------------------------------------*/

	if($.browser.msie || $.browser.opera) {
		$(window).resize(function() {
			$('div.reflect canvas,span.reflect canvas').each(function() {
				var canvas_h=$(this).height();
				var gridwrap_h = $(this).closest('.gridimg-wrap').height();
				var new_canvas_h = (gridwrap_h-canvas_h);
				new_canvas_h=(new_canvas_h/100*12);
				$(this).css('height',new_canvas_h);
			});
		});
	}

});