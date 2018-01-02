let ConfigShop = Backbone.Model.extend({
    defaults: {
        'shop': null,
        'newShopName': null,
        'addedShops': [],
    },

    /**
     * Add a new shop to the config.
     *
     * @since 0.9.16
     * @public
     * @param {Object} shop
     */
    addShop(shop) {
        let addedShops = this.get('addedShops');

        addedShops.push(shop);

        this.set({
            'shop': shop.slug,
            'newShopName': null,
            'addedShops': addedShops,
        });
    },
});

export default ConfigShop;
