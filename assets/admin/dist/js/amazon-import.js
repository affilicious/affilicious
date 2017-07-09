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
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});
var product = Backbone.Model.extend({
    parse: function parse(response) {
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

    events: {
        'click .aff-amazon-import-results-item-variants-show-all': 'showAll'
    },

    initialize: function initialize() {
        this.render();
    },

    render: function render() {
        this.setElement(this.template(this.model.attributes));
        return this;
    },
    showAll: function showAll(e) {
        e.preventDefault();
        this.$el.find('.aff-amazon-import-results-item-variants-show-all').hide();
        this.$el.find('.aff-amazon-import-results-item-variants-item').removeClass('aff-amazon-import-results-item-variants-item-hidden');
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9yZXN1bHQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvcmVzdWx0U2VhcmNoLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvcmVzdWx0LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvcmVzdWx0U2VhcmNoLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7QUNBQTs7OztBQUNBOzs7Ozs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7O0FBZUEsSUFBSSxnQkFBZ0IsMkJBQWlCLEVBQUMsTUFBTSxDQUFQLEVBQWpCLENBQXBCO0FBQ0EsSUFBSSxvQkFBb0IsMkJBQXFCO0FBQ3pDLGdCQUFZLGFBRDZCO0FBRXpDLFFBQUk7QUFGcUMsQ0FBckIsQ0FBeEI7O0FBS0EsY0FBYyxLQUFkO0FBQ0Esa0JBQWtCLE1BQWxCOzs7Ozs7OztBQ3pCQSxJQUFJLFVBQVUsU0FBUyxLQUFULENBQWUsTUFBZixDQUFzQjtBQUNoQyxTQURnQyxpQkFDMUIsUUFEMEIsRUFDakI7QUFDWCxlQUFPLFFBQVA7QUFDSDtBQUgrQixDQUF0QixDQUFkOztrQkFNZSxPOzs7Ozs7Ozs7QUNOZjs7Ozs7O2tCQUVlLFNBQVMsVUFBVCxDQUFvQixNQUFwQixDQUEyQjtBQUN0QywyQkFEc0M7O0FBR3RDLGNBSHNDLHNCQUczQixPQUgyQixFQUdsQjtBQUFBOztBQUNoQixZQUFHLFdBQVcsUUFBUSxNQUF0QixFQUE4QjtBQUMxQixpQkFBSyxNQUFMLEdBQWMsUUFBUSxNQUF0QjtBQUNIOztBQUVELFlBQUcsV0FBVyxRQUFRLElBQXRCLEVBQTRCO0FBQ3hCLGlC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R0FBZ0IsQ0FBM0IsR0FBZ0MsTUFBaEMsR0FBeUMsS0FBbEU7QUFDQSxhQUFLLFVBQUwsQ0FBZ0IsS0FBaEIsQ0FBc0IsRUFBQyxRQUFRLEtBQVQsRUFBdEIsRUFBdUMsSUFBdkMsQ0FBNEMsWUFBTSxDQUVqRCxDQUZEO0FBR0g7QUFsRCtCLENBQXJCLEMiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiaW1wb3J0IFJlc3VsdFNlYXJjaCBmcm9tICcuL21vZGVsL3Jlc3VsdFNlYXJjaCc7XG5pbXBvcnQgUmVzdWx0U2VhcmNoVmlldyBmcm9tICcuL3ZpZXcvcmVzdWx0U2VhcmNoJztcblxuLypcbmpRdWVyeShmdW5jdGlvbigkKSB7XG4gICAgJC5hamF4KHtcbiAgICAgICAgdXJsIDogYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXgsXG4gICAgICAgIHR5cGUgOiAncG9zdCcsXG4gICAgICAgIGRhdGEgOiB7XG4gICAgICAgICAgICBhY3Rpb24gOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX3NlYXJjaCcsXG4gICAgICAgIH0sXG4gICAgICAgIHN1Y2Nlc3MgOiBmdW5jdGlvbiggcmVzcG9uc2UgKSB7XG4gICAgICAgICAgICBhbGVydChyZXNwb25zZSlcbiAgICAgICAgfVxuICAgIH0pO1xufSk7XG4qL1xuXG5sZXQgcHJvZHVjdFNlYXJjaCA9IG5ldyBSZXN1bHRTZWFyY2goe3BhZ2U6IDF9KTtcbmxldCBwcm9kdWN0U2VhcmNoVmlldyA9IG5ldyBSZXN1bHRTZWFyY2hWaWV3KHtcbiAgICBjb2xsZWN0aW9uOiBwcm9kdWN0U2VhcmNoLFxuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0J1xufSk7XG5cbnByb2R1Y3RTZWFyY2guZmV0Y2goKTtcbnByb2R1Y3RTZWFyY2hWaWV3LnJlbmRlcigpO1xuIiwibGV0IHByb2R1Y3QgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIHBhcnNlKHJlc3BvbnNlKXtcbiAgICAgICAgcmV0dXJuIHJlc3BvbnNlO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBwcm9kdWN0O1xuIiwiaW1wb3J0IFByb2R1Y3QgZnJvbSAnLi9yZXN1bHQnO1xuXG5leHBvcnQgZGVmYXVsdCBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG4gICAgbW9kZWw6IFByb2R1Y3QsXG5cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgaWYob3B0aW9ucyAmJiBvcHRpb25zLnNlYXJjaCkge1xuICAgICAgICAgICAgdGhpcy5zZWFyY2ggPSBvcHRpb25zLnNlYXJjaDtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKG9wdGlvbnMgJiYgb3B0aW9ucy5wYWdlKSB7XG4gICAgICAgICAgICB0aGlzLnBhZ2UgPSBvcHRpb25zLnBhZ2U7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLm9uKCdjaGFuZ2U6c2VsZWN0ZWQnLCAoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLnRyaWdnZXIoJ2NoYW5nZScpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgdXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXggKyAnP2FjdGlvbj1hZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25fc2VhcmNoJztcbiAgICB9LFxufSk7XG4iLCJleHBvcnQgZGVmYXVsdCBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgdGVtcGxhdGU6IF8udGVtcGxhdGUoalF1ZXJ5KCcuYWZmLWFtYXpvbi1pbXBvcnQtcmVzdWx0cy10ZW1wbGF0ZScpLmh0bWwoKSksXG5cbiAgICB0YWdOYW1lOiAnZGl2JyxcblxuICAgIGNsYXNzTmFtZTogJycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NsaWNrIC5hZmYtYW1hem9uLWltcG9ydC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtc2hvdy1hbGwnOiAnc2hvd0FsbCdcbiAgICB9LFxuXG4gICAgaW5pdGlhbGl6ZTogZnVuY3Rpb24oKSB7XG4gICAgICAgIHRoaXMucmVuZGVyKCk7XG4gICAgfSxcblxuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zZXRFbGVtZW50KHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICBzaG93QWxsKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJykuaGlkZSgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLWl0ZW0nKS5yZW1vdmVDbGFzcygnYWZmLWFtYXpvbi1pbXBvcnQtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLWl0ZW0taGlkZGVuJyk7XG4gICAgfSxcbn0pO1xuIiwiaW1wb3J0IFByb2R1Y3RWaWV3IGZyb20gJy4vcmVzdWx0JztcblxuZXhwb3J0IGRlZmF1bHQgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbiA9IG9wdGlvbnMuY29sbGVjdGlvbjtcbiAgICAgICAgdGhpcy5wYWdlID0gb3B0aW9ucy5wYWdlO1xuICAgICAgICB0aGlzLnNlYXJjaElucHV0ID0gdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC12YWx1ZScpO1xuICAgICAgICB0aGlzLnJlc3VsdHMgPSB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtcmVzdWx0cycpO1xuXG4gICAgICAgIC8vIEVuc3VyZSBvdXIgbWV0aG9kcyBrZWVwIHRoZSBgdGhpc2AgcmVmZXJlbmNlIHRvIHRoZSB2aWV3IGl0c2VsZlxuICAgICAgICBfLmJpbmRBbGwodGhpcywgJ3NlYXJjaCcpO1xuICAgICAgICBfLmJpbmRBbGwodGhpcywgJ3JlbmRlcicpO1xuICAgICAgICBfLmJpbmRBbGwodGhpcywgJ2FkZE9uZScpO1xuXG4gICAgICAgIC8vIEJpbmQgdGhlIGNvbGxlY3Rpb24gZXZlbnRzXG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZXNldCcsIHRoaXMucmVuZGVyKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ2FkZCcsIHRoaXMucmVuZGVyKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3JlbW92ZScsIHRoaXMucmVuZGVyKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3N5bmMnLCB0aGlzLnJlbmRlcik7XG5cbiAgICAgICAgLy8gVHJpZ2dlciB0aGUgc2VhcmNoIGlmIHRoZSB1c2VyIGNvbXBsZXRlcyB0eXBpbmcuXG4gICAgICAgIC8vdGhpcy5zZWFyY2hJbnB1dC5vbigna2V5dXAnLCBfLmRlYm91bmNlKHRoaXMuc2VhcmNoLCA0MDApKTtcbiAgICB9LFxuXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLmFkZEFsbCgpO1xuICAgIH0sXG5cbiAgICBhZGRBbGwoKSB7XG4gICAgICAgIHRoaXMucmVzdWx0cy5lbXB0eSgpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uZm9yRWFjaCh0aGlzLmFkZE9uZSk7XG4gICAgfSxcblxuICAgIGFkZE9uZShwcm9kdWN0KSB7XG4gICAgICAgIGxldCB2aWV3ID0gbmV3IFByb2R1Y3RWaWV3KHtcbiAgICAgICAgICAgIG1vZGVsOiBwcm9kdWN0LFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuYXBwZW5kKHZpZXcucmVuZGVyKCkuZWwpO1xuICAgIH0sXG5cbiAgICBzZWFyY2goZSkge1xuICAgICAgICBpZihlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgc2VhcmNoID0gdGhpcy5zZWFyY2hJbnB1dC52YWwoKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLnNlYXJjaCA9IChzZWFyY2ggJiYgc2VhcmNoLmxlbmd0aCA+IDApID8gc2VhcmNoIDogZmFsc2U7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5mZXRjaCh7cmVtb3ZlOiBmYWxzZX0pLmRvbmUoKCkgPT4ge1xuXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbn0pO1xuIl19
