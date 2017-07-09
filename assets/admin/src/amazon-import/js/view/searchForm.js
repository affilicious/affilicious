let searchForm =  Backbone.View.extend({
    el: '.aff-amazon-import-search',

    events: {
        'change': 'changed',
        'submit': 'submitted'
    },

    render() {
        let html = jQuery('.aff-amazon-import-search-template').html(),
            template = _.template(html);

        this.$el.html(template(this.model.attributes));

        return this;
    },

    changed() {
        let term = this.$el.find('input[name="term"]').val(),
            type = this.$el.find('select[name="type"]').val(),
            category = this.$el.find('select[name="category"]').val();

        this.model.set('term', term);
        this.model.set('type', type);
        this.model.set('category', category);

        this.render();
    },

    submitted(e) {
        e.preventDefault();

        this.trigger('search', e);
    }
});

export default searchForm;
