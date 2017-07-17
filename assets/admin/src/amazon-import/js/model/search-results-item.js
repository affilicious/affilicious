let SearchResultsItem = Backbone.Model.extend({

    /**
     * Import the search result item.
     *
     * @since 0.9
     * @public
     */
    import() {
        this.trigger('aff:amazon-import:search:results:item:import', this);
    },
});

export default SearchResultsItem;
