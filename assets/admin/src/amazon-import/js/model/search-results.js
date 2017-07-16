import SearchResultItem from './search-results-item';

let SearchResults = Backbone.Collection.extend({
    model: SearchResultItem,

    /**
     * Parse the Wordpress json Ajax response.
     *
     * @since 0.9
     * @param {Array} response
     * @returns {Array}
     */
    parse: function(response) {
        return response && response.success ? response.data : [];
    }
});

export default SearchResults;
