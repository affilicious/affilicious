(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _resultSearch = require('./model/resultSearch');

var _resultSearch2 = _interopRequireDefault(_resultSearch);

var _resultSearch3 = require('./view/resultSearch');

var _resultSearch4 = _interopRequireDefault(_resultSearch3);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/*
jQuery(function($) {
    $.ajax({
        url : affAdminAmazonImportUrls.ajax,
        type : 'post',
        data : {
            action : 'aff_product_admin_amazon_search',
        },
        success : function( response ) {
            alert(response)
        }
    });
});
*/

var productSearch = new _resultSearch2.default({ page: 1 });
var productSearchView = new _resultSearch4.default({
    collection: productSearch,
    el: '.aff-amazon-import'
});

productSearch.fetch();
productSearchView.render();

},{"./model/resultSearch":3,"./view/resultSearch":5}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var product = Backbone.Model.extend({
    defaults: {
        title: '#'
    },

    parse: function parse(response) {
        console.log(response);
        return response;
    }
});

exports.default = product;

},{}],3:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _result = require('./result');

var _result2 = _interopRequireDefault(_result);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = Backbone.Collection.extend({
    model: _result2.default,

    initialize: function initialize(options) {
        var _this = this;

        if (options && options.search) {
            this.search = options.search;
        }

        if (options && options.page) {
            this.page = options.page;
        }

        this.on('change:selected', function () {
            _this.trigger('change');
        });
    },
    url: function url() {
        return affAdminAmazonImportUrls.ajax + '?action=aff_product_admin_amazon_search';
    }
});

},{"./result":2}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = Backbone.View.extend({

    template: _.template(jQuery('.aff-amazon-import-results-template').html()),

    tagName: 'div',

    className: '',

    render: function render() {
        console.log(jQuery('.aff-amazon-import-results-template').html());
        this.$el.empty();
        this.$el.append(this.template(this.model.attributes));

        return this;
    }
});

},{}],5:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _result = require('./result');

