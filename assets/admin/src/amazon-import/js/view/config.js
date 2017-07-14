let Config =  Backbone.View.extend({
    el: '.aff-amazon-import-config',

    events: {
        'change input[name="shop"]': 'changeShop',
        'change input[name="action"]': 'changeAction',
    },

    /**
     * Initialize the config.
     *
     * @since 0.9
     * @public
     */
    initialize() {
        let templateHtml = jQuery('#aff-amazon-import-config-template').html();
        this.template = _.template(templateHtml);
    },

    /**
     * Render the config.
     *
     * @since 0.9
     * @returns {Config}
     * @public
     */
    render() {
        let html = this.template(this.model.attributes);
        this.$el.html(html);

        this.$el.find('.aff-amazon-import-config-option-merge-product-id').selectize({
            maxItems: 1,
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            create: false,
            load: function(query, callback) {
                if (!query.length) return callback();
                jQuery.ajax({
                    url: '/wp-json/wp/v2/aff-products/?search=' + query,
                    type: 'GET',
                    error: function() {
                        callback();
                    },
                    success: function(results) {
                        results = results.map((result) => {
                            return {
                                'id': result.id,
                                'name': result.title.rendered
                            }
                        });

                        callback(results);
                    }
                });
            }
        });

        return this;
    },

    /**
     * Load the new config parameters into the model on change.
     *
     * @since 0.9
     * @public
     */
    changeShop() {
        let selectedShop = this.$el.find('input[name="shop"]:checked'),
            newShopName = this.$el.find('input[name="new-shop-name"]');

        selectedShop.val() === 'new-shop' ? newShopName.removeAttr('disabled') : newShopName.attr('disabled', 'disabled');

        this.model.set({
            'selectedShop': selectedShop.val(),
            'newShopName': newShopName.val(),
        });
    },

    /**
     * Load the new config parameters into the model on change.
     *
     * @since 0.9
     * @public
     */
    changeAction() {
        let selectedAction = this.$el.find('input[name="action"]:checked'),
            mergeProductId = this.$el.find('input[name="merge-product-id"]'),
            selectize = mergeProductId.selectize()[0].selectize;

        selectedAction.val() === 'merge-product' ? selectize.enable() : selectize.disable();

        this.model.set({
            'selectedAction': selectedAction.val(),
            'mergeProductId': mergeProductId.val()
        });
    },
});

export default Config;
