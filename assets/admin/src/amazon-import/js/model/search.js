import SearchForm from './search-form';
import SearchResults from './search-results';

let Search = Backbone.Model.extend({
    initialize(options) {
        this.form = new SearchForm();
        this.results = new SearchResults();
        this.page = options && options.page ? options.page : 1;

        _.bindAll(this, 'process');
    },

    process() {
        this.results.fetch();
    }
});

export default Search;
