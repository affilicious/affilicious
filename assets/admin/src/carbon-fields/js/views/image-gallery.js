export default carbon.fields.View.Image.extend({
    events: _.extend({}, carbon.fields.View.prototype.events, {
        'click .c2_open_media': 'openMedia',
        'click .carbon-image-remove': 'removeImage'
    }),

    initialize: function() {
        carbon.fields.View.prototype.initialize.apply(this);

        this.on('field:beforeRender', this.loadDescriptionTemplate);
        this.on('field:rendered', this.sortableImages);

        this.listenTo(this.model, 'change:images', this.updateInput);
        this.listenTo(this.model, 'change:value', this.render);
    },

    sortableImages: function() {
        var _this = this,
            $imageGallery = this.$('ul.carbon-image-gallery');

        // Image ordering.
        $imageGallery.sortable({
            items: 'li.carbon-image',
            cursor: 'move',
            helper: 'clone',
            scrollSensitivity: 42,
            forcePlaceholderSize: true,
            opacity: 0.75,
            forceHelperSize: false,
            placeholder: 'carbon-sortable-placeholder',
            update: function() {
                var images = [];

                $imageGallery.find('li.carbon-image').each(function() {
                    var image = $(this),
                        value = image.attr('data-image-value'),
                        url = image.find('img').attr('src');

                    images.push({
                        value: value,
                        url: url
                    });
                });

                _this.model.set('images', images);
            }
        });
    },
    openMedia: function(event) {
        var _this = this;
        var type = this.model.get('type');
        var images = this.model.get('images');
        var buttonLabel = this.model.get('window_button_label');
        var windowLabel = this.model.get('window_label');
        var typeFilter = this.model.get('type_filter');
        var valueType = this.model.get('value_type');
        var mediaTypes = {};

        mediaTypes[type] = wp.media.frames.crbMediaField = wp.media({
            title: windowLabel ? windowLabel : crbl10n.title,
            library: { type: typeFilter }, // audio, video, image
            button: { text: buttonLabel },
            multiple: true
        });

        var mediaField = mediaTypes[type];

        // Runs when an image is selected.
        mediaField.on('select', function () {
            var selections = mediaField.state().get('selection').toJSON();
            var images = _.clone(_this.model.get('images'));

            _.each(selections, function(selection) {
                images.push({
                    'value': selection[valueType],
                    'url': selection.url
                });
            });

            _this.model.set('images', images);
        });

        // Opens the media library frame
        mediaField.open();

        event.preventDefault();
    },

    updateInput: function(model) {
        console.log(model);
        var $input = this.$('input.carbon-image-gallery-field'),
            images = model.get('images'),
            imageValues = _.map(images, function(image) { return image.value; }),
            value = imageValues.join();

        $input.val(value).trigger('change');
    },

    removeImage: function(event) {
        var $target = this.$(event.currentTarget),
            $selections = $target.parents('ul.carbon-image-gallery').children(),
            $selection = $target.parents('li.carbon-image'),
            images = _.clone(this.model.get('images')),
            index = $selections.index($selection);

        // Remove the image (splice is not working here, even with _.clone)
        var result = [];
        for(var i = 0; i < images.length; i++) {
            if (i != index) {
                result.push(images[i]);
            }
        }

        this.model.set('images', result);
    }
});
