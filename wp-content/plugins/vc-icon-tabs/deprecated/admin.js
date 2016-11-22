// Custom Icon tab view deprecated

(function ($) {

    $(function(){

   var i18n = window.i18nLocale,
        store = vc.storage,
        Shortcodes = vc.shortcodes;


   var MySettingsView = Backbone.View.extend({
        tagName:'div',
        className:'wpb_bootstrap_modals',
        template:_.template($('#wpb-element-settings-modal-template').html() || '<div></div>'),
        textarea_html_checksum:'',
        dependent_elements:{},
        mapped_params:{},
        events:{
            'click .wpb_save_edit_form':'save',
            // 'click .close':'close',
            'hidden':'remove',
            'hide':'askSaveData',
            'shown':'loadContent'
        },
        content: function() {
          return this.$content;
        },
        window: function() {
          return window;
        },
        initialize:function () {
            var tag = this.model.get('shortcode'),
                params = _.isObject(vc.map[tag]) && _.isArray(vc.map[tag].params) ? vc.map[tag].params : [];
            _.bindAll(this, 'hookDependent');
            this.dependent_elements = {};
            this.mapped_params = {};
            _.each(params, function (param) {
                this.mapped_params[param.param_name] = param;
            }, this);
        },
        render:function () {
            $('body').append(this.$el.html(this.template()));
            this.$content = this.$el.find('.modal-body > div');
            return this;
        },
        initDependency:function () {
            // setup dependencies
            _.each(this.mapped_params, function (param) {
                if (_.isObject(param) && _.isObject(param.dependency) && _.isString(param.dependency.element)) {
                    var $masters = $('[name=' + param.dependency.element + '].wpb_vc_param_value', this.$content),
                        $slave = $('[name= ' + param.param_name + '].wpb_vc_param_value', this.$content);
                    _.each($masters, function (master) {
                        var $master = $(master),
                            rules = param.dependency;
                        if (!_.isArray(this.dependent_elements[$master.attr('name')])) this.dependent_elements[$master.attr('name')] = [];
                        this.dependent_elements[$master.attr('name')].push($slave);
                        $master.bind('keyup change', this.hookDependent);
                        this.hookDependent({currentTarget:$master}, [$slave]);
                        if (_.isString(rules.callback)) {
                            window[rules.callback].call(this);
                        }
                    }, this);
                }
            }, this);
        },
        hookDependent:function (e, dependent_elements) {
            var $master = $(e.currentTarget),
                $master_container = $master.closest('.vc_row-fluid'),
                master_value,
                is_empty;
            dependent_elements = _.isArray(dependent_elements) ? dependent_elements : this.dependent_elements[$master.attr('name')],
            master_value = $master.is(':checkbox') ? _.map(this.$content.find('[name=' + $(e.currentTarget).attr('name') + '].wpb_vc_param_value:checked'),
                    function (element) {
                    return $(element).val();
                })
                    : $master.val();
            is_empty = $master.is(':checkbox') ? !this.$content.find('[name=' + $master.attr('name') + '].wpb_vc_param_value:checked').length
                    : !master_value.length;
            if($master_container.hasClass('vc-dependent-hidden')) {
                _.each(dependent_elements, function($element) {
                    $element.closest('.vc_row-fluid').addClass('vc-dependent-hidden');
                });
            } else {
                _.each(dependent_elements, function ($element) {
                    var param_name = $element.attr('name'),
                        rules = _.isObject(this.mapped_params[param_name]) && _.isObject(this.mapped_params[param_name].dependency) ? this.mapped_params[param_name].dependency : {},
                        $param_block = $element.closest('.vc_row-fluid');
                    if (_.isBoolean(rules.not_empty) && rules.not_empty === true && !is_empty) { // Check is not empty show dependent Element.
                        $param_block.removeClass('vc-dependent-hidden');
                    } else if (_.isBoolean(rules.is_empty) && rules.is_empty === true && is_empty) {
                        $param_block.removeClass('vc-dependent-hidden');
                    } else if (_.intersection((_.isArray(rules.value) ? rules.value : [rules.value]), (_.isArray(master_value) ? master_value : [master_value])).length) {
                        $param_block.removeClass('vc-dependent-hidden');
                    } else {
                        $param_block.addClass('vc-dependent-hidden')
                    }
                    $element.trigger('change');
                }, this);
            }
            return this;
        },
        loadContent:function () {
            $.ajax({
                type:'POST',
                url:window.ajaxurl,
                data:{
                    action:'wpb_show_edit_form',
                    element:this.model.get('shortcode'),
                    post_id: $('#post_ID').val(),
                    shortcode:store.createShortcodeString(this.model.toJSON()) // TODO: do it on server-side
                },
                context:this
            }).done(function (data) {
                    this.$content.html(data);
                    this.$el.find('h3').text(this.$content.find('> [data-title]').data('title'));
                    this.initDependency();
                });
        },
        save:function (e) {

            if (_.isObject(e)) e.preventDefault();
            var params = this.getParams();


              $.ajax({
                type:'POST',
                url:window.ajaxurl,
                data:{
                    action:'vcit_save_post_data',
                    post_id: $('#post_ID').val(),
                    settings:params
                },
                context:this
            }).done(function (data) {
                   console.log(data)
                });

            this.model.save({params:params});
            if(parseInt(Backbone.VERSION)=== 0) {
                this.model.trigger('change:params', this.model);
            }
            this.data_saved = true;
            this.close();
            return this;
        },
        getParams: function() {
            var attributes_settings = this.mapped_params;
            this.params = jQuery.extend(true, {}, this.model.get('params'));
            _.each(attributes_settings, function (param) {
                this.params[param.param_name] = vc.atts.parse.call(this, param);
            }, this);
            _.each(vc.edit_form_callbacks, function(callback){
              callback.call(this);
            }, this);
            return this.params;
        },
        getCurrentParams: function() {
            var attributes_settings = this.mapped_params,
                params = jQuery.extend(true, {}, this.model.get('params'));
            _.each(attributes_settings, function (param) {
                if(_.isUndefined(params[param.param_name])) params[param.param_name] = '';
                if(param.type === "textarea_html") params[param.param_name] = params[param.param_name].replace(/\n/g, '');
            }, this);
            return params;
        },
        show:function () {
            this.render();
            $(window).bind('resize.ModalView', this.setSize);
            this.setSize();
            this.$el.modal('show');
        },
        _killEditor:function () {
            if(!_.isUndefined(window.tinyMCE)) {
                $('textarea.textarea_html', this.$el).each(function () {
                    var id = $(this).attr('id');
                    if(tinymce.majorVersion === "4") {
                      window.tinyMCE.execCommand('mceRemoveEditor', true, id);
                    } else {
                      window.tinyMCE.execCommand("mceRemoveControl", true, id);
                    }
                    // window.tinyMCE.execCommand('mceAddEditor', false, id);
                    // window.tinymce.activeEditor = tinymce.get('content');
                    // $('#wp-fullscreen-save .button').attr('onclick', 'wp.editor.fullscreen.save()').addClass('button-primary');
                });
            }
        },
        dataNotChanged: function() {
            var current_params = this.getCurrentParams(),
                new_params = this.getParams();
            return _.isEqual(current_params, new_params);
        },
        askSaveData:function () {
            if (this.data_saved || this.dataNotChanged() || confirm(window.i18nLocale.if_close_data_lost)) {
                this._killEditor();
                this.data_saved = true;
                $(window).unbind('resize.ModalView');
              return true;
            }
            return false;
        },
        close:function () {
            if (this.askSaveData()) {
                this.$el.modal('hide');
            }
        },
        setSize: function() {
            var height = $(window).height() - 250;
            this.$el.find('.modal-body').css('maxHeight', height);
        }
    });

     window.VcIconTabsView = vc.shortcode_view.extend({
        new_tab_adding:false,
        events:{
            'click .add_tab':'addTab',
            'click > .controls .column_delete':'deleteShortcode',
            'click > .controls .column_edit':'editElement',
            'click > .controls .column_clone':'clone',
             'click .wpb_save_edit_form':'save',
        },
        initialize:function (params) {
            window.VcIconTabsView.__super__.initialize.call(this, params);

            _.bindAll(this, 'stopSorting');

        },
        render:function () {
            window.VcIconTabsView.__super__.render.call(this);
            this.$tabs = this.$el.find('.wpb_tabs_holder');
            this.createAddTabButton();
            return this;
        },
        ready:function (e) {
            window.VcIconTabsView.__super__.ready.call(this, e);
        },
        createAddTabButton:function () {

            var new_tab_button_id = (+new Date() + '-' + Math.floor(Math.random() * 11));
            this.$tabs.append('<div id="new-tab-' + new_tab_button_id + '" class="new_element_button"></div>');
            this.$add_button = $('<li class="add_tab_block"><a href="#new-tab-' + new_tab_button_id + '" class="add_tab" title="' + window.i18nLocale.add_tab + '"></a></li>').appendTo(this.$tabs.find(".tabs_controls"));
        },
        addTab:function (e) {
            e.preventDefault();
            this.new_tab_adding = true;
            var tab_title = this.model.get('shortcode') === 'vc_tour' ? window.i18nLocale.slide : window.i18nLocale.tab,
                tabs_count = this.$tabs.find('[data-element_type=vc_icon_tab]').length,
                tab_id = (+new Date() + '-' + tabs_count + '-' + Math.floor(Math.random() * 11));
            vc.shortcodes.create({shortcode:'vc_icon_tab', params:{title:tab_title, tab_id:tab_id}, parent_id:this.model.id});
            return false;
        },
        stopSorting:function (event, ui) {
            var shortcode;

            this.$tabs.find('ul.tabs_controls li:not(.add_tab_block)').each(function (index) {
                var href = $(this).find('a').attr('href').replace("#", "");
                // $('#' + href).appendTo(this.$tabs);
                shortcode = vc.shortcodes.get($('[id=' + $(this).attr('aria-controls') + ']').data('model-id'));
                vc.storage.lock();
                shortcode.save({'order':$(this).index()}); // Optimize
            });
            shortcode.save();
        },
              editElement:function (e) {
               if (_.isObject(e)) e.preventDefault();
            vc.edit_element_block_view = new MySettingsView({model:this.model});
            vc.edit_element_block_view.show();
              },
        changedContent:function (view) {
            var params = view.model.get('params');
            if (!this.$tabs.hasClass('ui-tabs')) {
                this.$tabs.tabs({
                    select:function (event, ui) {
                        if ($(ui.tab).hasClass('add_tab')) {
                            return false;
                        }
                        return true;
                    }
                });
                this.$tabs.find(".ui-tabs-nav").prependTo(this.$tabs);
                this.$tabs.find(".ui-tabs-nav").sortable({
                    axis:(this.$tabs.closest('[data-element_type]').data('element_type') == 'vc_tour' ? 'y' : 'x'),
                    update:this.stopSorting,
                    items:"> li:not(.add_tab_block)"
                });
            }
            if (view.model.get('cloned') === true) {
                var cloned_from = view.model.get('cloned_from'),
                    $after_tab = $('[href=#tab-' + cloned_from.params.tab_id + ']', this.$content).parent(),
                    $new_tab = $("<li><a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>").insertAfter($after_tab);
                this.$tabs.tabs('refresh');
                this.$tabs.tabs("option", 'active', $new_tab.index());
            } else {
                if(params.icon){
                        $("<li> <i class='icn " + params.icon + "'></i> <a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>")
                    .insertBefore(this.$add_button);
                }else{
                           $("<li><a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>")
                    .insertBefore(this.$add_button);
                }

                this.$tabs.tabs('refresh');
                this.$tabs.tabs("option", "active", this.new_tab_adding ? $('.ui-tabs-nav li', this.$content).length - 2 : 0);

            }
            this.new_tab_adding = false;
        },


        cloneModel:function (model, parent_id, save_order) {
            var shortcodes_to_resort = [],
                new_order = _.isBoolean(save_order) && save_order === true ? model.get('order') : parseFloat(model.get('order')) + vc.clone_index,
                model_clone,
                new_params = _.extend({}, model.get('params'));
             if (model.get('shortcode') === 'vc_icon_tabs') _.extend(new_params, {tab_contid:+new Date() + '-' + Math.floor(Math.random() * 11)});


            if (model.get('shortcode') === 'vc_icon_tab') _.extend(new_params, {tab_id:+new Date() + '-' + this.$tabs.find('[data-element-type=vc_icon_tab]').length + '-' + Math.floor(Math.random() * 11)});
            model_clone = Shortcodes.create({shortcode:model.get('shortcode'), id:vc_guid(), parent_id:parent_id, order:new_order, cloned:(model.get('shortcode') === 'vc_icon_tab' ? false : true), cloned_from:model.toJSON(), params:new_params});
            _.each(Shortcodes.where({parent_id:model.id}), function (shortcode) {
                this.cloneModel(shortcode, model_clone.get('id'), true);
            }, this);
            return model_clone;
        }
    });




window.VcIconTabView = window.VcColumnView.extend({
        render:function () {
            var params = this.model.get('params');
            window.VcIconTabView.__super__.render.call(this);
            if(!params.tab_id) {
              params.tab_id = (+new Date() + '-' + Math.floor(Math.random() * 11));
              this.model.save('params', params);
            }
            this.id = 'tab-' + params.tab_id;
            this.$el.attr('id', this.id);
            return this;
        },
        ready:function (e) {
            window.VcIconTabView.__super__.ready.call(this, e);
            this.$tabs = this.$el.closest('.wpb_tabs_holder');
            var params = this.model.get('params');
            if(params)
            return this;
        },
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.VcAccordionTabView.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params) && _.isString(params.title) && _.isString(params.tab_id)) {
                $('.ui-tabs-nav [href=#tab-' + params.tab_id + ']').text(params.title);
            }


            if(params.icon && model._changing ){
                    $('.ui-tabs-nav [href=#tab-' + params.tab_id + ']').parent().find('i.icn').remove()
                      $('.ui-tabs-nav [href=#tab-' + params.tab_id + ']').parent().prepend(" <i class='icn " + params.icon + "'></i>");

             }

        },

        deleteShortcode:function (e) {
            if (_.isObject(e)) e.preventDefault();
            var answer = confirm(window.i18nLocale.press_ok_to_delete_section);
            if (answer !== true) return false;
            this.model.destroy();
            var params = this.model.get('params'),
                current_tab_index = $('[href=#tab-' + params.tab_id + ']', this.$tabs).parent().index();
            $('[href=#tab-' + params.tab_id + ']').parent().remove();
            this.$tabs.tabs('refresh');
            var tab_length = this.$tabs.find('.ui-tabs-nav li:not(.add_tab_block)').length;
            if (current_tab_index < tab_length) {
                this.$tabs.tabs("option", "active", current_tab_index);
            } else if(tab_length>0) {
                this.$tabs.tabs("option", "active", tab_length-1);
            }
        },
        cloneModel:function (model, parent_id, save_order) {
            var shortcodes_to_resort = [],
                new_order = _.isBoolean(save_order) && save_order === true ? model.get('order') : parseFloat(model.get('order')) + vc.clone_index,
                new_params = _.extend({}, model.get('params'));
            if (model.get('shortcode') === 'vc_icon_tab') _.extend(new_params, {tab_id:+new Date() + '-' + this.$tabs.find('[data-element_type=vc_icon_tab]').length + '-' + Math.floor(Math.random() * 11)});
            var model_clone = Shortcodes.create({shortcode:model.get('shortcode'), parent_id:parent_id, order:new_order, cloned:true, cloned_from:model.toJSON(), params:new_params});
            _.each(Shortcodes.where({parent_id:model.id}), function (shortcode) {
                this.cloneModel(shortcode, model_clone.id, true);
            }, this);
            return model_clone;
        }
    });


    })
})(jQuery)