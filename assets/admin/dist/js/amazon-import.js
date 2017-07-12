(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _search = require('./model/search');

var _search2 = _interopRequireDefault(_search);

var _search3 = require('./view/search');

var _search4 = _interopRequireDefault(_search3);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var search = new _search2.default();
var searchView = new _search4.default({ model: search });

searchView.render();

},{"./model/search":6,"./view/search":11}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchForm = Backbone.Model.extend({
    defaults: {
        'term': '',
        'type': 'keywords',
        'category': 'all',
        'loading': false
    },

    /**
     * Submit the form the form and trigger the loading animation.
     *
     * @public
     */
    submit: function submit() {
        this.set('loading', true);
        this.trigger('aff:amazon-import:search:search-form:submit', this);
    },


    /**
     * Finish the submit and stop the loading animation.
     *
     * @public
     */
    done: function done() {
        this.set('loading', false);
        this.trigger('aff:amazon-import:search:search-form:done', this);
    }
});

exports.default = SearchForm;

},{}],3:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchLoadMore = Backbone.Model.extend({
    defaults: {
        'loading': false,
        'noResults': false
    },

    /**
     * Activate the loading spinner animation.
     *
     * @public
     */
    load: function load() {
        this.set('loading', true);
        this.trigger('aff:amazon-import:search:load-more:load', this);
    },


    /**
     * Show the load more button and deactivate the spinner animation.
     *
     * @public
     */
    done: function done() {
        this.set('loading', false);
        this.trigger('aff:amazon-import:search:load-more:done', this);
    },


    /**
     * Show the no results message and deactivate the spinner animation.
     *
     * @public
     */
    noResults: function noResults() {
        this.set({
            'loading': false,
            'noResults': true
        });

        this.trigger('aff:amazon-import:search:load-more:no-results', this);
    }
});

exports.default = SearchLoadMore;

},{}],4:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
var SearchResultsItem = Backbone.Model.extend({});

exports.default = SearchResultsItem;

},{}],5:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchResultsItem = require('./search-results-item');

var _searchResultsItem2 = _interopRequireDefault(_searchResultsItem);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var SearchResults = Backbone.Collection.extend({
    model: _searchResultsItem2.default
});

exports.default = SearchResults;

},{"./search-results-item":4}],6:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchForm = require('./search-form');

var _searchForm2 = _interopRequireDefault(_searchForm);

var _searchLoadMore = require('./search-load-more');

var _searchLoadMore2 = _interopRequireDefault(_searchLoadMore);

var _searchResults = require('./search-results');

var _searchResults2 = _interopRequireDefault(_searchResults);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Search = Backbone.Model.extend({
    defaults: {
        'started': false,
        'action': 'aff_product_admin_amazon_search',
        'page': 1
    },

    /**
     * Initialize the search with the given options.
     *
     * @param {array} options
     */
    initialize: function initialize(options) {
        this.form = new _searchForm2.default();
        this.results = new _searchResults2.default();
        this.loadMore = new _searchLoadMore2.default();
        this.page = options && options.page ? options.page : 1;

        this.form.on('aff:amazon-import:search:search-form:submit', this.start, this);
        this.loadMore.on('aff:amazon-import:search:load-more:load', this.load, this);
    },


    /**
     * Start the search with the first page.
     *
     * @public
     */
    start: function start() {
        var _this = this;

        if (this.form.get('term') === null) {
            return;
        }

        this.set('page', 1);
        this.results.url = this._buildUrl();

        this.results.fetch().done(function () {
            _this.set('started', true);
            _this.form.done();
        });
    },


    /**
     * Load more search results by increasing the page.
     *
     * @public
     */
    load: function load() {
        var _this2 = this;

        this.set('page', this.get('page') + 1);

        this.results.url = this._buildUrl();
        this.results.fetch({ 'remove': false }).done(function () {
            _this2.loadMore.done();
        });
    },


    /**
     * Build the search API url based on the given parameters.
     *
     * @returns {string}
     * @private
     */
    _buildUrl: function _buildUrl() {
        return affAdminAmazonImportUrls.ajax + ('?action=' + this.get('action')) + ('&term=' + this.form.get('term')) + ('&type=' + this.form.get('type')) + ('&category=' + this.form.get('category')) + ('&page=' + this.get('page'));
    }
});

