let ConfigTaxonomy =  Backbone.View.extend({
    el: '#aff-amazon-import-config-taxonomy',

    events: {
        'change select[name="taxonomy"]': '_onChangeTaxonomy',
        'change select[name="term"]': '_onChangeTerm',
        'submit': '_onChangeTerm',
    },

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize() {
        let template = jQuery('#aff-amazon-import-config-taxonomy-template');
        this.template = _.template(template.html());

        this.listenTo(this.model, 'change', this.render);
    },

    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {ConfigTaxonomy}
     */
    render() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    },

    /**
     * Load the current taxonomy config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChangeTaxonomy(e) {
        e.preventDefault();

        let taxonomies = this.$el.find('select[name="taxonomy"]');

        this.model.set({
            'taxonomy': taxonomies.val() !== 'none' ? taxonomies.val() : null,
            'term': null,
        });
    },

    /**
     * Load the current term config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChangeTerm(e) {
        e.preventDefault();

        let terms = this.$el.find('select[name="term"]');

        this.model.set({
            'term': terms.val() !== 'none' ? terms.val() : null,
        });
    },
});

export default ConfigTaxonomy;
