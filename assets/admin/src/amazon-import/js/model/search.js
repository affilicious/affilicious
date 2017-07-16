import SearchForm from './search-form';
import SearchLoadMore from './search-load-more';
import SearchResults from './search-results';

let Search = Backbone.Model.extend({
    defaults: {
        'started': false,
        'action': 'aff_product_admin_amazon_search',
        'page' : 1,
    },

    /**
     * Initialize the search with the given options.
     *
     * @since 0.9
     * @param {array} options
     */
    initialize(options) {
        this.form = new SearchForm();
        this.results = new SearchResults();
        this.loadMore = new SearchLoadMore();
        this.page = options && options.page ? options.page : 1;

        this.form.on('aff:amazon-import:search:search-form:submit', this.start, this);
        this.loadMore.on('aff:amazon-import:search:load-more:load', this.load, this);
    },

    /**
     * Start the search with the first page.
     *
     * @since 0.9
     * @public
     */
    start() {
        if(this.form.get('term') === null) {
            return;
        }

        this.set('page', 1);
        this.results.url = this._buildUrl();

        this.results.fetch().done(() => {
            this.set('started', true);
            this.form.done();
        });
    },

    /**
     * Load more search results by increasing the page.
     *
     * @since 0.9
     * @public
     */
    load() {
        this.set('page', this.get('page') + 1);

        this.results.url = this._buildUrl();
        this.results.fetch({'remove': false}).done(() => {
            this.loadMore.done();
        });
    },

    /**
     * Build the search API url based on the given parameters.
     *
     * @since 0.9
     * @returns {string}
     * @private
     */
    _buildUrl() {
        return affAdminAmazonImportUrls.ajax
            + `?action=${this.get('action')}`
            + `&aff-term=${this.form.get('term')}`
            + `&aff-type=${this.form.get('type')}`
            + `&aff-category=${this.form.get('category')}`
            + `&aff-with-variants=${this.form.get('withVariants')}`
            + `&aff-page=${this.get('page')}`
    }
});

export default Search;
