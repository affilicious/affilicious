import ConfigShop from "./config-shop";
import ConfigStatus from "./config-status";
import ConfigTaxonomy from "./config-taxonomy";
import ConfigAction from "./config-action";

let Config =  Backbone.View.extend({
    el: '#aff-amazon-import-config',

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize() {
        this.shop = new ConfigShop({model: this.model.shop});
        this.status = new ConfigStatus({model: this.model.status});
        this.taxonomy = new ConfigTaxonomy({model: this.model.taxonomy});
        this.action = new ConfigAction({model: this.model.action});
    },

    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {Config}
     */
    render() {
        this.shop.render();
        this.status.render();
        this.taxonomy.render();
        this.action.render();

        return this;
    },
});

export default Config;
