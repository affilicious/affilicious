window.carbon = window.carbon || {};

(function($) {
    var carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    carbon.fields.Model.Tags = carbon.fields.Model.extend({
        initialize: function() {
            carbon.fields.Model.prototype.initialize.apply(this);  // do not delete
        },
    });

    carbon.fields.View.Tags = carbon.fields.View.extend({
        initialize: function() {
            // Initialize the parent view
            carbon.fields.View.prototype.initialize.apply(this); // do not delete

            // Wait for the field to be added to the DOM and run an init method
            this.on('field:rendered', this.initField);
        },

        initField: function() {
            $('.aff-tags').tagsInput({
                'width':'100%',
                'height': 'auto',
                'defaultText': translations.addTag,
                'delimiter': ';',
                'minChars' : 1,
                'maxChars' : 100,
                'placeholderColor' : '#666666'
            });
        },
    });
}(jQuery));
