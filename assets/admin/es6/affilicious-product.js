jQuery(function($) {
    const carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    class AffiliciousProduct {
        constructor() {
            this.view = this.getContainerView();
            this.typeView = this.getTypeView();
            this.variantsView = this.getVariantsView();
            this.enabledAttributesView = this.getEnabledAttributesView();

            this.typeView.model.on('change:value', this.toggleTabs, this);
            this.variantsView.model.on('change:value', this.toggleAttributes, this);
            this.enabledAttributesView.model.on('change:value', this.toggleAttributes, this);
        }

        toggleTabs() {
            // Supports multiple languages
            var productType = this.typeView.model.get('value'),
                variants = this.view.$el.find('a[data-id="' + translations.variants.trim().toLowerCase() + '"]').parent(),
                shops = this.view.$el.find('a[data-id="' + translations.shops.trim().toLowerCase() + '"]').parent();

            if(productType === 'complex') {
                variants.show();
                shops.hide();
            } else {
                variants.hide();
                shops.show();
            }
        }

        toggleAttributes() {
            let attributesViews = this.getVariantEnabledAttributesViews(),
                value = this.enabledAttributesView.model.get('value');

            for (let attributesView of attributesViews) {
                attributesView.model.set('value', value);
            }
        }

        getContainerView() {
            var containerView = null;

            _.each(carbon.views, function(view) {
                if(view.model && view.model.attributes && view.model.attributes.title == translations.container) {
                    containerView = view;
                }
            });

            return containerView;
        }

        getVariantsView() {
            var variantsView = null;

            _.each(carbon.views, function(view) {
                if(view.templateVariables && view.templateVariables.base_name == '_affilicious_product_variants') {
                    variantsView = view;
                }
            });

            return variantsView;
        }

        getTypeView() {
            var typeView = null;

            _.each(carbon.views, function(view) {
                if(view.templateVariables && view.templateVariables.base_name == '_affilicious_product_type') {
                    typeView = view;
                }
            });

            return typeView;
        }

        getEnabledAttributesView() {
            var enabledAttributesView = null;

            _.each(carbon.views, function(view) {
                if(view.templateVariables && view.templateVariables.base_name == '_affilicious_product_enabled_attributes') {
                    enabledAttributesView = view;
                }
            });

            return enabledAttributesView;
        }

        getVariantEnabledAttributesViews() {
            var variantEnabledAttributesViews = [];

            _.each(carbon.views, function(view) {
                if(view.templateVariables && view.templateVariables.base_name == 'enabled_attributes') {
                    variantEnabledAttributesViews.push(view);
                }
            });

            return variantEnabledAttributesViews;
        }
    }

    window.affiliciousProduct = new AffiliciousProduct();
});
