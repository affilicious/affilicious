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
/**
 * The view of the tags field used for Carbon Fields.
 *
 * @since 0.8.16
 */
var Tags = carbon.fields.View.extend({

    /**
     * Initialize the tags view.
     *
     * @since 0.8.16
     * @public
     */
    initialize: function initialize() {
        carbon.fields.View.prototype.initialize.apply(this);

        this.on('field:rendered', this._initTags);
        this.listenTo(this.model, 'change:visible', this._initTags);
    },


    /**
     * Initialize the tags with Selectize.
     *
     * @since 0.9.18
     * @private
     */
    _initTags: function _initTags() {
        var tags = this.model.get('tags');
        var maxItems = this.model.get('maxItems');

        if (tags) {
            this._initPredefinedTags(tags, maxItems);
        } else {
            this._initCustomTags(maxItems);
        }
    },


    /**
     * Initialize the predefined tags with auto-completion.
     *
     * @since 0.9.18
     * @private
     * @param {array} tags
     * @param {int} maxItems
     */
    _initPredefinedTags: function _initPredefinedTags(tags, maxItems) {
        var _this = this;

        var select = this.$el.find('.aff-tags-predefined');
        var selectize = select[0].selectize;
        var options = Object.keys(tags).map(function (key) {
            return { id: key, text: tags[key] };
        });

        if (!selectize) {
            select.selectize({
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
                onChange: function onChange() {
                    return _this._onChangeTags();
                }
            });
        } else {
            selectize.enable();
        }
    },


    /**
     * Initialize the custom tags without auto-completion.
     *
     * @private
     * @since 0.9.18
     * @param {int} maxItems
     */
    _initCustomTags: function _initCustomTags(maxItems) {
        var _this2 = this;

        var select = this.$el.find('.aff-tags-custom');
        var selectize = select[0].selectize;

        if (!selectize) {
            select.selectize({
                plugins: ['drag_drop', 'remove_button'],
                delimiter: ',',
                persist: true,
                maxItems: maxItems,
                create: true,
                onChange: function onChange() {
                    return _this2._onChangeTags();
                }
            });
        } else {
            selectize.enable();
        }
    },


    /**
     * Set the new value of the model if the tags have changed.
     *
     * @since 0.9.18
     * @private
     */
    _onChangeTags: function _onChangeTags() {
        var input = this.$el.find('input[name="' + this.templateVariables.name + '"]');
        this.model.set('value', input.val());
    }
});

