let ConfigShop =  Backbone.View.extend({
    el: '#aff-amazon-import-config-shop',

    events: {
        'change input[name="shop"]': '_onChange',
        'blur input[name="new-shop-name"]': '_onChange',
        'submit': '_onChange',
    },

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize() {
        let templateHtml = jQuery('#aff-amazon-import-config-shop-template').html();
        this.template = _.template(templateHtml);

        this.listenTo(this.model, 'change', this.render);
    },

    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {ConfigShop}
     */
    render() {
        this.$el.html(this.template(this.model.toJSON()));
        this._initShop();
        this._checkShop();

        return this;
    },

    /**
     * Load the current config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChange(e) {
        e.preventDefault();

        let shop = this.$el.find('input[name="shop"]:checked');
        let newShopName = this.$el.find('input[name="new-shop-name"]');

        this.model.set({
            'shop': shop.val(),
            'newShopName': shop.val() === 'new-shop' ? newShopName.val() : null,
        });
    },

    /**
     * Check the selected shop.
     *
     * @since 0.9.16
     * @private
     */
    _initShop() {
        let shops = this.$el.find('input[name="shop"]');

        if(this.model.get('shop') == null) {
            this.model.set('shop', shops.first().val());
        }

        return this;
    },

    /**
     * Check the selected shop.
     *
     * @since 0.9.16
     * @private
     */
    _checkShop() {
        let shops = this.$el.find('input[name="shop"]');
        let shop = this.model.get('shop') == null ? shops.first().val() : this.model.get('shop');

        shops.val([shop]);

        return this;
    }
});

export default ConfigShop;
