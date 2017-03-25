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
                if(view.model && view.model.attributes && view.model.attributes.title == translations.container) {
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
                variants = view.$el.find('a[data-id="' + translations.variants.trim().toLowerCase() + '"]').parent(),
                shops = view.$el.find('a[data-id="' + translations.shops.trim().toLowerCase() + '"]').parent();

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
            }
        }
    }

    window.affiliciousProduct = new AffiliciousProduct();
});
