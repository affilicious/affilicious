let searchForm = Backbone.Model.extend({
    defaults: {
        'term': '',
        'type': 'keywords',
        'category': 'all'
    }
});

export default searchForm;
