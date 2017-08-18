let Config =  Backbone.View.extend({
    el: '#aff-amazon-import-config',

    events: {
        'change input[name="shop"]': 'changeShop',
        'change input[name="new-shop-name"]': 'changeShop',
        'change input[name="action"]': 'changeAction',
        'change input[name="merge-product-id"]': 'changeAction',
        'change input[name="replace-product-id"]': 'changeAction',
        'change input[name="status"]': 'changeStatus',
    },

    /**
     * Initialize the config.
     *
     * @since 0.9
     * @public
     */
    initialize() {
        let template = jQuery('#aff-amazon-import-config-template');
        this.template = _.template(template.html());

        this.model.set('selectedShop', jQuery(template.html()).find('input[name="shop"]').first().val());

        this.model.on('aff:amazon-import:config:add-shop', this.addShop, this);
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

        this.$el.find('.aff-amazon-import-config-group-option-merge-product-id, .aff-amazon-import-config-group-option-replace-product-id').selectize({
            maxItems: 1,
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            create: false,
            load(query, callback) {
                if (!query.length) return callback();
                jQuery.ajax({
                    url: affAdminAmazonImportUrls.apiRoot + 'wp/v2/aff-products/?status=publish,draft&search=' + query,
                    type: 'GET',
                    data: {
                        'post_parent': 0,
                    },
                    beforeSend(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', affAdminAmazonImportUrls.nonce)
                    },
                    error() {
                        callback();
                    },
                    success(results) {
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
     * Add a new shop
     *
     * @since 0.9
     * @param {Object} shop
     * @public
     */
    addShop(shop) {
        this.$el.find('input[value="new-shop"]').parent().before(`
            <label class="aff-import-config-group-label" for="${shop.slug}">
                <input id="${shop.slug}" class="aff-import-config-group-option" name="shop" type="radio" value="${shop.slug}">
                ${shop.name}         
            </label>
        `);

        this.$el.find(`input[name="shop"][value="${shop.slug}"]`).prop("checked", true);

        let newShopName = this.$el.find('input[name="new-shop-name"]');
        newShopName.selectize()[0].selectize.clear(true);
    },

    /**
     * Load the new shop configuration into the model on change.
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
     * Load the new action configuration into the model on change.
     *
     * @since 0.9
     * @public
     */
    changeAction() {
        let selectedAction = this.$el.find('input[name="action"]:checked'),
            mergeProductId = this.$el.find('input[name="merge-product-id"]'),
            replaceProductId = this.$el.find('input[name="replace-product-id"]'),
            mergeSelectize = mergeProductId.selectize()[0].selectize,
            replaceSelectize = replaceProductId.selectize()[0].selectize;

        selectedAction.val() === 'merge-product' ? mergeSelectize.enable() : mergeSelectize.disable();
        selectedAction.val() === 'replace-product' ? replaceSelectize.enable() : replaceSelectize.disable();

        this.model.set({
            'selectedAction': selectedAction.val(),
            'mergeProductId': mergeProductId.val(),
            'replaceProductId': replaceProductId.val()
        });
    },

    /**
     * Load the new status configuration into the model on change.
     *
     * @since 0.9
     * @public
     */
    changeStatus() {
        let selectedStatus = this.$el.find('input[name="status"]:checked');

        this.model.set({
            'status': selectedStatus.val(),
        });
    },
});

export default Config;
