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
     * @param {Backbone.Model} product
     * @public
     */
    import(product) {
        let data = {
            'product': product.attributes,
            'config': this.config.attributes,
            'form': this.search.form.attributes,
        };

        jQuery.ajax({
            type: 'POST',
            url: this._buildUrl(),
            data: data,
            dataType: "application/json",
        }).done(function() {
            alert('Done');
        }).fail(function() {
            alert('Fail');
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
