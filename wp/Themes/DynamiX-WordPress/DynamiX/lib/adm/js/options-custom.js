/**
 * Prints out the inline javascript needed for the colorpicker and choosing
 * the tabs in the panel.
 */ 


(function ($) {

     $.fn.themevaImportDemo = function() {
   
		$("a.import-demo").delegate(this, "click", function() {
                triggerImport();
                return false;
		});
  
		
        triggerImport = function() {
            var d = {
                action: "themeva_options_import_demo",
                _ajax_nonce: $("#_ajax_nonce").val()
            };
            $('body').css('cursor', 'wait');
            $.post(ajaxurl, d, function (r) {
								
                if (r != -1)
				{		
					$('.ajax-message').html('<a href="#" data-role="button" data-icon="check" data-iconpos="notext" data-theme="c" data-inline="true" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" title="Check" class="ui-btn ui-shadow ui-btn-corner-all ui-btn-inline ui-btn-icon-notext ui-btn-up-c"><span class="ui-btn-inner"><span class="ui-btn-text">Check</span><span class="ui-icon ui-icon-check ui-icon-shadow">&nbsp;</span></span></a> ' + r).fadeIn('fast');
					
					setTimeout(function() {
						localStorage.setItem("activetab", '#of-option-general');
                        window.location.reload();
                    }, 4000);					
				
                } else {

					r = '<div class="message warning"><span>&nbsp;</span>The demo data could not be imported.</div>';

					$('.ajax-message').fadeIn('fast').html(r).delay(3000, function()
					{
                		$('.ajax-message').fadeOut();
            		});					
                }
            });
            return false;
        }
    };

    $(document).ready(function () {
        $('.import-demo').themevaImportDemo();
    })

})(jQuery);


jQuery(document).ready(function($) {
	
	if( localStorage.getItem("activetab") == 'undefined' && localStorage.getItem("themeva-init") == 'undefined'  )
	{
		localStorage.getItem("activetab") = '#of-option-gettingstarted';
		localStorage.getItem("themeva-init") = 'activated';
	}
	
	// Fade out the save message
	$('.fade').delay(1000).fadeOut(1000);
	
	// Color Picker
	$('.colorSelector').each(function(){
		var Othis = this; //cache a copy of the this variable for use inside nested function
		var initialColor = $(Othis).next('input').attr('value');
		$(this).ColorPicker({
		color: initialColor,
		onShow: function (colpkr) {
		$(colpkr).fadeIn(500);
		return false;
		},
		onHide: function (colpkr) {
		$(colpkr).fadeOut(500);
		return false;
		},
		onChange: function (hsb, hex, rgb) {
		$(Othis).children('div').css('backgroundColor', '#' + hex);
		$(Othis).next('input').attr('value','#' + hex);
	}
	});
	}); //end color picker
	
	// Switches option sections
	$('.group_meta_box').hide();
	var activetab = '';
	if (typeof(localStorage) != 'undefined' ) {
		activetab = localStorage.getItem("activetab");
	}
	if (activetab != '' && $(activetab).length ) {
		$(activetab).fadeIn();
	} else {
		$('.group_meta_box:first').fadeIn();
	}
	$('.group_meta_box .collapsed').each(function(){
		$(this).find('input:checked').parent().parent().parent().nextAll().each( 
			function(){
				if ($(this).hasClass('last')) {
					$(this).removeClass('hidden');
						return false;
					}
				$(this).filter('.hidden').removeClass('hidden');
			});
	});
	
	if (activetab != '' && $(activetab + '-tab').length ) {
		$(activetab + '-tab').addClass('nav-tab-active');
	}
	else {
		$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
	}
	$('.nav-tab-wrapper a').click(function(evt) {
		$('.nav-tab-wrapper a').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active').blur();
		var clicked_group = $(this).attr('href');
		if (typeof(localStorage) != 'undefined' ) {
			localStorage.setItem("activetab", $(this).attr('href'));
		}
		$('.group_meta_box').hide();
		$(clicked_group).fadeIn();
		evt.preventDefault();
		
		// Editor Height (needs improvement)
		$('.wp-editor-wrap').each(function() {
			var editor_iframe = $(this).find('iframe');
			if ( editor_iframe.height() < 30 ) {
				editor_iframe.css({'height':'auto'});
			}
		});
	
	});
           					
	$('.group_meta_box .collapsed input:checkbox').click(unhideHidden);
				
	function unhideHidden(){
		if ($(this).attr('checked')) {
			$(this).parent().parent().parent().nextAll().removeClass('hidden');
		}
		else {
			$(this).parent().parent().parent().nextAll().each( 
			function(){
				if ($(this).filter('.last').length) {
					$(this).addClass('hidden');
					return false;		
					}
				$(this).addClass('hidden');
			});
           					
		}
	}
	
	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');		
	});
		
	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	$(".update_options").click(function(e){
		e.preventDefault();
		$("#options").submit();
		return false;
	});

	$(".reset_options").click(function(e){
		e.preventDefault();

		var confirm_restore = confirm('Click OK to reset. Any theme settings will be lost!');
		
		if ( confirm_restore )
		{
		   $("#options").append('<input name="reset" id="reset" type="hidden" value="1" />').submit();
		}
	
		return false;
		
	});	
				
});

jQuery(document).bind("mobileinit", function () {
	jQuery.mobile.ajaxEnabled=false;
});	
