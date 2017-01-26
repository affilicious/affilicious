!function(a){var b=new Array,c=new Array;a.fn.doAutosize=function(b){var c=a(this).data("minwidth"),d=a(this).data("maxwidth"),e="",f=a(this),g=a("#"+a(this).data("tester_id"));if(e!==(e=f.val())){var h=e.replace(/&/g,"&amp;").replace(/\s/g," ").replace(/</g,"&lt;").replace(/>/g,"&gt;");g.html(h);var i=g.width(),j=i+b.comfortZone>=c?i+b.comfortZone:c,k=f.width(),l=k>j&&j>=c||j>c&&d>j;l&&f.width(j)}},a.fn.resetAutosize=function(b){var c=a(this).data("minwidth")||b.minInputWidth||a(this).width(),d=a(this).data("maxwidth")||b.maxInputWidth||a(this).closest(".tagsinput").width()-b.inputPadding,e=a(this),f=a("<tester/>").css({position:"absolute",top:-9999,left:-9999,width:"auto",fontSize:e.css("fontSize"),fontFamily:e.css("fontFamily"),fontWeight:e.css("fontWeight"),letterSpacing:e.css("letterSpacing"),whiteSpace:"nowrap"}),g=a(this).attr("id")+"_autosize_tester";!a("#"+g).length>0&&(f.attr("id",g),f.appendTo("body")),e.data("minwidth",c),e.data("maxwidth",d),e.data("tester_id",g),e.css("width",c)},a.fn.addTag=function(d,e){return e=jQuery.extend({focus:!1,callback:!0},e),this.each(function(){var f=a(this).attr("id"),g=a(this).val().split(b[f]);if(""==g[0]&&(g=new Array),d=jQuery.trim(d),e.unique){var h=a(this).tagExist(d);1==h&&a("#"+f+"_tag").addClass("not_valid")}else var h=!1;if(""!=d&&1!=h){if(a("<span>").addClass("tag").append(a("<span>").text(d).append("&nbsp;&nbsp;"),a("<a>",{href:"#",title:"Removing tag",text:"x"}).click(function(){return a("#"+f).removeTag(escape(d))})).insertBefore("#"+f+"_addTag"),g.push(d),a("#"+f+"_tag").val(""),e.focus?a("#"+f+"_tag").focus():a("#"+f+"_tag").blur(),a.fn.tagsInput.updateTagsField(this,g),e.callback&&c[f]&&c[f].onAddTag){var i=c[f].onAddTag;i.call(this,d)}if(c[f]&&c[f].onChange){var j=g.length,i=c[f].onChange;i.call(this,a(this),g[j-1])}}}),!1},a.fn.removeTag=function(d){return d=unescape(d),this.each(function(){var e=a(this).attr("id"),f=a(this).val().split(b[e]);for(a("#"+e+"_tagsinput .tag").remove(),str="",i=0;i<f.length;i++)f[i]!=d&&(str=str+b[e]+f[i]);if(a.fn.tagsInput.importTags(this,str),c[e]&&c[e].onRemoveTag){var g=c[e].onRemoveTag;g.call(this,d)}}),!1},a.fn.tagExist=function(c){var d=a(this).attr("id"),e=a(this).val().split(b[d]);return jQuery.inArray(c,e)>=0},a.fn.importTags=function(b){var c=a(this).attr("id");a("#"+c+"_tagsinput .tag").remove(),a.fn.tagsInput.importTags(this,b)},a.fn.tagsInput=function(e){var f=jQuery.extend({interactive:!0,defaultText:"add a tag",minChars:0,width:"300px",height:"100px",autocomplete:{selectFirst:!1},hide:!0,delimiter:",",unique:!0,removeWithBackspace:!0,placeholderColor:"#666666",autosize:!0,comfortZone:20,inputPadding:12},e),g=0;return this.each(function(){if("undefined"==typeof a(this).attr("data-tagsinput-init")){a(this).attr("data-tagsinput-init",!0),f.hide&&a(this).hide();var e=a(this).attr("id");(!e||b[a(this).attr("id")])&&(e=a(this).attr("id","tags"+(new Date).getTime()+g++).attr("id"));var h=jQuery.extend({pid:e,real_input:"#"+e,holder:"#"+e+"_tagsinput",input_wrapper:"#"+e+"_addTag",fake_input:"#"+e+"_tag"},f);b[e]=h.delimiter,(f.onAddTag||f.onRemoveTag||f.onChange)&&(c[e]=new Array,c[e].onAddTag=f.onAddTag,c[e].onRemoveTag=f.onRemoveTag,c[e].onChange=f.onChange);var i='<div id="'+e+'_tagsinput" class="tagsinput"><div id="'+e+'_addTag">';if(f.interactive&&(i=i+'<input id="'+e+'_tag" value="" data-default="'+f.defaultText+'" />'),i+='</div><div class="tags_clear"></div></div>',a(i).insertAfter(this),a(h.holder).css("width",f.width),a(h.holder).css("min-height",f.height),a(h.holder).css("height",f.height),""!=a(h.real_input).val()&&a.fn.tagsInput.importTags(a(h.real_input),a(h.real_input).val()),f.interactive){if(a(h.fake_input).val(a(h.fake_input).attr("data-default")),a(h.fake_input).css("color",f.placeholderColor),a(h.fake_input).resetAutosize(f),a(h.holder).bind("click",h,function(b){a(b.data.fake_input).focus()}),a(h.fake_input).bind("focus",h,function(b){a(b.data.fake_input).val()==a(b.data.fake_input).attr("data-default")&&a(b.data.fake_input).val(""),a(b.data.fake_input).css("color","#000000")}),void 0!=f.autocomplete_url){autocomplete_options={source:f.autocomplete_url};for(attrname in f.autocomplete)autocomplete_options[attrname]=f.autocomplete[attrname];void 0!==jQuery.Autocompleter?(a(h.fake_input).autocomplete(f.autocomplete_url,f.autocomplete),a(h.fake_input).bind("result",h,function(b,c,d){c&&a("#"+e).addTag(c[0]+"",{focus:!0,unique:f.unique})})):void 0!==jQuery.ui.autocomplete&&(a(h.fake_input).autocomplete(autocomplete_options),a(h.fake_input).bind("autocompleteselect",h,function(b,c){return a(b.data.real_input).addTag(c.item.value,{focus:!0,unique:f.unique}),!1}))}else a(h.fake_input).bind("blur",h,function(b){var c=a(this).attr("data-default");return""!=a(b.data.fake_input).val()&&a(b.data.fake_input).val()!=c?b.data.minChars<=a(b.data.fake_input).val().length&&(!b.data.maxChars||b.data.maxChars>=a(b.data.fake_input).val().length)&&a(b.data.real_input).addTag(a(b.data.fake_input).val(),{focus:!0,unique:f.unique}):(a(b.data.fake_input).val(a(b.data.fake_input).attr("data-default")),a(b.data.fake_input).css("color",f.placeholderColor)),!1});a(h.fake_input).bind("keypress",h,function(b){return d(b)?(b.preventDefault(),b.data.minChars<=a(b.data.fake_input).val().length&&(!b.data.maxChars||b.data.maxChars>=a(b.data.fake_input).val().length)&&a(b.data.real_input).addTag(a(b.data.fake_input).val(),{focus:!0,unique:f.unique}),a(b.data.fake_input).resetAutosize(f),!1):void(b.data.autosize&&a(b.data.fake_input).doAutosize(f))}),h.removeWithBackspace&&a(h.fake_input).bind("keydown",function(b){if(8==b.keyCode&&""==a(this).val()){b.preventDefault();var c=a(this).closest(".tagsinput").find(".tag:last").text(),d=a(this).attr("id").replace(/_tag$/,"");c=c.replace(/[\s]+x$/,""),a("#"+d).removeTag(escape(c)),a(this).trigger("focus")}}),a(h.fake_input).blur(),h.unique&&a(h.fake_input).keydown(function(b){(8==b.keyCode||String.fromCharCode(b.which).match(/\w+|[áéíóúÁÉÍÓÚñÑ,/]+/))&&a(this).removeClass("not_valid")})}}}),this},a.fn.tagsInput.updateTagsField=function(c,d){var e=a(c).attr("id");a(c).val(d.join(b[e]))},a.fn.tagsInput.importTags=function(d,e){a(d).val("");var f=a(d).attr("id"),g=e.split(b[f]);for(i=0;i<g.length;i++)a(d).addTag(g[i],{focus:!1,callback:!1});if(c[f]&&c[f].onChange){var h=c[f].onChange;h.call(d,d,g[i])}};var d=function(b){var c=!1;return 13==b.which?!0:("string"==typeof b.data.delimiter?b.which==b.data.delimiter.charCodeAt(0)&&(c=!0):a.each(b.data.delimiter,function(a,d){b.which==d.charCodeAt(0)&&(c=!0)}),c)}}(jQuery);
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

