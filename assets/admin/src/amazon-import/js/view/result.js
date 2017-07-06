export default Backbone.View.extend({

    template: _.template(jQuery('.aff-amazon-import-results-template').html()),

    tagName: 'div',

    className: '',

    render() {
        console.log(jQuery('.aff-amazon-import-results-template').html());
        this.$el.empty();
        this.$el.append(this.template(this.model.attributes));

        return this;
    },
});
