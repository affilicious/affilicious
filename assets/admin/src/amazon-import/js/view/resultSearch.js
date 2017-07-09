import ProductView from './result';

export default Backbone.View.extend({

    initialize(options) {
        this.collection = options.collection;
        this.page = options.page;
        this.results = this.$el.find('.aff-amazon-import-results');

        // Ensure our methods keep the `this` reference to the view itself
        _.bindAll(this, 'search', 'render', 'addOne');

        // Bind the collection events
        /*this.collection.bind('reset', this.render);
        this.collection.bind('add', this.render);
        this.collection.bind('remove', this.render);*/
        this.collection.bind('sync', this.render);

        // Trigger the search if the user completes typing.
        //this.searchInput.on('keyup', _.debounce(this.search, 400));
    },

    render() {
        this.addAll();
    },

    addAll() {
        this.results.empty();
        this.collection.forEach(this.addOne);
    },

    addOne(product) {
        let view = new ProductView({
            model: product,
        });

        this.results.append(view.render().el);
    },

    search(e) {
        if(e) {
            e.preventDefault();
        }

        let search = this.searchInput.val();
        this.collection.search = (search && search.length > 0) ? search : false;
        this.collection.fetch({remove: false}).done(() => {

        });
    },

});
