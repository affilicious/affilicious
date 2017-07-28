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

        this.results.on('aff:amazon-import:search:results:import-item', this.import, this);
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

        this.results.fetch().done((results) => {
            this.loadMore.set('enabled', this._isLoadMoreEnabled(results));
            this.form.done();
        }).fail((result) => {
            let errorMessage = ((((result || {}).responseJSON || {}).data || {})[0] || {}).message || null;

            this.form.error(errorMessage);
            this.loadMore.set('enabled', false);
        }).always(() => {
            this.set('started', true);
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

        this.results.fetch({'remove': false}).done((results) => {
            this.loadMore.done(this._isLoadMoreEnabled(results));
        }).fail(() => {
            let errorMessage = ((((result || {}).responseJSON || {}).data || {})[0] || {}).message || null;

            this.loadMore.error(errorMessage);
        });
    },

    /**
     * Import the given search results item.
     *
     * @since 0.9
     * @param {Object} model
     * @public
     */
    import(model) {
        this.trigger('aff:amazon-import:import-results-item', model);
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
            + `&term=${this.form.get('term')}`
            + `&type=${this.form.get('type')}`
            + `&category=${this.form.get('category')}`
            + `&with-variants=${this.form.get('withVariants')}`
            + `&page=${this.get('page')}`
    },

    /**
     * Check if the load more button is enabled (visible).
     *
     * @since 0.9
     * @param {Array|null} results
     * @returns {bool}
     * @private
     */
    _isLoadMoreEnabled(results) {
        return (results && results.data && results.data.length > 0)
            && this.get('page') < 5
            && this.form.get('type') === 'keywords';
    }
});

export default Search;
