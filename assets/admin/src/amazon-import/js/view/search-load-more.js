let SearchLoadMore =  Backbone.View.extend({
    el: '#aff-amazon-import-search-load-more',

    events: {
        'click .aff-import-search-load-more-button': 'load',
    },

    /**
     * Initialize the search load more.
     *
     * @since 0.9
     * @public
     */
    initialize() {
        let templateHtml = jQuery('#aff-amazon-import-search-load-more-template').html();

        this.template = _.template(templateHtml);
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
        this.$el.html(this.template(this.model.attributes));

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
