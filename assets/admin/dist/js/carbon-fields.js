(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2NhcmJvbi1maWVsZHMvanMvY2FyYm9uLWZpZWxkcy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvY2FyYm9uLWZpZWxkcy9qcy92aWV3cy9pbWFnZS1nYWxsZXJ5LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9jYXJib24tZmllbGRzL2pzL3ZpZXdzL3RhZ3MuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBOzs7O0FBQ0E7Ozs7OztBQUVBLE9BQU8sTUFBUCxHQUFnQixPQUFPLE1BQVAsSUFBaUIsRUFBakM7O0FBRUMsYUFBVztBQUNSLFFBQUksU0FBUyxPQUFPLE1BQXBCO0FBQ0EsUUFBSSxPQUFPLE9BQU8sTUFBZCxLQUF5QixXQUE3QixFQUEwQztBQUN0QyxlQUFPLEtBQVA7QUFDSDs7QUFFRCxXQUFPLE1BQVAsQ0FBYyxJQUFkLENBQW1CLFlBQW5CO0FBQ0EsV0FBTyxNQUFQLENBQWMsSUFBZCxDQUFtQixJQUFuQjtBQUNILENBUkEsR0FBRDs7Ozs7Ozs7a0JDTGUsT0FBTyxNQUFQLENBQWMsSUFBZCxDQUFtQixLQUFuQixDQUF5QixNQUF6QixDQUFnQztBQUMzQyxZQUFRLEVBQUUsTUFBRixDQUFTLEVBQVQsRUFBYSxPQUFPLE1BQVAsQ0FBYyxJQUFkLENBQW1CLFNBQW5CLENBQTZCLE1BQTFDLEVBQWtEO0FBQ3RELGdDQUF3QixXQUQ4QjtBQUV0RCxzQ0FBOEI7QUFGd0IsS0FBbEQsQ0FEbUM7O0FBTTNDLGdCQUFZLHNCQUFXO0FBQ25CLGVBQU8sTUFBUCxDQUFjLElBQWQsQ0FBbUIsU0FBbkIsQ0FBNkIsVUFBN0IsQ0FBd0MsS0FBeEMsQ0FBOEMsSUFBOUM7O0FBRUEsYUFBSyxFQUFMLENBQVEsb0JBQVIsRUFBOEIsS0FBSyx1QkFBbkM7QUFDQSxhQUFLLEVBQUwsQ0FBUSxnQkFBUixFQUEwQixLQUFLLGNBQS9COztBQUVBLGFBQUss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dW5jdGlvbigpe2Z1bmN0aW9uIHIoZSxuLHQpe2Z1bmN0aW9uIG8oaSxmKXtpZighbltpXSl7aWYoIWVbaV0pe3ZhciBjPVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmU7aWYoIWYmJmMpcmV0dXJuIGMoaSwhMCk7aWYodSlyZXR1cm4gdShpLCEwKTt2YXIgYT1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK2krXCInXCIpO3Rocm93IGEuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixhfXZhciBwPW5baV09e2V4cG9ydHM6e319O2VbaV1bMF0uY2FsbChwLmV4cG9ydHMsZnVuY3Rpb24ocil7dmFyIG49ZVtpXVsxXVtyXTtyZXR1cm4gbyhufHxyKX0scCxwLmV4cG9ydHMscixlLG4sdCl9cmV0dXJuIG5baV0uZXhwb3J0c31mb3IodmFyIHU9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZSxpPTA7aTx0Lmxlbmd0aDtpKyspbyh0W2ldKTtyZXR1cm4gb31yZXR1cm4gcn0pKCkiLCJpbXBvcnQgaW1hZ2VHYWxsZXJ5VmlldyBmcm9tICcuL3ZpZXdzL2ltYWdlLWdhbGxlcnknO1xuaW1wb3J0IHRhZ3NWaWV3IGZyb20gJy4vdmlld3MvdGFncyc7XG5cbndpbmRvdy5jYXJib24gPSB3aW5kb3cuY2FyYm9uIHx8IHt9O1xuXG4oZnVuY3Rpb24oKSB7XG4gICAgbGV0IGNhcmJvbiA9IHdpbmRvdy5jYXJib247XG4gICAgaWYgKHR5cGVvZiBjYXJib24uZmllbGRzID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgY2FyYm9uLmZpZWxkcy5WaWV3LkltYWdlR2FsbGVyeSA9IGltYWdlR2FsbGVyeVZpZXc7XG4gICAgY2FyYm9uLmZpZWxkcy5WaWV3LlRhZ3MgPSB0YWdzVmlldztcbn0oKSk7XG4iLCJleHBvcnQgZGVmYXVsdCBjYXJib24uZmllbGRzLlZpZXcuSW1hZ2UuZXh0ZW5kKHtcbiAgICBldmVudHM6IF8uZXh0ZW5kKHt9LCBjYXJib24uZmllbGRzLlZpZXcucHJvdG90eXBlLmV2ZW50cywge1xuICAgICAgICAnY2xpY2sgLmMyX29wZW5fbWVkaWEnOiAnb3Blbk1lZGlhJyxcbiAgICAgICAgJ2NsaWNrIC5jYXJib24taW1hZ2UtcmVtb3ZlJzogJ3JlbW92ZUltYWdlJ1xuICAgIH0pLFxuXG4gICAgaW5pdGlhbGl6ZTogZnVuY3Rpb24oKSB7XG4gICAgICAgIGNhcmJvbi5maWVsZHMuVmlldy5wcm90b3R5cGUuaW5pdGlhbGl6ZS5hcHBseSh0aGlzKTtcblxuICAgICAgICB0aGlzLm9uKCdmaWVsZDpiZWZvcmVSZW5kZXInLCB0aGlzLmxvYWREZXNjcmlwdGlvblRlbXBsYXRlKTtcbiAgICAgICAgdGhpcy5vbignZmllbGQ6cmVuZGVyZWQnLCB0aGlzLnNvcnRhYmxlSW1hZ2VzKTtcblxuICAgICAgICB0aGlzLmxpc3RlblRvKHRoaXMubW9kZWwsICdjaGFuZ2U6aW1hZ2VzJywgdGhpcy51cGRhdGVJbnB1dCk7XG4gICAgICAgIHRoaXMubGlzdGVuVG8odGhpcy5tb2RlbCwgJ2NoYW5nZTp2YWx1ZScsIHRoaXMucmVuZGVyKTtcbiAgICB9LFxuXG4gICAgc29ydGFibGVJbWFnZXM6IGZ1bmN0aW9uKCkge1xuICAgICAgICB2YXIgX3RoaXMgPSB0aGlzLFxuICAgICAgICAgICAgJGltYWdlR2FsbGVyeSA9IHRoaXMuJCgndWwuY2FyYm9uLWltYWdlLWdhbGxlcnknKTtcblxuICAgICAgICAvLyBJbWFnZSBvcmRlcmluZy5cbiAgICAgICAgJGltYWdlR2FsbGVyeS5zb3J0YWJsZSh7XG4gICAgICAgICAgICBpdGVtczogJ2xpLmNhcmJvbi1pbWFnZScsXG4gICAgICAgICAgICBjdXJzb3I6ICdtb3ZlJyxcbiAgICAgICAgICAgIGhlbHBlcjogJ2Nsb25lJyxcbiAgICAgICAgICAgIHNjcm9sbFNlbnNpdGl2aXR5OiA0MixcbiAgICAgICAgICAgIGZvcmNlUGxhY2Vob2xkZXJTaXplOiB0cnVlLFxuICAgICAgICAgICAgb3BhY2l0eTogMC43NSxcbiAgICAgICAgICAgIGZvcmNlSGVscGVyU2l6ZTogZmFsc2UsXG4gICAgICAgICAgICBwbGFjZWhvbGRlcjogJ2NhcmJvbi1zb3J0YWJsZS1wbGFjZWhvbGRlcicsXG4gICAgICAgICAgICB1cGRhdGU6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIHZhciBpbWFnZXMgPSBbXTtcblxuICAgICAgICAgICAgICAgICRpbWFnZUdhbGxlcnkuZmluZCgnbGkuY2FyYm9uLWltYWdlJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGltYWdlID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlID0gaW1hZ2UuYXR0cignZGF0YS1pbWFnZS12YWx1ZScpLFxuICAgICAgICAgICAgICAgICAgICAgICAgdXJsID0gaW1hZ2UuZmluZCgnaW1nJykuYXR0cignc3JjJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaW1hZ2VzLnB1c2goe1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWU6IHZhbHVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgdXJsOiB1cmxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICBfdGhpcy5tb2RlbC5zZXQoJ2ltYWdlcycsIGltYWdlcyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH0sXG4gICAgb3Blbk1lZGlhOiBmdW5jdGlvbihldmVudCkge1xuICAgICAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgICAgICB2YXIgdHlwZSA9IHRoaXMubW9kZWwuZ2V0KCd0eXBlJyk7XG4gICAgICAgIHZhciBpbWFnZXMgPSB0aGlzLm1vZGVsLmdldCgnaW1hZ2VzJyk7XG4gICAgICAgIHZhciBidXR0b25MYWJlbCA9IHRoaXMubW9kZWwuZ2V0KCd3aW5kb3dfYnV0dG9uX2xhYmVsJyk7XG4gICAgICAgIHZhciB3aW5kb3dMYWJlbCA9IHRoaXMubW9kZWwuZ2V0KCd3aW5kb3dfbGFiZWwnKTtcbiAgICAgICAgdmFyIHR5cGVGaWx0ZXIgPSB0aGlzLm1vZGVsLmdldCgndHlwZV9maWx0ZXInKTtcbiAgICAgICAgdmFyIHZhbHVlVHlwZSA9IHRoaXMubW9kZWwuZ2V0KCd2YWx1ZV90eXBlJyk7XG4gICAgICAgIHZhciBtZWRpYVR5cGVzID0ge307XG5cbiAgICAgICAgbWVkaWFUeXBlc1t0eXBlXSA9IHdwLm1lZGlhLmZyYW1lcy5jcmJNZWRpYUZpZWxkID0gd3AubWVkaWEoe1xuICAgICAgICAgICAgdGl0bGU6IHdpbmRvd0xhYmVsID8gd2luZG93TGFiZWwgOiBjcmJsMTBuLnRpdGxlLFxuICAgICAgICAgICAgbGlicmFyeTogeyB0eXBlOiB0eXBlRmlsdGVyIH0sIC8vIGF1ZGlvLCB2aWRlbywgaW1hZ2VcbiAgICAgICAgICAgIGJ1dHRvbjogeyB0ZXh0OiBidXR0b25MYWJlbCB9LFxuICAgICAgICAgICAgbXVsdGlwbGU6IHRydWVcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdmFyIG1lZGlhRmllbGQgPSBtZWRpYVR5cGVzW3R5cGVdO1xuXG4gICAgICAgIC8vIFJ1bnMgd2hlbiBhbiBpbWFnZSBpcyBzZWxlY3RlZC5cbiAgICAgICAgbWVkaWFGaWVsZC5vbignc2VsZWN0JywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyIHNlbGVjdGlvbnMgPSBtZWRpYUZpZWxkLnN0YXRlKCkuZ2V0KCdzZWxlY3Rpb24nKS50b0pTT04oKTtcbiAgICAgICAgICAgIHZhciBpbWFnZXMgPSBfLmNsb25lKF90aGlzLm1vZGVsLmdldCgnaW1hZ2VzJykpO1xuXG4gICAgICAgICAgICBfLmVhY2goc2VsZWN0aW9ucywgZnVuY3Rpb24oc2VsZWN0aW9uKSB7XG4gICAgICAgICAgICAgICAgaW1hZ2VzLnB1c2goe1xuICAgICAgICAgICAgICAgICAgICAndmFsdWUnOiBzZWxlY3Rpb25bdmFsdWVUeXBlXSxcbiAgICAgICAgICAgICAgICAgICAgJ3VybCc6IHNlbGVjdGlvbi51cmxcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBfdGhpcy5tb2RlbC5zZXQoJ2ltYWdlcycsIGltYWdlcyk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIE9wZW5zIHRoZSBtZWRpYSBsaWJyYXJ5IGZyYW1lXG4gICAgICAgIG1lZGlhRmllbGQub3BlbigpO1xuXG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgfSxcblxuICAgIHVwZGF0ZUlucHV0OiBmdW5jdGlvbihtb2RlbCkge1xuICAgICAgICBjb25zb2xlLmxvZyhtb2RlbCk7XG4gICAgICAgIHZhciAkaW5wdXQgPSB0aGlzLiQoJ2lucHV0LmNhcmJvbi1pbWFnZS1nYWxsZXJ5LWZpZWxkJyksXG4gICAgICAgICAgICBpbWFnZXMgPSBtb2RlbC5nZXQoJ2ltYWdlcycpLFxuICAgICAgICAgICAgaW1hZ2VWYWx1ZXMgPSBfLm1hcChpbWFnZXMsIGZ1bmN0aW9uKGltYWdlKSB7IHJldHVybiBpbWFnZS52YWx1ZTsgfSksXG4gICAgICAgICAgICB2YWx1ZSA9IGltYWdlVmFsdWVzLmpvaW4oKTtcblxuICAgICAgICAkaW5wdXQudmFsKHZhbHVlKS50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICB9LFxuXG4gICAgcmVtb3ZlSW1hZ2U6IGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgIHZhciAkdGFyZ2V0ID0gdGhpcy4kKGV2ZW50LmN1cnJlbnRUYXJnZXQpLFxuICAgICAgICAgICAgJHNlbGVjdGlvbnMgPSAkdGFyZ2V0LnBhcmVudHMoJ3VsLmNhcmJvbi1pbWFnZS1nYWxsZXJ5JykuY2hpbGRyZW4oKSxcbiAgICAgICAgICAgICRzZWxlY3Rpb24gPSAkdGFyZ2V0LnBhcmVudHMoJ2xpLmNhcmJvbi1pbWFnZScpLFxuICAgICAgICAgICAgaW1hZ2VzID0gXy5jbG9uZSh0aGlzLm1vZGVsLmdldCgnaW1hZ2VzJykpLFxuICAgICAgICAgICAgaW5kZXggPSAkc2VsZWN0aW9ucy5pbmRleCgkc2VsZWN0aW9uKTtcblxuICAgICAgICAvLyBSZW1vdmUgdGhlIGltYWdlIChzcGxpY2UgaXMgbm90IHdvcmtpbmcgaGVyZSwgZXZlbiB3aXRoIF8uY2xvbmUpXG4gICAgICAgIHZhciByZXN1bHQgPSBbXTtcbiAgICAgICAgZm9yKHZhciBpID0gMDsgaSA8IGltYWdlcy5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgaWYgKGkgIT0gaW5kZXgpIHtcbiAgICAgICAgICAgICAgICByZXN1bHQucHVzaChpbWFnZXNbaV0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoJ2ltYWdlcycsIHJlc3VsdCk7XG4gICAgfVxufSk7XG4iLCIvKipcbiAqIFRoZSB2aWV3IG9mIHRoZSB0YWdzIGZpZWxkIHVzZWQgZm9yIENhcmJvbiBGaWVsZHMuXG4gKlxuICogQHNpbmNlIDAuOC4xNlxuICovXG5sZXQgVGFncyA9IGNhcmJvbi5maWVsZHMuVmlldy5leHRlbmQoe1xuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgdGFncyB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOC4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBjYXJib24uZmllbGRzLlZpZXcucHJvdG90eXBlLmluaXRpYWxpemUuYXBwbHkodGhpcyk7XG5cbiAgICAgICAgdGhpcy5vbignZmllbGQ6cmVuZGVyZWQnLCB0aGlzLl9pbml0VGFncyk7XG4gICAgICAgIHRoaXMubGlzdGVuVG8odGhpcy5tb2RlbCwgJ2NoYW5nZTp2aXNpYmxlJywgdGhpcy5faW5pdFRhZ3MpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSB0YWdzIHdpdGggU2VsZWN0aXplLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xOFxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2luaXRUYWdzKCkge1xuICAgICAgICBsZXQgdGFncyA9IHRoaXMubW9kZWwuZ2V0KCd0YWdzJyk7XG4gICAgICAgIGxldCBtYXhJdGVtcyA9IHRoaXMubW9kZWwuZ2V0KCdtYXhJdGVtcycpO1xuXG4gICAgICAgIGlmKHRhZ3MpIHtcbiAgICAgICAgICAgIHRoaXMuX2luaXRQcmVkZWZpbmVkVGFncyh0YWdzLCBtYXhJdGVtcyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB0aGlzLl9pbml0Q3VzdG9tVGFncyhtYXhJdGVtcyk7XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgcHJlZGVmaW5lZCB0YWdzIHdpdGggYXV0by1jb21wbGV0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xOFxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHthcnJheX0gdGFnc1xuICAgICAqIEBwYXJhbSB7aW50fSBtYXhJdGVtc1xuICAgICAqL1xuICAgIF9pbml0UHJlZGVmaW5lZFRhZ3ModGFncywgbWF4SXRlbXMpIHtcbiAgICAgICAgbGV0IHNlbGVjdCA9IHRoaXMuJGVsLmZpbmQoJy5hZmYtdGFncy1wcmVkZWZpbmVkJyk7XG4gICAgICAgIGxldCBzZWxlY3RpemUgPSBzZWxlY3RbMF0uc2VsZWN0aXplO1xuICAgICAgICBsZXQgb3B0aW9ucyA9IE9iamVjdC5rZXlzKHRhZ3MpLm1hcCgoa2V5KSA9PiB7XG4gICAgICAgICAgICByZXR1cm4ge2lkOiBrZXksIHRleHQ6IHRhZ3Nba2V5XX1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgaWYoIXNlbGVjdGl6ZSkge1xuICAgICAgICAgICAgc2VsZWN0LnNlbGVjdGl6ZSh7XG4gICAgICAgICAgICAgICAgcGx1Z2luczogWydkcmFnX2Ryb3AnLCAncmVtb3ZlX2J1dHRvbiddLFxuICAgICAgICAgICAgICAgIGRlbGltaXRlcjogJywnLFxuICAgICAgICAgICAgICAgIHBlcnNpc3Q6IHRydWUsXG4gICAgICAgICAgICAgICAgY3JlYXRlOiB0cnVlLFxuICAgICAgICAgICAgICAgIHZhbHVlRmllbGQ6ICdpZCcsXG4gICAgICAgICAgICAgICAgbGFiZWxGaWVsZDogJ3RleHQnLFxuICAgICAgICAgICAgICAgIG1heE9wdGlvbnM6IDEwLFxuICAgICAgICAgICAgICAgIHNlYXJjaEZpZWxkOiBbJ3RleHQnXSxcbiAgICAgICAgICAgICAgICBtYXhJdGVtczogbWF4SXRlbXMsXG4gICAgICAgICAgICAgICAgb3B0aW9uczogb3B0aW9ucyxcbiAgICAgICAgICAgICAgICBvbkNoYW5nZTogKCkgPT4gdGhpcy5fb25DaGFuZ2VUYWdzKCksXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHNlbGVjdGl6ZS5lbmFibGUoKTtcbiAgICAgICAgfVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjdXN0b20gdGFncyB3aXRob3V0IGF1dG8tY29tcGxldGlvbi5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHNpbmNlIDAuOS4xOFxuICAgICAqIEBwYXJhbSB7aW50fSBtYXhJdGVtc1xuICAgICAqL1xuICAgIF9pbml0Q3VzdG9tVGFncyhtYXhJdGVtcykge1xuICAgICAgICBsZXQgc2VsZWN0ID0gdGhpcy4kZWwuZmluZCgnLmFmZi10YWdzLWN1c3RvbScpO1xuICAgICAgICBsZXQgc2VsZWN0aXplID0gc2VsZWN0WzBdLnNlbGVjdGl6ZTtcblxuICAgICAgICBpZighc2VsZWN0aXplKSB7XG4gICAgICAgICAgICBzZWxlY3Quc2VsZWN0aXplKHtcbiAgICAgICAgICAgICAgICBwbHVnaW5zOiBbJ2RyYWdfZHJvcCcsICdyZW1vdmVfYnV0dG9uJ10sXG4gICAgICAgICAgICAgICAgZGVsaW1pdGVyOiAnLCcsXG4gICAgICAgICAgICAgICAgcGVyc2lzdDogdHJ1ZSxcbiAgICAgICAgICAgICAgICBtYXhJdGVtczogbWF4SXRlbXMsXG4gICAgICAgICAgICAgICAgY3JlYXRlOiB0cnVlLFxuICAgICAgICAgICAgICAgIG9uQ2hhbmdlOiAoKSA9PiB0aGlzLl9vbkNoYW5nZVRhZ3MoKSxcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgc2VsZWN0aXplLmVuYWJsZSgpO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNldCB0aGUgbmV3IHZhbHVlIG9mIHRoZSBtb2RlbCBpZiB0aGUgdGFncyBoYXZlIGNoYW5nZWQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE4XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfb25DaGFuZ2VUYWdzKCkge1xuICAgICAgICBsZXQgaW5wdXQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwiJyArIHRoaXMudGVtcGxhdGVWYXJpYWJsZXMubmFtZSArICdcIl0nKTtcbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoJ3ZhbHVlJywgaW5wdXQudmFsKCkpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBUYWdzO1xuIl19
