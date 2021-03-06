/**
 *	:: Slide Manager
 *	---------------------------------------------------------------------
 */
 
jQuery(document).ready(function($){ 	

	// Decide to show select-image-none element for each image chooser
	jQuery('.slide-manager').each(function(){
		if(jQuery(this).find('#selected-slide ul').children().size() > 1 ){
			jQuery(this).find('#selected-slide-none').css('display','none');
		}
	});
	
	
	// Slide Options
	$(document).delegate('.edit-slide', 'click', function()
	{
		var slide_options = $(this).parents('li.slide-init').find('.slide-options-wrapper'),
			slide_title = $(slide_options).attr('data-title'),
			slide_wrap = $(this).parents('li.slide-init'),
			image_url = $( slide_wrap ).find('#slide_manager_image_url');
	
		$(slide_options).attr('data-options','{"mode":"blank","blankContent":true,"blankContentAdopt":true,"headerText":"'+ slide_title +'","headerClose":true,"themeHeader":"c"}').simpledialog2();


		$( image_url ).bind('blur', function()
		{
			var value = $( this ).val(),
				image = $( slide_wrap ).find('.selected-slide-wrapper img');
		
	
			if( value != '' )
			{
				$( image ).attr('src', value);
			}
		});
		
		
	});


	
	// Remove Slide
	$(document).delegate('.remove-slide', 'click', function()
	{
		var remove_slide = $(this),
			remove_title = $(this).attr('title');

		$('<div>').simpledialog2(
		{			
	   		mode: 'button',
			headerText: remove_title,
			headerClose: true,
			buttonPrompt: '',
			themeHeader:"c",
			buttons : 
			{
		  		'Delete':
				{
					click: function ()
					{
						remove_slide.parents('li.slide-init').slideUp('200',function()
						{
							jQuery(this).remove();
						});
						
						if ( remove_slide.parents('#slide-manager').find('#selected-slide ul').children().size() == 2 )
						{
							remove_slide.parents('#slide-manager').find('#selected-slide-none').slideDown();
						}
					}
		  		},
				'Cancel':
				{
					click: function ()
					{},
					icon: "delete",
					theme: "c"
		  		}
			}
		});
	});
	
	
	$('div.selected-slide ul').sortable({ tolerance: 'pointer', forcePlaceholderSize: true });
	
	$('.media-gallery-nav ul a li').click(function()
	{
		$(this).medialib_pagination();
	});

	// Add Blank Slide Button
	medialib_addslide = function()
	{
		$('#media-image-gallery ul').not('.media-gallery-nav ul').prepend('<li class="add-blank-slide ui-btn-up-b"><a href="#" data-mini="true" data-role="button" data-iconpos="top" data-theme="b" data-icon="plus">Custom Media</a></li>');	
		
		$('.add-blank-slide a').button();
	}
	
	medialib_addslide();
	
	// WordPress Media Library Pagination
	$.fn.medialib_pagination = function()
	{
		var slide_manager = $(this).parents('#slide-manager'),
			current_medialib_list = slide_manager.find('#media-image-gallery'),
			paged = $(this).attr('rel');
		
		current_medialib_list.slideUp('200');
	
		$.post(ajaxurl,{ action:'get_media_image', page: paged },function(data)
		{
			paged='';
			
			current_medialib_list.html(data);
			
			medialib_addslide();
			
			current_medialib_list.find('ul li img, .add-blank-slide').bind('click',function()
			{
				$(this).bind_medialib_selection();
			});
			
			current_medialib_list.find('#media-gallery-nav ul a li').bind('click',function()
			{
				$(this).medialib_pagination();
			});
			
			current_medialib_list.slideDown('200');
			
		});
	}
	
	// Bind the selected Media item
	$('.slide-manager').find('#media-image-gallery').find('ul li img,.add-blank-slide').mousedown( function()
	{
		$(this).bind_medialib_selection();
	});
	
	$.fn.bind_medialib_selection = function()
	{
		var clone_slide = $(this).parents('#slide-manager').find('#default-slide').clone(true);
		clone_slide.find('input, textarea, select').attr('name',function()
		{
			return $(this).attr('id') + '[]';
		});
		
		clone_slide.attr('id','slide-init');
		clone_slide.attr('class','slide-init');
		clone_slide.css('display','none');
		clone_slide.find('.slide-media-url').attr('value', $(this).attr('attid')); 
		clone_slide.find('img').attr('src',$(this).attr('rel')); 
		clone_slide.find('img').attr('rel', $(this).attr('rel'));
		 
		$(this).parents('#slide-manager').find('#selected-slide-none').slideUp();
		$(this).parents('#slide-manager').find('#selected-slide ul').append(clone_slide);
		$(this).parents('#slide-manager').find('#selected-slide ul li:last').not('#default-slide').show('slide', { duration: 400, easing: 'easeInOutBack', direction: 'down' });
	}
	
});