jQuery(function ($) {
    var carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    console.log(window.carbon.fields);

    var CarbonView = function () {
        function CarbonView() {
            _classCallCheck(this, CarbonView);
        }

        _createClass(CarbonView, null, [{
            key: 'getContainerView',
            value: function getContainerView() {
                var containerView = null;

                _.each(carbon.views, function (view) {
                    if (view.model && view.model.attributes && view.model.attributes.title == translations.container) {
                        containerView = view;
                    }
                });

                return containerView;
            }
        }, {
            key: 'getVariantsView',
            value: function getVariantsView() {
                var variantsView = null;

                _.each(carbon.views, function (view) {
                    if (view.templateVariables && view.templateVariables.base_name == '_affilicious_product_variants') {
                        variantsView = view;
                    }
                });

                return variantsView;
            }
        }, {
            key: 'getTypeView',
            value: function getTypeView() {
                var typeView = null;

                _.each(carbon.views, function (view) {
                    if (view.templateVariables && view.templateVariables.base_name == '_affilicious_product_type') {
                        typeView = view;
                    }
                });

                return typeView;
            }
        }, {
            key: 'getEnabledAttributesView',
            value: function getEnabledAttributesView() {
                var enabledAttributesView = null;

                _.each(carbon.views, function (view) {
                    if (view.templateVariables && view.templateVariables.base_name == '_affilicious_product_enabled_attributes') {
                        enabledAttributesView = view;
                    }
                });

                return enabledAttributesView;
            }
        }, {
            key: 'getVariantEnabledAttributesViews',
            value: function getVariantEnabledAttributesViews() {
                var variantEnabledAttributesViews = [];

                _.each(carbon.views, function (view) {
                    if (view.templateVariables && view.templateVariables.base_name == 'enabled_attributes') {
                        variantEnabledAttributesViews.push(view);
                    }
                });

                return variantEnabledAttributesViews;
            }
        }]);

        return CarbonView;
    }();

    var AffiliciousProduct = function () {
        function AffiliciousProduct() {
            _classCallCheck(this, AffiliciousProduct);

            var typeView = CarbonView.getTypeView();
            var variantsView = CarbonView.getVariantsView();
            var enabledAttributesView = CarbonView.getEnabledAttributesView();

            typeView.$el.ready(this.toggleTabs);
            typeView.model.on('change:value', this.toggleTabs);
            variantsView.$el.ready(this.toggleAttributes);
            variantsView.model.on('change:value', this.toggleAttributes);
            enabledAttributesView.$el.ready(this.toggleAttributes);
            enabledAttributesView.model.on('change:value', this.toggleAttributes);
        }

        _createClass(AffiliciousProduct, [{
            key: 'toggleTabs',
            value: function toggleTabs() {
                // Supports multiple languages
                var view = CarbonView.getContainerView(),
                    typeView = CarbonView.getTypeView(),
                    productType = typeView.model.get('value'),
                    variants = view.$el.find('a[data-id="' + translations.variants.trim().toLowerCase() + '"]').parent(),
                    shops = view.$el.find('a[data-id="' + translations.shops.trim().toLowerCase() + '"]').parent();

                if (productType === 'complex') {
                    variants.show();
                    shops.hide();
                } else {
                    variants.hide();
                    shops.show();
                }
            }
        }, {
            key: 'toggleAttributes',
            value: function toggleAttributes() {
                var enabledAttributesView = CarbonView.getEnabledAttributesView(),
                    attributesViews = CarbonView.getVariantEnabledAttributesViews(),
                    value = enabledAttributesView.model.get('value');

                var _iteratorNormalCompletion = true;
                var _didIteratorError = false;
                var _iteratorError = undefined;

                try {
                    for (var _iterator = attributesViews[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                        var attributesView = _step.value;

                        attributesView.model.set('value', value);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally {
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return) {
                            _iterator.return();
                        }
                    } finally {
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        }]);

        return AffiliciousProduct;
    }();

    window.affiliciousProduct = new AffiliciousProduct();
});
'use strict';

window.carbon = window.carbon || {};

(function ($) {
    var carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    // Image Gallery VIEW
    carbon.fields.View.ImageGallery = carbon.fields.View.Image.extend({
        events: _.extend({}, carbon.fields.View.prototype.events, {
            'click .c2_open_media': 'openMedia',
            'click .carbon-image-remove': 'removeImage'
        }),

        initialize: function initialize() {
            carbon.fields.View.prototype.initialize.apply(this);

            this.on('field:beforeRender', this.loadDescriptionTemplate);
            this.on('field:rendered', this.sortableImages);

            this.listenTo(this.model, 'change:images', this.updateInput);
            this.listenTo(this.model, 'change:value', this.render);
        },

        sortableImages: function sortableImages() {
            var _this = this,
                $imageGallery = this.$('ul.carbon-image-gallery');

            // Image ordering.
            $imageGallery.sortable({
                items: 'li.carbon-image',
                cursor: 'move',
                helper: 'clone',
                scrollSensitivity: 42,
                forcePlaceholderSize: true,
                opacity: 0.75,
                forceHelperSize: false,
                placeholder: 'carbon-sortable-placeholder',
                update: function update() {
                    var images = [];

                    $imageGallery.find('li.carbon-image').each(function () {
                        var image = $(this),
                            value = image.attr('data-image-value'),
                            url = image.find('img').attr('src');

                        images.push({
                            value: value,
                            url: url
                        });
                    });

                    _this.model.set('images', images);
                }
            });
        },
        openMedia: function openMedia(event) {
            var _this = this;
            var type = this.model.get('type');
            var images = this.model.get('images');
            var buttonLabel = this.model.get('window_button_label');
            var windowLabel = this.model.get('window_label');
            var typeFilter = this.model.get('type_filter');
            var valueType = this.model.get('value_type');
            var mediaTypes = {};

            mediaTypes[type] = wp.media.frames.crbMediaField = wp.media({
                title: windowLabel ? windowLabel : crbl10n.title,
                library: { type: typeFilter }, // audio, video, image
                button: { text: buttonLabel },
                multiple: true
            });

            var mediaField = mediaTypes[type];

            // Runs when an image is selected.
            mediaField.on('select', function () {
                var selections = mediaField.state().get('selection').toJSON();
                var images = _.clone(_this.model.get('images'));

                _.each(selections, function (selection) {
                    images.push({
                        'value': selection[valueType],
                        'url': selection.url
                    });
                });

                _this.model.set('images', images);
            });

            // Opens the media library frame
            mediaField.open();

            event.preventDefault();
        },

        updateInput: function updateInput(model) {
            console.log(model);
            var $input = this.$('input.carbon-image-gallery-field'),
                images = model.get('images'),
                imageValues = _.map(images, function (image) {
                return image.value;
            }),
                value = imageValues.join();

            $input.val(value).trigger('change');
        },

        removeImage: function removeImage(event) {
            var $target = this.$(event.currentTarget),
                $selections = $target.parents('ul.carbon-image-gallery').children(),
                $selection = $target.parents('li.carbon-image'),
                images = _.clone(this.model.get('images')),
                index = $selections.index($selection);

            // Remove the image (splice is not working here, even with _.clone)
            var result = [];
            for (var i = 0; i < images.length; i++) {
                if (i != index) {
                    result.push(images[i]);
                }
            }

            this.model.set('images', result);
        }
    });
})(jQuery);
'use strict';

window.carbon = window.carbon || {};

(function ($) {
    var carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    carbon.fields.Model.Tags = carbon.fields.Model.extend({
        initialize: function initialize() {
            carbon.fields.Model.prototype.initialize.apply(this); // do not delete
        }
    });

    carbon.fields.View.Tags = carbon.fields.View.extend({
        initialize: function initialize() {
            // Initialize the parent view
            carbon.fields.View.prototype.initialize.apply(this); // do not delete

            // Wait for the field to be added to the DOM and run an init method
            this.on('field:rendered', this.initField);
        },

        initField: function initField() {
            var self = this;

            $('.aff-tags').tagsInput({
                'width': '100%',
                'height': 'auto',
                'defaultText': translations.addTag,
                'interactive': true,
                'delimiter': ';',
                'minChars': 1,
                'maxChars': 100,
                'placeholderColor': '#666666',
                'onChange': function onChange() {
                    self.model.set('value', $('input[name="' + self.templateVariables.name + '"]').val());
                }
            });
        }
    });
})(jQuery);
'use strict';

jQuery(function ($) {
    // TODO: Remove the code below in the beta
    var product_gallery_frame;
    var $image_gallery_ids = $('#product_image_gallery');
    var $product_images = $('#product_images_container').find('ul.product_images');

    $('.add_product_images').on('click', 'a', function (event) {
        var $el = $(this);

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (product_gallery_frame) {
            product_gallery_frame.open();
            return;
        }

        // Create the media frame.
        product_gallery_frame = wp.media.frames.product_gallery = wp.media({
            // Set the title of the modal.
            title: $el.data('choose'),
            button: {
                text: $el.data('update')
            },
            states: [new wp.media.controller.Library({
                title: $el.data('choose'),
                filterable: 'all',
                multiple: true
            })]
        });

        // When an image is selected, run a callback.
        product_gallery_frame.on('select', function () {
            var selection = product_gallery_frame.state().get('selection');
            var attachment_ids = $image_gallery_ids.val();

            selection.map(function (attachment) {
                attachment = attachment.toJSON();

                if (attachment.id) {
                    attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $product_images.append('<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>');
                }
            });

            $image_gallery_ids.val(attachment_ids);
        });

        // Finally, open the modal.
        product_gallery_frame.open();
    });

    // Image ordering.
    $product_images.sortable({
        items: 'li.image',
        cursor: 'move',
        scrollSensitivity: 40,
        forcePlaceholderSize: true,
        forceHelperSize: false,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'wc-metabox-sortable-placeholder',
        start: function start(event, ui) {
            ui.item.css('background-color', '#f6f6f6');
        },
        stop: function stop(event, ui) {
            ui.item.removeAttr('style');
        },
        update: function update() {
            var attachment_ids = '';

            $('#product_images_container').find('ul li.image').css('cursor', 'default').each(function () {
                var attachment_id = $(this).attr('data-attachment_id');
                attachment_ids = attachment_ids + attachment_id + ',';
            });

            $image_gallery_ids.val(attachment_ids);
        }
    });

    // Remove images.
    $('#product_images_container').on('click', 'a.delete', function () {
        $(this).closest('li.image').remove();

        var attachment_ids = '';

        $('#product_images_container').find('ul li.image').css('cursor', 'default').each(function () {
            var attachment_id = $(this).attr('data-attachment_id');
            attachment_ids = attachment_ids + attachment_id + ',';
        });

        $image_gallery_ids.val(attachment_ids);

        // Remove any lingering tooltips.
        $('#tiptip_holder').removeAttr('style');
        $('#tiptip_arrow').removeAttr('style');

        return false;
    });
});