var _result2 = _interopRequireDefault(_result);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = Backbone.View.extend({
    initialize: function initialize(options) {
        this.collection = options.collection;
        this.page = options.page;
        this.searchInput = this.$el.find('.aff-amazon-import-search-value');
        this.results = this.$el.find('.aff-amazon-import-results');

        // Ensure our methods keep the `this` reference to the view itself
        _.bindAll(this, 'search');
        _.bindAll(this, 'render');
        _.bindAll(this, 'addOne');

        // Bind the collection events
        this.collection.bind('reset', this.render);
        this.collection.bind('add', this.render);
        this.collection.bind('remove', this.render);
        this.collection.bind('sync', this.render);

        // Trigger the search if the user completes typing.
        //this.searchInput.on('keyup', _.debounce(this.search, 400));
    },
    render: function render() {
        this.addAll();
    },
    addAll: function addAll() {
        this.results.empty();
        this.collection.forEach(this.addOne);
    },
    addOne: function addOne(product) {
        var view = new _result2.default({
            model: product
        });

        this.results.append(view.render().el);
    },
    search: function search(e) {
        if (e) {
            e.preventDefault();
        }

        var search = this.searchInput.val();
        this.collection.search = search && search.length > 0 ? search : false;
        this.collection.fetch({ remove: false }).done(function () {});
    }
});

},{"./result":4}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9yZXN1bHQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvcmVzdWx0U2VhcmNoLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvcmVzdWx0LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvcmVzdWx0U2VhcmNoLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7QUNBQTs7OztBQUNBOzs7Ozs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7O0FBZUEsSUFBSSxnQkFBZ0IsMkJBQWlCLEVBQUMsTUFBTSxDQUFQLEVBQWpCLENBQXBCO0FBQ0EsSUFBSSxvQkFBb0IsMkJBQXFCO0FBQ3pDLGdCQUFZLGFBRDZCO0FBRXpDLFFBQUk7QUFGcUMsQ0FBckIsQ0FBeEI7O0FBS0EsY0FBYyxLQUFkO0FBQ0Esa0JBQWtCLE1BQWxCOzs7Ozs7OztBQ3pCQSxJQUFJLFVBQVUsU0FBUyxLQUFULENBQWUsTUFBZixDQUFzQjtBQUNoQyxjQUFVO0FBQ04sZUFBTztBQURELEtBRHNCOztBQUtoQyxTQUxnQyxpQkFLMUIsUUFMMEIsRUFLakI7QUFDWCxnQkFBUSxHQUFSLENBQVksUUFBWjtBQUNBLGVBQU8sUUFBUDtBQUNIO0FBUitCLENBQXRCLENBQWQ7O2tCQVdlLE87Ozs7Ozs7OztBQ1hmOzs7Ozs7a0JBRWUsU0FBUyxVQUFULENBQW9CLE1BQXBCLENBQTJCO0FBQ3RDLDJCQURzQzs7QUFHdEMsY0FIc0Msc0JBRzNCLE9BSDJCLEVBR2xCO0FBQUE7O0FBQ2hCLFlBQUcsV0FBVyxRQUFRLE1BQXRCLEVBQThCO0FBQzFCLGlCQUFLLE1BQUwsR0FB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QU0sQ0FFakQsQ0FGRDtBQUdIO0FBbEQrQixDQUFyQixDIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsImltcG9ydCBSZXN1bHRTZWFyY2ggZnJvbSAnLi9tb2RlbC9yZXN1bHRTZWFyY2gnO1xuaW1wb3J0IFJlc3VsdFNlYXJjaFZpZXcgZnJvbSAnLi92aWV3L3Jlc3VsdFNlYXJjaCc7XG5cbi8qXG5qUXVlcnkoZnVuY3Rpb24oJCkge1xuICAgICQuYWpheCh7XG4gICAgICAgIHVybCA6IGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hamF4LFxuICAgICAgICB0eXBlIDogJ3Bvc3QnLFxuICAgICAgICBkYXRhIDoge1xuICAgICAgICAgICAgYWN0aW9uIDogJ2FmZl9wcm9kdWN0X2FkbWluX2FtYXpvbl9zZWFyY2gnLFxuICAgICAgICB9LFxuICAgICAgICBzdWNjZXNzIDogZnVuY3Rpb24oIHJlc3BvbnNlICkge1xuICAgICAgICAgICAgYWxlcnQocmVzcG9uc2UpXG4gICAgICAgIH1cbiAgICB9KTtcbn0pO1xuKi9cblxubGV0IHByb2R1Y3RTZWFyY2ggPSBuZXcgUmVzdWx0U2VhcmNoKHtwYWdlOiAxfSk7XG5sZXQgcHJvZHVjdFNlYXJjaFZpZXcgPSBuZXcgUmVzdWx0U2VhcmNoVmlldyh7XG4gICAgY29sbGVjdGlvbjogcHJvZHVjdFNlYXJjaCxcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydCdcbn0pO1xuXG5wcm9kdWN0U2VhcmNoLmZldGNoKCk7XG5wcm9kdWN0U2VhcmNoVmlldy5yZW5kZXIoKTtcbiIsImxldCBwcm9kdWN0ID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICB0aXRsZTogJyMnXG4gICAgfSxcblxuICAgIHBhcnNlKHJlc3BvbnNlKXtcbiAgICAgICAgY29uc29sZS5sb2cocmVzcG9uc2UpO1xuICAgICAgICByZXR1cm4gcmVzcG9uc2U7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IHByb2R1Y3Q7XG4iLCJpbXBvcnQgUHJvZHVjdCBmcm9tICcuL3Jlc3VsdCc7XG5cbmV4cG9ydCBkZWZhdWx0IEJhY2tib25lLkNvbGxlY3Rpb24uZXh0ZW5kKHtcbiAgICBtb2RlbDogUHJvZHVjdCxcblxuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICBpZihvcHRpb25zICYmIG9wdGlvbnMuc2VhcmNoKSB7XG4gICAgICAgICAgICB0aGlzLnNlYXJjaCA9IG9wdGlvbnMuc2VhcmNoO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYob3B0aW9ucyAmJiBvcHRpb25zLnBhZ2UpIHtcbiAgICAgICAgICAgIHRoaXMucGFnZSA9IG9wdGlvbnMucGFnZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMub24oJ2NoYW5nZTpzZWxlY3RlZCcsICgpID0+IHtcbiAgICAgICAgICAgIHRoaXMudHJpZ2dlcignY2hhbmdlJyk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICB1cmwoKSB7XG4gICAgICAgIHJldHVybiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYWpheCArICc/YWN0aW9uPWFmZl9wcm9kdWN0X2FkbWluX2FtYXpvbl9zZWFyY2gnO1xuICAgIH0sXG59KTtcbiIsImV4cG9ydCBkZWZhdWx0IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblxuICAgIHRlbXBsYXRlOiBfLnRlbXBsYXRlKGpRdWVyeSgnLmFmZi1hbWF6b24taW1wb3J0LXJlc3VsdHMtdGVtcGxhdGUnKS5odG1sKCkpLFxuXG4gICAgdGFnTmFtZTogJ2RpdicsXG5cbiAgICBjbGFzc05hbWU6ICcnLFxuXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBjb25zb2xlLmxvZyhqUXVlcnkoJy5hZmYtYW1hem9uLWltcG9ydC1yZXN1bHRzLXRlbXBsYXRlJykuaHRtbCgpKTtcbiAgICAgICAgdGhpcy4kZWwuZW1wdHkoKTtcbiAgICAgICAgdGhpcy4kZWwuYXBwZW5kKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbn0pO1xuIiwiaW1wb3J0IFByb2R1Y3RWaWV3IGZyb20gJy4vcmVzdWx0JztcblxuZXhwb3J0IGRlZmF1bHQgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbiA9IG9wdGlvbnMuY29sbGVjdGlvbjtcbiAgICAgICAgdGhpcy5wYWdlID0gb3B0aW9ucy5wYWdlO1xuICAgICAgICB0aGlzLnNlYXJjaElucHV0ID0gdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC12YWx1ZScpO1xuICAgICAgICB0aGlzLnJlc3VsdHMgPSB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtcmVzdWx0cycpO1xuXG4gICAgICAgIC8vIEVuc3VyZSBvdXIgbWV0aG9kcyBrZWVwIHRoZSBgdGhpc2AgcmVmZXJlbmNlIHRvIHRoZSB2aWV3IGl0c2VsZlxuICAgICAgICBfLmJpbmRBbGwodGhpcywgJ3NlYXJjaCcpO1xuICAgICAgICBfLmJpbmRBbGwodGhpcywgJ3JlbmRlcicpO1xuICAgICAgICBfLmJpbmRBbGwodGhpcywgJ2FkZE9uZScpO1xuXG4gICAgICAgIC8vIEJpbmQgdGhlIGNvbGxlY3Rpb24gZXZlbnRzXG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZXNldCcsIHRoaXMucmVuZGVyKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ2FkZCcsIHRoaXMucmVuZGVyKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3JlbW92ZScsIHRoaXMucmVuZGVyKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3N5bmMnLCB0aGlzLnJlbmRlcik7XG5cbiAgICAgICAgLy8gVHJpZ2dlciB0aGUgc2VhcmNoIGlmIHRoZSB1c2VyIGNvbXBsZXRlcyB0eXBpbmcuXG4gICAgICAgIC8vdGhpcy5zZWFyY2hJbnB1dC5vbigna2V5dXAnLCBfLmRlYm91bmNlKHRoaXMuc2VhcmNoLCA0MDApKTtcbiAgICB9LFxuXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLmFkZEFsbCgpO1xuICAgIH0sXG5cbiAgICBhZGRBbGwoKSB7XG4gICAgICAgIHRoaXMucmVzdWx0cy5lbXB0eSgpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uZm9yRWFjaCh0aGlzLmFkZE9uZSk7XG4gICAgfSxcblxuICAgIGFkZE9uZShwcm9kdWN0KSB7XG4gICAgICAgIGxldCB2aWV3ID0gbmV3IFByb2R1Y3RWaWV3KHtcbiAgICAgICAgICAgIG1vZGVsOiBwcm9kdWN0LFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuYXBwZW5kKHZpZXcucmVuZGVyKCkuZWwpO1xuICAgIH0sXG5cbiAgICBzZWFyY2goZSkge1xuICAgICAgICBpZihlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgc2VhcmNoID0gdGhpcy5zZWFyY2hJbnB1dC52YWwoKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLnNlYXJjaCA9IChzZWFyY2ggJiYgc2VhcmNoLmxlbmd0aCA+IDApID8gc2VhcmNoIDogZmFsc2U7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5mZXRjaCh7cmVtb3ZlOiBmYWxzZX0pLmRvbmUoKCkgPT4ge1xuXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbn0pO1xuIl19
