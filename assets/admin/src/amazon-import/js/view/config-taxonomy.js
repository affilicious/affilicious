let ConfigTaxonomy =  Backbone.View.extend({
    el: '#aff-amazon-import-config-taxonomy',

    events: {
        'change select[name="taxonomy"]': '_onChange',
        'change input[name="terms"]': '_onChange',
        'submit': '_onChange',
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

        this.listenTo(this.model, 'change:taxonomy', this.render);
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
        this._selectize();

        return this;
    },

    /**
     * Load the current config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChange(e) {
        e.preventDefault();

        let taxonomies = this.$el.find('select[name="taxonomy"]');
        let terms = this.$el.find('input[name="terms"]');
        let selectize = terms.selectize()[0].selectize;

        taxonomies.val() === null || taxonomies.val() === 'none' ? selectize.disable() : selectize.enable();

        this.model.set({
            'taxonomy': taxonomies.val() !== 'none' ? taxonomies.val() : null,
            'terms': terms.val(),
        });
    },

    /**
     * Selectize the input for enabling auto-completion and product search.
     *
     * @since 0.9.16
     * @private
     */
    _selectize() {
        let apiRoot = affAdminAmazonImportUrls.apiRoot;
        let nonce = affAdminAmazonImportUrls.nonce;
        let terms = this.$el.find('input[name="terms"]');

        terms.selectize({
            delimiter: ',',
            valueField: 'slug',
            labelField: 'name',
            searchField: 'name',
            create: false,
            load: (query, callback) => {
                let taxonomy = this.model.get('taxonomy');

                if (!query.length || !taxonomy) {
                    return callback();
                }

                jQuery.ajax({
                    url: `${apiRoot}wp/v2/${taxonomy}`,
                    type: 'GET',
                    beforeSend(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', nonce)
                    },
                    error() {
                        callback();
                    },
                    success(results) {
                        results = results.map((result) => {
                            return {
                                'id': result.id,
                                'name': result.name,
                                'slug': result.slug,
                                'taxonomy': result.taxonomy,
                            }
                        });

                        callback(results);
                    }
                });
            }
        });
    }
});

export default ConfigTaxonomy;
