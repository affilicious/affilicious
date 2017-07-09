export default Backbone.View.extend({
    template: _.template(jQuery('.aff-amazon-import-results-template').html()),

    tagName: 'div',

    className: '',

    events: {
        'click .aff-amazon-import-results-item-variants-show-all': 'showAll'
    },

    initialize: function() {
        this.render();
    },

    render() {
        this.setElement(this.template(this.model.attributes));
        return this;
    },

    showAll(e) {
        e.preventDefault();
        this.$el.find('.aff-amazon-import-results-item-variants-show-all').hide();
        this.$el.find('.aff-amazon-import-results-item-variants-item').removeClass('aff-amazon-import-results-item-variants-item-hidden');
    },
});
