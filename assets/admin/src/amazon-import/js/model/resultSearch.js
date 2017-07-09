import Product from './result';
import Search from './searchForm';

export default Backbone.Collection.extend({
    model: Product,

    search: new Search(),

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

        this.search.on('change')




    },

    url() {
        return affAdminAmazonImportUrls.ajax + '?action=aff_product_admin_amazon_search';
    },
});
