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
        let term = this.$el.find('input[name="term"]').val(),
            type = this.$el.find('select[name="type"]').val(),
            category = this.$el.find('select[name="category"]').val();


        this.model.set({
            'term': term,
            'type': type,
            'category': category
        });
    },
});

export default SearchForm;
