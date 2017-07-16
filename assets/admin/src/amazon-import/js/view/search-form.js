let SearchForm =  Backbone.View.extend({
    el: '.aff-amazon-import-search-form',

    events: {
        'change': 'change',
        'submit': 'submit',
    },

    /**
     * Initialize the search form.
     *
     * @since 0.9
     * @public
     */
    initialize() {
        let providerConfigured = this.$el.data('provider-configured');

        this.model.set('providerConfigured', providerConfigured === true || providerConfigured === 'true');
        this.model.on('change', this.render, this);
    },

    /**
     * Render the search form.
     *
     * @since 0.9
     * @returns {SearchForm}
     * @public
     */
    render() {
        let html = jQuery('#aff-amazon-import-search-form-template').html(),
            template = _.template(html);

        this.$el.html(template(this.model.attributes));

        return this;
    },

    /**
     * Submit the search form.
     *
     * @since 0.9
     * @param e
     * @public
     */
    submit(e) {
        e.preventDefault();

        this.change();
        this.model.submit();
    },

    /**
     * Load the new search parameters into the model on form change.
     *
     * @since 0.9
     * @public
     */
    change() {
        let term = this.$el.find('input[name="term"]'),
            type = this.$el.find('select[name="type"]'),
            category = this.$el.find('select[name="category"]'),
            withVariants = this.$el.find('select[name="with-variants"]');

        this.model.set({
            'term': term.val(),
            'type': type.val(),
            'category': category.val(),
            'withVariants': withVariants.val()
        });
    },
});

export default SearchForm;
