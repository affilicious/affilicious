import SearchForm from './search-form';
import SearchLoadMore from './search-load-more';
import SearchResults from './search-results';

let Search = Backbone.View.extend({
    el: '#aff-amazon-import-search',

    /**
     * Initialize the search.
     *
     * @since 0.9
     * @public
     */
    initialize() {
        this.form = new SearchForm({
            model: this.model.form,
        });

        this.results = new SearchResults({
            collection: this.model.results,
        });

        this.loadMore = new SearchLoadMore({
            model: this.model.loadMore,
        });

        this.model.on('change', this.render, this);
    },

    /**
     * Render the search.
     *
     * @since 0.9
     * @public
     */
    render() {
        this.form.render();
        this.results.render();

        if(this.model.get('started')) {
            this.loadMore.render();
        }

        return this;
    },
});

export default Search;
