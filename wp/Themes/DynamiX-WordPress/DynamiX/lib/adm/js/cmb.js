/**
 * Controls the behaviours of custom metabox fields.
 *
 * @author Andrew Norcross
 * @author Jared Atchison
 * @author Bill Erickson
 * @see    https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */

/*jslint browser: true, devel: true, indent: 4, maxerr: 50, sub: true */
/*global jQuery, tb_show, tb_remove */

/**
 * Custom jQuery for Custom Metaboxes and Fields
 */
jQuery(document).ready(function ($) {
	'use strict';

	// Add MetaBox Menu and jQuery Mobile Wrapper
	var postbox_id = $('.cmb_metabox').parents('.postbox-container').attr('id');	
		
	$(META_BOX.meta_box_ids).addClass('group_meta_box');
		
	$('#'+ postbox_id +' .group_meta_box').wrapAll('<div data-role="page" class="clearfix" id="nv_meta_boxes" /></div>');
		
	// Show tabs if more than one
	if( META_BOX.meta_box_count > 1 ) 
	{
		if( $('#'+postbox_id).length ) $('<h2 class="nav-tab-wrapper clearfix">'+ META_BOX.meta_box_menu +'</h2>').insertBefore('[data-role="page"]');
	}

	var formfield;

	/**
	 * Initialize timepicker (this will be moved inline in a future release)
	 */
	$('.cmb_timepicker').each(function () {
		$('#' + jQuery(this).attr('id')).timePicker({
			startTime: "07:00",
			endTime: "22:00",
			show24Hours: false,
			separator: ':',
			step: 30
		});
	});

	/**
	 * Initialize jQuery UI datepicker (this will be moved inline in a future release)
	 */
	$('.cmb_datepicker').each(function () {
		$('#' + jQuery(this).attr('id')).datepicker();
		// $('#' + jQuery(this).attr('id')).datepicker({ dateFormat: 'yy-mm-dd' });
		// For more options see http://jqueryui.com/demos/datepicker/#option-dateFormat
	});
	// Wrap date picker in class to narrow the scope of jQuery UI CSS and prevent conflicts
	$("#ui-datepicker-div").wrap('<div class="cmb_element" />');
	
	/**
	 * Initialize color picker
	 */
    $('input:text.cmb_colorpicker').each(function (i) {
        $(this).after('<div id="picker-' + i + '" style="z-index: 1000; background: #EEE; border: 1px solid #CCC; position: absolute; display: block;"></div>');
        $('#picker-' + i).hide().farbtastic($(this));
    })
    .focus(function() {
        $(this).next().show();
    })
    .blur(function() {
        $(this).next().hide();
    });

	/**
	 * File and image upload handling
	 */
	$('.cmb_upload_file').change(function () {
		formfield = $(this).attr('name');
		$('#' + formfield + '_id').val("");
	});

	$('.cmb_upload_button').live('click', function () { //_themeva_mod
		var buttonLabel;
		formfield = $(this).parents('.ui-field-contain').find('.cmb_upload_file').attr('name');

		buttonLabel = 'Use as ' + $('label[for=' + formfield + ']').text();
		tb_show('', 'media-upload.php?post_id=' + $('#post_ID').val() + '&type=file&cmb_force_send=true&cmb_send_label=' + buttonLabel + '&TB_iframe=true');
		return false;
	});

	$('.cmb_remove_file_button').live('click', function () {
		formfield = $(this).attr('rel');
		$('input#' + formfield).val('');
		$('input#' + formfield + '_id').val('');
		$(this).parent().remove();
		return false;
	});

	window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function (html) {
		var itemurl, itemclass, itemClassBits, itemid, htmlBits, itemtitle,
			image, uploadStatus = true;

		if (formfield) {

	        if ($(html).html(html).find('img').length > 0) {
				itemurl = $(html).html(html).find('img').attr('src'); // Use the URL to the size selected.
				itemclass = $(html).html(html).find('img').attr('class'); // Extract the ID from the returned class name.
				itemClassBits = itemclass.split(" ");
				itemid = itemClassBits[itemClassBits.length - 1];
				itemid = itemid.replace('wp-image-', '');
	        } else {
				// It's not an image. Get the URL to the file instead.
				htmlBits = html.split("'"); // jQuery seems to strip out XHTML when assigning the string to an object. Use alternate method.
				itemurl = htmlBits[1]; // Use the URL to the file.
				itemtitle = htmlBits[2];
				itemtitle = itemtitle.replace('>', '');
				itemtitle = itemtitle.replace('</a>', '');
				itemid = ""; // TO DO: Get ID for non-image attachments.
			}

			image = /(jpe?g|png|gif|ico)$/gi;

			if (itemurl.match(image)) {
				uploadStatus = '<div class="img_status"><img src="' + itemurl + '" alt="" /><p><a href="#" class="cmb_remove_file_button" rel="' + formfield + '">Remove Image</a></p></div>';
			} else {
				// No output preview if it's not an image
				// Standard generic output if it's not an image.
				html = '<a href="' + itemurl + '" target="_blank" rel="external">View File</a>';
				uploadStatus = '<div class="no_image"><span class="file_link">' + html + '</span>&nbsp;&nbsp;&nbsp;<a href="#" class="cmb_remove_file_button" rel="' + formfield + '">Remove</a></div>';
			}

			$('#' + formfield).val(itemurl);
			$('#' + formfield + '_id').val(itemid);
			$('#' + formfield).siblings('.cmb_upload_status').slideDown().html(uploadStatus);
			tb_remove();

		} else {
			window.original_send_to_editor(html);
		}

		formfield = '';
	};

	$('.cmb_metabox .control input[type=radio]').live('change', function() {  
		
		var data_value = $(this).data( $(this).data('control') ),
			curr_data_value = $('.datasource_select select').val(),
			data_source = '';
		
		$(".cmb_metabox .gallery_section, li.data-source").hide();
		
		if( curr_data_value !='' && curr_data_value !== undefined )
		{
			data_source = ', li.data-source.'+ curr_data_value;
		}
		else
		{
			data_source = '';
		}
		
		if ( data_value !='none' && data_value.length !== 0 ) {
			$('.cmb_metabox .show_all,.cmb_metabox .show_'+ data_value +', .cmb_metabox .gallery_section.'+ data_value + data_source ).not('.hide_'+data_value+',.none').fadeIn();
		}
		
	});
	
	// Show Data Source on Selection
	$('.datasource_select select').live('change', function()
	{
		var data_value = $(this).val();
		
		$("li.data-source").hide();
		
		if( data_value !='' )
		{
			$('li.data-source.'+ data_value).fadeIn();
		}
	});
	
	
	// Show Data Source on Load
	var curr_data_value = $('.datasource_select select').val(),
		control_value = $('.cmb_metabox .control input[type=radio]:checked').data( $('.cmb_metabox .control input[type=radio]:checked').data('control') );
		
	if( typeof control_value !== 'undefined' && typeof control_value !== 'none' && control_value.length !== 0  )
	{
		$(".cmb_metabox .gallery_section, li.data-source").hide();
		
		if( curr_data_value !='' && curr_data_value !== undefined )
		{
			$('.cmb_metabox .show_all, .cmb_metabox .show_'+ control_value +', .cmb_metabox .gallery_section.'+ control_value +', li.data-source.'+ curr_data_value ).not('.hide_'+ control_value +',.none').fadeIn();
		}
		else if ( curr_data_value !== undefined )
		{
			$("li.data-source").hide();	
			$('.cmb_metabox .show_all,.cmb_metabox .show_'+ control_value +', .cmb_metabox .gallery_section.'+ control_value ).not('.hide_'+ control_value +',.none').fadeIn();
		}
	}
	else
	{
		$(".cmb_metabox .gallery_section, li.data-source").hide();
	}
	
	
	/*$('.wpb-edit-form').bind('DOMNodeInserted', function(e) {
  
		$( '.wpb_vc_param_value.attached_id' ).addClass('sc_data_source');
		
		// Shortcode Data Source
		var element = e.target,
			data_source = $( element ).find('select.data_source').val();

  		
		$( '.sc_data_source' ).closest('div.row-fluid').hide();		
		
		if( data_source !='' )
		{
			$( '.sc_data_source.'+ data_source ).closest('div.row-fluid').show();
		}
	
	});
	

	$('select.data_source').live('change', function()
	{
		var value = $( this ).val();
		
		$( '.sc_data_source.'+ value ).closest('div.row-fluid').show();
			
	});*/
	
});




