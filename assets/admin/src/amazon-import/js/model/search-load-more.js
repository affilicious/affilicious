let SearchLoadMore = Backbone.Model.extend({
    defaults: {
        'loading': false,
        'noResults': false,
    },

    /**
     * Activate the loading spinner animation.
     *
     * @public
     */
    load() {
        this.set('loading', true);
        this.trigger('aff:amazon-import:search:load-more:load', this);
    },

    /**
     * Show the load more button and deactivate the spinner animation.
     *
     * @public
     */
    done() {
        this.set('loading', false);
        this.trigger('aff:amazon-import:search:load-more:done', this);
    },

    /**
     * Show the no results message and deactivate the spinner animation.
     *
     * @public
     */
    noResults() {
        this.set({
            'loading' : false,
            'noResults': true,
        });

        this.trigger('aff:amazon-import:search:load-more:no-results', this);
    }
});

export default SearchLoadMore;
