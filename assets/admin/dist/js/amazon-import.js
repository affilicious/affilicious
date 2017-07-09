(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _search = require('./model/search');

var _search2 = _interopRequireDefault(_search);

var _search3 = require('./view/search');

var _search4 = _interopRequireDefault(_search3);

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

var search = new _search2.default();
var searchView = new _search4.default({
    model: search
});

searchView.render();

},{"./model/search":5,"./view/search":9}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchForm = Backbone.Model.extend({
    defaults: {
        'term': '',
        'type': 'keywords',
        'category': 'all'
    }
});

exports.default = SearchForm;

},{}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchResultsItem = Backbone.Model.extend({
    parse: function parse(response) {
        return response;
    }
});

exports.default = SearchResultsItem;

},{}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchResultsItem = require('./search-results-item');

var _searchResultsItem2 = _interopRequireDefault(_searchResultsItem);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var SearchResults = Backbone.Collection.extend({
    model: _searchResultsItem2.default,

    initialize: function initialize(options) {
        if (options && options.page) {
            this.page = options.page;
        }
    },
    url: function url() {
        return affAdminAmazonImportUrls.ajax + '?action=aff_product_admin_amazon_search';
    }
});

exports.default = SearchResults;

},{"./search-results-item":3}],5:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchForm = require('./search-form');

var _searchForm2 = _interopRequireDefault(_searchForm);

var _searchResults = require('./search-results');

var _searchResults2 = _interopRequireDefault(_searchResults);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Search = Backbone.Model.extend({
    initialize: function initialize(options) {
        this.form = new _searchForm2.default();
        this.results = new _searchResults2.default();
        this.page = options && options.page ? options.page : 1;

        _.bindAll(this, 'process');
    },
    process: function process() {
        this.results.fetch();
    }
});

exports.default = Search;

},{"./search-form":2,"./search-results":4}],6:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchForm = Backbone.View.extend({
    el: '.aff-amazon-import-search-form',

    events: {
        'change': 'changed'
    },

    render: function render() {
        var html = jQuery('#aff-amazon-import-search-form-template').html(),
            template = _.template(html);

        this.$el.html(template(this.model.attributes));

        return this;
    },
    changed: function changed() {
        var term = this.$el.find('input[name="term"]').val(),
            type = this.$el.find('select[name="type"]').val(),
            category = this.$el.find('select[name="category"]').val();

        this.model.set('term', term);
        this.model.set('type', type);
        this.model.set('category', category);

        this.render();
    }
});

exports.default = SearchForm;

},{}],7:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchResultsItem = Backbone.View.extend({
    tagName: 'div',

    className: '',

    events: {
        'click .aff-amazon-import-search-results-item-variants-show-all': 'showAll'
    },

    initialize: function initialize() {
        _.bindAll(this, 'render');

        this.model.on('change', this.render);
    },
    render: function render() {
        var html = jQuery('#aff-amazon-import-search-results-item-template').html(),
            template = _.template(html);

        this.setElement(template(this.model.attributes));

        return this;
    },
    showAll: function showAll(e) {
        e.preventDefault();

        this.$el.find('.aff-amazon-import-search-results-item-variants-show-all').hide();
        this.$el.find('.aff-amazon-import-search-results-item-variants-item').show();
    }
});

exports.default = SearchResultsItem;

},{}],8:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchResultsItem = require('./search-results-item');

var _searchResultsItem2 = _interopRequireDefault(_searchResultsItem);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var SearchResults = Backbone.View.extend({
    el: '.aff-amazon-import-search-results',

    initialize: function initialize(options) {
        this.collection = options.collection;

        // Ensure our methods keep the 'this' reference to the view itself
        _.bindAll(this, 'render', 'addOne');

        // Bind the collection events
        this.collection.bind('reset', this.render);
        this.collection.bind('add', this.render);
        this.collection.bind('remove', this.render);
        this.collection.bind('sync', this.render);
    },
    render: function render() {
        this.addAll();
    },
    addAll: function addAll() {
        this.$el.empty();
        this.collection.forEach(this.addOne);
    },
    addOne: function addOne(product) {
        var view = new _searchResultsItem2.default({
            model: product
        });

        this.$el.append(view.render().el);
    }
});