/*


// Assign Columns to Pricing Table
function assign_columns( element, type )
{
	var $ = jQuery,
		$holder = $( element ),
		plans = $holder.find('.group').length-1,
		cols =  100/plans;
					
	$holder.find('.group').each(function()
	{
		if( type == 'all' || type == 'visual' )
		{
			$(this).css( "width", cols +'%' );
		}
		
		if( type == 'all' || type == 'shortcode' )
		{
			$(this).find('.wpb_vc_param_value.width').val( plans );
		}
	});
}

function wpb_init_pricing_plan_controls($tabs, row) {
    var $ = jQuery;
    $(".column_delete", row.$elementControls).unbind('click').click(function (e) {
        e.preventDefault();
        var answer = confirm(i18nLocale.press_ok_to_delete_section),
			$holder = $(this).closest(".wpb_pricing_table_holder");
			
        if (answer) {	
			$parent = $(this).closest(".group");
            $parent.remove();
            $.wpb_stage.checkIsEmpty();
            $.wpb_stage.sizeRows();
			assign_columns( $holder, 'shortcode' );
			$.jsComposer.save_composer_html();
			assign_columns( $holder, 'visual' );
        }
		
		
    });
    $(".column_clone", row.$elementControls).unbind('click').click(function (e) {
        e.preventDefault();
        var $group = row.$element.closest('.group').clone(false),
			$holder = $(this).closest(".wpb_pricing_table_holder");
		
		
		
        $group.insertAfter(row.$element.closest('.group'));
        var new_row = new $.wpbShortcode($group.find('[data-element_type=plan]'), '1/1', $.wpb_stage);
        new_row.init();
        new_row.initColumn();
        new_row.column._setSortable();
        wpb_init_pricing_plan_controls($tabs, new_row);
		assign_columns( $holder, 'shortcode' );
        save_composer_html();
        $.wpb_stage.sizeRows();
        $tabs.sortable({
                axis:"x",
                handle:"h3",
                stop:function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.children("h3").triggerHandler("focusout");
                    //
                    save_composer_html();
                }
            });

		assign_columns( $holder, 'visual' );
			
    });
}

function wpbPricingPlanInitCallBack(element) {
    element.find('.wpb_pricing_table_holder').not('.wpb_initialized').each(function (index) {
        jQuery(this).addClass('wpb_initialized');
 
        //
        var $tabs,
            that = this,
            $add_btn = jQuery(this).closest('.wpb_element_wrapper').find('.add_tab'),
			$holder = jQuery(this),
        	$tabs = jQuery(that).sortable({
                axis:"x",
                handle:"h3",
                stop:function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.prev().triggerHandler("focusout");
                    // so
                    save_composer_html();
                }
            });
		
		jQuery(this).find('.wpb_plan').each(function(){
            var $ = jQuery,
                row = new $.wpbShortcode($(this), '1/2', $.wpb_stage);
            row.init();
            row.initColumn();
            row.column._setSortable();
            wpb_init_pricing_plan_controls($tabs, row);
            // save_composer_html();
        });
		
        $add_btn.unbind('click').click(function(e){
            var $ = jQuery,
                row = new $.wpbShortcode($(this), '1/1', $.wpb_stage);
			
            e.preventDefault();
            $tabs.append($tabs.find('.wpb_template').html());
			
			assign_columns( $holder, 'shortcode' );
            
            $.log($tabs.find('.wpb_plan:last'));
            row = new $.wpbShortcode( $tabs.find('.wpb_plan:last'), '1/1', $.wpb_stage);
            row.init();
            row.initColumn();
            row.column._setSortable();
            wpb_init_pricing_plan_controls($tabs, row);
            save_composer_html();
			
			assign_columns( $holder, 'visual' );
			
        });
		
		assign_columns( $holder, 'all' );

    });
    // initDroppable();
}
*/

