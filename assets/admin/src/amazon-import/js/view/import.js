import Search from './search';
import Config from './config';

let Import = Backbone.View.extend({
    el: '.aff-amazon-import',

    /**
     * Initialize the import.
     *
     * @since 0.9
     * @public
     */
    initialize() {
        this.search = new Search({
            model: this.model.search,
        });

        this.config = new Config({
            model: this.model.config,
        });
    },

    /**
     * Render the import.
     *
     * @since 0.9
     * @public
     */
    render() {
        this.search.render();
        this.config.render();

        return this;
    },
});

export default Import;
