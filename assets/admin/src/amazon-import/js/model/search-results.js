import SearchResultItem from './search-results-item';

let SearchResults = Backbone.Collection.extend({
    model: SearchResultItem,

    initialize(options) {
        if(options && options.page) {
            this.page = options.page;
        }
    },

    url() {
        return affAdminAmazonImportUrls.ajax + '?action=aff_product_admin_amazon_search';
    },
});

export default SearchResults;