exports.default = Search;

},{"./search-form":2,"./search-load-more":3,"./search-results":5}],7:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchForm = Backbone.View.extend({
    el: '.aff-amazon-import-search-form',

    events: {
        'change': 'change',
        'submit': 'submit'
    },

    /**
     * Initialize the search form.
     *
     * @public
     */
    initialize: function initialize() {
        this.model.on('change', this.render, this);
    },


    /**
     * Render the search form.
     *
     * @returns {SearchForm}
     * @public
     */
    render: function render() {
        var html = jQuery('#aff-amazon-import-search-form-template').html(),
            template = _.template(html);

        this.$el.html(template(this.model.attributes));

        return this;
    },


    /**
     * Submit the search form.
     *
     * @param e
     * @public
     */
    submit: function submit(e) {
        e.preventDefault();

        this.change();
        this.model.submit();
    },


    /**
     * Load the new search parameters into the model on form change.
     *
     * @public
     */
    change: function change() {
        var term = this.$el.find('input[name="term"]').val(),
            type = this.$el.find('select[name="type"]').val(),
            category = this.$el.find('select[name="category"]').val();

        this.model.set({
            'term': term,
            'type': type,
            'category': category
        });
    }
});

exports.default = SearchForm;

},{}],8:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchLoadMore = Backbone.View.extend({
    el: '.aff-amazon-import-load-more',

    events: {
        'click .aff-amazon-import-load-more-button': 'load'
    },

    /**
     * Initialize the search load more.
     *
     * @public
     */
    initialize: function initialize() {
        this.model.on('change', this.render, this);
    },


    /**
     * Render the search load more.
     *
     * @return {SearchLoadMore}
     * @public
     */
    render: function render() {
        var html = jQuery('#aff-amazon-import-load-more-template').html(),
            template = _.template(html);

        this.$el.html(template(this.model.attributes));

        return this;
    },


    /**
     * Enable the loading animation.
     *
     * @public
     */
    load: function load() {
        this.model.load();
    }
});

exports.default = SearchLoadMore;

},{}],9:[function(require,module,exports){
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

    /**
     * Initialize the search results item.
     *
     * @public
     */
    initialize: function initialize() {
        this.model.on('change', this.render, this);
    },


    /**
     * Render the search results item.
     *
     * @return {SearchResultsItem}
     * @public
     */
    render: function render() {
        var html = jQuery('#aff-amazon-import-search-results-item-template').html(),
            template = _.template(html);

        this.setElement(template(this.model.attributes));

        return this;
    },


    /**
     * Show all hidden variants.
     *
     * @param e
     * @public
     */
    showAll: function showAll(e) {
        e.preventDefault();

        this.$el.find('.aff-amazon-import-search-results-item-variants-show-all').hide();
        this.$el.find('.aff-amazon-import-search-results-item-variants-item').show();
    }
});

exports.default = SearchResultsItem;

},{}],10:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchResultsItem = require('./search-results-item');

var _searchResultsItem2 = _interopRequireDefault(_searchResultsItem);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var SearchResults = Backbone.View.extend({
    el: '.aff-amazon-import-search-results',

    /**
     * Initialize the search results.
     *
     * @param {array} options
     * @public
     */
    initialize: function initialize(options) {
        var _this = this;

        this.collection = options.collection;

        // Bind the collection events
        this.collection.bind('reset', function () {
            return _this.render();
        });
        this.collection.bind('add', function () {
            return _this.render();
        });
        this.collection.bind('remove', function () {
            return _this.render();
        });
        this.collection.bind('sync', function () {
            return _this.render();
        });
    },


    /**
     * Render the search results.
     *
     * @public
     */
    render: function render() {
        this._addAll();
    },


    /**
     * Add all search results items to the view.
     *
     * @private
     */
    _addAll: function _addAll() {
        this.$el.empty();
        this.collection.forEach(this._addOne, this);
    },


    /**
     * Add one search results item to the view.
     *
     * @private
     */
    _addOne: function _addOne(product) {
        var view = new _searchResultsItem2.default({
            model: product
        });

        this.$el.append(view.render().el);
    }
});

exports.default = SearchResults;

},{"./search-results-item":9}],11:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchForm = require('./search-form');

