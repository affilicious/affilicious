jQuery(function($) {
    const carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    class CarbonView
    {
        static getContainerView() {
            var containerView = null;

            _.each(carbon.views, function(view) {
                if(view.model && view.model.attributes && view.model.attributes.title == affProductTranslations.container) {
                    containerView = view;
                }
            });

            return containerView;
        }

        static getVariantsView() {
            var variantsView = null;

            _.each(carbon.views, function(view) {
                if(view.templateVariables && view.templateVariables.base_name == '_affilicious_product_variants') {
                    variantsView = view;
                }
            });

            return variantsView;
        }

        static getTypeView() {
            var typeView = null;

            _.each(carbon.views, function(view) {
                if(view.templateVariables && view.templateVariables.base_name == '_affilicious_product_type') {
                    typeView = view;
                }
            });

            return typeView;
        }

        static getEnabledAttributesView() {
            var enabledAttributesView = null;

            _.each(carbon.views, function(view) {
                if(view.templateVariables && view.templateVariables.base_name == '_affilicious_product_enabled_attributes') {
                    enabledAttributesView = view;
                }
            });

            return enabledAttributesView;
        }

        static getVariantEnabledAttributesViews() {
            var variantEnabledAttributesViews = [];

            _.each(carbon.views, function(view) {
                if(view.templateVariables && view.templateVariables.base_name == 'enabled_attributes') {
                    variantEnabledAttributesViews.push(view);
                }
            });

            return variantEnabledAttributesViews;
        }
    }

    class AffiliciousProduct {
        constructor() {
            let typeView = CarbonView.getTypeView();
            let variantsView = CarbonView.getVariantsView();
            let enabledAttributesView = CarbonView.getEnabledAttributesView();

            if(!!typeView) {
                typeView.$el.ready(this.toggleTabs);
                typeView.model.on('change:value', this.toggleTabs);
            }

            if(!!variantsView) {
                variantsView.$el.ready(this.toggleAttributes);
                variantsView.model.on('change:value', this.toggleAttributes);
            }

            if(!!enabledAttributesView) {
                enabledAttributesView.$el.ready(this.toggleAttributes);
                enabledAttributesView.model.on('change:value', this.toggleAttributes);
            }
        }

        toggleTabs() {
            // Supports multiple languages
            var view = CarbonView.getContainerView(),
                typeView = CarbonView.getTypeView(),
                productType = typeView.model.get('value'),
                variants = view.$el.find('a[data-id="' + affProductTranslations.variants.trim().toLowerCase() + '"]').parent(),
                shops = view.$el.find('a[data-id="' + affProductTranslations.shops.trim().toLowerCase() + '"]').parent();

            if(productType === 'complex') {
                variants.show();
                shops.hide();
            } else {
                variants.hide();
                shops.show();
            }
        }

        toggleAttributes() {
            let enabledAttributesView = CarbonView.getEnabledAttributesView(),
                attributesViews = CarbonView.getVariantEnabledAttributesViews(),
                value = enabledAttributesView.model.get('value');

            for (let attributesView of attributesViews) {
                attributesView.model.set('value', value);
                attributesView.$el.find('input').val(value);
            }
        }
    }

    window.affiliciousProduct = new AffiliciousProduct();
});

jQuery(function($) {
    // TODO: Remove the code below in the beta
    var product_gallery_frame;
    var $image_gallery_ids = $( '#product_image_gallery' );
    var $product_images    = $( '#product_images_container' ).find( 'ul.product_images' );

    $( '.add_product_images' ).on( 'click', 'a', function( event ) {
        var $el = $( this );

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( product_gallery_frame ) {
            product_gallery_frame.open();
            return;
        }

        // Create the media frame.
        product_gallery_frame = wp.media.frames.product_gallery = wp.media({
            // Set the title of the modal.
            title: $el.data( 'choose' ),
            button: {
                text: $el.data( 'update' )
            },
            states: [
                new wp.media.controller.Library({
                    title: $el.data( 'choose' ),
                    filterable: 'all',
                    multiple: true
                })
            ]
        });

        // When an image is selected, run a callback.
        product_gallery_frame.on( 'select', function() {
            var selection = product_gallery_frame.state().get( 'selection' );
            var attachment_ids = $image_gallery_ids.val();

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $product_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
                }
            });

            $image_gallery_ids.val( attachment_ids );
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
        start: function( event, ui ) {
            ui.item.css( 'background-color', '#f6f6f6' );
        },
        stop: function( event, ui ) {
            ui.item.removeAttr( 'style' );
        },
        update: function() {
            var attachment_ids = '';

            $( '#product_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
                var attachment_id = $( this ).attr( 'data-attachment_id' );
                attachment_ids = attachment_ids + attachment_id + ',';
            });

            $image_gallery_ids.val( attachment_ids );
        }
    });

    // Remove images.
    $( '#product_images_container' ).on( 'click', 'a.delete', function() {
        $( this ).closest( 'li.image' ).remove();

        var attachment_ids = '';

        $( '#product_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
            var attachment_id = $( this ).attr( 'data-attachment_id' );
            attachment_ids = attachment_ids + attachment_id + ',';
        });

        $image_gallery_ids.val( attachment_ids );

        // Remove any lingering tooltips.
        $( '#tiptip_holder' ).removeAttr( 'style' );
        $( '#tiptip_arrow' ).removeAttr( 'style' );

        return false;
    });
});
