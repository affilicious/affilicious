let ConfigAction =  Backbone.View.extend({
    el: '#aff-amazon-import-config-action',

    events: {
        'change input[name="action"]': '_onChange',
        'change input[name="merge-product-id"]': '_onChange',
        'submit': '_onChange',
    },

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize() {
        let template = jQuery('#aff-amazon-import-config-action-template');
        this.template = _.template(template.html());
    },

    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {ConfigAction}
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

        let action = this.$el.find('input[name="action"]:checked');
        let mergeProductId = this.$el.find('input[name="merge-product-id"]');
        let mergeSelectize = mergeProductId.selectize()[0].selectize;

        action.val() === 'merge-product' ? mergeSelectize.enable() : mergeSelectize.disable();

        this.model.set({
            'action': action.val(),
            'mergeProductId': mergeProductId.val(),
        });
    },

    /**
     * Selectize the input for enabling auto-completion and product search.
     *
     * @since 0.9.16
     * @private
     */
    _selectize() {
        let mergeProductId = this.$el.find('input[name="merge-product-id"]');

        mergeProductId.selectize({
            maxItems: 1,
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            create: false,
            load(query, callback) {
                if (!query.length) return callback();
                jQuery.ajax({
                    url: affAdminAmazonImportUrls.apiRoot + 'wp/v2/aff-products/?status=publish,draft&search=' + query,
                    type: 'GET',
                    data: {
                        'post_parent': 0,
                    },
                    beforeSend(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', affAdminAmazonImportUrls.nonce)
                    },
                    error() {
                        callback();
                    },
                    success(results) {
                        results = results.map((result) => {
                            return {
                                'id': result.id,
                                'name': result.title.rendered
                            }
                        });

                        callback(results);
                    }
                });
            }
        });
    }
});

export default ConfigAction;