var _searchForm2 = _interopRequireDefault(_searchForm);

var _searchLoadMore = require('./search-load-more');

var _searchLoadMore2 = _interopRequireDefault(_searchLoadMore);

var _searchResults = require('./search-results');

var _searchResults2 = _interopRequireDefault(_searchResults);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Search = Backbone.View.extend({
    el: '.aff-amazon-import-search',

    /**
     * Initialize the search.
     *
     * @public
     */
    initialize: function initialize() {
        this.form = new _searchForm2.default({
            model: this.model.form
        });

        this.results = new _searchResults2.default({
            collection: this.model.results
        });

        this.loadMore = new _searchLoadMore2.default({
            model: this.model.loadMore
        });

        this.model.on('change', this.render, this);
    },


    /**
     * Render the search.
     *
     * @public
     */
    render: function render() {
        this.form.render();
        this.results.render();

        if (this.model.get('started')) {
            this.loadMore.render();
        }

        return this;
    }
});

exports.default = Search;

},{"./search-form":7,"./search-load-more":8,"./search-results":10}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtbG9hZC1tb3JlLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1yZXN1bHRzLWl0ZW0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLWZvcm0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtbG9hZC1tb3JlLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMtaXRlbS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1yZXN1bHRzLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7QUNBQTs7OztBQUNBOzs7Ozs7QUFFQSxJQUFJLFNBQVMsc0JBQWI7QUFDQSxJQUFJLGFBQWEscUJBQWUsRUFBQyxPQUFPLE1BQVIsRUFBZixDQUFqQjs7QUFFQSxXQUFXLE1BQVg7Ozs7Ozs7O0FDTkEsSUFBSSxhQUFhLFNBQVMsS0FBVCxDQUFlLE1BQWYsQ0FBc0I7QUFDbkMsY0FBVTtBQUNO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QUssS0FBTCxDQUFXO0FBREssU0FBZixDQUFaOztBQUlBLGFBQUssT0FBTCxHQUFlLDRCQUFrQjtBQUM3Qix3QkFBWSxLQUFLLEtBQUwsQ0FBVztBQURNLFNBQWxCLENBQWY7O0FBSUEsYUFBSyxRQUFMLEdBQWdCLDZCQUFtQjtBQUMvQixtQkFBTyxLQUFLLEtBQUwsQ0FBVztBQURhLFNBQW5CLENBQWhCOztBQUlBLGFBQUssS0FBTCxDQUFXLEVBQVgsQ0FBYyxRQUFkLEVBQXdCLEtBQUssTUFBN0IsRUFBcUMsSUFBckM7QUFDSCxLQXRCNkI7OztBQXdCOUI7Ozs7O0FBS0EsVUE3QjhCLG9CQTZCckI7QUFDTCxhQUFLLElBQUwsQ0FBVSxNQUFWO0FBQ0EsYUFBSyxPQUFMLENBQWEsTUFBYjs7QUFFQSxZQUFHLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxTQUFmLENBQUgsRUFBOEI7QUFDMUIsaUJBQUssUUFBTCxDQUFjLE1BQWQ7QUFDSDs7QUFFRCxlQUFPLElBQVA7QUFDSDtBQXRDNkIsQ0FBckIsQ0FBYjs7a0JBeUNlLE0iLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiaW1wb3J0IFNlYXJjaCBmcm9tICcuL21vZGVsL3NlYXJjaCc7XG5pbXBvcnQgU2VhcmNoVmlldyBmcm9tICcuL3ZpZXcvc2VhcmNoJztcblxubGV0IHNlYXJjaCA9IG5ldyBTZWFyY2goKTtcbmxldCBzZWFyY2hWaWV3ID0gbmV3IFNlYXJjaFZpZXcoe21vZGVsOiBzZWFyY2h9KTtcblxuc2VhcmNoVmlldy5yZW5kZXIoKTtcbiIsImxldCBTZWFyY2hGb3JtID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAndGVybSc6ICcnLFxuICAgICAgICAndHlwZSc6ICdrZXl3b3JkcycsXG4gICAgICAgICdjYXRlZ29yeSc6ICdhbGwnLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIGZvcm0gdGhlIGZvcm0gYW5kIHRyaWdnZXIgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEZpbmlzaCB0aGUgc3VibWl0IGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBkb25lKCkge1xuICAgICAgICB0aGlzLnNldCgnbG9hZGluZycsIGZhbHNlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZG9uZScsIHRoaXMpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFjdGl2YXRlIHRoZSBsb2FkaW5nIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgdHJ1ZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIGxvYWQgbW9yZSBidXR0b24gYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgZmFsc2UpO1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZG9uZScsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IHRoZSBubyByZXN1bHRzIG1lc3NhZ2UgYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cygpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnIDogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOm5vLXJlc3VsdHMnLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdEl0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG4gICAgbW9kZWw6IFNlYXJjaFJlc3VsdEl0ZW0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3N0YXJ0ZWQnOiBmYWxzZSxcbiAgICAgICAgJ2FjdGlvbic6ICdhZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25fc2VhcmNoJyxcbiAgICAgICAgJ3BhZ2UnIDogMSxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHdpdGggdGhlIGdpdmVuIG9wdGlvbnMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge2FycmF5fSBvcHRpb25zXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuZm9ybSA9IG5ldyBTZWFyY2hGb3JtKCk7XG4gICAgICAgIHRoaXMucmVzdWx0cyA9IG5ldyBTZWFyY2hSZXN1bHRzKCk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUgPSBuZXcgU2VhcmNoTG9hZE1vcmUoKTtcbiAgICAgICAgdGhpcy5wYWdlID0gb3B0aW9ucyAmJiBvcHRpb25zLnBhZ2UgPyBvcHRpb25zLnBhZ2UgOiAxO1xuXG4gICAgICAgIHRoaXMuZm9ybS5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOnN1Ym1pdCcsIHRoaXMuc3RhcnQsIHRoaXMpO1xuICAgICAgICB0aGlzLmxvYWRNb3JlLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmxvYWQnLCB0aGlzLmxvYWQsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdGFydCB0aGUgc2VhcmNoIHdpdGggdGhlIGZpcnN0IHBhZ2UuXG4gICAgICpcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3RhcnQoKSB7XG4gICAgICAgIGlmKHRoaXMuZm9ybS5nZXQoJ3Rlcm0nKSA9PT0gbnVsbCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCAxKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnVybCA9IHRoaXMuX2J1aWxkVXJsKCk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLmZldGNoKCkuZG9uZSgoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLnNldCgnc3RhcnRlZCcsIHRydWUpO1xuICAgICAgICAgICAgdGhpcy5mb3JtLmRvbmUoKTtcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgbW9yZSBzZWFyY2ggcmVzdWx0cyBieSBpbmNyZWFzaW5nIHRoZSBwYWdlLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdwYWdlJywgdGhpcy5nZXQoJ3BhZ2UnKSArIDEpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goeydyZW1vdmUnOiBmYWxzZX0pLmRvbmUoKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5kb25lKCk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgc2VhcmNoIEFQSSB1cmwgYmFzZWQgb24gdGhlIGdpdmVuIHBhcmFtZXRlcnMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnRlcm09JHt0aGlzLmZvcm0uZ2V0KCd0ZXJtJyl9YFxuICAgICAgICAgICAgKyBgJnR5cGU9JHt0aGlzLmZvcm0uZ2V0KCd0eXBlJyl9YFxuICAgICAgICAgICAgKyBgJmNhdGVnb3J5PSR7dGhpcy5mb3JtLmdldCgnY2F0ZWdvcnknKX1gXG4gICAgICAgICAgICArIGAmcGFnZT0ke3RoaXMuZ2V0KCdwYWdlJyl9YFxuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2g7XG4iLCJsZXQgU2VhcmNoRm9ybSA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWZvcm0nLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UnOiAnY2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdzdWJtaXQnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggZm9ybS5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7U2VhcmNoRm9ybX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBsZXQgaHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtLXRlbXBsYXRlJykuaHRtbCgpLFxuICAgICAgICAgICAgdGVtcGxhdGUgPSBfLnRlbXBsYXRlKGh0bWwpO1xuXG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdWJtaXQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5jaGFuZ2UoKTtcbiAgICAgICAgdGhpcy5tb2RlbC5zdWJtaXQoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHNlYXJjaCBwYXJhbWV0ZXJzIGludG8gdGhlIG1vZGVsIG9uIGZvcm0gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZSgpIHtcbiAgICAgICAgbGV0IHRlcm0gPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwidGVybVwiXScpLnZhbCgpLFxuICAgICAgICAgICAgdHlwZSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwidHlwZVwiXScpLnZhbCgpLFxuICAgICAgICAgICAgY2F0ZWdvcnkgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNhdGVnb3J5XCJdJykudmFsKCk7XG5cblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAndGVybSc6IHRlcm0sXG4gICAgICAgICAgICAndHlwZSc6IHR5cGUsXG4gICAgICAgICAgICAnY2F0ZWdvcnknOiBjYXRlZ29yeVxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaEZvcm07XG4iLCJsZXQgU2VhcmNoTG9hZE1vcmUgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LWxvYWQtbW9yZScsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NsaWNrIC5hZmYtYW1hem9uLWltcG9ydC1sb2FkLW1vcmUtYnV0dG9uJzogJ2xvYWQnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggbG9hZCBtb3JlLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggbG9hZCBtb3JlLlxuICAgICAqXG4gICAgICogQHJldHVybiB7U2VhcmNoTG9hZE1vcmV9XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgbGV0IGh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1sb2FkLW1vcmUtdGVtcGxhdGUnKS5odG1sKCksXG4gICAgICAgICAgICB0ZW1wbGF0ZSA9IF8udGVtcGxhdGUoaHRtbCk7XG5cbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRW5hYmxlIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBsb2FkKCkge1xuICAgICAgICB0aGlzLm1vZGVsLmxvYWQoKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgdGFnTmFtZTogJ2RpdicsXG5cbiAgICBjbGFzc05hbWU6ICcnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCc6ICdzaG93QWxsJ1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHJldHVybiB7U2VhcmNoUmVzdWx0c0l0ZW19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgbGV0IGh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXRlbXBsYXRlJykuaHRtbCgpLFxuICAgICAgICAgICAgdGVtcGxhdGUgPSBfLnRlbXBsYXRlKGh0bWwpO1xuXG4gICAgICAgIHRoaXMuc2V0RWxlbWVudCh0ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhbGwgaGlkZGVuIHZhcmlhbnRzLlxuICAgICAqXG4gICAgICogQHBhcmFtIGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc2hvd0FsbChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCcpLmhpZGUoKTtcbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtaXRlbScpLnNob3coKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFByb2R1Y3RWaWV3IGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzJyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uID0gb3B0aW9ucy5jb2xsZWN0aW9uO1xuXG4gICAgICAgIC8vIEJpbmQgdGhlIGNvbGxlY3Rpb24gZXZlbnRzXG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZXNldCcsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnYWRkJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZW1vdmUnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3N5bmMnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuX2FkZEFsbCgpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgYWxsIHNlYXJjaCByZXN1bHRzIGl0ZW1zIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYWRkQWxsKCkge1xuICAgICAgICB0aGlzLiRlbC5lbXB0eSgpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uZm9yRWFjaCh0aGlzLl9hZGRPbmUsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgb25lIHNlYXJjaCByZXN1bHRzIGl0ZW0gdG8gdGhlIHZpZXcuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRPbmUocHJvZHVjdCkge1xuICAgICAgICBsZXQgdmlldyA9IG5ldyBQcm9kdWN0Vmlldyh7XG4gICAgICAgICAgICBtb2RlbDogcHJvZHVjdCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy4kZWwuYXBwZW5kKHZpZXcucmVuZGVyKCkuZWwpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuZm9ybSA9IG5ldyBTZWFyY2hGb3JtKHtcbiAgICAgICAgICAgIG1vZGVsOiB0aGlzLm1vZGVsLmZvcm0sXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cyA9IG5ldyBTZWFyY2hSZXN1bHRzKHtcbiAgICAgICAgICAgIGNvbGxlY3Rpb246IHRoaXMubW9kZWwucmVzdWx0cyxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5sb2FkTW9yZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaC5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuZm9ybS5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnJlbmRlcigpO1xuXG4gICAgICAgIGlmKHRoaXMubW9kZWwuZ2V0KCdzdGFydGVkJykpIHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUucmVuZGVyKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiJdfQ==
