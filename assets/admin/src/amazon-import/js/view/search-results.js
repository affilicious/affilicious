import ProductView from './search-results-item';

let SearchResults = Backbone.View.extend({
    el: '.aff-amazon-import-search-results',

    initialize(options) {
        this.collection = options.collection;

        // Ensure our methods keep the 'this' reference to the view itself
        _.bindAll(this, 'render', 'addOne');

        // Bind the collection events
        this.collection.bind('reset', this.render);
        this.collection.bind('add', this.render);
        this.collection.bind('remove', this.render);
        this.collection.bind('sync', this.render);
    },

    render() {
        this.addAll();
    },

    addAll() {
        this.$el.empty();
        this.collection.forEach(this.addOne);
    },

    addOne(product) {
        let view = new ProductView({
            model: product,
        });

        this.$el.append(view.render().el);
    },
});

export default SearchResults;