/* SOCIAL ICONS */

function wpb_init_socialwrap_controls($tabs, row) {
    var $ = jQuery;
    $(".column_delete", row.$elementControls).unbind('click').click(function (e) {
        e.preventDefault();
        var answer = confirm(i18nLocale.press_ok_to_delete_section);
			
        if (answer) {	
			$parent = $(this).closest(".wpb_socialicon");
            $parent.remove();
            $.wpb_stage.checkIsEmpty();
            $.wpb_stage.sizeRows();
			$.jsComposer.save_composer_html();
        }
		
		
    });
    $(".column_clone", row.$elementControls).unbind('click').click(function (e) {
        e.preventDefault();
        var $group = row.$element.closest('.wpb_socialicon').clone(false);
		
        $group.insertAfter(row.$element.closest('.wpb_socialicon'));
        var new_row = new $.wpbShortcode($group.find('[data-element_type=socialicon]'), '1/1', $.wpb_stage);
        new_row.init();
        new_row.initColumn();
        new_row.column._setSortable();
        wpb_init_socialwrap_controls($tabs, new_row);
        save_composer_html();
        $.wpb_stage.sizeRows();
        $tabs.sortable({
                axis:"x",
                handle:"h3",
                stop:function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.children("h3").triggerHandler("focusout");
                    //
                    save_composer_html();
                }
            });
			
    });
}

