let SearchResultsItem = Backbone.Model.extend({
    defaults: {
        'loading': false,
        'success': false,
        'successMessage': null,
        'error': false,
        'errorMessage': null,
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
     * Successfully finish the import with an optional message.
     *
     * @since 0.9
     * @param {string|null} message
     * @public
     */
    showSuccessMessage(message = null) {
        this.set({
            'loading': false,
            'success': true,
            'successMessage': message
        });

        this.trigger('aff:amazon-import:search:results:item:success', this);
    },

    /**
     * Display an error for import with an optional message.
     *
     * @since 0.9
     * @param {string|null} message
     * @public
     */
    showErrorMessage(message = null) {
        this.set({
            'loading': false,
            'error': true,
            'errorMessage': message,
        });

        this.trigger('aff:amazon-import:search:results:item:error', this);
    },
});

export default SearchResultsItem;
