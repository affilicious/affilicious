import Product from './result';

export default Backbone.Collection.extend({
    model: Product,

    initialize(options) {
        if(options && options.search) {
            this.search = options.search;
        }

        if(options && options.page) {
            this.page = options.page;
        }

        this.on('change:selected', () => {
            this.trigger('change');
        });
    },

    url() {
        return affAdminAmazonImportUrls.ajax + '?action=aff_product_admin_amazon_search';
    },
});