function wpbSocialWrapInitCallBack(element) {
    element.find('.wpb_socialwrap_holder').not('.wpb_initialized').each(function (index) {
        jQuery(this).addClass('wpb_initialized');
 
        //
        var $tabs,
            that = this,
            $add_btn = jQuery(this).closest('.wpb_element_wrapper').find('.add_tab'),
        	$tabs = jQuery(that).sortable({
                axis:"x",
                handle:"h3",
                stop:function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.prev().triggerHandler("focusout");
                    // so
                    save_composer_html();
                }
            });
		
		jQuery(this).find('.wpb_socialicon').each(function(){
            var $ = jQuery,
                row = new $.wpbShortcode($(this), '1/2', $.wpb_stage);
            row.init();
            row.initColumn();
            row.column._setSortable();
            wpb_init_socialwrap_controls($tabs, row);
            // save_composer_html();
        });
		
        $add_btn.unbind('click').click(function(e){
            var $ = jQuery,
                row = new $.wpbShortcode($(this), '1/1', $.wpb_stage);
			
            e.preventDefault();
            $tabs.append($tabs.find('.wpb_template').html());
            
            $.log($tabs.find('.wpb_socialicon:last'));
            row = new $.wpbShortcode( $tabs.find('.wpb_socialicon:last'), '1/1', $.wpb_stage);
            row.init();
            row.initColumn();
            row.column._setSortable();
            wpb_init_socialwrap_controls($tabs, row);
            save_composer_html();

			
        });
	

    });
    // initDroppable();
}// JavaScript Document


jQuery(document).bind("mobileinit", function ()
{
	jQuery.mobile.ajaxEnabled = false;
	jQuery.mobile.ignoreContentEnabled = true;
});	