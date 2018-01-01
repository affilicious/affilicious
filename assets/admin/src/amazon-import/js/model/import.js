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
     * @param {SearchResultsItem} searchResultsItem
     * @public
     */
    import(searchResultsItem) {
        let data = {
            'product': {
                'name' : searchResultsItem.get('name'),
                'type' : searchResultsItem.get('type'),
                'shops' : searchResultsItem.get('shops'),
                'custom_values' : searchResultsItem.get('custom_values'),
            },
            'config': this.config.parse(),
            'form': this.search.form.parse(),
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

            searchResultsItem.showSuccessMessage();
        }).fail((result) => {
            let errorMessage = ((((result || {}).responseJSON || {}).data || {})[0] || {}).message || null;

            searchResultsItem.showErrorMessage(errorMessage);
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
