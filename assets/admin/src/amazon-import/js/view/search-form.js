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
        let templateHtml = jQuery('#aff-amazon-import-search-form-template').html(),
            providerConfigured = this.$el.data('provider-configured');

        this.template = _.template(templateHtml);

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
        this.$el.html(this.template(this.model.attributes));

        let type = this.$el.find('select[name="type"]'),
            category = this.$el.find('select[name="category"]'),
            withVariants = this.$el.find('select[name="with-variants"]');

        type.val(this.model.get('type'));
        category.val(this.model.get('category'));
        withVariants.val(this.model.get('withVariants'));

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
