let SearchResultsItem = Backbone.Model.extend({
    defaults: {
        'loading': false,
        'done': false,
        'error': false,
    },

    /**
     * Import the search result item.
     *
     * @since 0.9
     * @public
     */
    import() {
        this.set('loading', true);

        this.trigger('aff:amazon-import:search:results:item:import', this);
    },

    /**
     * Finish the search result item import.
     *
     * @since 0.9
     * @public
     */
    done() {
        this.set({
            'loading': false,
            'done': true,
        });

        this.trigger('aff:amazon-import:search:results:item:done', this);
    },

    /**
     * Display an error for the search result item import.
     *
     * @since 0.9
     * @public
     */
    error() {
        this.set({
            'loading': false,
            'error': true,
        });

        this.trigger('aff:amazon-import:search:results:item:error', this);
    },
});

export default SearchResultsItem;