exports.default = SearchResults;

},{"./search-results-item":7}],9:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchForm = require('./search-form');

var _searchForm2 = _interopRequireDefault(_searchForm);

var _searchResults = require('./search-results');

var _searchResults2 = _interopRequireDefault(_searchResults);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Search = Backbone.View.extend({
    el: '.aff-amazon-import-search',

    initialize: function initialize() {
        this.form = new _searchForm2.default({
            model: this.model.form
        });

        this.results = new _searchResults2.default({
            collection: this.model.results
        });

        _.bindAll(this, 'process');

        this.form.$el.on('submit', this.process);
    },
    render: function render() {
        this.form.render();
        this.results.render();

        return this;
    },
    process: function process(e) {
        e.preventDefault();

        this.model.process();
        this.render();
    }
});

exports.default = Search;

},{"./search-form":6,"./search-results":8}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1yZXN1bHRzLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMtaXRlbS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1yZXN1bHRzLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7QUNBQTs7OztBQUNBOzs7Ozs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7O0FBZUEsSUFBSSxTQUFTLHNCQUFiO0FBQ0EsSUFBSSxhQUFhLHFCQUFlO0FBQzdCLFdBQU87QUFEc0IsQ0FBZixDQUFqQjs7QUFJQSxXQUFXLE1BQVg7Ozs7Ozs7O0FDdkJBLElBQUksYUFBYSxTQUFTLEtBQVQsQ0FBZSxNQUFmLENBQXNCO0FBQ25DLGNBQVU7QUFDTixnQkFBUSxFQURGO0FBRU4sZ0JBQVEsVUFGRjtBQUdOLG9CQUFZO0FBSE47QUFEeUIsQ0FBdEIsQ0FBakI7O2tCQVFlLFU7Ozs7Ozs7O0FDUmYsSUFBSSxvQkFBb0IsU0FBUyxL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WDs7QUFJQSxhQUFLLEdBQUwsQ0FBUyxNQUFULENBQWdCLEtBQUssTUFBTCxHQUFjLEVBQTlCO0FBQ0g7QUEvQm9DLENBQXJCLENBQXBCOztrQkFrQ2UsYTs7Ozs7Ozs7O0FDcENmOzs7O0FBQ0E7Ozs7OztBQUVBLElBQUksU0FBUyxTQUFTLElBQVQsQ0FBYyxNQUFkLENBQXFCO0FBQzlCLFFBQUksMkJBRDBCOztBQUc5QixjQUg4Qix3QkFHakI7QUFDVCxhQUFLLElBQUwsR0FBWSx5QkFBZTtBQUN2QixtQkFBTyxLQUFLLEtBQUwsQ0FBVztBQURLLFNBQWYsQ0FBWjs7QUFJQSxhQUFLLE9BQUwsR0FBZSw0QkFBa0I7QUFDN0Isd0JBQVksS0FBSyxLQUFMLENBQVc7QUFETSxTQUFsQixDQUFmOztBQUlBLFVBQUUsT0FBRixDQUFVLElBQVYsRUFBZ0IsU0FBaEI7O0FBRUEsYUFBSyxJQUFMLENBQVUsR0FBVixDQUFjLEVBQWQsQ0FBaUIsUUFBakIsRUFBMkIsS0FBSyxPQUFoQztBQUNILEtBZjZCO0FBaUI5QixVQWpCOEIsb0JBaUJyQjtBQUNMLGFBQUssSUFBTCxDQUFVLE1BQVY7QUFDQSxhQUFLLE9BQUwsQ0FBYSxNQUFiOztBQUVBLGVBQU8sSUFBUDtBQUNILEtBdEI2QjtBQXdCOUIsV0F4QjhCLG1CQXdCdEIsQ0F4QnNCLEVBd0JuQjtBQUNQLFVBQUUsY0FBRjs7QUFFQSxhQUFLLEtBQUwsQ0FBVyxPQUFYO0FBQ0EsYUFBSyxNQUFMO0FBQ0g7QUE3QjZCLENBQXJCLENBQWI7O2tCQWdDZSxNIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9tb2RlbC9zZWFyY2gnO1xuaW1wb3J0IFNlYXJjaFZpZXcgZnJvbSAnLi92aWV3L3NlYXJjaCc7XG5cbi8qXG5qUXVlcnkoZnVuY3Rpb24oJCkge1xuICAgICQuYWpheCh7XG4gICAgICAgIHVybCA6IGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hamF4LFxuICAgICAgICB0eXBlIDogJ3Bvc3QnLFxuICAgICAgICBkYXRhIDoge1xuICAgICAgICAgICAgYWN0aW9uIDogJ2FmZl9wcm9kdWN0X2FkbWluX2FtYXpvbl9zZWFyY2gnLFxuICAgICAgICB9LFxuICAgICAgICBzdWNjZXNzIDogZnVuY3Rpb24oIHJlc3BvbnNlICkge1xuICAgICAgICAgICAgYWxlcnQocmVzcG9uc2UpXG4gICAgICAgIH1cbiAgICB9KTtcbn0pO1xuKi9cblxubGV0IHNlYXJjaCA9IG5ldyBTZWFyY2goKTtcbmxldCBzZWFyY2hWaWV3ID0gbmV3IFNlYXJjaFZpZXcoe1xuICAgbW9kZWw6IHNlYXJjaCxcbn0pO1xuXG5zZWFyY2hWaWV3LnJlbmRlcigpO1xuIiwibGV0IFNlYXJjaEZvcm0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICd0ZXJtJzogJycsXG4gICAgICAgICd0eXBlJzogJ2tleXdvcmRzJyxcbiAgICAgICAgJ2NhdGVnb3J5JzogJ2FsbCdcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hSZXN1bHRzSXRlbSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgcGFyc2UocmVzcG9uc2Upe1xuICAgICAgICByZXR1cm4gcmVzcG9uc2U7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdEl0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG4gICAgbW9kZWw6IFNlYXJjaFJlc3VsdEl0ZW0sXG5cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgaWYob3B0aW9ucyAmJiBvcHRpb25zLnBhZ2UpIHtcbiAgICAgICAgICAgIHRoaXMucGFnZSA9IG9wdGlvbnMucGFnZTtcbiAgICAgICAgfVxuICAgIH0sXG5cbiAgICB1cmwoKSB7XG4gICAgICAgIHJldHVybiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYWpheCArICc/YWN0aW9uPWFmZl9wcm9kdWN0X2FkbWluX2FtYXpvbl9zZWFyY2gnO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSgpO1xuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cygpO1xuICAgICAgICB0aGlzLnBhZ2UgPSBvcHRpb25zICYmIG9wdGlvbnMucGFnZSA/IG9wdGlvbnMucGFnZSA6IDE7XG5cbiAgICAgICAgXy5iaW5kQWxsKHRoaXMsICdwcm9jZXNzJyk7XG4gICAgfSxcblxuICAgIHByb2Nlc3MoKSB7XG4gICAgICAgIHRoaXMucmVzdWx0cy5mZXRjaCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2g7XG4iLCJsZXQgU2VhcmNoRm9ybSA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWZvcm0nLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UnOiAnY2hhbmdlZCcsXG4gICAgfSxcblxuICAgIHJlbmRlcigpIHtcbiAgICAgICAgbGV0IGh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybS10ZW1wbGF0ZScpLmh0bWwoKSxcbiAgICAgICAgICAgIHRlbXBsYXRlID0gXy50ZW1wbGF0ZShodG1sKTtcblxuICAgICAgICB0aGlzLiRlbC5odG1sKHRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICBjaGFuZ2VkKCkge1xuICAgICAgICBsZXQgdGVybSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJ0ZXJtXCJdJykudmFsKCksXG4gICAgICAgICAgICB0eXBlID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0eXBlXCJdJykudmFsKCksXG4gICAgICAgICAgICBjYXRlZ29yeSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nKS52YWwoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCgndGVybScsIHRlcm0pO1xuICAgICAgICB0aGlzLm1vZGVsLnNldCgndHlwZScsIHR5cGUpO1xuICAgICAgICB0aGlzLm1vZGVsLnNldCgnY2F0ZWdvcnknLCBjYXRlZ29yeSk7XG5cbiAgICAgICAgdGhpcy5yZW5kZXIoKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaEZvcm07XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgdGFnTmFtZTogJ2RpdicsXG5cbiAgICBjbGFzc05hbWU6ICcnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCc6ICdzaG93QWxsJ1xuICAgIH0sXG5cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBfLmJpbmRBbGwodGhpcywgJ3JlbmRlcicpO1xuXG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyKTtcbiAgICB9LFxuXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBsZXQgaHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdGVtcGxhdGUnKS5odG1sKCksXG4gICAgICAgICAgICB0ZW1wbGF0ZSA9IF8udGVtcGxhdGUoaHRtbCk7XG5cbiAgICAgICAgdGhpcy5zZXRFbGVtZW50KHRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICBzaG93QWxsKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJykuaGlkZSgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1pdGVtJykuc2hvdygpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0c0l0ZW07XG4iLCJpbXBvcnQgUHJvZHVjdFZpZXcgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMnLFxuXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbiA9IG9wdGlvbnMuY29sbGVjdGlvbjtcblxuICAgICAgICAvLyBFbnN1cmUgb3VyIG1ldGhvZHMga2VlcCB0aGUgJ3RoaXMnIHJlZmVyZW5jZSB0byB0aGUgdmlldyBpdHNlbGZcbiAgICAgICAgXy5iaW5kQWxsKHRoaXMsICdyZW5kZXInLCAnYWRkT25lJyk7XG5cbiAgICAgICAgLy8gQmluZCB0aGUgY29sbGVjdGlvbiBldmVudHNcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3Jlc2V0JywgdGhpcy5yZW5kZXIpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnYWRkJywgdGhpcy5yZW5kZXIpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgncmVtb3ZlJywgdGhpcy5yZW5kZXIpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnc3luYycsIHRoaXMucmVuZGVyKTtcbiAgICB9LFxuXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLmFkZEFsbCgpO1xuICAgIH0sXG5cbiAgICBhZGRBbGwoKSB7XG4gICAgICAgIHRoaXMuJGVsLmVtcHR5KCk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5mb3JFYWNoKHRoaXMuYWRkT25lKTtcbiAgICB9LFxuXG4gICAgYWRkT25lKHByb2R1Y3QpIHtcbiAgICAgICAgbGV0IHZpZXcgPSBuZXcgUHJvZHVjdFZpZXcoe1xuICAgICAgICAgICAgbW9kZWw6IHByb2R1Y3QsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuJGVsLmFwcGVuZCh2aWV3LnJlbmRlcigpLmVsKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hSZXN1bHRzIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMnO1xuXG5sZXQgU2VhcmNoID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaCcsXG5cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5mb3JtLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cyh7XG4gICAgICAgICAgICBjb2xsZWN0aW9uOiB0aGlzLm1vZGVsLnJlc3VsdHMsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIF8uYmluZEFsbCh0aGlzLCAncHJvY2VzcycpO1xuXG4gICAgICAgIHRoaXMuZm9ybS4kZWwub24oJ3N1Ym1pdCcsIHRoaXMucHJvY2VzcylcbiAgICB9LFxuXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLmZvcm0ucmVuZGVyKCk7XG4gICAgICAgIHRoaXMucmVzdWx0cy5yZW5kZXIoKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgcHJvY2VzcyhlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnByb2Nlc3MoKTtcbiAgICAgICAgdGhpcy5yZW5kZXIoKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoO1xuIl19
