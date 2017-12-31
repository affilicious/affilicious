let ConfigStatus =  Backbone.View.extend({
    el: '#aff-amazon-import-config-status',

    events: {
        'change input[name="status"]': '_onChange',
        'submit': '_onChange',
    },

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize() {
        let templateHtml = jQuery('#aff-amazon-import-config-status-template').html();
        this.template = _.template(templateHtml);

        this.listenTo(this.model, 'change', this.render);
    },

    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {ConfigStatus}
     */
    render() {
        this.$el.html(this.template(this.model.toJSON()));

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

        let status = this.$el.find('input[name="status"]:checked');

        this.model.set({
            'status': status.val(),
        });
    },
});

export default ConfigStatus;
