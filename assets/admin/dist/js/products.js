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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL3Byb2R1Y3RzL2pzL3Byb2R1Y3RzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7Ozs7O0FDQUEsT0FBTyxVQUFTLENBQVQsRUFBWTtBQUNmLFFBQU0sU0FBUyxPQUFPLE1BQXRCO0FBQ0EsUUFBSSxPQUFPLE9BQU8sTUFBZCxLQUF5QixXQUE3QixFQUEwQztBQUN0QyxlQUFPLEtBQVA7QUFDSDs7QUFKYyxRQU1ULFVBTlM7QUFBQTtBQUFBO0FBQUE7O0FBQUE7QUFBQTtBQUFBLCtDQVFlO0FBQ3RCLG9CQUFJLGdCQUFnQixJQUFwQjs7QUFFQSxrQkFBRSxJQUFGLENBQU8sT0FBTyxLQUFkLEVBQXFCLFVBQVMsSUFBVCxFQUFlO0FBQ2hDLHdCQUFHLEtBQUssS0FBTCxJQUFjLEtBQUssS0FBTCxDQUFXLFVBQXpCLElBQXVDLEtBQUssS0FBTCxDQUFXLFVBQVgsQ0FBc0IsS0FBdEIsSUFBK0IsdUJBQXVCLFNBQWhHLEVBQTJHO0FBQ3ZHLHdDQUFnQixJQUFoQjtBQUNIO0FBQ0osaUJBSkQ7O0FBTUEsdUJBQU8sYUFBUDtBQUNIO0FBbEJVO0FBQUE7QUFBQSw4Q0FvQmM7QUFDckIsb0JBQUksZUFBZSxJQUFuQjs7QUFFQSxrQkFBRSxJQUFGLENBQU8sT0FBTyxLQUFkLEVBQXFCLFVBQVMsSUFBVCxFQUFlO0FBQ2hDLHdCQUFHLEtBQUssaUJBQUwsSUFBMEIsS0FBSyxpQkFBTCxDQUF1QixTQUF2QixJQUFvQywrQkFBakUsRUFBa0c7QUFDOUYsdUNBQWUsSUFBZjtBQUNIO0FBQ0osaUJBSkQ7O0FBTUEsdUJBQU8sWUFBUDtBQUNIO0FBOUJVO0FBQUE7QUFBQSwwQ0FnQ1U7QUFDakIsb0JBQUksV0FBVyxJQUFmOztBQUVBLGtCQUFFLElBQUYsQ0FBTyxPQUFPLEtBQWQsRUFBcUIsVUFBUyxJQUFULEVBQWU7QUFDaEMsd0JBQUcsS0FBSyxpQkFB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QUNBLFVBQUcsZUFBSCxFQUFxQixVQUFyQixDQUFpQyxPQUFqQzs7QUFFQSxlQUFPLEtBQVA7QUFDSCxLQWpCRDtBQWtCSCxDQXZHRCIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCJqUXVlcnkoZnVuY3Rpb24oJCkge1xuICAgIGNvbnN0IGNhcmJvbiA9IHdpbmRvdy5jYXJib247XG4gICAgaWYgKHR5cGVvZiBjYXJib24uZmllbGRzID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgY2xhc3MgQ2FyYm9uVmlld1xuICAgIHtcbiAgICAgICAgc3RhdGljIGdldENvbnRhaW5lclZpZXcoKSB7XG4gICAgICAgICAgICB2YXIgY29udGFpbmVyVmlldyA9IG51bGw7XG5cbiAgICAgICAgICAgIF8uZWFjaChjYXJib24udmlld3MsIGZ1bmN0aW9uKHZpZXcpIHtcbiAgICAgICAgICAgICAgICBpZih2aWV3Lm1vZGVsICYmIHZpZXcubW9kZWwuYXR0cmlidXRlcyAmJiB2aWV3Lm1vZGVsLmF0dHJpYnV0ZXMudGl0bGUgPT0gYWZmUHJvZHVjdFRyYW5zbGF0aW9ucy5jb250YWluZXIpIHtcbiAgICAgICAgICAgICAgICAgICAgY29udGFpbmVyVmlldyA9IHZpZXc7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiBjb250YWluZXJWaWV3O1xuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIGdldFZhcmlhbnRzVmlldygpIHtcbiAgICAgICAgICAgIHZhciB2YXJpYW50c1ZpZXcgPSBudWxsO1xuXG4gICAgICAgICAgICBfLmVhY2goY2FyYm9uLnZpZXdzLCBmdW5jdGlvbih2aWV3KSB7XG4gICAgICAgICAgICAgICAgaWYodmlldy50ZW1wbGF0ZVZhcmlhYmxlcyAmJiB2aWV3LnRlbXBsYXRlVmFyaWFibGVzLmJhc2VfbmFtZSA9PSAnX2FmZmlsaWNpb3VzX3Byb2R1Y3RfdmFyaWFudHMnKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhcmlhbnRzVmlldyA9IHZpZXc7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiB2YXJpYW50c1ZpZXc7XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgZ2V0VHlwZVZpZXcoKSB7XG4gICAgICAgICAgICB2YXIgdHlwZVZpZXcgPSBudWxsO1xuXG4gICAgICAgICAgICBfLmVhY2goY2FyYm9uLnZpZXdzLCBmdW5jdGlvbih2aWV3KSB7XG4gICAgICAgICAgICAgICAgaWYodmlldy50ZW1wbGF0ZVZhcmlhYmxlcyAmJiB2aWV3LnRlbXBsYXRlVmFyaWFibGVzLmJhc2VfbmFtZSA9PSAnX2FmZmlsaWNpb3VzX3Byb2R1Y3RfdHlwZScpIHtcbiAgICAgICAgICAgICAgICAgICAgdHlwZVZpZXcgPSB2aWV3O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICByZXR1cm4gdHlwZVZpZXc7XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgZ2V0RW5hYmxlZEF0dHJpYnV0ZXNWaWV3KCkge1xuICAgICAgICAgICAgdmFyIGVuYWJsZWRBdHRyaWJ1dGVzVmlldyA9IG51bGw7XG5cbiAgICAgICAgICAgIF8uZWFjaChjYXJib24udmlld3MsIGZ1bmN0aW9uKHZpZXcpIHtcbiAgICAgICAgICAgICAgICBpZih2aWV3LnRlbXBsYXRlVmFyaWFibGVzICYmIHZpZXcudGVtcGxhdGVWYXJpYWJsZXMuYmFzZV9uYW1lID09ICdfYWZmaWxpY2lvdXNfcHJvZHVjdF9lbmFibGVkX2F0dHJpYnV0ZXMnKSB7XG4gICAgICAgICAgICAgICAgICAgIGVuYWJsZWRBdHRyaWJ1dGVzVmlldyA9IHZpZXc7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiBlbmFibGVkQXR0cmlidXRlc1ZpZXc7XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgZ2V0VmFyaWFudEVuYWJsZWRBdHRyaWJ1dGVzVmlld3MoKSB7XG4gICAgICAgICAgICB2YXIgdmFyaWFudEVuYWJsZWRBdHRyaWJ1dGVzVmlld3MgPSBbXTtcblxuICAgICAgICAgICAgXy5lYWNoKGNhcmJvbi52aWV3cywgZnVuY3Rpb24odmlldykge1xuICAgICAgICAgICAgICAgIGlmKHZpZXcudGVtcGxhdGVWYXJpYWJsZXMgJiYgdmlldy50ZW1wbGF0ZVZhcmlhYmxlcy5iYXNlX25hbWUgPT0gJ2VuYWJsZWRfYXR0cmlidXRlcycpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyaWFudEVuYWJsZWRBdHRyaWJ1dGVzVmlld3MucHVzaCh2aWV3KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgcmV0dXJuIHZhcmlhbnRFbmFibGVkQXR0cmlidXRlc1ZpZXdzO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgY2xhc3MgQWZmaWxpY2lvdXNQcm9kdWN0IHtcbiAgICAgICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgICAgICBsZXQgdHlwZVZpZXcgPSBDYXJib25WaWV3LmdldFR5cGVWaWV3KCk7XG4gICAgICAgICAgICBsZXQgdmFyaWFudHNWaWV3ID0gQ2FyYm9uVmlldy5nZXRWYXJpYW50c1ZpZXcoKTtcbiAgICAgICAgICAgIGxldCBlbmFibGVkQXR0cmlidXRlc1ZpZXcgPSBDYXJib25WaWV3LmdldEVuYWJsZWRBdHRyaWJ1dGVzVmlldygpO1xuXG4gICAgICAgICAgICBpZighIXR5cGVWaWV3KSB7XG4gICAgICAgICAgICAgICAgdHlwZVZpZXcuJGVsLnJlYWR5KHRoaXMudG9nZ2xlVGFicyk7XG4gICAgICAgICAgICAgICAgdHlwZVZpZXcubW9kZWwub24oJ2NoYW5nZTp2YWx1ZScsIHRoaXMudG9nZ2xlVGFicyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCEhdmFyaWFudHNWaWV3KSB7XG4gICAgICAgICAgICAgICAgdmFyaWFudHNWaWV3LiRlbC5yZWFkeSh0aGlzLnRvZ2dsZUF0dHJpYnV0ZXMpO1xuICAgICAgICAgICAgICAgIHZhcmlhbnRzVmlldy5tb2RlbC5vbignY2hhbmdlOnZhbHVlJywgdGhpcy50b2dnbGVBdHRyaWJ1dGVzKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoISFlbmFibGVkQXR0cmlidXRlc1ZpZXcpIHtcbiAgICAgICAgICAgICAgICBlbmFibGVkQXR0cmlidXRlc1ZpZXcuJGVsLnJlYWR5KHRoaXMudG9nZ2xlQXR0cmlidXRlcyk7XG4gICAgICAgICAgICAgICAgZW5hYmxlZEF0dHJpYnV0ZXNWaWV3Lm1vZGVsLm9uKCdjaGFuZ2U6dmFsdWUnLCB0aGlzLnRvZ2dsZUF0dHJpYnV0ZXMpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgdG9nZ2xlVGFicygpIHtcbiAgICAgICAgICAgIC8vIFN1cHBvcnRzIG11bHRpcGxlIGxhbmd1YWdlc1xuICAgICAgICAgICAgdmFyIHZpZXcgPSBDYXJib25WaWV3LmdldENvbnRhaW5lclZpZXcoKSxcbiAgICAgICAgICAgICAgICB0eXBlVmlldyA9IENhcmJvblZpZXcuZ2V0VHlwZVZpZXcoKSxcbiAgICAgICAgICAgICAgICBwcm9kdWN0VHlwZSA9IHR5cGVWaWV3Lm1vZGVsLmdldCgndmFsdWUnKSxcbiAgICAgICAgICAgICAgICB2YXJpYW50cyA9IHZpZXcuJGVsLmZpbmQoJ2FbZGF0YS1pZD1cIicgKyBhZmZQcm9kdWN0VHJhbnNsYXRpb25zLnZhcmlhbnRzLnRyaW0oKS50b0xvd2VyQ2FzZSgpICsgJ1wiXScpLnBhcmVudCgpLFxuICAgICAgICAgICAgICAgIHNob3BzID0gdmlldy4kZWwuZmluZCgnYVtkYXRhLWlkPVwiJyArIGFmZlByb2R1Y3RUcmFuc2xhdGlvbnMuc2hvcHMudHJpbSgpLnRvTG93ZXJDYXNlKCkgKyAnXCJdJykucGFyZW50KCk7XG5cbiAgICAgICAgICAgIGlmKHByb2R1Y3RUeXBlID09PSAnY29tcGxleCcpIHtcbiAgICAgICAgICAgICAgICB2YXJpYW50cy5zaG93KCk7XG4gICAgICAgICAgICAgICAgc2hvcHMuaGlkZSgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB2YXJpYW50cy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgc2hvcHMuc2hvdygpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgdG9nZ2xlQXR0cmlidXRlcygpIHtcbiAgICAgICAgICAgIGxldCBlbmFibGVkQXR0cmlidXRlc1ZpZXcgPSBDYXJib25WaWV3LmdldEVuYWJsZWRBdHRyaWJ1dGVzVmlldygpLFxuICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXNWaWV3cyA9IENhcmJvblZpZXcuZ2V0VmFyaWFudEVuYWJsZWRBdHRyaWJ1dGVzVmlld3MoKSxcbiAgICAgICAgICAgICAgICB2YWx1ZSA9IGVuYWJsZWRBdHRyaWJ1dGVzVmlldy5tb2RlbC5nZXQoJ3ZhbHVlJyk7XG5cbiAgICAgICAgICAgIGZvciAobGV0IGF0dHJpYnV0ZXNWaWV3IG9mIGF0dHJpYnV0ZXNWaWV3cykge1xuICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXNWaWV3Lm1vZGVsLnNldCgndmFsdWUnLCB2YWx1ZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICB3aW5kb3cuYWZmaWxpY2lvdXNQcm9kdWN0ID0gbmV3IEFmZmlsaWNpb3VzUHJvZHVjdCgpO1xufSk7XG5cbmpRdWVyeShmdW5jdGlvbigkKSB7XG4gICAgLy8gVE9ETzogUmVtb3ZlIHRoZSBjb2RlIGJlbG93IGluIHRoZSBiZXRhXG4gICAgdmFyIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZTtcbiAgICB2YXIgJGltYWdlX2dhbGxlcnlfaWRzID0gJCggJyNwcm9kdWN0X2ltYWdlX2dhbGxlcnknICk7XG4gICAgdmFyICRwcm9kdWN0X2ltYWdlcyAgICA9ICQoICcjcHJvZHVjdF9pbWFnZXNfY29udGFpbmVyJyApLmZpbmQoICd1bC5wcm9kdWN0X2ltYWdlcycgKTtcblxuICAgICQoICcuYWRkX3Byb2R1Y3RfaW1hZ2VzJyApLm9uKCAnY2xpY2snLCAnYScsIGZ1bmN0aW9uKCBldmVudCApIHtcbiAgICAgICAgdmFyICRlbCA9ICQoIHRoaXMgKTtcblxuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIC8vIElmIHRoZSBtZWRpYSBmcmFtZSBhbHJlYWR5IGV4aXN0cywgcmVvcGVuIGl0LlxuICAgICAgICBpZiAoIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZSApIHtcbiAgICAgICAgICAgIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZS5vcGVuKCk7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBDcmVhdGUgdGhlIG1lZGlhIGZyYW1lLlxuICAgICAgICBwcm9kdWN0X2dhbGxlcnlfZnJhbWUgPSB3cC5tZWRpYS5mcmFtZXMucHJvZHVjdF9nYWxsZXJ5ID0gd3AubWVkaWEoe1xuICAgICAgICAgICAgLy8gU2V0IHRoZSB0aXRsZSBvZiB0aGUgbW9kYWwuXG4gICAgICAgICAgICB0aXRsZTogJGVsLmRhdGEoICdjaG9vc2UnICksXG4gICAgICAgICAgICBidXR0b246IHtcbiAgICAgICAgICAgICAgICB0ZXh0OiAkZWwuZGF0YSggJ3VwZGF0ZScgKVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHN0YXRlczogW1xuICAgICAgICAgICAgICAgIG5ldyB3cC5tZWRpYS5jb250cm9sbGVyLkxpYnJhcnkoe1xuICAgICAgICAgICAgICAgICAgICB0aXRsZTogJGVsLmRhdGEoICdjaG9vc2UnICksXG4gICAgICAgICAgICAgICAgICAgIGZpbHRlcmFibGU6ICdhbGwnLFxuICAgICAgICAgICAgICAgICAgICBtdWx0aXBsZTogdHJ1ZVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICBdXG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIFdoZW4gYW4gaW1hZ2UgaXMgc2VsZWN0ZWQsIHJ1biBhIGNhbGxiYWNrLlxuICAgICAgICBwcm9kdWN0X2dhbGxlcnlfZnJhbWUub24oICdzZWxlY3QnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBzZWxlY3Rpb24gPSBwcm9kdWN0X2dhbGxlcnlfZnJhbWUuc3RhdGUoKS5nZXQoICdzZWxlY3Rpb24nICk7XG4gICAgICAgICAgICB2YXIgYXR0YWNobWVudF9pZHMgPSAkaW1hZ2VfZ2FsbGVyeV9pZHMudmFsKCk7XG5cbiAgICAgICAgICAgIHNlbGVjdGlvbi5tYXAoIGZ1bmN0aW9uKCBhdHRhY2htZW50ICkge1xuICAgICAgICAgICAgICAgIGF0dGFjaG1lbnQgPSBhdHRhY2htZW50LnRvSlNPTigpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCBhdHRhY2htZW50LmlkICkge1xuICAgICAgICAgICAgICAgICAgICBhdHRhY2htZW50X2lkcyAgID0gYXR0YWNobWVudF9pZHMgPyBhdHRhY2htZW50X2lkcyArICcsJyArIGF0dGFjaG1lbnQuaWQgOiBhdHRhY2htZW50LmlkO1xuICAgICAgICAgICAgICAgICAgICB2YXIgYXR0YWNobWVudF9pbWFnZSA9IGF0dGFjaG1lbnQuc2l6ZXMgJiYgYXR0YWNobWVudC5zaXplcy50aHVtYm5haWwgPyBhdHRhY2htZW50LnNpemVzLnRodW1ibmFpbC51cmwgOiBhdHRhY2htZW50LnVybDtcblxuICAgICAgICAgICAgICAgICAgICAkcHJvZHVjdF9pbWFnZXMuYXBwZW5kKCAnPGxpIGNsYXNzPVwiaW1hZ2VcIiBkYXRhLWF0dGFjaG1lbnRfaWQ9XCInICsgYXR0YWNobWVudC5pZCArICdcIj48aW1nIHNyYz1cIicgKyBhdHRhY2htZW50X2ltYWdlICsgJ1wiIC8+PHVsIGNsYXNzPVwiYWN0aW9uc1wiPjxsaT48YSBocmVmPVwiI1wiIGNsYXNzPVwiZGVsZXRlXCIgdGl0bGU9XCInICsgJGVsLmRhdGEoJ2RlbGV0ZScpICsgJ1wiPicgKyAkZWwuZGF0YSgndGV4dCcpICsgJzwvYT48L2xpPjwvdWw+PC9saT4nICk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICRpbWFnZV9nYWxsZXJ5X2lkcy52YWwoIGF0dGFjaG1lbnRfaWRzICk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIEZpbmFsbHksIG9wZW4gdGhlIG1vZGFsLlxuICAgICAgICBwcm9kdWN0X2dhbGxlcnlfZnJhbWUub3BlbigpO1xuICAgIH0pO1xuXG4gICAgLy8gSW1hZ2Ugb3JkZXJpbmcuXG4gICAgJHByb2R1Y3RfaW1hZ2VzLnNvcnRhYmxlKHtcbiAgICAgICAgaXRlbXM6ICdsaS5pbWFnZScsXG4gICAgICAgIGN1cnNvcjogJ21vdmUnLFxuICAgICAgICBzY3JvbGxTZW5zaXRpdml0eTogNDAsXG4gICAgICAgIGZvcmNlUGxhY2Vob2xkZXJTaXplOiB0cnVlLFxuICAgICAgICBmb3JjZUhlbHBlclNpemU6IGZhbHNlLFxuICAgICAgICBoZWxwZXI6ICdjbG9uZScsXG4gICAgICAgIG9wYWNpdHk6IDAuNjUsXG4gICAgICAgIHBsYWNlaG9sZGVyOiAnd2MtbWV0YWJveC1zb3J0YWJsZS1wbGFjZWhvbGRlcicsXG4gICAgICAgIHN0YXJ0OiBmdW5jdGlvbiggZXZlbnQsIHVpICkge1xuICAgICAgICAgICAgdWkuaXRlbS5jc3MoICdiYWNrZ3JvdW5kLWNvbG9yJywgJyNmNmY2ZjYnICk7XG4gICAgICAgIH0sXG4gICAgICAgIHN0b3A6IGZ1bmN0aW9uKCBldmVudCwgdWkgKSB7XG4gICAgICAgICAgICB1aS5pdGVtLnJlbW92ZUF0dHIoICdzdHlsZScgKTtcbiAgICAgICAgfSxcbiAgICAgICAgdXBkYXRlOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBhdHRhY2htZW50X2lkcyA9ICcnO1xuXG4gICAgICAgICAgICAkKCAnI3Byb2R1Y3RfaW1hZ2VzX2NvbnRhaW5lcicgKS5maW5kKCAndWwgbGkuaW1hZ2UnICkuY3NzKCAnY3Vyc29yJywgJ2RlZmF1bHQnICkuZWFjaCggZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgdmFyIGF0dGFjaG1lbnRfaWQgPSAkKCB0aGlzICkuYXR0ciggJ2RhdGEtYXR0YWNobWVudF9pZCcgKTtcbiAgICAgICAgICAgICAgICBhdHRhY2htZW50X2lkcyA9IGF0dGFjaG1lbnRfaWRzICsgYXR0YWNobWVudF9pZCArICcsJztcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkaW1hZ2VfZ2FsbGVyeV9pZHMudmFsKCBhdHRhY2htZW50X2lkcyApO1xuICAgICAgICB9XG4gICAgfSk7XG5cbiAgICAvLyBSZW1vdmUgaW1hZ2VzLlxuICAgICQoICcjcHJvZHVjdF9pbWFnZXNfY29udGFpbmVyJyApLm9uKCAnY2xpY2snLCAnYS5kZWxldGUnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgJCggdGhpcyApLmNsb3Nlc3QoICdsaS5pbWFnZScgKS5yZW1vdmUoKTtcblxuICAgICAgICB2YXIgYXR0YWNobWVudF9pZHMgPSAnJztcblxuICAgICAgICAkKCAnI3Byb2R1Y3RfaW1hZ2VzX2NvbnRhaW5lcicgKS5maW5kKCAndWwgbGkuaW1hZ2UnICkuY3NzKCAnY3Vyc29yJywgJ2RlZmF1bHQnICkuZWFjaCggZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB2YXIgYXR0YWNobWVudF9pZCA9ICQoIHRoaXMgKS5hdHRyKCAnZGF0YS1hdHRhY2htZW50X2lkJyApO1xuICAgICAgICAgICAgYXR0YWNobWVudF9pZHMgPSBhdHRhY2htZW50X2lkcyArIGF0dGFjaG1lbnRfaWQgKyAnLCc7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICRpbWFnZV9nYWxsZXJ5X2lkcy52YWwoIGF0dGFjaG1lbnRfaWRzICk7XG5cbiAgICAgICAgLy8gUmVtb3ZlIGFueSBsaW5nZXJpbmcgdG9vbHRpcHMuXG4gICAgICAgICQoICcjdGlwdGlwX2hvbGRlcicgKS5yZW1vdmVBdHRyKCAnc3R5bGUnICk7XG4gICAgICAgICQoICcjdGlwdGlwX2Fycm93JyApLnJlbW92ZUF0dHIoICdzdHlsZScgKTtcblxuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfSk7XG59KTtcblxuIl19
