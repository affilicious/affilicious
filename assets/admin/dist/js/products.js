(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL3Byb2R1Y3RzL2pzL3Byb2R1Y3RzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7Ozs7O0FDQUEsT0FBTyxVQUFTLENBQVQsRUFBWTtBQUNmLFFBQU0sU0FBUyxPQUFPLE1BQXRCO0FBQ0EsUUFBSSxPQUFPLE9BQU8sTUFBZCxLQUF5QixXQUE3QixFQUEwQztBQUN0QyxlQUFPLEtBQVA7QUFDSDs7QUFKYyxRQU1ULFVBTlM7QUFBQTtBQUFBO0FBQUE7O0FBQUE7QUFBQTtBQUFBLCtDQVFlO0FBQ3RCLG9CQUFJLGdCQUFnQixJQUFwQjs7QUFFQSxrQkFBRSxJQUFGLENBQU8sT0FBTyxLQUFkLEVBQXFCLFVBQVMsSUFBVCxFQUFlO0FBQ2hDLHdCQUFHLEtBQUssS0FBTCxJQUFjLEtBQUssS0FBTCxDQUFXLFVBQXpCLElBQXVDLEtBQUssS0FBTCxDQUFXLFVBQVgsQ0FBc0IsS0FBdEIsSUFBK0IsdUJBQXVCLFNBQWhHLEVBQTJHO0FBQ3ZHLHdDQUFnQixJQUFoQjtBQUNIO0FBQ0osaUJBSkQ7O0FBTUEsdUJBQU8sYUFBUDtBQUNIO0FBbEJVO0FBQUE7QUFBQSw4Q0FvQmM7QUFDckIsb0JBQUksZUFBZSxJQUFuQjs7QUFFQSxrQkFBRSxJQUFGLENBQU8sT0FBTyxLQUFkLEVBQXFCLFVBQVMsSUFBVCxFQUFlO0FBQ2hDLHdCQUFHLEtBQUssaUJBQUwsSUFBMEIsS0FBSyxpQkFBTCxDQUF1QixTQUF2QixJQUFvQywrQkFBakUsRUFBa0c7QUFDOUYsdUNBQWUsSUFBZjtBQUNIO0FBQ0osaUJBSkQ7O0FBTUEsdUJBQU8sWUFBUDtBQUNIO0FBOUJVO0FBQUE7QUFBQSwwQ0FnQ1U7QUFDakIsb0JBQUksV0FBVyxJQUFmOztBQUVBLGtCQUFFLElBQUYsQ0FBTyxPQUFPLEtBQWQsRUFBcUIsVUFBUyxJQUFULEVBQWU7QUFDaEMsd0JBQUcsS0FBSyxpQkFB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QUFuQixDQUF3QixjQUF4Qjs7QUFFQTtBQUNBLFVBQUcsZ0JBQUgsRUFBc0IsVUFBdEIsQ0FBa0MsT0FBbEM7QUFDQSxVQUFHLGVBQUgsRUFBcUIsVUFBckIsQ0FBaUMsT0FBakM7O0FBRUEsZUFBTyxLQUFQO0FBQ0gsS0FqQkQ7QUFrQkgsQ0F2R0QiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbigpe2Z1bmN0aW9uIHIoZSxuLHQpe2Z1bmN0aW9uIG8oaSxmKXtpZighbltpXSl7aWYoIWVbaV0pe3ZhciBjPVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmU7aWYoIWYmJmMpcmV0dXJuIGMoaSwhMCk7aWYodSlyZXR1cm4gdShpLCEwKTt2YXIgYT1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK2krXCInXCIpO3Rocm93IGEuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixhfXZhciBwPW5baV09e2V4cG9ydHM6e319O2VbaV1bMF0uY2FsbChwLmV4cG9ydHMsZnVuY3Rpb24ocil7dmFyIG49ZVtpXVsxXVtyXTtyZXR1cm4gbyhufHxyKX0scCxwLmV4cG9ydHMscixlLG4sdCl9cmV0dXJuIG5baV0uZXhwb3J0c31mb3IodmFyIHU9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZSxpPTA7aTx0Lmxlbmd0aDtpKyspbyh0W2ldKTtyZXR1cm4gb31yZXR1cm4gcn0pKCkiLCJqUXVlcnkoZnVuY3Rpb24oJCkge1xuICAgIGNvbnN0IGNhcmJvbiA9IHdpbmRvdy5jYXJib247XG4gICAgaWYgKHR5cGVvZiBjYXJib24uZmllbGRzID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgY2xhc3MgQ2FyYm9uVmlld1xuICAgIHtcbiAgICAgICAgc3RhdGljIGdldENvbnRhaW5lclZpZXcoKSB7XG4gICAgICAgICAgICB2YXIgY29udGFpbmVyVmlldyA9IG51bGw7XG5cbiAgICAgICAgICAgIF8uZWFjaChjYXJib24udmlld3MsIGZ1bmN0aW9uKHZpZXcpIHtcbiAgICAgICAgICAgICAgICBpZih2aWV3Lm1vZGVsICYmIHZpZXcubW9kZWwuYXR0cmlidXRlcyAmJiB2aWV3Lm1vZGVsLmF0dHJpYnV0ZXMudGl0bGUgPT0gYWZmUHJvZHVjdFRyYW5zbGF0aW9ucy5jb250YWluZXIpIHtcbiAgICAgICAgICAgICAgICAgICAgY29udGFpbmVyVmlldyA9IHZpZXc7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiBjb250YWluZXJWaWV3O1xuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIGdldFZhcmlhbnRzVmlldygpIHtcbiAgICAgICAgICAgIHZhciB2YXJpYW50c1ZpZXcgPSBudWxsO1xuXG4gICAgICAgICAgICBfLmVhY2goY2FyYm9uLnZpZXdzLCBmdW5jdGlvbih2aWV3KSB7XG4gICAgICAgICAgICAgICAgaWYodmlldy50ZW1wbGF0ZVZhcmlhYmxlcyAmJiB2aWV3LnRlbXBsYXRlVmFyaWFibGVzLmJhc2VfbmFtZSA9PSAnX2FmZmlsaWNpb3VzX3Byb2R1Y3RfdmFyaWFudHMnKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhcmlhbnRzVmlldyA9IHZpZXc7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiB2YXJpYW50c1ZpZXc7XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgZ2V0VHlwZVZpZXcoKSB7XG4gICAgICAgICAgICB2YXIgdHlwZVZpZXcgPSBudWxsO1xuXG4gICAgICAgICAgICBfLmVhY2goY2FyYm9uLnZpZXdzLCBmdW5jdGlvbih2aWV3KSB7XG4gICAgICAgICAgICAgICAgaWYodmlldy50ZW1wbGF0ZVZhcmlhYmxlcyAmJiB2aWV3LnRlbXBsYXRlVmFyaWFibGVzLmJhc2VfbmFtZSA9PSAnX2FmZmlsaWNpb3VzX3Byb2R1Y3RfdHlwZScpIHtcbiAgICAgICAgICAgICAgICAgICAgdHlwZVZpZXcgPSB2aWV3O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICByZXR1cm4gdHlwZVZpZXc7XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgZ2V0RW5hYmxlZEF0dHJpYnV0ZXNWaWV3KCkge1xuICAgICAgICAgICAgdmFyIGVuYWJsZWRBdHRyaWJ1dGVzVmlldyA9IG51bGw7XG5cbiAgICAgICAgICAgIF8uZWFjaChjYXJib24udmlld3MsIGZ1bmN0aW9uKHZpZXcpIHtcbiAgICAgICAgICAgICAgICBpZih2aWV3LnRlbXBsYXRlVmFyaWFibGVzICYmIHZpZXcudGVtcGxhdGVWYXJpYWJsZXMuYmFzZV9uYW1lID09ICdfYWZmaWxpY2lvdXNfcHJvZHVjdF9lbmFibGVkX2F0dHJpYnV0ZXMnKSB7XG4gICAgICAgICAgICAgICAgICAgIGVuYWJsZWRBdHRyaWJ1dGVzVmlldyA9IHZpZXc7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiBlbmFibGVkQXR0cmlidXRlc1ZpZXc7XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgZ2V0VmFyaWFudEVuYWJsZWRBdHRyaWJ1dGVzVmlld3MoKSB7XG4gICAgICAgICAgICB2YXIgdmFyaWFudEVuYWJsZWRBdHRyaWJ1dGVzVmlld3MgPSBbXTtcblxuICAgICAgICAgICAgXy5lYWNoKGNhcmJvbi52aWV3cywgZnVuY3Rpb24odmlldykge1xuICAgICAgICAgICAgICAgIGlmKHZpZXcudGVtcGxhdGVWYXJpYWJsZXMgJiYgdmlldy50ZW1wbGF0ZVZhcmlhYmxlcy5iYXNlX25hbWUgPT0gJ2VuYWJsZWRfYXR0cmlidXRlcycpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyaWFudEVuYWJsZWRBdHRyaWJ1dGVzVmlld3MucHVzaCh2aWV3KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgcmV0dXJuIHZhcmlhbnRFbmFibGVkQXR0cmlidXRlc1ZpZXdzO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgY2xhc3MgQWZmaWxpY2lvdXNQcm9kdWN0IHtcbiAgICAgICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgICAgICBsZXQgdHlwZVZpZXcgPSBDYXJib25WaWV3LmdldFR5cGVWaWV3KCk7XG4gICAgICAgICAgICBsZXQgdmFyaWFudHNWaWV3ID0gQ2FyYm9uVmlldy5nZXRWYXJpYW50c1ZpZXcoKTtcbiAgICAgICAgICAgIGxldCBlbmFibGVkQXR0cmlidXRlc1ZpZXcgPSBDYXJib25WaWV3LmdldEVuYWJsZWRBdHRyaWJ1dGVzVmlldygpO1xuXG4gICAgICAgICAgICBpZighIXR5cGVWaWV3KSB7XG4gICAgICAgICAgICAgICAgdHlwZVZpZXcuJGVsLnJlYWR5KHRoaXMudG9nZ2xlVGFicyk7XG4gICAgICAgICAgICAgICAgdHlwZVZpZXcubW9kZWwub24oJ2NoYW5nZTp2YWx1ZScsIHRoaXMudG9nZ2xlVGFicyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCEhdmFyaWFudHNWaWV3KSB7XG4gICAgICAgICAgICAgICAgdmFyaWFudHNWaWV3LiRlbC5yZWFkeSh0aGlzLnRvZ2dsZUF0dHJpYnV0ZXMpO1xuICAgICAgICAgICAgICAgIHZhcmlhbnRzVmlldy5tb2RlbC5vbignY2hhbmdlOnZhbHVlJywgdGhpcy50b2dnbGVBdHRyaWJ1dGVzKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoISFlbmFibGVkQXR0cmlidXRlc1ZpZXcpIHtcbiAgICAgICAgICAgICAgICBlbmFibGVkQXR0cmlidXRlc1ZpZXcuJGVsLnJlYWR5KHRoaXMudG9nZ2xlQXR0cmlidXRlcyk7XG4gICAgICAgICAgICAgICAgZW5hYmxlZEF0dHJpYnV0ZXNWaWV3Lm1vZGVsLm9uKCdjaGFuZ2U6dmFsdWUnLCB0aGlzLnRvZ2dsZUF0dHJpYnV0ZXMpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgdG9nZ2xlVGFicygpIHtcbiAgICAgICAgICAgIC8vIFN1cHBvcnRzIG11bHRpcGxlIGxhbmd1YWdlc1xuICAgICAgICAgICAgdmFyIHZpZXcgPSBDYXJib25WaWV3LmdldENvbnRhaW5lclZpZXcoKSxcbiAgICAgICAgICAgICAgICB0eXBlVmlldyA9IENhcmJvblZpZXcuZ2V0VHlwZVZpZXcoKSxcbiAgICAgICAgICAgICAgICBwcm9kdWN0VHlwZSA9IHR5cGVWaWV3Lm1vZGVsLmdldCgndmFsdWUnKSxcbiAgICAgICAgICAgICAgICB2YXJpYW50cyA9IHZpZXcuJGVsLmZpbmQoJ2FbZGF0YS1pZD1cIicgKyBhZmZQcm9kdWN0VHJhbnNsYXRpb25zLnZhcmlhbnRzLnRyaW0oKS50b0xvd2VyQ2FzZSgpICsgJ1wiXScpLnBhcmVudCgpLFxuICAgICAgICAgICAgICAgIHNob3BzID0gdmlldy4kZWwuZmluZCgnYVtkYXRhLWlkPVwiJyArIGFmZlByb2R1Y3RUcmFuc2xhdGlvbnMuc2hvcHMudHJpbSgpLnRvTG93ZXJDYXNlKCkgKyAnXCJdJykucGFyZW50KCk7XG5cbiAgICAgICAgICAgIGlmKHByb2R1Y3RUeXBlID09PSAnY29tcGxleCcpIHtcbiAgICAgICAgICAgICAgICB2YXJpYW50cy5zaG93KCk7XG4gICAgICAgICAgICAgICAgc2hvcHMuaGlkZSgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB2YXJpYW50cy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgc2hvcHMuc2hvdygpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgdG9nZ2xlQXR0cmlidXRlcygpIHtcbiAgICAgICAgICAgIGxldCBlbmFibGVkQXR0cmlidXRlc1ZpZXcgPSBDYXJib25WaWV3LmdldEVuYWJsZWRBdHRyaWJ1dGVzVmlldygpLFxuICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXNWaWV3cyA9IENhcmJvblZpZXcuZ2V0VmFyaWFudEVuYWJsZWRBdHRyaWJ1dGVzVmlld3MoKSxcbiAgICAgICAgICAgICAgICB2YWx1ZSA9IGVuYWJsZWRBdHRyaWJ1dGVzVmlldy5tb2RlbC5nZXQoJ3ZhbHVlJyk7XG5cbiAgICAgICAgICAgIGZvciAobGV0IGF0dHJpYnV0ZXNWaWV3IG9mIGF0dHJpYnV0ZXNWaWV3cykge1xuICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXNWaWV3Lm1vZGVsLnNldCgndmFsdWUnLCB2YWx1ZSk7XG4gICAgICAgICAgICAgICAgYXR0cmlidXRlc1ZpZXcuJGVsLmZpbmQoJ2lucHV0JykudmFsKHZhbHVlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIHdpbmRvdy5hZmZpbGljaW91c1Byb2R1Y3QgPSBuZXcgQWZmaWxpY2lvdXNQcm9kdWN0KCk7XG59KTtcblxualF1ZXJ5KGZ1bmN0aW9uKCQpIHtcbiAgICAvLyBUT0RPOiBSZW1vdmUgdGhlIGNvZGUgYmVsb3cgaW4gdGhlIGJldGFcbiAgICB2YXIgcHJvZHVjdF9nYWxsZXJ5X2ZyYW1lO1xuICAgIHZhciAkaW1hZ2VfZ2FsbGVyeV9pZHMgPSAkKCAnI3Byb2R1Y3RfaW1hZ2VfZ2FsbGVyeScgKTtcbiAgICB2YXIgJHByb2R1Y3RfaW1hZ2VzICAgID0gJCggJyNwcm9kdWN0X2ltYWdlc19jb250YWluZXInICkuZmluZCggJ3VsLnByb2R1Y3RfaW1hZ2VzJyApO1xuXG4gICAgJCggJy5hZGRfcHJvZHVjdF9pbWFnZXMnICkub24oICdjbGljaycsICdhJywgZnVuY3Rpb24oIGV2ZW50ICkge1xuICAgICAgICB2YXIgJGVsID0gJCggdGhpcyApO1xuXG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgLy8gSWYgdGhlIG1lZGlhIGZyYW1lIGFscmVhZHkgZXhpc3RzLCByZW9wZW4gaXQuXG4gICAgICAgIGlmICggcHJvZHVjdF9nYWxsZXJ5X2ZyYW1lICkge1xuICAgICAgICAgICAgcHJvZHVjdF9nYWxsZXJ5X2ZyYW1lLm9wZW4oKTtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIENyZWF0ZSB0aGUgbWVkaWEgZnJhbWUuXG4gICAgICAgIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZSA9IHdwLm1lZGlhLmZyYW1lcy5wcm9kdWN0X2dhbGxlcnkgPSB3cC5tZWRpYSh7XG4gICAgICAgICAgICAvLyBTZXQgdGhlIHRpdGxlIG9mIHRoZSBtb2RhbC5cbiAgICAgICAgICAgIHRpdGxlOiAkZWwuZGF0YSggJ2Nob29zZScgKSxcbiAgICAgICAgICAgIGJ1dHRvbjoge1xuICAgICAgICAgICAgICAgIHRleHQ6ICRlbC5kYXRhKCAndXBkYXRlJyApXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgc3RhdGVzOiBbXG4gICAgICAgICAgICAgICAgbmV3IHdwLm1lZGlhLmNvbnRyb2xsZXIuTGlicmFyeSh7XG4gICAgICAgICAgICAgICAgICAgIHRpdGxlOiAkZWwuZGF0YSggJ2Nob29zZScgKSxcbiAgICAgICAgICAgICAgICAgICAgZmlsdGVyYWJsZTogJ2FsbCcsXG4gICAgICAgICAgICAgICAgICAgIG11bHRpcGxlOiB0cnVlXG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIF1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gV2hlbiBhbiBpbWFnZSBpcyBzZWxlY3RlZCwgcnVuIGEgY2FsbGJhY2suXG4gICAgICAgIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZS5vbiggJ3NlbGVjdCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgdmFyIHNlbGVjdGlvbiA9IHByb2R1Y3RfZ2FsbGVyeV9mcmFtZS5zdGF0ZSgpLmdldCggJ3NlbGVjdGlvbicgKTtcbiAgICAgICAgICAgIHZhciBhdHRhY2htZW50X2lkcyA9ICRpbWFnZV9nYWxsZXJ5X2lkcy52YWwoKTtcblxuICAgICAgICAgICAgc2VsZWN0aW9uLm1hcCggZnVuY3Rpb24oIGF0dGFjaG1lbnQgKSB7XG4gICAgICAgICAgICAgICAgYXR0YWNobWVudCA9IGF0dGFjaG1lbnQudG9KU09OKCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoIGF0dGFjaG1lbnQuaWQgKSB7XG4gICAgICAgICAgICAgICAgICAgIGF0dGFjaG1lbnRfaWRzICAgPSBhdHRhY2htZW50X2lkcyA/IGF0dGFjaG1lbnRfaWRzICsgJywnICsgYXR0YWNobWVudC5pZCA6IGF0dGFjaG1lbnQuaWQ7XG4gICAgICAgICAgICAgICAgICAgIHZhciBhdHRhY2htZW50X2ltYWdlID0gYXR0YWNobWVudC5zaXplcyAmJiBhdHRhY2htZW50LnNpemVzLnRodW1ibmFpbCA/IGF0dGFjaG1lbnQuc2l6ZXMudGh1bWJuYWlsLnVybCA6IGF0dGFjaG1lbnQudXJsO1xuXG4gICAgICAgICAgICAgICAgICAgICRwcm9kdWN0X2ltYWdlcy5hcHBlbmQoICc8bGkgY2xhc3M9XCJpbWFnZVwiIGRhdGEtYXR0YWNobWVudF9pZD1cIicgKyBhdHRhY2htZW50LmlkICsgJ1wiPjxpbWcgc3JjPVwiJyArIGF0dGFjaG1lbnRfaW1hZ2UgKyAnXCIgLz48dWwgY2xhc3M9XCJhY3Rpb25zXCI+PGxpPjxhIGhyZWY9XCIjXCIgY2xhc3M9XCJkZWxldGVcIiB0aXRsZT1cIicgKyAkZWwuZGF0YSgnZGVsZXRlJykgKyAnXCI+JyArICRlbC5kYXRhKCd0ZXh0JykgKyAnPC9hPjwvbGk+PC91bD48L2xpPicgKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJGltYWdlX2dhbGxlcnlfaWRzLnZhbCggYXR0YWNobWVudF9pZHMgKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gRmluYWxseSwgb3BlbiB0aGUgbW9kYWwuXG4gICAgICAgIHByb2R1Y3RfZ2FsbGVyeV9mcmFtZS5vcGVuKCk7XG4gICAgfSk7XG5cbiAgICAvLyBJbWFnZSBvcmRlcmluZy5cbiAgICAkcHJvZHVjdF9pbWFnZXMuc29ydGFibGUoe1xuICAgICAgICBpdGVtczogJ2xpLmltYWdlJyxcbiAgICAgICAgY3Vyc29yOiAnbW92ZScsXG4gICAgICAgIHNjcm9sbFNlbnNpdGl2aXR5OiA0MCxcbiAgICAgICAgZm9yY2VQbGFjZWhvbGRlclNpemU6IHRydWUsXG4gICAgICAgIGZvcmNlSGVscGVyU2l6ZTogZmFsc2UsXG4gICAgICAgIGhlbHBlcjogJ2Nsb25lJyxcbiAgICAgICAgb3BhY2l0eTogMC42NSxcbiAgICAgICAgcGxhY2Vob2xkZXI6ICd3Yy1tZXRhYm94LXNvcnRhYmxlLXBsYWNlaG9sZGVyJyxcbiAgICAgICAgc3RhcnQ6IGZ1bmN0aW9uKCBldmVudCwgdWkgKSB7XG4gICAgICAgICAgICB1aS5pdGVtLmNzcyggJ2JhY2tncm91bmQtY29sb3InLCAnI2Y2ZjZmNicgKTtcbiAgICAgICAgfSxcbiAgICAgICAgc3RvcDogZnVuY3Rpb24oIGV2ZW50LCB1aSApIHtcbiAgICAgICAgICAgIHVpLml0ZW0ucmVtb3ZlQXR0ciggJ3N0eWxlJyApO1xuICAgICAgICB9LFxuICAgICAgICB1cGRhdGU6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgdmFyIGF0dGFjaG1lbnRfaWRzID0gJyc7XG5cbiAgICAgICAgICAgICQoICcjcHJvZHVjdF9pbWFnZXNfY29udGFpbmVyJyApLmZpbmQoICd1bCBsaS5pbWFnZScgKS5jc3MoICdjdXJzb3InLCAnZGVmYXVsdCcgKS5lYWNoKCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICB2YXIgYXR0YWNobWVudF9pZCA9ICQoIHRoaXMgKS5hdHRyKCAnZGF0YS1hdHRhY2htZW50X2lkJyApO1xuICAgICAgICAgICAgICAgIGF0dGFjaG1lbnRfaWRzID0gYXR0YWNobWVudF9pZHMgKyBhdHRhY2htZW50X2lkICsgJywnO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICRpbWFnZV9nYWxsZXJ5X2lkcy52YWwoIGF0dGFjaG1lbnRfaWRzICk7XG4gICAgICAgIH1cbiAgICB9KTtcblxuICAgIC8vIFJlbW92ZSBpbWFnZXMuXG4gICAgJCggJyNwcm9kdWN0X2ltYWdlc19jb250YWluZXInICkub24oICdjbGljaycsICdhLmRlbGV0ZScsIGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCB0aGlzICkuY2xvc2VzdCggJ2xpLmltYWdlJyApLnJlbW92ZSgpO1xuXG4gICAgICAgIHZhciBhdHRhY2htZW50X2lkcyA9ICcnO1xuXG4gICAgICAgICQoICcjcHJvZHVjdF9pbWFnZXNfY29udGFpbmVyJyApLmZpbmQoICd1bCBsaS5pbWFnZScgKS5jc3MoICdjdXJzb3InLCAnZGVmYXVsdCcgKS5lYWNoKCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBhdHRhY2htZW50X2lkID0gJCggdGhpcyApLmF0dHIoICdkYXRhLWF0dGFjaG1lbnRfaWQnICk7XG4gICAgICAgICAgICBhdHRhY2htZW50X2lkcyA9IGF0dGFjaG1lbnRfaWRzICsgYXR0YWNobWVudF9pZCArICcsJztcbiAgICAgICAgfSk7XG5cbiAgICAgICAgJGltYWdlX2dhbGxlcnlfaWRzLnZhbCggYXR0YWNobWVudF9pZHMgKTtcblxuICAgICAgICAvLyBSZW1vdmUgYW55IGxpbmdlcmluZyB0b29sdGlwcy5cbiAgICAgICAgJCggJyN0aXB0aXBfaG9sZGVyJyApLnJlbW92ZUF0dHIoICdzdHlsZScgKTtcbiAgICAgICAgJCggJyN0aXB0aXBfYXJyb3cnICkucmVtb3ZlQXR0ciggJ3N0eWxlJyApO1xuXG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9KTtcbn0pO1xuIl19
