import SearchResultItem from './search-results-item';

let SearchResults = Backbone.Collection.extend({
    model: SearchResultItem,
});

export default SearchResults;