exports.default = Tags;

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2NhcmJvbi1maWVsZHMvanMvY2FyYm9uLWZpZWxkcy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvY2FyYm9uLWZpZWxkcy9qcy92aWV3cy9pbWFnZS1nYWxsZXJ5LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9jYXJib24tZmllbGRzL2pzL3ZpZXdzL3RhZ3MuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBOzs7O0FBQ0E7Ozs7OztBQUVBLE9BQU8sTUFBUCxHQUFnQixPQUFPLE1BQVAsSUFBaUIsRUFBakM7O0FBRUMsYUFBVztBQUNSLFFBQUksU0FBUyxPQUFPLE1BQXBCO0FBQ0EsUUFBSSxPQUFPLE9BQU8sTUFBZCxLQUF5QixXQUE3QixFQUEwQztBQUN0QyxlQUFPLEtBQVA7QUFDSDs7QUFFRCxXQUFPLE1BQVAsQ0FBYyxJQUFkLENBQW1CLFlBQW5CO0FBQ0EsV0FBTyxNQUFQLENBQWMsSUFBZCxDQUFtQixJQUFuQjtBQUNILENBUkEsR0FBRDs7Ozs7Ozs7a0JDTGUsT0FBTyxNQUFQLENBQWMsSUFBZCxDQUFtQixLQUFuQixDQUF5QixNQUF6QixDQUFnQztBQUMzQyxZQUFRLEVBQUUsTUFBRixDQUFTLEVBQVQsRUFBYSxPQUFPLE1BQVAsQ0FBYyxJQUFkLENBQW1CLFNBQW5CLENBQTZCLE1BQTFDLEVBQWtEO0FBQ3RELGdDQUF3QixXQUQ4QjtBQUV0RCxzQ0FBOEI7QUFGd0IsS0FBbEQsQ0FEbUM7O0FBTTNDLGdCQUFZLHNCQUFXO0FBQ25CLGVBQU8sTUFBUCxDQUFjLElBQWQsQ0FBbUIsU0FBbkIsQ0FBNkIsVUFBN0IsQ0FBd0MsS0FBeEMsQ0FBOEMsSUFBOUM7O0FBRUEsYUFBSyxFQUFMLENBQVEsb0JBQVIsRUFBOEIsS0FBSyx1QkFBbkM7QUFDQSxhQUFLLEVBQUwsQ0FBUSxnQkFBUixFQUEwQixLQUFLLGNBQS9COztBQUVBLGFBQUss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dW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiaW1wb3J0IGltYWdlR2FsbGVyeVZpZXcgZnJvbSAnLi92aWV3cy9pbWFnZS1nYWxsZXJ5JztcbmltcG9ydCB0YWdzVmlldyBmcm9tICcuL3ZpZXdzL3RhZ3MnO1xuXG53aW5kb3cuY2FyYm9uID0gd2luZG93LmNhcmJvbiB8fCB7fTtcblxuKGZ1bmN0aW9uKCkge1xuICAgIGxldCBjYXJib24gPSB3aW5kb3cuY2FyYm9uO1xuICAgIGlmICh0eXBlb2YgY2FyYm9uLmZpZWxkcyA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIGNhcmJvbi5maWVsZHMuVmlldy5JbWFnZUdhbGxlcnkgPSBpbWFnZUdhbGxlcnlWaWV3O1xuICAgIGNhcmJvbi5maWVsZHMuVmlldy5UYWdzID0gdGFnc1ZpZXc7XG59KCkpO1xuIiwiZXhwb3J0IGRlZmF1bHQgY2FyYm9uLmZpZWxkcy5WaWV3LkltYWdlLmV4dGVuZCh7XG4gICAgZXZlbnRzOiBfLmV4dGVuZCh7fSwgY2FyYm9uLmZpZWxkcy5WaWV3LnByb3RvdHlwZS5ldmVudHMsIHtcbiAgICAgICAgJ2NsaWNrIC5jMl9vcGVuX21lZGlhJzogJ29wZW5NZWRpYScsXG4gICAgICAgICdjbGljayAuY2FyYm9uLWltYWdlLXJlbW92ZSc6ICdyZW1vdmVJbWFnZSdcbiAgICB9KSxcblxuICAgIGluaXRpYWxpemU6IGZ1bmN0aW9uKCkge1xuICAgICAgICBjYXJib24uZmllbGRzLlZpZXcucHJvdG90eXBlLmluaXRpYWxpemUuYXBwbHkodGhpcyk7XG5cbiAgICAgICAgdGhpcy5vbignZmllbGQ6YmVmb3JlUmVuZGVyJywgdGhpcy5sb2FkRGVzY3JpcHRpb25UZW1wbGF0ZSk7XG4gICAgICAgIHRoaXMub24oJ2ZpZWxkOnJlbmRlcmVkJywgdGhpcy5zb3J0YWJsZUltYWdlcyk7XG5cbiAgICAgICAgdGhpcy5saXN0ZW5Ubyh0aGlzLm1vZGVsLCAnY2hhbmdlOmltYWdlcycsIHRoaXMudXBkYXRlSW5wdXQpO1xuICAgICAgICB0aGlzLmxpc3RlblRvKHRoaXMubW9kZWwsICdjaGFuZ2U6dmFsdWUnLCB0aGlzLnJlbmRlcik7XG4gICAgfSxcblxuICAgIHNvcnRhYmxlSW1hZ2VzOiBmdW5jdGlvbigpIHtcbiAgICAgICAgdmFyIF90aGlzID0gdGhpcyxcbiAgICAgICAgICAgICRpbWFnZUdhbGxlcnkgPSB0aGlzLiQoJ3VsLmNhcmJvbi1pbWFnZS1nYWxsZXJ5Jyk7XG5cbiAgICAgICAgLy8gSW1hZ2Ugb3JkZXJpbmcuXG4gICAgICAgICRpbWFnZUdhbGxlcnkuc29ydGFibGUoe1xuICAgICAgICAgICAgaXRlbXM6ICdsaS5jYXJib24taW1hZ2UnLFxuICAgICAgICAgICAgY3Vyc29yOiAnbW92ZScsXG4gICAgICAgICAgICBoZWxwZXI6ICdjbG9uZScsXG4gICAgICAgICAgICBzY3JvbGxTZW5zaXRpdml0eTogNDIsXG4gICAgICAgICAgICBmb3JjZVBsYWNlaG9sZGVyU2l6ZTogdHJ1ZSxcbiAgICAgICAgICAgIG9wYWNpdHk6IDAuNzUsXG4gICAgICAgICAgICBmb3JjZUhlbHBlclNpemU6IGZhbHNlLFxuICAgICAgICAgICAgcGxhY2Vob2xkZXI6ICdjYXJib24tc29ydGFibGUtcGxhY2Vob2xkZXInLFxuICAgICAgICAgICAgdXBkYXRlOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICB2YXIgaW1hZ2VzID0gW107XG5cbiAgICAgICAgICAgICAgICAkaW1hZ2VHYWxsZXJ5LmZpbmQoJ2xpLmNhcmJvbi1pbWFnZScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBpbWFnZSA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZSA9IGltYWdlLmF0dHIoJ2RhdGEtaW1hZ2UtdmFsdWUnKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHVybCA9IGltYWdlLmZpbmQoJ2ltZycpLmF0dHIoJ3NyYycpO1xuXG4gICAgICAgICAgICAgICAgICAgIGltYWdlcy5wdXNoKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlOiB2YWx1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHVybDogdXJsXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgX3RoaXMubW9kZWwuc2V0KCdpbWFnZXMnLCBpbWFnZXMpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9LFxuICAgIG9wZW5NZWRpYTogZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgdmFyIF90aGlzID0gdGhpcztcbiAgICAgICAgdmFyIHR5cGUgPSB0aGlzLm1vZGVsLmdldCgndHlwZScpO1xuICAgICAgICB2YXIgaW1hZ2VzID0gdGhpcy5tb2RlbC5nZXQoJ2ltYWdlcycpO1xuICAgICAgICB2YXIgYnV0dG9uTGFiZWwgPSB0aGlzLm1vZGVsLmdldCgnd2luZG93X2J1dHRvbl9sYWJlbCcpO1xuICAgICAgICB2YXIgd2luZG93TGFiZWwgPSB0aGlzLm1vZGVsLmdldCgnd2luZG93X2xhYmVsJyk7XG4gICAgICAgIHZhciB0eXBlRmlsdGVyID0gdGhpcy5tb2RlbC5nZXQoJ3R5cGVfZmlsdGVyJyk7XG4gICAgICAgIHZhciB2YWx1ZVR5cGUgPSB0aGlzLm1vZGVsLmdldCgndmFsdWVfdHlwZScpO1xuICAgICAgICB2YXIgbWVkaWFUeXBlcyA9IHt9O1xuXG4gICAgICAgIG1lZGlhVHlwZXNbdHlwZV0gPSB3cC5tZWRpYS5mcmFtZXMuY3JiTWVkaWFGaWVsZCA9IHdwLm1lZGlhKHtcbiAgICAgICAgICAgIHRpdGxlOiB3aW5kb3dMYWJlbCA/IHdpbmRvd0xhYmVsIDogY3JibDEwbi50aXRsZSxcbiAgICAgICAgICAgIGxpYnJhcnk6IHsgdHlwZTogdHlwZUZpbHRlciB9LCAvLyBhdWRpbywgdmlkZW8sIGltYWdlXG4gICAgICAgICAgICBidXR0b246IHsgdGV4dDogYnV0dG9uTGFiZWwgfSxcbiAgICAgICAgICAgIG11bHRpcGxlOiB0cnVlXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHZhciBtZWRpYUZpZWxkID0gbWVkaWFUeXBlc1t0eXBlXTtcblxuICAgICAgICAvLyBSdW5zIHdoZW4gYW4gaW1hZ2UgaXMgc2VsZWN0ZWQuXG4gICAgICAgIG1lZGlhRmllbGQub24oJ3NlbGVjdCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciBzZWxlY3Rpb25zID0gbWVkaWFGaWVsZC5zdGF0ZSgpLmdldCgnc2VsZWN0aW9uJykudG9KU09OKCk7XG4gICAgICAgICAgICB2YXIgaW1hZ2VzID0gXy5jbG9uZShfdGhpcy5tb2RlbC5nZXQoJ2ltYWdlcycpKTtcblxuICAgICAgICAgICAgXy5lYWNoKHNlbGVjdGlvbnMsIGZ1bmN0aW9uKHNlbGVjdGlvbikge1xuICAgICAgICAgICAgICAgIGltYWdlcy5wdXNoKHtcbiAgICAgICAgICAgICAgICAgICAgJ3ZhbHVlJzogc2VsZWN0aW9uW3ZhbHVlVHlwZV0sXG4gICAgICAgICAgICAgICAgICAgICd1cmwnOiBzZWxlY3Rpb24udXJsXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgX3RoaXMubW9kZWwuc2V0KCdpbWFnZXMnLCBpbWFnZXMpO1xuICAgICAgICB9KTtcblxuICAgICAgICAvLyBPcGVucyB0aGUgbWVkaWEgbGlicmFyeSBmcmFtZVxuICAgICAgICBtZWRpYUZpZWxkLm9wZW4oKTtcblxuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIH0sXG5cbiAgICB1cGRhdGVJbnB1dDogZnVuY3Rpb24obW9kZWwpIHtcbiAgICAgICAgY29uc29sZS5sb2cobW9kZWwpO1xuICAgICAgICB2YXIgJGlucHV0ID0gdGhpcy4kKCdpbnB1dC5jYXJib24taW1hZ2UtZ2FsbGVyeS1maWVsZCcpLFxuICAgICAgICAgICAgaW1hZ2VzID0gbW9kZWwuZ2V0KCdpbWFnZXMnKSxcbiAgICAgICAgICAgIGltYWdlVmFsdWVzID0gXy5tYXAoaW1hZ2VzLCBmdW5jdGlvbihpbWFnZSkgeyByZXR1cm4gaW1hZ2UudmFsdWU7IH0pLFxuICAgICAgICAgICAgdmFsdWUgPSBpbWFnZVZhbHVlcy5qb2luKCk7XG5cbiAgICAgICAgJGlucHV0LnZhbCh2YWx1ZSkudHJpZ2dlcignY2hhbmdlJyk7XG4gICAgfSxcblxuICAgIHJlbW92ZUltYWdlOiBmdW5jdGlvbihldmVudCkge1xuICAgICAgICB2YXIgJHRhcmdldCA9IHRoaXMuJChldmVudC5jdXJyZW50VGFyZ2V0KSxcbiAgICAgICAgICAgICRzZWxlY3Rpb25zID0gJHRhcmdldC5wYXJlbnRzKCd1bC5jYXJib24taW1hZ2UtZ2FsbGVyeScpLmNoaWxkcmVuKCksXG4gICAgICAgICAgICAkc2VsZWN0aW9uID0gJHRhcmdldC5wYXJlbnRzKCdsaS5jYXJib24taW1hZ2UnKSxcbiAgICAgICAgICAgIGltYWdlcyA9IF8uY2xvbmUodGhpcy5tb2RlbC5nZXQoJ2ltYWdlcycpKSxcbiAgICAgICAgICAgIGluZGV4ID0gJHNlbGVjdGlvbnMuaW5kZXgoJHNlbGVjdGlvbik7XG5cbiAgICAgICAgLy8gUmVtb3ZlIHRoZSBpbWFnZSAoc3BsaWNlIGlzIG5vdCB3b3JraW5nIGhlcmUsIGV2ZW4gd2l0aCBfLmNsb25lKVxuICAgICAgICB2YXIgcmVzdWx0ID0gW107XG4gICAgICAgIGZvcih2YXIgaSA9IDA7IGkgPCBpbWFnZXMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgIGlmIChpICE9IGluZGV4KSB7XG4gICAgICAgICAgICAgICAgcmVzdWx0LnB1c2goaW1hZ2VzW2ldKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdpbWFnZXMnLCByZXN1bHQpO1xuICAgIH1cbn0pO1xuIiwiLyoqXG4gKiBUaGUgdmlldyBvZiB0aGUgdGFncyBmaWVsZCB1c2VkIGZvciBDYXJib24gRmllbGRzLlxuICpcbiAqIEBzaW5jZSAwLjguMTZcbiAqL1xubGV0IFRhZ3MgPSBjYXJib24uZmllbGRzLlZpZXcuZXh0ZW5kKHtcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHRhZ3Mgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjguMTZcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgY2FyYm9uLmZpZWxkcy5WaWV3LnByb3RvdHlwZS5pbml0aWFsaXplLmFwcGx5KHRoaXMpO1xuXG4gICAgICAgIHRoaXMub24oJ2ZpZWxkOnJlbmRlcmVkJywgdGhpcy5faW5pdFRhZ3MpO1xuICAgICAgICB0aGlzLmxpc3RlblRvKHRoaXMubW9kZWwsICdjaGFuZ2U6dmlzaWJsZScsIHRoaXMuX2luaXRUYWdzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgdGFncyB3aXRoIFNlbGVjdGl6ZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMThcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9pbml0VGFncygpIHtcbiAgICAgICAgbGV0IHRhZ3MgPSB0aGlzLm1vZGVsLmdldCgndGFncycpO1xuICAgICAgICBsZXQgbWF4SXRlbXMgPSB0aGlzLm1vZGVsLmdldCgnbWF4SXRlbXMnKTtcblxuICAgICAgICBpZih0YWdzKSB7XG4gICAgICAgICAgICB0aGlzLl9pbml0UHJlZGVmaW5lZFRhZ3ModGFncywgbWF4SXRlbXMpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgdGhpcy5faW5pdEN1c3RvbVRhZ3MobWF4SXRlbXMpO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHByZWRlZmluZWQgdGFncyB3aXRoIGF1dG8tY29tcGxldGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMThcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IHRhZ3NcbiAgICAgKiBAcGFyYW0ge2ludH0gbWF4SXRlbXNcbiAgICAgKi9cbiAgICBfaW5pdFByZWRlZmluZWRUYWdzKHRhZ3MsIG1heEl0ZW1zKSB7XG4gICAgICAgIGxldCBzZWxlY3QgPSB0aGlzLiRlbC5maW5kKCcuYWZmLXRhZ3MtcHJlZGVmaW5lZCcpO1xuICAgICAgICBsZXQgc2VsZWN0aXplID0gc2VsZWN0WzBdLnNlbGVjdGl6ZTtcbiAgICAgICAgbGV0IG9wdGlvbnMgPSBPYmplY3Qua2V5cyh0YWdzKS5tYXAoKGtleSkgPT4ge1xuICAgICAgICAgICAgcmV0dXJuIHtpZDoga2V5LCB0ZXh0OiB0YWdzW2tleV19XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGlmKCFzZWxlY3RpemUpIHtcbiAgICAgICAgICAgIHNlbGVjdC5zZWxlY3RpemUoe1xuICAgICAgICAgICAgICAgIHBsdWdpbnM6IFsnZHJhZ19kcm9wJywgJ3JlbW92ZV9idXR0b24nXSxcbiAgICAgICAgICAgICAgICBkZWxpbWl0ZXI6ICcsJyxcbiAgICAgICAgICAgICAgICBwZXJzaXN0OiB0cnVlLFxuICAgICAgICAgICAgICAgIGNyZWF0ZTogdHJ1ZSxcbiAgICAgICAgICAgICAgICB2YWx1ZUZpZWxkOiAnaWQnLFxuICAgICAgICAgICAgICAgIGxhYmVsRmllbGQ6ICd0ZXh0JyxcbiAgICAgICAgICAgICAgICBtYXhPcHRpb25zOiAxMCxcbiAgICAgICAgICAgICAgICBzZWFyY2hGaWVsZDogWyd0ZXh0J10sXG4gICAgICAgICAgICAgICAgbWF4SXRlbXM6IG1heEl0ZW1zLFxuICAgICAgICAgICAgICAgIG9wdGlvbnM6IG9wdGlvbnMsXG4gICAgICAgICAgICAgICAgb25DaGFuZ2U6ICgpID0+IHRoaXMuX29uQ2hhbmdlVGFncygpLFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBzZWxlY3RpemUuZW5hYmxlKCk7XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY3VzdG9tIHRhZ3Mgd2l0aG91dCBhdXRvLWNvbXBsZXRpb24uXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqIEBzaW5jZSAwLjkuMThcbiAgICAgKiBAcGFyYW0ge2ludH0gbWF4SXRlbXNcbiAgICAgKi9cbiAgICBfaW5pdEN1c3RvbVRhZ3MobWF4SXRlbXMpIHtcbiAgICAgICAgbGV0IHNlbGVjdCA9IHRoaXMuJGVsLmZpbmQoJy5hZmYtdGFncy1jdXN0b20nKTtcbiAgICAgICAgbGV0IHNlbGVjdGl6ZSA9IHNlbGVjdFswXS5zZWxlY3RpemU7XG5cbiAgICAgICAgaWYoIXNlbGVjdGl6ZSkge1xuICAgICAgICAgICAgc2VsZWN0LnNlbGVjdGl6ZSh7XG4gICAgICAgICAgICAgICAgcGx1Z2luczogWydkcmFnX2Ryb3AnLCAncmVtb3ZlX2J1dHRvbiddLFxuICAgICAgICAgICAgICAgIGRlbGltaXRlcjogJywnLFxuICAgICAgICAgICAgICAgIHBlcnNpc3Q6IHRydWUsXG4gICAgICAgICAgICAgICAgbWF4SXRlbXM6IG1heEl0ZW1zLFxuICAgICAgICAgICAgICAgIGNyZWF0ZTogdHJ1ZSxcbiAgICAgICAgICAgICAgICBvbkNoYW5nZTogKCkgPT4gdGhpcy5fb25DaGFuZ2VUYWdzKCksXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHNlbGVjdGl6ZS5lbmFibGUoKTtcbiAgICAgICAgfVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTZXQgdGhlIG5ldyB2YWx1ZSBvZiB0aGUgbW9kZWwgaWYgdGhlIHRhZ3MgaGF2ZSBjaGFuZ2VkLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xOFxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX29uQ2hhbmdlVGFncygpIHtcbiAgICAgICAgbGV0IGlucHV0ID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIicgKyB0aGlzLnRlbXBsYXRlVmFyaWFibGVzLm5hbWUgKyAnXCJdJyk7XG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCd2YWx1ZScsIGlucHV0LnZhbCgpKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgVGFncztcbiJdfQ==
