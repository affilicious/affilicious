import ConfigShop from "./config-shop";
import ConfigStatus from "./config-status";
import ConfigTaxonomy from "./config-taxonomy";
import ConfigAction from "./config-action";

let Config = Backbone.Model.extend({

    /**
     * Initialize the config with all sub configs.
     *
     * @since 0.9.16
     * @public
     */
    initialize() {
        this.shop = new ConfigShop();
        this.status = new ConfigStatus();
        this.taxonomy = new ConfigTaxonomy();
        this.action = new ConfigAction();
    },

    /**
     * Parse the config into an object.
     *
     * @since 0.9.16
     * @public
     * @returns {{shop, newShopName, status, taxonomy, term, action, mergeProductId}}
     */
    parse() {
        return {
            'shop': this.shop.get('shop'),
            'newShopName': this.shop.get('newShopName'),
            'status': this.status.get('status'),
            'taxonomy': this.taxonomy.get('taxonomy'),
            'terms': this.taxonomy.get('terms'),
            'action': this.action.get('action'),
            'mergeProductId': this.action.get('mergeProductId'),
        }
    }
});

export default Config;
