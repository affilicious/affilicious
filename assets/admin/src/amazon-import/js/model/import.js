import Search from './search';
import Config from './config';

let Import = Backbone.Model.extend({
    defaults: {
        'action': 'aff_product_admin_amazon_import',
    },

    /**
     * Initialize the import.
     *
     * @since 0.9
     */
    initialize() {
        this.search = new Search();
        this.config = new Config();

        this.search.on('aff:amazon-import:import-results-item', this.import, this);
    },

    /**
     * Import the product.
     *
     * @since 0.9
     * @param product
     * @public
     */
    import(product) {
        let data = {
            'product': {
                'name' : product.attributes.name,
                'type' : product.attributes.type,
                'shops' : product.attributes.shops,
                'custom_values' : product.attributes.custom_values,
            },
            'config': this.config.attributes,
            'form': this.search.form.attributes,
        };

        jQuery.ajax({
            type: 'POST',
            url: this._buildUrl(),
            data: data,
        }).done((result) => {
            let shopTemplate = ((result || {}).data || {}).shop_template || null;

            if(shopTemplate) {
                this.config.trigger('aff:amazon-import:config:add-shop', shopTemplate);
            }

            product.showSuccessMessage();
        }).fail((result) => {
            let errorMessage = ((((result || {}).responseJSON || {}).data || {})[0] || {}).message || null;

            product.showErrorMessage(errorMessage);
        })
    },

    /**
     * Build the import url based on the given parameters.
     *
     * @since 0.9
     * @returns {string}
     * @private
     */
    _buildUrl() {
        return affAdminAmazonImportUrls.ajax
            + `?action=${this.get('action')}`
        ;
    },
});

export default Import;
