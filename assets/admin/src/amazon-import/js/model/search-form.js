let SearchForm = Backbone.Model.extend({
    defaults: {
        'term': '',
        'type': 'keywords',
        'category': 'All',
        'minPrice': null,
        'maxPrice': null,
        'condition': 'New',
        'sort': '-price',
        'withVariants': 'no',
        'loading': false,
        'error': false,
        'errorMessage': null,
        'noResults': false,
        'noResultsMessage': null,
        'providerConfigured': false
    },

    /**
     * Submit the form the form and trigger the loading animation.
     *
     * @since 0.9
     * @public
     */
    submit() {
        this.set({
            'loading': true,
            'error': false,
            'errorMessage': null,
            'noResults': false,
            'noResultsMessage': null,
        });

        this.trigger('aff:amazon-import:search:search-form:submit', this);
    },

    /**
     * Finish the submit and stop the loading animation.
     *
     * @since 0.9
     * @public
     */
    done() {
        this.set('loading', false);

        this.trigger('aff:amazon-import:search:search-form:done', this);
    },

    /**
     * Finish the search submit with no results and stop the loading animation.
     *
     * @since 0.9.14
     * @param {string|null} message
     * @public
     */
    noResults(message = null) {
        this.set({
            'loading': false,
            'noResults': true,
            'noResultsMessage': message,
        });

        this.trigger('affebayiu:ebay-import:search:search-form:no-results', this);
    },

    /**
     * Show a submit error and stop the loading animation.
     *
     * @since 0.9
     * @param {string|null} message
     * @public
     */
    error(message = null) {
        this.set({
            'loading': false,
            'error': true,
            'errorMessage': message,
        });

        this.trigger('aff:amazon-import:search:search-form:error', this);
    }
});

export default SearchForm;
