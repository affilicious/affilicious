let SearchLoadMore = Backbone.Model.extend({
    defaults: {
        'enabled': true,
        'loading': false,
        'noResults': false,
        'error': false,
        'errorMessage': null,
    },

    /**
     * Activate the loading spinner animation.
     *
     * @since 0.9
     * @public
     */
    load() {
        this.set('loading', true);
        this.trigger('aff:amazon-import:search:load-more:load', this);
    },

    /**
     * Show the load more button and deactivate the spinner animation.
     *
     * @since 0.9
     * @param {boolean} enabled
     * @public
     */
    done(enabled = true) {
        this.set({
            'loading': false,
            'enabled': enabled,
        });

        this.trigger('aff:amazon-import:search:load-more:done', this);
    },

    /**
     * Show the no results message and deactivate the spinner animation.
     *
     * @since 0.9
     * @public
     */
    noResults() {
        this.set({
            'loading' : false,
            'noResults': true,
        });

        this.trigger('aff:amazon-import:search:load-more:no-results', this);
    },

    /**
     * Show a load more error and deactivate the spinner animation.
     *
     * @since 0.9
     * @param {string|null} message
     * @public
     */
    error(message = null) {
        this.set({
            'enabled': true,
            'loading': false,
            'error': true,
            'errorMessage': message,
        });

        this.trigger('aff:amazon-import:search:load-more:error', this);
    }
});

export default SearchLoadMore;
