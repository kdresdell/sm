// JavaScript Document   // themeva_mod

(function ($) {

   var Shortcodes = vc.shortcodes;

    window.StyledBoxView = vc.shortcode_view.extend({
        change_columns_layout:false,
        events:{
            'click > .controls .column_delete':'deleteShortcode',
            'click > .controls .set_columns':'setColumns',
            'click > .controls .column_add':'addElement',
            'click > .controls .column_edit':'editElement',
            'click > .controls .column_clone':'clone',
            'click > .controls .column_move':'moveElement'
        },
        _convertStyledBoxColumns:function (layout) {
            var layout_split = layout.toString().split(/\_/),
                columns = Shortcodes.where({parent_id:this.model.id}),
                new_columns = [],
                new_width = '';
            _.each(layout_split, function (value, i) {
                var column_data = _.map(value.toString().split(''), function (v, i) {
                        return parseInt(v, 10);
                    }),
                    new_column_params, new_column;
                if(column_data.length > 3)
                    new_width = column_data[0] + '' + column_data[1] + '/' + column_data[2] + '' + column_data[3];
                else if(column_data.length > 2)
                    new_width = column_data[0] + '/' + column_data[1] + '' + column_data[2];
                else
                    new_width = column_data[0] + '/' + column_data[1];
                new_column_params = _.extend(!_.isUndefined(columns[i]) ? columns[i].get('params') : {}, {width: new_width}),
                vc.storage.lock();
      
				  new_column = Shortcodes.create({shortcode:( this.model.get('shortcode')==='styledbox' ?  'vc_column_inner' : 'vc_column'), params:new_column_params, parent_id:this.model.id}); // _themeva_mod added: || this.model.get('shortcode')==='styledbox'              	  
				if (_.isObject(columns[i])) {
                    _.each(Shortcodes.where({parent_id:columns[i].id}), function (shortcode) {
                        vc.storage.lock();
                        shortcode.save({parent_id:new_column.id});
                        vc.storage.lock();
                        shortcode.trigger('change_parent_id');
                    });
                }
                new_columns.push(new_column);
            }, this);
            if (layout_split.length < columns.length) {
                _.each(columns.slice(layout_split.length), function (column) {
                    _.each(Shortcodes.where({parent_id:column.id}), function (shortcode) {
                        vc.storage.lock();
                        shortcode.save({'parent_id':_.last(new_columns).id});
                        vc.storage.lock();
                        shortcode.trigger('change_parent_id');
                    });
                });
            }
            _.each(columns, function (shortcode) {
                vc.storage.lock();
                shortcode.destroy();
            }, this);
            this.model.save();
            // this.sizeRows();
            return false;
        },
        changeShortcodeParams:function (model) {
          var image, params = model.get('params'),
              $column_edit = this.$el.find('> .controls .column_edit');
          window.StyledBoxView.__super__.changeShortcodeParams.call(this, model);
          this.$el.find('> .controls .vc_row_color').remove();
          this.$el.find('> .controls .vc_row_image').remove();
          if(!_.isEmpty(params.bg_color)) {
              $('<span class="vc_row_color" style="background-color: ' + params.bg_color + '" title="' + i18nLocale.row_background_color + '"></span>').insertAfter($column_edit);
          }
          if(!_.isEmpty(params.bg_image)) {
            image = $('<span class="vc_row_image" style="background-image: url(' + $('.vc_loading_block > img').attr('src') + ');" title="' + i18nLocale.row_background_image + '"></span>')
            image.insertAfter($column_edit);
            $.ajax({
              type:'POST',
              url:window.ajaxurl,
              data:{
                action:'wpb_single_image_src',
                content: params.bg_image,
                size: 'thumbnail'
              },
              dataType:'html'
            }).done(function (url) {
                image.css({backgroundImage: 'url(' + url + ')'});
              });
          } else {

          }

        },
        _getCurrentLayoutString: function() {
            var layouts = [];
            $('> .wpb_vc_column, > .wpb_vc_column_inner', this.$content).each(function () {
                var width = $(this).data('width');
                layouts.push(_.isUndefined(width) ? '1/1' : width);
            });
            return layouts.join(' + ');
        },
        setSorting:function () {
            var that = this;
            if (this.$content.find("> [data-element_type=vc_column], > [data-element_type=vc_column_inner]").length > 1) {
                this.$content.removeClass('wpb-not-sortable').sortable({
                    forcePlaceholderSize:true,
                    placeholder:"widgets-placeholder-column",
                    tolerance:"pointer",
                    // cursorAt: { left: 10, top : 20 },
                    cursor:"move",
                    //handle: '.controls',
                    items:"> [data-element_type=vc_column], > [data-element_type=vc_column_inner]", //wpb_sortablee
                    distance:0.5,
                    start:function (event, ui) {
                        $('#visual_composer_content').addClass('sorting-started');
                        ui.placeholder.width(ui.item.width());
                    },
                    stop:function (event, ui) {
                        $('#visual_composer_content').removeClass('sorting-started');
                    },
                    update:function () {
                        var $columns = $("> [data-element_type=vc_column], > [data-element_type=vc_column_inner]", that.$content);
                        $columns.each(function () {
                            var model = $(this).data('model'),
                                index = $(this).index();
                            model.set('order', index);
                            if ($columns.length - 1 > index) vc.storage.lock();
                            model.save();
                        });
                    },
                    over:function (event, ui) {
                        ui.placeholder.css({maxWidth:ui.placeholder.parent().width()});
                        ui.placeholder.removeClass('hidden-placeholder');
                        // if (ui.item.hasClass('not-column-inherit') && ui.placeholder.parent().hasClass('not-column-inherit')) {
                        //     ui.placeholder.addClass('hidden-placeholder');
                        // }
                    },
                    beforeStop:function (event, ui) {
                        // if (ui.item.hasClass('not-column-inherit') && ui.placeholder.parent().hasClass('not-column-inherit')) {
                        //     return false;
                        // }
                    }
                });
            } else {
                if (this.$content.hasClass('ui-sortable')) this.$content.sortable('destroy');
                this.$content.addClass('wpb-not-sortable');
            }
        },
        validateCellsList: function(cells) {
            var return_cells = [],
                split = cells.replace(/\s/g, '').split('+'),
                b;
            var sum = _.reduce(_.map(split, function(c){
                if(c.match(/^[vc\_]{0,1}span\d{1,2}$/)) {
                    var converted_c = vc_convert_column_span_size(c);
                    if(converted_c===false) return 1000;
                    b = converted_c.split(/\//);
                    return_cells.push(b[0] + '' + b[1]);
                    return 12*parseInt(b[0], 10)/parseInt(b[1], 10);
                } else if(c.match(/^[1-9]|1[0-2]\/[1-9]|1[0-2]$/)) {
                    b = c.split(/\//);
                    return_cells.push(b[0] + '' + b[1]);
                    return 12*parseInt(b[0], 10)/parseInt(b[1], 10);
                }
                return 10000;

            }), function(num, memo) {
                memo = memo + num;
                return memo;
            }, 0);
            if(sum!==12) return false;
            return return_cells.join('_');
        },
        setColumns:function (e) {
            if (_.isObject(e)) e.preventDefault();
            var $button = $(e.currentTarget);
            if($button.data('cells')==='custom') {
                var cells = window.prompt(window.i18nLocale.enter_custom_layout, this._getCurrentLayoutString());
                if(_.isString(cells)) {
                    if((cells = this.validateCellsList(cells))!==false) {
                        this.change_columns_layout = true;
                        this._convertStyledBoxColumns(cells);
                        this.$el.find('> .controls .vc_active').removeClass('vc_active');
                        $button.addClass('vc_active');
                    } else {
                        window.alert(window.i18nLocale.wrong_cells_layout);
                    }
                }
                return;
            }
            if(vc.is_mobile) {
              var $parent = $button.parent();
              if(!$parent.hasClass('vc_visible')) {
                $parent.addClass('vc_visible');
                $(document).bind('click.vcRowColumnsControl', function(e){
                  $parent.removeClass('vc_visible');
                  $(document).unbind('click.vcRowColumnsControl');
                });
              }
            }
            if ($button.is('.vc_active')) {
              return false;
            }

            this.$el.find('> .controls .vc_active').removeClass('vc_active');
            $button.addClass('vc_active');
            this.change_columns_layout = true;
                _.defer(function (view, cells) {
                    view._convertStyledBoxColumns(cells);
                }, this, $button.data('cells'));
        },
        sizeRows:function () {
            var max_height = 35;
            $('> .wpb_vc_column, > .wpb_vc_column_inner', this.$content).each(function () {
                var content_height = $(this).find('> .wpb_element_wrapper > .wpb_column_container').css({minHeight:0}).height();
                if (content_height > max_height) max_height = content_height;
            }).each(function () {
                    $(this).find('> .wpb_element_wrapper > .wpb_column_container').css({minHeight:max_height });
                });
        },
        ready:function (e) {
            window.StyledBoxView.__super__.ready.call(this, e);
            return this;
        },
        checkIsEmpty:function () {
            window.StyledBoxView.__super__.checkIsEmpty.call(this);
            this.setSorting();
        },
        changedContent:function (view) {
            this.sizeRows();
            if (this.change_columns_layout) return this;
            var column_layout = [];
            $('> .wpb_vc_column, > .wpb_vc_column_inner', this.$content).each(function () {
                var width = $(this).data('width');
                column_layout.push(_.isUndefined(width) ? '11' : width.replace('/', ''));
            });
            this.$el.find('> .controls .vc_active').removeClass('vc_active');
            var $button = this.$el.find('> .controls [data-cells-mask=' + vc_get_column_mask(column_layout.join('_')) + ']');
            if($button.length) {
               $button.addClass('vc_active');
            } else {
                this.$el.find('> .controls [data-cells-mask=custom]').addClass('vc_active');
            }
            this.sizeRows();
        },
        moveElement:function (e) {
            e.preventDefault();
        }
    });   
   
   window.PricingTableView = vc.shortcode_view.extend({
        adding_new_tab: false,
        events: {
                'click .add_plan': 'addTab',
                'click > .controls .column_delete': 'deleteShortcode',
                'click > .controls .column_edit': 'editElement',
                'click > .controls .column_clone': 'clone'
        },
        render: function() {
            window.PricingTableView.__super__.render.call(this);
            this.$content.sortable({
                axis:"x",
                handle:"h4",
                stop:function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.prev().triggerHandler("focusout");
                    $(this).find('> .wpb_plan').each(function(){
                        var shortcode = $(this).data('model');
                        shortcode.save({'order': $(this).index()}); // Optimize
                    });
                }
            });
            return this;
        },
        changeShortcodeParams: function(model) {
            window.PricingTableView.__super__.changeShortcodeParams.call(this, model);	
        },
        updateColumns:function (model) {
        
            var params = this.model.get('params'),
				column_value = this.$content.find('.wpb_plan').length,
				column_width = 100/column_value;
				
				this.$content.find('.wpb_plan').css('width',column_width+'%');
  
			params.columns = String(column_value); // Sign  a new value for columns attribute
            this.model.save({params:params});
    
        },		
        changedContent: function(view) {
            this.adding_new_tab = false;
        },
        addTab: function(e) {
            this.adding_new_tab = true;
            e.preventDefault();
			
            vc.shortcodes.create({shortcode: 'plan', params: {title: 'New Plan',content: '<ul><li>List Item</li><li>List Item</li></ul>'}, parent_id: this.model.id});
			this.updateColumns(this.model);	
        },		
        _loadDefaults: function() {
            window.PricingTableView.__super__._loadDefaults.call(this);
        }
    });


   window.PlanView = vc.shortcode_view.extend({
        adding_new_tab: false,
        events: {
                'click > .controls .column_delete': 'deleteShortcode',
                'click > .controls .column_edit': 'editElement',
                'click > .controls .column_clone': 'clone'
        },	
        changeShortcodeParams: function(model) {
            window.PlanView.__super__.changeShortcodeParams.call(this, model);

			var parent_id = vc.app.views[this.model.get('parent_id')];
			this.updateColumns(parent_id);									
        },	
        updateColumns:function (id) {
			var params = id.model.get('params'),
				column_value = id.$content.find('.wpb_plan').length,
				column_width = 100/column_value;
				
			id.$content.find('.wpb_plan').css('width',column_width+'%');
			
			params.columns = String(column_value); // Sign  a new value for columns attribute	
			id.model.save({params:params});
        },			
        deleteShortcode: function(e) {
            if(_.isObject(e)) e.preventDefault();
            var answer = confirm(window.i18nLocale.press_ok_to_delete_section);
            if (answer!==true) return false;
            this.model.destroy();
			
			var parent_id = vc.app.views[this.model.get('parent_id')];
			this.updateColumns(parent_id);		
        },				
        _loadDefaults: function() {
            window.PlanView.__super__._loadDefaults.call(this);
        }
    });	

   window.SocialWrapView = vc.shortcode_view.extend({
        adding_new_tab: false,
        events: {
                'click .add_social': 'addTab',
                'click > .controls .column_delete': 'deleteShortcode',
                'click > .controls .column_edit': 'editElement',
                'click > .controls .column_clone': 'clone'
        },
        render: function() {
            window.SocialWrapView.__super__.render.call(this);
            this.$content.sortable({
                axis:"x,y",
                handle:"h4",
                stop:function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.prev().triggerHandler("focusout");
                    $(this).find('> .wpb_socialicon').each(function(){
                        var shortcode = $(this).data('model');
                        shortcode.save({'order': $(this).index()}); // Optimize
                    });
                }
            });
            return this;
        },
        changedContent: function(view) {
            this.adding_new_tab = false;
        },		
        changeShortcodeParams: function(model) {
            window.SocialWrapView.__super__.changeShortcodeParams.call(this, model);			
        },
        addTab: function(e) {
            this.adding_new_tab = true;
            e.preventDefault();
            vc.shortcodes.create({shortcode: 'socialicon', params: {name: 'Edit Me'}, parent_id: this.model.id});		
        },		
        _loadDefaults: function() {
            window.SocialWrapView.__super__._loadDefaults.call(this);
        }
    });		
	
})(window.jQuery);