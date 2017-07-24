(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

jQuery(function ($) {
    var carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    var CarbonView = function () {
        function CarbonView() {
            _classCallCheck(this, CarbonView);
        }

        _createClass(CarbonView, null, [{
            key: 'getContainerView',
            value: function getContainerView() {
                var containerView = null;

                _.each(carbon.views, function (view) {
                    if (view.model && view.model.attributes && view.model.attributes.title == affProductTranslations.container) {
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

            if (!!typeView) {
                typeView.$el.ready(this.toggleTabs);
                typeView.model.on('change:value', this.toggleTabs);
            }

            if (!!variantsView) {
                variantsView.$el.ready(this.toggleAttributes);
                variantsView.model.on('change:value', this.toggleAttributes);
            }

            if (!!enabledAttributesView) {
                enabledAttributesView.$el.ready(this.toggleAttributes);
                enabledAttributesView.model.on('change:value', this.toggleAttributes);
            }
        }

        _createClass(AffiliciousProduct, [{
            key: 'toggleTabs',
            value: function toggleTabs() {
                // Supports multiple languages
                var view = CarbonView.getContainerView(),
                    typeView = CarbonView.getTypeView(),
                    productType = typeView.model.get('value'),
                    variants = view.$el.find('a[data-id="' + affProductTranslations.variants.trim().toLowerCase() + '"]').parent(),
                    shops = view.$el.find('a[data-id="' + affProductTranslations.shops.trim().toLowerCase() + '"]').parent();

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
                        attributesView.$el.find('input').val(value);
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

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL3Byb2R1Y3RzL2pzL3Byb2R1Y3RzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7Ozs7O0FDQUEsT0FBTyxVQUFTLENBQVQsRUFBWTtBQUNmLFFBQU0sU0FBUyxPQUFPLE1BQXRCO0FBQ0EsUUFBSSxPQUFPLE9BQU8sTUFBZCxLQUF5QixXQUE3QixFQUEwQztBQUN0QyxlQUFPLEtBQVA7QUFDSDs7QUFKYyxRQU1ULFVBTlM7QUFBQTtBQUFBO0FBQUE7O0FBQUE7QUFBQTtBQUFBLCtDQVFlO0FBQ3RCLG9CQUFJLGdCQUFnQixJQUFwQjs7QUFFQSxrQkFBRSxJQUFGLENBQU8sT0FBTyxLQUFkLEVBQXFCLFVBQVMsSUFBVCxFQUFlO0FBQ2hDLHdCQUFHLEtBQUssS0FBTCxJQUFjLEtBQUssS0FBTCxDQUFXLFVBQXpCLElBQXVDLEtBQUssS0FBTCxDQUFXLFVBQVgsQ0FBc0IsS0FBdEIsSUFBK0IsdUJBQXVCLFNBQWhHLEVBQTJHO0FBQ3ZHLHdDQUFnQixJQUFoQjtBQUNIO0FBQ0osaUJBSkQ7O0FBTUEsdUJBQU8sYUFBUDtBQUNIO0FBbEJVO0FBQUE7QUFBQSw4Q0FvQmM7QUFDckIsb0JBQUksZUFBZSxJQUFuQjs7QUFFQSxrQkFBRSxJQUFGLENBQU8sT0FBTyxLQUFkLEVBQXFCLFVBQVMsSUFBVCxFQUFlO0FBQ2hDLHdCQUFHLEtBQUssaUJBQUwsSUFBMEIsS0FBSyxpQkFBTCxDQUF1QixTQUF2QixJQUFvQywrQkFBakUsRUFBa0c7QUFDOUYsdUNBQWUsSUFBZjtBQUNIO0FBQ0osaUJBSkQ7O0FBTUEsdUJBQU8sWUFBUDtBQUNIO0FBOUJVO0FBQUE7QUFBQSwwQ0FnQ1U7QUFDakIsb0JBQUksV0FBVyxJQUFmOztBQUVBLGtCQUFFLElBQUYsQ0FBTyxPQUFPLEtBQWQsRUFBcUIsVUFBUyxJQUFULEVBQWU7QUFDaEMsd0JBQUcsS0FBSyxpQkFB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QUFuQixDQUF3QixjQUF4Qjs7QUFFQTtBQUNBLFVBQUcsZ0JBQUgsRUFBc0IsVUFBdEIsQ0FBa0MsT0FBbEM7QUFDQSxVQUFHLGVBQUgsRUFBcUIsVUFBckIsQ0FBaUMsT0FBakM7O0FBRUEsZUFBTyxLQUFQO0FBQ0gsS0FqQkQ7QUFrQkgsQ0F2R0QiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwialF1ZXJ5KGZ1bmN0aW9uKCQpIHtcbiAgICBjb25zdCBjYXJib24gPSB3aW5kb3cuY2FyYm9uO1xuICAgIGlmICh0eXBlb2YgY2FyYm9uLmZpZWxkcyA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIGNsYXNzIENhcmJvblZpZXdcbiAgICB7XG4gICAgICAgIHN0YXRpYyBnZXRDb250YWluZXJWaWV3KCkge1xuICAgICAgICAgICAgdmFyIGNvbnRhaW5lclZpZXcgPSBudWxsO1xuXG4gICAgICAgICAgICBfLmVhY2goY2FyYm9uLnZpZXdzLCBmdW5jdGlvbih2aWV3KSB7XG4gICAgICAgICAgICAgICAgaWYodmlldy5tb2RlbCAmJiB2aWV3Lm1vZGVsLmF0dHJpYnV0ZXMgJiYgdmlldy5tb2RlbC5hdHRyaWJ1dGVzLnRpdGxlID09IGFmZlByb2R1Y3RUcmFuc2xhdGlvbnMuY29udGFpbmVyKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnRhaW5lclZpZXcgPSB2aWV3O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICByZXR1cm4gY29udGFpbmVyVmlldztcbiAgICAgICAgfVxuXG4gICAgICAgIHN0YXRpYyBnZXRWYXJpYW50c1ZpZXcoKSB7XG4gICAgICAgICAgICB2YXIgdmFyaWFudHNWaWV3ID0gbnVsbDtcblxuICAgICAgICAgICAgXy5lYWNoKGNhcmJvbi52aWV3cywgZnVuY3Rpb24odmlldykge1xuICAgICAgICAgICAgICAgIGlmKHZpZXcudGVtcGxhdGVWYXJpYWJsZXMgJiYgdmlldy50ZW1wbGF0ZVZhcmlhYmxlcy5iYXNlX25hbWUgPT0gJ19hZmZpbGljaW91c19wcm9kdWN0X3ZhcmlhbnRzJykge1xuICAgICAgICAgICAgICAgICAgICB2YXJpYW50c1ZpZXcgPSB2aWV3O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICByZXR1cm4gdmFyaWFudHNWaWV3O1xuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIGdldFR5cGVWaWV3KCkge1xuICAgICAgICAgICAgdmFyIHR5cGVWaWV3ID0gbnVsbDtcblxuICAgICAgICAgICAgXy5lYWNoKGNhcmJvbi52aWV3cywgZnVuY3Rpb24odmlldykge1xuICAgICAgICAgICAgICAgIGlmKHZpZXcudGVtcGxhdGVWYXJpYWJsZXMgJiYgdmlldy50ZW1wbGF0ZVZhcmlhYmxlcy5iYXNlX25hbWUgPT0gJ19hZmZpbGljaW91c19wcm9kdWN0X3R5cGUnKSB7XG4gICAgICAgICAgICAgICAgICAgIHR5cGVWaWV3ID0gdmlldztcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgcmV0dXJuIHR5cGVWaWV3O1xuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIGdldEVuYWJsZWRBdHRyaWJ1dGVzVmlldygpIHtcbiAgICAgICAgICAgIHZhciBlbmFibGVkQXR0cmlidXRlc1ZpZXcgPSBudWxsO1xuXG4gICAgICAgICAgICBfLmVhY2goY2FyYm9uLnZpZXdzLCBmdW5jdGlvbih2aWV3KSB7XG4gICAgICAgICAgICAgICAgaWYodmlldy50ZW1wbGF0ZVZhcmlhYmxlcyAmJiB2aWV3LnRlbXBsYXRlVmFyaWFibGVzLmJhc2VfbmFtZSA9PSAnX2FmZmlsaWNpb3VzX3Byb2R1Y3RfZW5hYmxlZF9hdHRyaWJ1dGVzJykge1xuICAgICAgICAgICAgICAgICAgICBlbmFibGVkQXR0cmlidXRlc1ZpZXcgPSB2aWV3O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICByZXR1cm4gZW5hYmxlZEF0dHJpYnV0ZXNWaWV3O1xuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIGdldFZhcmlhbnRFbmFibGVkQXR0cmlidXRlc1ZpZXdzKCkge1xuICAgICAgICAgICAgdmFyIHZhcmlhbnRFbmFibGVkQXR0cmlidXRlc1ZpZXdzID0gW107XG5cbiAgICAgICAgICAgIF8uZWFjaChjYXJib24udmlld3MsIGZ1bmN0aW9uKHZpZXcpIHtcbiAgICAgICAgICAgICAgICBpZih2aWV3LnRlbXBsYXRlVmFyaWFibGVzICYmIHZpZXcudGVtcGxhdGVWYXJpYWJsZXMuYmFzZV9uYW1lID09ICdlbmFibGVkX2F0dHJpYnV0ZXMnKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhcmlhbnRFbmFibGVkQXR0cmlidXRlc1ZpZXdzLnB1c2godmlldyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiB2YXJpYW50RW5hYmxlZEF0dHJpYnV0ZXNWaWV3cztcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGNsYXNzIEFmZmlsaWNpb3VzUHJvZHVjdCB7XG4gICAgICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICAgICAgbGV0IHR5cGVWaWV3ID0gQ2FyYm9uVmlldy5nZXRUeXBlVmlldygpO1xuICAgICAgICAgICAgbGV0IHZhcmlhbnRzVmlldyA9IENhcmJvblZpZXcuZ2V0VmFyaWFudHNWaWV3KCk7XG4gICAgICAgICAgICBsZXQgZW5hYmxlZEF0dHJpYnV0ZXNWaWV3ID0gQ2FyYm9uVmlldy5nZXRFbmFibGVkQXR0cmlidXRlc1ZpZXcoKTtcblxuICAgICAgICAgICAgaWYoISF0eXBlVmlldykge1xuICAgICAgICAgICAgICAgIHR5cGVWaWV3LiRlbC5yZWFkeSh0aGlzLnRvZ2dsZVRhYnMpO1xuICAgICAgICAgICAgICAgIHR5cGVWaWV3Lm1vZGVsLm9uKCdjaGFuZ2U6dmFsdWUnLCB0aGlzLnRvZ2dsZVRhYnMpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZighIXZhcmlhbnRzVmlldykge1xuICAgICAgICAgICAgICAgIHZhcmlhbnRzVmlldy4kZWwucmVhZHkodGhpcy50b2dnbGVBdHRyaWJ1dGVzKTtcbiAgICAgICAgICAgICAgICB2YXJpYW50c1ZpZXcubW9kZWwub24oJ2NoYW5nZTp2YWx1ZScsIHRoaXMudG9nZ2xlQXR0cmlidXRlcyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCEhZW5hYmxlZEF0dHJpYnV0ZXNWaWV3KSB7XG4gICAgICAgICAgICAgICAgZW5hYmxlZEF0dHJpYnV0ZXNWaWV3LiRlbC5yZWFkeSh0aGlzLnRvZ2dsZUF0dHJpYnV0ZXMpO1xuICAgICAgICAgICAgICAgIGVuYWJsZWRBdHRyaWJ1dGVzVmlldy5tb2RlbC5vbignY2hhbmdlOnZhbHVlJywgdGhpcy50b2dnbGVBdHRyaWJ1dGVzKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRvZ2dsZVRhYnMoKSB7XG4gICAgICAgICAgICAvLyBTdXBwb3J0cyBtdWx0aXBsZSBsYW5ndWFnZXNcbiAgICAgICAgICAgIHZhciB2aWV3ID0gQ2FyYm9uVmlldy5nZXRDb250YWluZXJWaWV3KCksXG4gICAgICAgICAgICAgICAgdHlwZVZpZXcgPSBDYXJib25WaWV3LmdldFR5cGVWaWV3KCksXG4gICAgICAgICAgICAgICAgcHJvZHVjdFR5cGUgPSB0eXBlVmlldy5tb2RlbC5nZXQoJ3ZhbHVlJyksXG4gICAgICAgICAgICAgICAgdmFyaWFudHMgPSB2aWV3LiRlbC5maW5kKCdhW2RhdGEtaWQ9XCInICsgYWZmUHJvZHVjdFRyYW5zbGF0aW9ucy52YXJpYW50cy50cmltKCkudG9Mb3dlckNhc2UoKSArICdcIl0nKS5wYXJlbnQoKSxcbiAgICAgICAgICAgICAgICBzaG9wcyA9IHZpZXcuJGVsLmZpbmQoJ2FbZGF0YS1pZD1cIicgKyBhZmZQcm9kdWN0VHJhbnNsYXRpb25zLnNob3BzLnRyaW0oKS50b0xvd2VyQ2FzZSgpICsgJ1wiXScpLnBhcmVudCgpO1xuXG4gICAgICAgICAgICBpZihwcm9kdWN0VHlwZSA9PT0gJ2NvbXBsZXgnKSB7XG4gICAgICAgICAgICAgICAgdmFyaWFudHMuc2hvdygpO1xuICAgICAgICAgICAgICAgIHNob3BzLmhpZGUoKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdmFyaWFudHMuaGlkZSgpO1xuICAgICAgICAgICAgICAgIHNob3BzLnNob3coKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRvZ2dsZUF0dHJpYnV0ZXMoKSB7XG4gICAgICAgICAgICBsZXQgZW5hYmxlZEF0dHJpYnV0ZXNWaWV3ID0gQ2FyYm9uVmlldy5nZXRFbmFibGVkQXR0cmlidXRlc1ZpZXcoKSxcbiAgICAgICAgICAgICAgICBhdHRyaWJ1dGVzVmlld3MgPSBDYXJib25WaWV3LmdldFZhcmlhbnRFbmFibGVkQXR0cmlidXRlc1ZpZXdzKCksXG4gICAgICAgICAgICAgICAgdmFsdWUgPSBlbmFibGVkQXR0cmlidXRlc1ZpZXcubW9kZWwuZ2V0KCd2YWx1ZScpO1xuXG4gICAgICAgICAgICBmb3IgKGxldCBhdHRyaWJ1dGVzVmlldyBvZiBhdHRyaWJ1dGVzVmlld3MpIHtcbiAgICAgICAgICAgICAgICBhdHRyaWJ1dGVzVmlldy5tb2RlbC5zZXQoJ3ZhbHVlJywgdmFsdWUpO1xuICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXNWaWV3LiRlbC5maW5kKCdpbnB1dCcpLnZhbCh2YWx1ZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICB3aW5kb3cuYWZmaWxpY2lvdXNQcm9kdWN0ID0gbmV3IEFmZmlsaWNpb3VzUHJvZHVjdCgpO1xufSk7XG5cbmpRdWVyeShmdW5jdGlvbigkKSB7XG4gICAgLy8gVE9ETzogUmVtb3ZlIHRoZSBjb2RlIGJlbG93IGluIHRoZSBiZXRhXG4gICAgdmFyIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZTtcbiAgICB2YXIgJGltYWdlX2dhbGxlcnlfaWRzID0gJCggJyNwcm9kdWN0X2ltYWdlX2dhbGxlcnknICk7XG4gICAgdmFyICRwcm9kdWN0X2ltYWdlcyAgICA9ICQoICcjcHJvZHVjdF9pbWFnZXNfY29udGFpbmVyJyApLmZpbmQoICd1bC5wcm9kdWN0X2ltYWdlcycgKTtcblxuICAgICQoICcuYWRkX3Byb2R1Y3RfaW1hZ2VzJyApLm9uKCAnY2xpY2snLCAnYScsIGZ1bmN0aW9uKCBldmVudCApIHtcbiAgICAgICAgdmFyICRlbCA9ICQoIHRoaXMgKTtcblxuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIC8vIElmIHRoZSBtZWRpYSBmcmFtZSBhbHJlYWR5IGV4aXN0cywgcmVvcGVuIGl0LlxuICAgICAgICBpZiAoIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZSApIHtcbiAgICAgICAgICAgIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZS5vcGVuKCk7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBDcmVhdGUgdGhlIG1lZGlhIGZyYW1lLlxuICAgICAgICBwcm9kdWN0X2dhbGxlcnlfZnJhbWUgPSB3cC5tZWRpYS5mcmFtZXMucHJvZHVjdF9nYWxsZXJ5ID0gd3AubWVkaWEoe1xuICAgICAgICAgICAgLy8gU2V0IHRoZSB0aXRsZSBvZiB0aGUgbW9kYWwuXG4gICAgICAgICAgICB0aXRsZTogJGVsLmRhdGEoICdjaG9vc2UnICksXG4gICAgICAgICAgICBidXR0b246IHtcbiAgICAgICAgICAgICAgICB0ZXh0OiAkZWwuZGF0YSggJ3VwZGF0ZScgKVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHN0YXRlczogW1xuICAgICAgICAgICAgICAgIG5ldyB3cC5tZWRpYS5jb250cm9sbGVyLkxpYnJhcnkoe1xuICAgICAgICAgICAgICAgICAgICB0aXRsZTogJGVsLmRhdGEoICdjaG9vc2UnICksXG4gICAgICAgICAgICAgICAgICAgIGZpbHRlcmFibGU6ICdhbGwnLFxuICAgICAgICAgICAgICAgICAgICBtdWx0aXBsZTogdHJ1ZVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICBdXG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIFdoZW4gYW4gaW1hZ2UgaXMgc2VsZWN0ZWQsIHJ1biBhIGNhbGxiYWNrLlxuICAgICAgICBwcm9kdWN0X2dhbGxlcnlfZnJhbWUub24oICdzZWxlY3QnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBzZWxlY3Rpb24gPSBwcm9kdWN0X2dhbGxlcnlfZnJhbWUuc3RhdGUoKS5nZXQoICdzZWxlY3Rpb24nICk7XG4gICAgICAgICAgICB2YXIgYXR0YWNobWVudF9pZHMgPSAkaW1hZ2VfZ2FsbGVyeV9pZHMudmFsKCk7XG5cbiAgICAgICAgICAgIHNlbGVjdGlvbi5tYXAoIGZ1bmN0aW9uKCBhdHRhY2htZW50ICkge1xuICAgICAgICAgICAgICAgIGF0dGFjaG1lbnQgPSBhdHRhY2htZW50LnRvSlNPTigpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCBhdHRhY2htZW50LmlkICkge1xuICAgICAgICAgICAgICAgICAgICBhdHRhY2htZW50X2lkcyAgID0gYXR0YWNobWVudF9pZHMgPyBhdHRhY2htZW50X2lkcyArICcsJyArIGF0dGFjaG1lbnQuaWQgOiBhdHRhY2htZW50LmlkO1xuICAgICAgICAgICAgICAgICAgICB2YXIgYXR0YWNobWVudF9pbWFnZSA9IGF0dGFjaG1lbnQuc2l6ZXMgJiYgYXR0YWNobWVudC5zaXplcy50aHVtYm5haWwgPyBhdHRhY2htZW50LnNpemVzLnRodW1ibmFpbC51cmwgOiBhdHRhY2htZW50LnVybDtcblxuICAgICAgICAgICAgICAgICAgICAkcHJvZHVjdF9pbWFnZXMuYXBwZW5kKCAnPGxpIGNsYXNzPVwiaW1hZ2VcIiBkYXRhLWF0dGFjaG1lbnRfaWQ9XCInICsgYXR0YWNobWVudC5pZCArICdcIj48aW1nIHNyYz1cIicgKyBhdHRhY2htZW50X2ltYWdlICsgJ1wiIC8+PHVsIGNsYXNzPVwiYWN0aW9uc1wiPjxsaT48YSBocmVmPVwiI1wiIGNsYXNzPVwiZGVsZXRlXCIgdGl0bGU9XCInICsgJGVsLmRhdGEoJ2RlbGV0ZScpICsgJ1wiPicgKyAkZWwuZGF0YSgndGV4dCcpICsgJzwvYT48L2xpPjwvdWw+PC9saT4nICk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICRpbWFnZV9nYWxsZXJ5X2lkcy52YWwoIGF0dGFjaG1lbnRfaWRzICk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIEZpbmFsbHksIG9wZW4gdGhlIG1vZGFsLlxuICAgICAgICBwcm9kdWN0X2dhbGxlcnlfZnJhbWUub3BlbigpO1xuICAgIH0pO1xuXG4gICAgLy8gSW1hZ2Ugb3JkZXJpbmcuXG4gICAgJHByb2R1Y3RfaW1hZ2VzLnNvcnRhYmxlKHtcbiAgICAgICAgaXRlbXM6ICdsaS5pbWFnZScsXG4gICAgICAgIGN1cnNvcjogJ21vdmUnLFxuICAgICAgICBzY3JvbGxTZW5zaXRpdml0eTogNDAsXG4gICAgICAgIGZvcmNlUGxhY2Vob2xkZXJTaXplOiB0cnVlLFxuICAgICAgICBmb3JjZUhlbHBlclNpemU6IGZhbHNlLFxuICAgICAgICBoZWxwZXI6ICdjbG9uZScsXG4gICAgICAgIG9wYWNpdHk6IDAuNjUsXG4gICAgICAgIHBsYWNlaG9sZGVyOiAnd2MtbWV0YWJveC1zb3J0YWJsZS1wbGFjZWhvbGRlcicsXG4gICAgICAgIHN0YXJ0OiBmdW5jdGlvbiggZXZlbnQsIHVpICkge1xuICAgICAgICAgICAgdWkuaXRlbS5jc3MoICdiYWNrZ3JvdW5kLWNvbG9yJywgJyNmNmY2ZjYnICk7XG4gICAgICAgIH0sXG4gICAgICAgIHN0b3A6IGZ1bmN0aW9uKCBldmVudCwgdWkgKSB7XG4gICAgICAgICAgICB1aS5pdGVtLnJlbW92ZUF0dHIoICdzdHlsZScgKTtcbiAgICAgICAgfSxcbiAgICAgICAgdXBkYXRlOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBhdHRhY2htZW50X2lkcyA9ICcnO1xuXG4gICAgICAgICAgICAkKCAnI3Byb2R1Y3RfaW1hZ2VzX2NvbnRhaW5lcicgKS5maW5kKCAndWwgbGkuaW1hZ2UnICkuY3NzKCAnY3Vyc29yJywgJ2RlZmF1bHQnICkuZWFjaCggZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgdmFyIGF0dGFjaG1lbnRfaWQgPSAkKCB0aGlzICkuYXR0ciggJ2RhdGEtYXR0YWNobWVudF9pZCcgKTtcbiAgICAgICAgICAgICAgICBhdHRhY2htZW50X2lkcyA9IGF0dGFjaG1lbnRfaWRzICsgYXR0YWNobWVudF9pZCArICcsJztcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkaW1hZ2VfZ2FsbGVyeV9pZHMudmFsKCBhdHRhY2htZW50X2lkcyApO1xuICAgICAgICB9XG4gICAgfSk7XG5cbiAgICAvLyBSZW1vdmUgaW1hZ2VzLlxuICAgICQoICcjcHJvZHVjdF9pbWFnZXNfY29udGFpbmVyJyApLm9uKCAnY2xpY2snLCAnYS5kZWxldGUnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgJCggdGhpcyApLmNsb3Nlc3QoICdsaS5pbWFnZScgKS5yZW1vdmUoKTtcblxuICAgICAgICB2YXIgYXR0YWNobWVudF9pZHMgPSAnJztcblxuICAgICAgICAkKCAnI3Byb2R1Y3RfaW1hZ2VzX2NvbnRhaW5lcicgKS5maW5kKCAndWwgbGkuaW1hZ2UnICkuY3NzKCAnY3Vyc29yJywgJ2RlZmF1bHQnICkuZWFjaCggZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB2YXIgYXR0YWNobWVudF9pZCA9ICQoIHRoaXMgKS5hdHRyKCAnZGF0YS1hdHRhY2htZW50X2lkJyApO1xuICAgICAgICAgICAgYXR0YWNobWVudF9pZHMgPSBhdHRhY2htZW50X2lkcyArIGF0dGFjaG1lbnRfaWQgKyAnLCc7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICRpbWFnZV9nYWxsZXJ5X2lkcy52YWwoIGF0dGFjaG1lbnRfaWRzICk7XG5cbiAgICAgICAgLy8gUmVtb3ZlIGFueSBsaW5nZXJpbmcgdG9vbHRpcHMuXG4gICAgICAgICQoICcjdGlwdGlwX2hvbGRlcicgKS5yZW1vdmVBdHRyKCAnc3R5bGUnICk7XG4gICAgICAgICQoICcjdGlwdGlwX2Fycm93JyApLnJlbW92ZUF0dHIoICdzdHlsZScgKTtcblxuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfSk7XG59KTtcbiJdfQ==
