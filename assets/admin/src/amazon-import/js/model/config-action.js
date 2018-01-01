let ConfigAction = Backbone.Model.extend({
    defaults: {
        'action': 'new-product',
        'mergeProductId': null,
    },
});

export default ConfigAction;
