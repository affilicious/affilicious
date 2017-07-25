export default carbon.fields.View.extend({
    initialize: function() {
        carbon.fields.View.prototype.initialize.apply(this);

        this.on('field:rendered', this.initField);
    },

    initField: function() {
        let self = this,
            tags = this.model.get('tags'),
            maxItems = this.model.get('maxItems');

        if(tags) {
            let options = Object.keys(tags).map((key) => {
                return {id: key, text: tags[key]}
            });

            this.$el.find('.aff-tags.aff-tags-predefined').selectize({
                plugins: ['drag_drop', 'remove_button'],
                delimiter: ',',
                persist: true,
                create: true,
                valueField: 'id',
                labelField: 'text',
                maxOptions: 10,
                searchField: ['text'],
                maxItems: maxItems,
                options: options,
                onChange() {
                    self.model.set('value', self.$el.find('input[name="' + self.templateVariables.name +'"]').val());
                },
            });
        } else {
            this.$el.find('.aff-tags.aff-tags-custom').selectize({
                plugins: ['drag_drop', 'remove_button'],
                delimiter: ',',
                persist: true,
                maxItems: maxItems,
                create: true,
                onChange() {
                    self.model.set('value', self.$el.find('input[name="' + self.templateVariables.name +'"]').val());
                },
            });
        }
    },
});
