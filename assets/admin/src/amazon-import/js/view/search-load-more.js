let SearchLoadMore =  Backbone.View.extend({
    el: '.aff-amazon-import-load-more',

    events: {
        'click .aff-amazon-import-load-more-button': 'load',
    },

    /**
     * Initialize the search load more.
     *
     * @since 0.9
     * @public
     */
    initialize() {
        this.model.on('change', this.render, this);
    },

    /**
     * Render the search load more.
     *
     * @since 0.9
     * @return {SearchLoadMore}
     * @public
     */
    render() {
        let html = jQuery('#aff-amazon-import-load-more-template').html(),
            template = _.template(html);

        this.$el.html(template(this.model.attributes));

        return this;
    },

    /**
     * Enable the loading animation.
     *
     * @since 0.9
     * @public
     */
    load() {
        this.model.load();
    }
});

export default SearchLoadMore;
