jQuery(function($) {
    // ------------------------------------------------------------------------
    var carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    function getAffiliciousView() {
        var affiliciousView = null;
        _.each(carbon.views, function(view) {
            if(view.model && view.model.attributes && view.model.attributes.title == translations.container) {
                affiliciousView = view;
            }
        });

        return affiliciousView;
    }

    function getVariantsView() {
        var variantsView = null;
        _.each(carbon.views, function(view) {
            if(view.templateVariables && view.templateVariables.base_name == '_affilicious_product_variants') {
                variantsView = view;
            }
        });

        return variantsView;
    }

    function getEnabledAttributesView() {
        var enabledViews = [];
        _.each(carbon.views, function(view) {
            if(view.templateVariables && view.templateVariables.base_name == 'enabled_attributes') {
                enabledViews.push(view);
            }
        });

        return enabledViews;
    }

    function getBaseEnabledAttributesView() {
        var enabledView = null;
        _.each(carbon.views, function(view) {
            if(view.templateVariables && view.templateVariables.base_name == '_affilicious_product_enabled_attributes') {
                enabledView = view;
            }
        });

        return enabledView;
    }

    function toggleTabs() {
        // Supports multiple languages
        var affiliciousView = getAffiliciousView(),
            select = $('select[name="_affilicious_product_type"]'),
            value = select.val(),
            variants = affiliciousView.$el.find('a[data-id="' + translations.variants.trim().toLowerCase() + '"]').parent(),
            shops = affiliciousView.$el.find('a[data-id="shops"]').parent();

        if(value === 'complex') {
            variants.show();
            shops.hide();
        } else {
            variants.hide();
            shops.show();
        }
    }

    var affiliciousView = getAffiliciousView();

    affiliciousView.$el.find('select[name="_affilicious_product_type"]').ready(toggleTabs);
    affiliciousView.$el.on('change select[name="_affilicious_product_type"]', toggleTabs);

    var variantsView = getVariantsView();
    var baseAttributesView = getBaseEnabledAttributesView();

    variantsView.model.on('change:value',  function() {
        var attributesViews = getEnabledAttributesView();
        console.log(window.carbon);
        var value = baseAttributesView.model.get('value');

        _.each(attributesViews, function(attributesView) {
            attributesView.model.set('value', value);
        });
    });


    baseAttributesView.model.on('change:value',  function() {
        var attributesViews = getEnabledAttributesView();
        console.log(window.carbon);
        var value = baseAttributesView.model.get('value');

        _.each(attributesViews, function(attributesView) {
            attributesView.model.set('value', value);
        });
    });
});
