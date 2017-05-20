(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _imageGallery = require('./views/image-gallery');

var _imageGallery2 = _interopRequireDefault(_imageGallery);

var _tags = require('./views/tags');

var _tags2 = _interopRequireDefault(_tags);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.carbon = window.carbon || {};

(function () {
    var carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    carbon.fields.View.ImageGallery = _imageGallery2.default;
    carbon.fields.View.Tags = _tags2.default;
})();

},{"./views/image-gallery":2,"./views/tags":3}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = carbon.fields.View.Image.extend({
    events: _.extend({}, carbon.fields.View.prototype.events, {
        'click .c2_open_media': 'openMedia',
        'click .carbon-image-remove': 'removeImage'
    }),

    initialize: function initialize() {
        carbon.fields.View.prototype.initialize.apply(this);

        this.on('field:beforeRender', this.loadDescriptionTemplate);
        this.on('field:rendered', this.sortableImages);

        this.listenTo(this.model, 'change:images', this.updateInput);
        this.listenTo(this.model, 'change:value', this.render);
    },

    sortableImages: function sortableImages() {
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
            update: function update() {
                var images = [];

                $imageGallery.find('li.carbon-image').each(function () {
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
    openMedia: function openMedia(event) {
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

            _.each(selections, function (selection) {
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

    updateInput: function updateInput(model) {
        console.log(model);
        var $input = this.$('input.carbon-image-gallery-field'),
            images = model.get('images'),
            imageValues = _.map(images, function (image) {
            return image.value;
        }),
            value = imageValues.join();

        $input.val(value).trigger('change');
    },

    removeImage: function removeImage(event) {
        var $target = this.$(event.currentTarget),
            $selections = $target.parents('ul.carbon-image-gallery').children(),
            $selection = $target.parents('li.carbon-image'),
            images = _.clone(this.model.get('images')),
            index = $selections.index($selection);

        // Remove the image (splice is not working here, even with _.clone)
        var result = [];
        for (var i = 0; i < images.length; i++) {
            if (i != index) {
                result.push(images[i]);
            }
        }

        this.model.set('images', result);
    }
});

},{}],3:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = carbon.fields.View.extend({
    initialize: function initialize() {
        carbon.fields.View.prototype.initialize.apply(this);

        this.on('field:rendered', this.initField);
    },

    initField: function initField() {
        var self = this;
        var tags = this.model.get('tags');

        if (tags) {
            var options = Object.keys(tags).map(function (key) {
                return { id: key, text: tags[key] };
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
                options: options,
                onChange: function onChange() {
                    self.model.set('value', self.$el.find('input[name="' + self.templateVariables.name + '"]').val());
                }
            });
        } else {
            this.$el.find('.aff-tags.aff-tags-custom').selectize({
                plugins: ['drag_drop', 'remove_button'],
                delimiter: ',',
                persist: true,
                create: true,
                onChange: function onChange() {
                    self.model.set('value', self.$el.find('input[name="' + self.templateVariables.name + '"]').val());
                }
            });
        }
    }
});

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2NhcmJvbi1maWVsZHMvanMvY2FyYm9uLWZpZWxkcy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvY2FyYm9uLWZpZWxkcy9qcy92aWV3cy9pbWFnZS1nYWxsZXJ5LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9jYXJib24tZmllbGRzL2pzL3ZpZXdzL3RhZ3MuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBOzs7O0FBQ0E7Ozs7OztBQUVBLE9BQU8sTUFBUCxHQUFnQixPQUFPLE1BQVAsSUFBaUIsRUFBakM7O0FBRUMsYUFBVztBQUNSLFFBQUksU0FBUyxPQUFPLE1BQXBCO0FBQ0EsUUFBSSxPQUFPLE9BQU8sTUFBZCxLQUF5QixXQUE3QixFQUEwQztBQUN0QyxlQUFPLEtBQVA7QUFDSDs7QUFFRCxXQUFPLE1BQVAsQ0FBYyxJQUFkLENBQW1CLFlBQW5CO0FBQ0EsV0FBTyxNQUFQLENBQWMsSUFBZCxDQUFtQixJQUFuQjtBQUNILENBUkEsR0FBRDs7Ozs7Ozs7a0JDTGUsT0FBTyxNQUFQLENBQWMsSUFBZCxDQUFtQixLQUFuQixDQUF5QixNQUF6QixDQUFnQztBQUMzQyxZQUFRLEVBQUUsTUFBRixDQUFTLEVBQVQsRUFBYSxPQUFPLE1BQVAsQ0FBYyxJQUFkLENBQW1CLFNBQW5CLENBQTZCLE1BQTFDLEVBQWtEO0FBQ3RELGdDQUF3QixXQUQ4QjtBQUV0RCxzQ0FBOEI7QUFGd0IsS0FBbEQsQ0FEbUM7O0FBTTNDLGdCQUFZLHNCQUFXO0FBQ25CLGVBQU8sTUFBUCxDQUFjLElBQWQsQ0FBbUIsU0FBbkIsQ0FBNkIsVUFBN0IsQ0FBd0MsS0FBeEMsQ0FBOEMsSUFBOUM7O0FBRUEsYUFBSyxFQUFMLENBQVEsb0JBQVIsRUFBOEIsS0FBSyx1QkFBbkM7QUFDQSxhQUFLLEVBQUwsQ0FBUSxnQkFBUixFQUEwQixLQUFLLGNBQS9COztBQUVBLGFBQUss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QkFBUyxDQUFDLFdBQUQsRUFBYyxlQUFkLENBRHdDO0FBRWpELDJCQUFXLEdBRnNDO0FBR2pELHlCQUFTLElBSHdDO0FBSWpELHdCQUFRLElBSnlDO0FBS2pELHdCQUxpRCxzQkFLdEM7QUFDUCx5QkFBSyxLQUFMLENBQVcsR0FBWCxDQUFlLE9BQWYsRUFBd0IsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLGlCQUFpQixLQUFLLGlCQUFMLENBQXVCLElBQXhDLEdBQThDLElBQTVELEVBQWtFLEdBQWxFLEVBQXhCO0FBQ0g7QUFQZ0QsYUFBckQ7QUFTSDtBQUNKO0FBekNvQyxDQUExQixDIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsImltcG9ydCBpbWFnZUdhbGxlcnlWaWV3IGZyb20gJy4vdmlld3MvaW1hZ2UtZ2FsbGVyeSc7XG5pbXBvcnQgdGFnc1ZpZXcgZnJvbSAnLi92aWV3cy90YWdzJztcblxud2luZG93LmNhcmJvbiA9IHdpbmRvdy5jYXJib24gfHwge307XG5cbihmdW5jdGlvbigpIHtcbiAgICBsZXQgY2FyYm9uID0gd2luZG93LmNhcmJvbjtcbiAgICBpZiAodHlwZW9mIGNhcmJvbi5maWVsZHMgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG5cbiAgICBjYXJib24uZmllbGRzLlZpZXcuSW1hZ2VHYWxsZXJ5ID0gaW1hZ2VHYWxsZXJ5VmlldztcbiAgICBjYXJib24uZmllbGRzLlZpZXcuVGFncyA9IHRhZ3NWaWV3O1xufSgpKTtcbiIsImV4cG9ydCBkZWZhdWx0IGNhcmJvbi5maWVsZHMuVmlldy5JbWFnZS5leHRlbmQoe1xuICAgIGV2ZW50czogXy5leHRlbmQoe30sIGNhcmJvbi5maWVsZHMuVmlldy5wcm90b3R5cGUuZXZlbnRzLCB7XG4gICAgICAgICdjbGljayAuYzJfb3Blbl9tZWRpYSc6ICdvcGVuTWVkaWEnLFxuICAgICAgICAnY2xpY2sgLmNhcmJvbi1pbWFnZS1yZW1vdmUnOiAncmVtb3ZlSW1hZ2UnXG4gICAgfSksXG5cbiAgICBpbml0aWFsaXplOiBmdW5jdGlvbigpIHtcbiAgICAgICAgY2FyYm9uLmZpZWxkcy5WaWV3LnByb3RvdHlwZS5pbml0aWFsaXplLmFwcGx5KHRoaXMpO1xuXG4gICAgICAgIHRoaXMub24oJ2ZpZWxkOmJlZm9yZVJlbmRlcicsIHRoaXMubG9hZERlc2NyaXB0aW9uVGVtcGxhdGUpO1xuICAgICAgICB0aGlzLm9uKCdmaWVsZDpyZW5kZXJlZCcsIHRoaXMuc29ydGFibGVJbWFnZXMpO1xuXG4gICAgICAgIHRoaXMubGlzdGVuVG8odGhpcy5tb2RlbCwgJ2NoYW5nZTppbWFnZXMnLCB0aGlzLnVwZGF0ZUlucHV0KTtcbiAgICAgICAgdGhpcy5saXN0ZW5Ubyh0aGlzLm1vZGVsLCAnY2hhbmdlOnZhbHVlJywgdGhpcy5yZW5kZXIpO1xuICAgIH0sXG5cbiAgICBzb3J0YWJsZUltYWdlczogZnVuY3Rpb24oKSB7XG4gICAgICAgIHZhciBfdGhpcyA9IHRoaXMsXG4gICAgICAgICAgICAkaW1hZ2VHYWxsZXJ5ID0gdGhpcy4kKCd1bC5jYXJib24taW1hZ2UtZ2FsbGVyeScpO1xuXG4gICAgICAgIC8vIEltYWdlIG9yZGVyaW5nLlxuICAgICAgICAkaW1hZ2VHYWxsZXJ5LnNvcnRhYmxlKHtcbiAgICAgICAgICAgIGl0ZW1zOiAnbGkuY2FyYm9uLWltYWdlJyxcbiAgICAgICAgICAgIGN1cnNvcjogJ21vdmUnLFxuICAgICAgICAgICAgaGVscGVyOiAnY2xvbmUnLFxuICAgICAgICAgICAgc2Nyb2xsU2Vuc2l0aXZpdHk6IDQyLFxuICAgICAgICAgICAgZm9yY2VQbGFjZWhvbGRlclNpemU6IHRydWUsXG4gICAgICAgICAgICBvcGFjaXR5OiAwLjc1LFxuICAgICAgICAgICAgZm9yY2VIZWxwZXJTaXplOiBmYWxzZSxcbiAgICAgICAgICAgIHBsYWNlaG9sZGVyOiAnY2FyYm9uLXNvcnRhYmxlLXBsYWNlaG9sZGVyJyxcbiAgICAgICAgICAgIHVwZGF0ZTogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgdmFyIGltYWdlcyA9IFtdO1xuXG4gICAgICAgICAgICAgICAgJGltYWdlR2FsbGVyeS5maW5kKCdsaS5jYXJib24taW1hZ2UnKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgaW1hZ2UgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWUgPSBpbWFnZS5hdHRyKCdkYXRhLWltYWdlLXZhbHVlJyksXG4gICAgICAgICAgICAgICAgICAgICAgICB1cmwgPSBpbWFnZS5maW5kKCdpbWcnKS5hdHRyKCdzcmMnKTtcblxuICAgICAgICAgICAgICAgICAgICBpbWFnZXMucHVzaCh7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICAgICAgICAgICAgICAgICAgICB1cmw6IHVybFxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIF90aGlzLm1vZGVsLnNldCgnaW1hZ2VzJywgaW1hZ2VzKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSxcbiAgICBvcGVuTWVkaWE6IGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICAgIHZhciB0eXBlID0gdGhpcy5tb2RlbC5nZXQoJ3R5cGUnKTtcbiAgICAgICAgdmFyIGltYWdlcyA9IHRoaXMubW9kZWwuZ2V0KCdpbWFnZXMnKTtcbiAgICAgICAgdmFyIGJ1dHRvbkxhYmVsID0gdGhpcy5tb2RlbC5nZXQoJ3dpbmRvd19idXR0b25fbGFiZWwnKTtcbiAgICAgICAgdmFyIHdpbmRvd0xhYmVsID0gdGhpcy5tb2RlbC5nZXQoJ3dpbmRvd19sYWJlbCcpO1xuICAgICAgICB2YXIgdHlwZUZpbHRlciA9IHRoaXMubW9kZWwuZ2V0KCd0eXBlX2ZpbHRlcicpO1xuICAgICAgICB2YXIgdmFsdWVUeXBlID0gdGhpcy5tb2RlbC5nZXQoJ3ZhbHVlX3R5cGUnKTtcbiAgICAgICAgdmFyIG1lZGlhVHlwZXMgPSB7fTtcblxuICAgICAgICBtZWRpYVR5cGVzW3R5cGVdID0gd3AubWVkaWEuZnJhbWVzLmNyYk1lZGlhRmllbGQgPSB3cC5tZWRpYSh7XG4gICAgICAgICAgICB0aXRsZTogd2luZG93TGFiZWwgPyB3aW5kb3dMYWJlbCA6IGNyYmwxMG4udGl0bGUsXG4gICAgICAgICAgICBsaWJyYXJ5OiB7IHR5cGU6IHR5cGVGaWx0ZXIgfSwgLy8gYXVkaW8sIHZpZGVvLCBpbWFnZVxuICAgICAgICAgICAgYnV0dG9uOiB7IHRleHQ6IGJ1dHRvbkxhYmVsIH0sXG4gICAgICAgICAgICBtdWx0aXBsZTogdHJ1ZVxuICAgICAgICB9KTtcblxuICAgICAgICB2YXIgbWVkaWFGaWVsZCA9IG1lZGlhVHlwZXNbdHlwZV07XG5cbiAgICAgICAgLy8gUnVucyB3aGVuIGFuIGltYWdlIGlzIHNlbGVjdGVkLlxuICAgICAgICBtZWRpYUZpZWxkLm9uKCdzZWxlY3QnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgc2VsZWN0aW9ucyA9IG1lZGlhRmllbGQuc3RhdGUoKS5nZXQoJ3NlbGVjdGlvbicpLnRvSlNPTigpO1xuICAgICAgICAgICAgdmFyIGltYWdlcyA9IF8uY2xvbmUoX3RoaXMubW9kZWwuZ2V0KCdpbWFnZXMnKSk7XG5cbiAgICAgICAgICAgIF8uZWFjaChzZWxlY3Rpb25zLCBmdW5jdGlvbihzZWxlY3Rpb24pIHtcbiAgICAgICAgICAgICAgICBpbWFnZXMucHVzaCh7XG4gICAgICAgICAgICAgICAgICAgICd2YWx1ZSc6IHNlbGVjdGlvblt2YWx1ZVR5cGVdLFxuICAgICAgICAgICAgICAgICAgICAndXJsJzogc2VsZWN0aW9uLnVybFxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIF90aGlzLm1vZGVsLnNldCgnaW1hZ2VzJywgaW1hZ2VzKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gT3BlbnMgdGhlIG1lZGlhIGxpYnJhcnkgZnJhbWVcbiAgICAgICAgbWVkaWFGaWVsZC5vcGVuKCk7XG5cbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICB9LFxuXG4gICAgdXBkYXRlSW5wdXQ6IGZ1bmN0aW9uKG1vZGVsKSB7XG4gICAgICAgIGNvbnNvbGUubG9nKG1vZGVsKTtcbiAgICAgICAgdmFyICRpbnB1dCA9IHRoaXMuJCgnaW5wdXQuY2FyYm9uLWltYWdlLWdhbGxlcnktZmllbGQnKSxcbiAgICAgICAgICAgIGltYWdlcyA9IG1vZGVsLmdldCgnaW1hZ2VzJyksXG4gICAgICAgICAgICBpbWFnZVZhbHVlcyA9IF8ubWFwKGltYWdlcywgZnVuY3Rpb24oaW1hZ2UpIHsgcmV0dXJuIGltYWdlLnZhbHVlOyB9KSxcbiAgICAgICAgICAgIHZhbHVlID0gaW1hZ2VWYWx1ZXMuam9pbigpO1xuXG4gICAgICAgICRpbnB1dC52YWwodmFsdWUpLnRyaWdnZXIoJ2NoYW5nZScpO1xuICAgIH0sXG5cbiAgICByZW1vdmVJbWFnZTogZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgdmFyICR0YXJnZXQgPSB0aGlzLiQoZXZlbnQuY3VycmVudFRhcmdldCksXG4gICAgICAgICAgICAkc2VsZWN0aW9ucyA9ICR0YXJnZXQucGFyZW50cygndWwuY2FyYm9uLWltYWdlLWdhbGxlcnknKS5jaGlsZHJlbigpLFxuICAgICAgICAgICAgJHNlbGVjdGlvbiA9ICR0YXJnZXQucGFyZW50cygnbGkuY2FyYm9uLWltYWdlJyksXG4gICAgICAgICAgICBpbWFnZXMgPSBfLmNsb25lKHRoaXMubW9kZWwuZ2V0KCdpbWFnZXMnKSksXG4gICAgICAgICAgICBpbmRleCA9ICRzZWxlY3Rpb25zLmluZGV4KCRzZWxlY3Rpb24pO1xuXG4gICAgICAgIC8vIFJlbW92ZSB0aGUgaW1hZ2UgKHNwbGljZSBpcyBub3Qgd29ya2luZyBoZXJlLCBldmVuIHdpdGggXy5jbG9uZSlcbiAgICAgICAgdmFyIHJlc3VsdCA9IFtdO1xuICAgICAgICBmb3IodmFyIGkgPSAwOyBpIDwgaW1hZ2VzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICBpZiAoaSAhPSBpbmRleCkge1xuICAgICAgICAgICAgICAgIHJlc3VsdC5wdXNoKGltYWdlc1tpXSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLm1vZGVsLnNldCgnaW1hZ2VzJywgcmVzdWx0KTtcbiAgICB9XG59KTtcbiIsImV4cG9ydCBkZWZhdWx0IGNhcmJvbi5maWVsZHMuVmlldy5leHRlbmQoe1xuICAgIGluaXRpYWxpemU6IGZ1bmN0aW9uKCkge1xuICAgICAgICBjYXJib24uZmllbGRzLlZpZXcucHJvdG90eXBlLmluaXRpYWxpemUuYXBwbHkodGhpcyk7XG5cbiAgICAgICAgdGhpcy5vbignZmllbGQ6cmVuZGVyZWQnLCB0aGlzLmluaXRGaWVsZCk7XG4gICAgfSxcblxuICAgIGluaXRGaWVsZDogZnVuY3Rpb24oKSB7XG4gICAgICAgIGxldCBzZWxmID0gdGhpcztcbiAgICAgICAgbGV0IHRhZ3MgPSB0aGlzLm1vZGVsLmdldCgndGFncycpO1xuXG4gICAgICAgIGlmKHRhZ3MpIHtcbiAgICAgICAgICAgIGxldCBvcHRpb25zID0gT2JqZWN0LmtleXModGFncykubWFwKChrZXkpID0+IHtcbiAgICAgICAgICAgICAgICByZXR1cm4ge2lkOiBrZXksIHRleHQ6IHRhZ3Nba2V5XX1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLXRhZ3MuYWZmLXRhZ3MtcHJlZGVmaW5lZCcpLnNlbGVjdGl6ZSh7XG4gICAgICAgICAgICAgICAgcGx1Z2luczogWydkcmFnX2Ryb3AnLCAncmVtb3ZlX2J1dHRvbiddLFxuICAgICAgICAgICAgICAgIGRlbGltaXRlcjogJywnLFxuICAgICAgICAgICAgICAgIHBlcnNpc3Q6IHRydWUsXG4gICAgICAgICAgICAgICAgY3JlYXRlOiB0cnVlLFxuICAgICAgICAgICAgICAgIHZhbHVlRmllbGQ6ICdpZCcsXG4gICAgICAgICAgICAgICAgbGFiZWxGaWVsZDogJ3RleHQnLFxuICAgICAgICAgICAgICAgIG1heE9wdGlvbnM6IDEwLFxuICAgICAgICAgICAgICAgIHNlYXJjaEZpZWxkOiBbJ3RleHQnXSxcbiAgICAgICAgICAgICAgICBvcHRpb25zOiBvcHRpb25zLFxuICAgICAgICAgICAgICAgIG9uQ2hhbmdlKCkge1xuICAgICAgICAgICAgICAgICAgICBzZWxmLm1vZGVsLnNldCgndmFsdWUnLCBzZWxmLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwiJyArIHNlbGYudGVtcGxhdGVWYXJpYWJsZXMubmFtZSArJ1wiXScpLnZhbCgpKTtcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLXRhZ3MuYWZmLXRhZ3MtY3VzdG9tJykuc2VsZWN0aXplKHtcbiAgICAgICAgICAgICAgICBwbHVnaW5zOiBbJ2RyYWdfZHJvcCcsICdyZW1vdmVfYnV0dG9uJ10sXG4gICAgICAgICAgICAgICAgZGVsaW1pdGVyOiAnLCcsXG4gICAgICAgICAgICAgICAgcGVyc2lzdDogdHJ1ZSxcbiAgICAgICAgICAgICAgICBjcmVhdGU6IHRydWUsXG4gICAgICAgICAgICAgICAgb25DaGFuZ2UoKSB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYubW9kZWwuc2V0KCd2YWx1ZScsIHNlbGYuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCInICsgc2VsZi50ZW1wbGF0ZVZhcmlhYmxlcy5uYW1lICsnXCJdJykudmFsKCkpO1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH0sXG59KTtcbiJdfQ==
