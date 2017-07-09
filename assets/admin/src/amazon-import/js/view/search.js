import SearchForm from './search-form';
import SearchResults from './search-results';

let Search = Backbone.View.extend({
    el: '.aff-amazon-import-search',

    initialize() {
        this.form = new SearchForm({
            model: this.model.form,
        });

        this.results = new SearchResults({
            collection: this.model.results,
        });

        _.bindAll(this, 'process');

        this.form.$el.on('submit', this.process)
    },

    render() {
        this.form.render();
        this.results.render();

        return this;
    },

    process(e) {
        e.preventDefault();

        this.model.process();
        this.render();
    }
});

export default Search;
