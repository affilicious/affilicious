let SearchForm = Backbone.Model.extend({
    defaults: {
        'term': '',
        'type': 'keywords',
        'category': 'all',
        'loading': false,
    },

    /**
     * Submit the form the form and trigger the loading animation.
     *
     * @public
     */
    submit() {
        this.set('loading', true);
        this.trigger('aff:amazon-import:search:search-form:submit', this);
    },

    /**
     * Finish the submit and stop the loading animation.
     *
     * @public
     */
    done() {
        this.set('loading', false);
        this.trigger('aff:amazon-import:search:search-form:done', this);
    }
});

export default SearchForm;
