let SearchResultsItem = Backbone.View.extend({
    tagName: 'div',

    className: '',

    events: {
        'click .aff-amazon-import-search-results-item-variants-show-all': 'showAll'
    },

    /**
     * Initialize the search results item.
     *
     * @public
     */
    initialize() {
        this.model.on('change', this.render, this);
    },

    /**
     * Render the search results item.
     *
     * @return {SearchResultsItem}
     * @public
     */
    render() {
        let html = jQuery('#aff-amazon-import-search-results-item-template').html(),
            template = _.template(html);

        this.setElement(template(this.model.attributes));

        return this;
    },

    /**
     * Show all hidden variants.
     *
     * @param e
     * @public
     */
    showAll(e) {
        e.preventDefault();

        this.$el.find('.aff-amazon-import-search-results-item-variants-show-all').hide();
        this.$el.find('.aff-amazon-import-search-results-item-variants-item').show();
    },
});

export default SearchResultsItem;
