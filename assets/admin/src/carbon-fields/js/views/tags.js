export default carbon.fields.View.extend({
    initialize: function() {
        carbon.fields.View.prototype.initialize.apply(this);

        this.on('field:rendered', this.initField);
    },

    initField: function() {
        let self = this;

        this.$el.find('.aff-tags').tagsInput({
            'width':'100%',
            'height': 'auto',
            'defaultText': affCarbonFieldsTranslations.addTag,
            'interactive': true,
            'delimiter': ';',
            'minChars' : 1,
            'maxChars' : 100,
            'placeholderColor' : '#666666',
            'onChange' : function() {
                self.model.set('value', self.$el.find('input[name="' + self.templateVariables.name +'"]').val());
            },
        });
    },
});
