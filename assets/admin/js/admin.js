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