export default Backbone.View.extend({
    template: _.template(jQuery('.aff-amazon-import-results-template').html()),

    tagName: 'div',

    className: '',

    events: {
        'click .aff-amazon-import-results-item-variants-show-all': 'showAll'
    },

    initialize: function() {
        this.model.on('change', this.render);
        this.render();
    },

    render() {
        this.setElement(this.template(this.model.attributes));
        return this;
    },

    showAll(e) {
        e.preventDefault();
        this.$el.find('.aff-amazon-import-results-item-variants-show-all').hide();
        this.$el.find('.').removeClass('aff-amazon-import-results-item-variants-item-hidden');
    },
});
