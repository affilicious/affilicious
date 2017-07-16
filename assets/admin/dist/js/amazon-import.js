(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _search = require('./model/search');

var _search2 = _interopRequireDefault(_search);

var _search3 = require('./view/search');

var _search4 = _interopRequireDefault(_search3);

var _config = require('./model/config');

var _config2 = _interopRequireDefault(_config);

var _config3 = require('./view/config');

var _config4 = _interopRequireDefault(_config3);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var search = new _search2.default();
var searchView = new _search4.default({ model: search });

searchView.render();

var config = new _config2.default();
var configView = new _config4.default({ model: config });

configView.render();

},{"./model/config":2,"./model/search":7,"./view/config":8,"./view/search":13}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var Config = Backbone.Model.extend({
    defaults: {
        'selectedShop': 'amazon',
        'newShopName': null,
        'selectedAction': 'new-product',
        'mergeProductId': null,
        'replaceProductId': null,
        'status': 'draft'
    }
});

exports.default = Config;

},{}],3:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchForm = Backbone.Model.extend({
    defaults: {
        'term': '',
        'type': 'keywords',
        'category': 'All',
        'withVariants': 'no',
        'loading': false,
        'providerConfigured': false
    },

    /**
     * Submit the form the form and trigger the loading animation.
     *
     * @since 0.9
     * @public
     */
    submit: function submit() {
        this.set('loading', true);
        this.trigger('aff:amazon-import:search:search-form:submit', this);
    },


    /**
     * Finish the submit and stop the loading animation.
     *
     * @since 0.9
     * @public
     */
    done: function done() {
        this.set('loading', false);
        this.trigger('aff:amazon-import:search:search-form:done', this);
    }
});

exports.default = SearchForm;

},{}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchLoadMore = Backbone.Model.extend({
    defaults: {
        'enabled': true,
        'loading': false,
        'noResults': false
    },

    /**
     * Activate the loading spinner animation.
     *
     * @since 0.9
     * @public
     */
    load: function load() {
        this.set('loading', true);
        this.trigger('aff:amazon-import:search:load-more:load', this);
    },


    /**
     * Show the load more button and deactivate the spinner animation.
     *
     * @since 0.9
     * @public
     */
    done: function done() {
        this.set('loading', false);
        this.trigger('aff:amazon-import:search:load-more:done', this);
    },


    /**
     * Show the no results message and deactivate the spinner animation.
     *
     * @since 0.9
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

},{}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
var SearchResultsItem = Backbone.Model.extend({});

exports.default = SearchResultsItem;

},{}],6:[function(require,module,exports){
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

},{"./search-results-item":5}],7:[function(require,module,exports){
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
     * @since 0.9
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
     * @since 0.9
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
            _this.loadMore.set('enabled', _this.form.get('type') === 'keywords');
            _this.set('started', true);
            _this.form.done();
        });
    },


    /**
     * Load more search results by increasing the page.
     *
     * @since 0.9
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
     * @since 0.9
     * @returns {string}
     * @private
     */
    _buildUrl: function _buildUrl() {
        return affAdminAmazonImportUrls.ajax + ('?action=' + this.get('action')) + ('&term=' + this.form.get('term')) + ('&type=' + this.form.get('type')) + ('&category=' + this.form.get('category')) + ('&with-variants=' + this.form.get('withVariants')) + ('&page=' + this.get('page'));
    }
});

exports.default = Search;

},{"./search-form":3,"./search-load-more":4,"./search-results":6}],8:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var Config = Backbone.View.extend({
    el: '.aff-amazon-import-config',

    events: {
        'change input[name="shop"]': 'changeShop',
        'change input[name="action"]': 'changeAction',
        'change input[name="status"]': 'changeStatus'
    },

    /**
     * Initialize the config.
     *
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        var templateHtml = jQuery('#aff-amazon-import-config-template').html();
        this.template = _.template(templateHtml);
    },


    /**
     * Render the config.
     *
     * @since 0.9
     * @returns {Config}
     * @public
     */
    render: function render() {
        var html = this.template(this.model.attributes);
        this.$el.html(html);

        this.$el.find('.aff-amazon-import-config-group-option-merge-product-id, .aff-amazon-import-config-group-option-replace-product-id').selectize({
            maxItems: 1,
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            create: false,
            load: function load(query, callback) {
                if (!query.length) return callback();
                jQuery.ajax({
                    url: '/wp-json/wp/v2/aff-products/?search=' + query,
                    type: 'GET',
                    error: function error() {
                        callback();
                    },
                    success: function success(results) {
                        results = results.map(function (result) {
                            return {
                                'id': result.id,
                                'name': result.title.rendered
                            };
                        });

                        callback(results);
                    }
                });
            }
        });

        return this;
    },


    /**
     * Load the new shop configuration into the model on change.
     *
     * @since 0.9
     * @public
     */
    changeShop: function changeShop() {
        var selectedShop = this.$el.find('input[name="shop"]:checked'),
            newShopName = this.$el.find('input[name="new-shop-name"]');

        selectedShop.val() === 'new-shop' ? newShopName.removeAttr('disabled') : newShopName.attr('disabled', 'disabled');

        this.model.set({
            'selectedShop': selectedShop.val(),
            'newShopName': newShopName.val()
        });
    },


    /**
     * Load the new action configuration into the model on change.
     *
     * @since 0.9
     * @public
     */
    changeAction: function changeAction() {
        var selectedAction = this.$el.find('input[name="action"]:checked'),
            mergeProductId = this.$el.find('input[name="merge-product-id"]'),
            replaceProductId = this.$el.find('input[name="replace-product-id"]'),
            mergeSelectize = mergeProductId.selectize()[0].selectize,
            replaceSelectize = replaceProductId.selectize()[0].selectize;

        selectedAction.val() === 'merge-product' ? mergeSelectize.enable() : mergeSelectize.disable();
        selectedAction.val() === 'replace-product' ? replaceSelectize.enable() : replaceSelectize.disable();

        this.model.set({
            'selectedAction': selectedAction.val(),
            'mergeProductId': mergeProductId.val(),
            'replaceProductId': replaceProductId.val()
        });
    },


    /**
     * Load the new status configuration into the model on change.
     *
     * @since 0.9
     * @public
     */
    changeStatus: function changeStatus() {
        var selectedAction = this.$el.find('input[name="status"]:checked');

        this.model.set({
            'status': selectedAction.val()
        });
    }
});

exports.default = Config;

},{}],9:[function(require,module,exports){
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
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        var providerConfigured = this.$el.data('provider-configured');

        this.model.set('providerConfigured', providerConfigured === true || providerConfigured === 'true');
        this.model.on('change', this.render, this);
    },


    /**
     * Render the search form.
     *
     * @since 0.9
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
     * @since 0.9
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
     * @since 0.9
     * @public
     */
    change: function change() {
        var term = this.$el.find('input[name="term"]'),
            type = this.$el.find('select[name="type"]'),
            category = this.$el.find('select[name="category"]'),
            withVariants = this.$el.find('select[name="with-variants"]');

        this.model.set({
            'term': term.val(),
            'type': type.val(),
            'category': category.val(),
            'withVariants': withVariants.val()
        });
    }
});

exports.default = SearchForm;

},{}],10:[function(require,module,exports){
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
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        this.model.on('change', this.render, this);
    },


    /**
     * Render the search load more.
     *
     * @since 0.9
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
     * @since 0.9
     * @public
     */
    load: function load() {
        this.model.load();
    }
});

exports.default = SearchLoadMore;

},{}],11:[function(require,module,exports){
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
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        this.model.on('change', this.render, this);
    },


    /**
     * Render the search results item.
     *
     * @since 0.9
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
     * @since 0.9
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

},{}],12:[function(require,module,exports){
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
     * @since 0.9
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
     * @since 0.9
     * @public
     */
    render: function render() {
        this._addAll();
    },


    /**
     * Add all search results items to the view.
     *
     * @since 0.9
     * @private
     */
    _addAll: function _addAll() {
        this.$el.empty();
        this.collection.forEach(this._addOne, this);
    },


    /**
     * Add one search results item to the view.
     *
     * @since 0.9
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

},{"./search-results-item":11}],13:[function(require,module,exports){
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
     * @since 0.9
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
     * @since 0.9
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

},{"./search-form":9,"./search-load-more":10,"./search-results":12}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWZvcm0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWxvYWQtbW9yZS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1yZXN1bHRzLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L2NvbmZpZy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLWxvYWQtbW9yZS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1yZXN1bHRzLWl0ZW0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7O0FDQUE7Ozs7QUFDQTs7OztBQU9BOzs7O0FBQ0E7Ozs7OztBQU5BLElBQUksU0FBUyxzQkFBYjtBQUNBLElB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Qjs7a0JBd0RlLGE7Ozs7Ozs7OztBQzFEZjs7OztBQUNBOzs7O0FBQ0E7Ozs7OztBQUVBLElBQUksU0FBUyxTQUFTLElBQVQsQ0FBYyxNQUFkLENBQXFCO0FBQzlCLFFBQUksMkJBRDBCOztBQUc5Qjs7Ozs7O0FBTUEsY0FUOEIsd0JBU2pCO0FBQ1QsYUFBSyxJQUFMLEdBQVkseUJBQWU7QUFDdkIsbUJBQU8sS0FBSyxLQUFMLENBQVc7QUFESyxTQUFmLENBQVo7O0FBSUEsYUFBSyxPQUFMLEdBQWUsNEJBQWtCO0FBQzdCLHdCQUFZLEtBQUssS0FBTCxDQUFXO0FBRE0sU0FBbEIsQ0FBZjs7QUFJQSxhQUFLLFFBQUwsR0FBZ0IsNkJBQW1CO0FBQy9CLG1CQUFPLEtBQUssS0FBTCxDQUFXO0FBRGEsU0FBbkIsQ0FBaEI7O0FBSUEsYUFBSyxLQUFMLENBQVcsRUFBWCxDQUFjLFFBQWQsRUFBd0IsS0FBSyxNQUE3QixFQUFxQyxJQUFyQztBQUNILEtBdkI2Qjs7O0FBeUI5Qjs7Ozs7O0FBTUEsVUEvQjhCLG9CQStCckI7QUFDTCxhQUFLLElBQUwsQ0FBVSxNQUFWO0FBQ0EsYUFBSyxPQUFMLENBQWEsTUFBYjs7QUFFQSxZQUFHLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxTQUFmLENBQUgsRUFBOEI7QUFDMUIsaUJBQUssUUFBTCxDQUFjLE1BQWQ7QUFDSDs7QUFFRCxlQUFPLElBQVA7QUFDSDtBQXhDNkIsQ0FBckIsQ0FBYjs7a0JBMkNlLE0iLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiaW1wb3J0IFNlYXJjaCBmcm9tICcuL21vZGVsL3NlYXJjaCc7XG5pbXBvcnQgU2VhcmNoVmlldyBmcm9tICcuL3ZpZXcvc2VhcmNoJztcblxubGV0IHNlYXJjaCA9IG5ldyBTZWFyY2goKTtcbmxldCBzZWFyY2hWaWV3ID0gbmV3IFNlYXJjaFZpZXcoe21vZGVsOiBzZWFyY2h9KTtcblxuc2VhcmNoVmlldy5yZW5kZXIoKTtcblxuaW1wb3J0IENvbmZpZyBmcm9tICcuL21vZGVsL2NvbmZpZyc7XG5pbXBvcnQgQ29uZmlnVmlldyBmcm9tICcuL3ZpZXcvY29uZmlnJztcblxubGV0IGNvbmZpZyA9IG5ldyBDb25maWcoKTtcbmxldCBjb25maWdWaWV3ID0gbmV3IENvbmZpZ1ZpZXcoe21vZGVsOiBjb25maWd9KTtcblxuY29uZmlnVmlldy5yZW5kZXIoKTtcbiIsImxldCBDb25maWcgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzZWxlY3RlZFNob3AnOiAnYW1hem9uJyxcbiAgICAgICAgJ25ld1Nob3BOYW1lJzogbnVsbCxcbiAgICAgICAgJ3NlbGVjdGVkQWN0aW9uJzogJ25ldy1wcm9kdWN0JyxcbiAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbnVsbCxcbiAgICAgICAgJ3JlcGxhY2VQcm9kdWN0SWQnOiBudWxsLFxuICAgICAgICAnc3RhdHVzJzogJ2RyYWZ0JyxcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImxldCBTZWFyY2hGb3JtID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAndGVybSc6ICcnLFxuICAgICAgICAndHlwZSc6ICdrZXl3b3JkcycsXG4gICAgICAgICdjYXRlZ29yeSc6ICdBbGwnLFxuICAgICAgICAnd2l0aFZhcmlhbnRzJzogJ25vJyxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ3Byb3ZpZGVyQ29uZmlndXJlZCc6IGZhbHNlXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgZm9ybSB0aGUgZm9ybSBhbmQgdHJpZ2dlciB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEZpbmlzaCB0aGUgc3VibWl0IGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZG9uZSgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCBmYWxzZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOmRvbmUnLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hMb2FkTW9yZSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2VuYWJsZWQnOiB0cnVlLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFjdGl2YXRlIHRoZSBsb2FkaW5nIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBsb2FkKCkge1xuICAgICAgICB0aGlzLnNldCgnbG9hZGluZycsIHRydWUpO1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bG9hZCcsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IHRoZSBsb2FkIG1vcmUgYnV0dG9uIGFuZCBkZWFjdGl2YXRlIHRoZSBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZG9uZSgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCBmYWxzZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpkb25lJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIG5vIHJlc3VsdHMgbWVzc2FnZSBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cygpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnIDogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOm5vLXJlc3VsdHMnLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdEl0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG4gICAgbW9kZWw6IFNlYXJjaFJlc3VsdEl0ZW0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3N0YXJ0ZWQnOiBmYWxzZSxcbiAgICAgICAgJ2FjdGlvbic6ICdhZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25fc2VhcmNoJyxcbiAgICAgICAgJ3BhZ2UnIDogMSxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHdpdGggdGhlIGdpdmVuIG9wdGlvbnMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqL1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSgpO1xuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cygpO1xuICAgICAgICB0aGlzLmxvYWRNb3JlID0gbmV3IFNlYXJjaExvYWRNb3JlKCk7XG4gICAgICAgIHRoaXMucGFnZSA9IG9wdGlvbnMgJiYgb3B0aW9ucy5wYWdlID8gb3B0aW9ucy5wYWdlIDogMTtcblxuICAgICAgICB0aGlzLmZvcm0ub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpzdWJtaXQnLCB0aGlzLnN0YXJ0LCB0aGlzKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZS5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcy5sb2FkLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3RhcnQgdGhlIHNlYXJjaCB3aXRoIHRoZSBmaXJzdCBwYWdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdGFydCgpIHtcbiAgICAgICAgaWYodGhpcy5mb3JtLmdldCgndGVybScpID09PSBudWxsKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNldCgncGFnZScsIDEpO1xuICAgICAgICB0aGlzLnJlc3VsdHMudXJsID0gdGhpcy5fYnVpbGRVcmwoKTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goKS5kb25lKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuc2V0KCdlbmFibGVkJywgdGhpcy5mb3JtLmdldCgndHlwZScpID09PSAna2V5d29yZHMnKTtcbiAgICAgICAgICAgIHRoaXMuc2V0KCdzdGFydGVkJywgdHJ1ZSk7XG4gICAgICAgICAgICB0aGlzLmZvcm0uZG9uZSgpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCBtb3JlIHNlYXJjaCByZXN1bHRzIGJ5IGluY3JlYXNpbmcgdGhlIHBhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdwYWdlJywgdGhpcy5nZXQoJ3BhZ2UnKSArIDEpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goeydyZW1vdmUnOiBmYWxzZX0pLmRvbmUoKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5kb25lKCk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgc2VhcmNoIEFQSSB1cmwgYmFzZWQgb24gdGhlIGdpdmVuIHBhcmFtZXRlcnMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge3N0cmluZ31cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9idWlsZFVybCgpIHtcbiAgICAgICAgcmV0dXJuIGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hamF4XG4gICAgICAgICAgICArIGA/YWN0aW9uPSR7dGhpcy5nZXQoJ2FjdGlvbicpfWBcbiAgICAgICAgICAgICsgYCZ0ZXJtPSR7dGhpcy5mb3JtLmdldCgndGVybScpfWBcbiAgICAgICAgICAgICsgYCZ0eXBlPSR7dGhpcy5mb3JtLmdldCgndHlwZScpfWBcbiAgICAgICAgICAgICsgYCZjYXRlZ29yeT0ke3RoaXMuZm9ybS5nZXQoJ2NhdGVnb3J5Jyl9YFxuICAgICAgICAgICAgKyBgJndpdGgtdmFyaWFudHM9JHt0aGlzLmZvcm0uZ2V0KCd3aXRoVmFyaWFudHMnKX1gXG4gICAgICAgICAgICArIGAmcGFnZT0ke3RoaXMuZ2V0KCdwYWdlJyl9YFxuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2g7XG4iLCJsZXQgQ29uZmlnID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1jb25maWcnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInNob3BcIl0nOiAnY2hhbmdlU2hvcCcsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cImFjdGlvblwiXSc6ICdjaGFuZ2VBY3Rpb24nLFxuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJzdGF0dXNcIl0nOiAnY2hhbmdlU3RhdHVzJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLXRlbXBsYXRlJykuaHRtbCgpO1xuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7Q29uZmlnfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGxldCBodG1sID0gdGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpO1xuICAgICAgICB0aGlzLiRlbC5odG1sKGh0bWwpO1xuXG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtYW1hem9uLWltcG9ydC1jb25maWctZ3JvdXAtb3B0aW9uLW1lcmdlLXByb2R1Y3QtaWQsIC5hZmYtYW1hem9uLWltcG9ydC1jb25maWctZ3JvdXAtb3B0aW9uLXJlcGxhY2UtcHJvZHVjdC1pZCcpLnNlbGVjdGl6ZSh7XG4gICAgICAgICAgICBtYXhJdGVtczogMSxcbiAgICAgICAgICAgIHZhbHVlRmllbGQ6ICdpZCcsXG4gICAgICAgICAgICBsYWJlbEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBzZWFyY2hGaWVsZDogJ25hbWUnLFxuICAgICAgICAgICAgY3JlYXRlOiBmYWxzZSxcbiAgICAgICAgICAgIGxvYWQ6IGZ1bmN0aW9uKHF1ZXJ5LCBjYWxsYmFjaykge1xuICAgICAgICAgICAgICAgIGlmICghcXVlcnkubGVuZ3RoKSByZXR1cm4gY2FsbGJhY2soKTtcbiAgICAgICAgICAgICAgICBqUXVlcnkuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogJy93cC1qc29uL3dwL3YyL2FmZi1wcm9kdWN0cy8/c2VhcmNoPScgKyBxdWVyeSxcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uKHJlc3VsdHMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdHMgPSByZXN1bHRzLm1hcCgocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2lkJzogcmVzdWx0LmlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnbmFtZSc6IHJlc3VsdC50aXRsZS5yZW5kZXJlZFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhyZXN1bHRzKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHNob3AgY29uZmlndXJhdGlvbiBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZVNob3AoKSB7XG4gICAgICAgIGxldCBzZWxlY3RlZFNob3AgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwic2hvcFwiXTpjaGVja2VkJyksXG4gICAgICAgICAgICBuZXdTaG9wTmFtZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJuZXctc2hvcC1uYW1lXCJdJyk7XG5cbiAgICAgICAgc2VsZWN0ZWRTaG9wLnZhbCgpID09PSAnbmV3LXNob3AnID8gbmV3U2hvcE5hbWUucmVtb3ZlQXR0cignZGlzYWJsZWQnKSA6IG5ld1Nob3BOYW1lLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3NlbGVjdGVkU2hvcCc6IHNlbGVjdGVkU2hvcC52YWwoKSxcbiAgICAgICAgICAgICduZXdTaG9wTmFtZSc6IG5ld1Nob3BOYW1lLnZhbCgpLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IGFjdGlvbiBjb25maWd1cmF0aW9uIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlQWN0aW9uKCkge1xuICAgICAgICBsZXQgc2VsZWN0ZWRBY3Rpb24gPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwiYWN0aW9uXCJdOmNoZWNrZWQnKSxcbiAgICAgICAgICAgIG1lcmdlUHJvZHVjdElkID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1lcmdlLXByb2R1Y3QtaWRcIl0nKSxcbiAgICAgICAgICAgIHJlcGxhY2VQcm9kdWN0SWQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwicmVwbGFjZS1wcm9kdWN0LWlkXCJdJyksXG4gICAgICAgICAgICBtZXJnZVNlbGVjdGl6ZSA9IG1lcmdlUHJvZHVjdElkLnNlbGVjdGl6ZSgpWzBdLnNlbGVjdGl6ZSxcbiAgICAgICAgICAgIHJlcGxhY2VTZWxlY3RpemUgPSByZXBsYWNlUHJvZHVjdElkLnNlbGVjdGl6ZSgpWzBdLnNlbGVjdGl6ZTtcblxuICAgICAgICBzZWxlY3RlZEFjdGlvbi52YWwoKSA9PT0gJ21lcmdlLXByb2R1Y3QnID8gbWVyZ2VTZWxlY3RpemUuZW5hYmxlKCkgOiBtZXJnZVNlbGVjdGl6ZS5kaXNhYmxlKCk7XG4gICAgICAgIHNlbGVjdGVkQWN0aW9uLnZhbCgpID09PSAncmVwbGFjZS1wcm9kdWN0JyA/IHJlcGxhY2VTZWxlY3RpemUuZW5hYmxlKCkgOiByZXBsYWNlU2VsZWN0aXplLmRpc2FibGUoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc2VsZWN0ZWRBY3Rpb24nOiBzZWxlY3RlZEFjdGlvbi52YWwoKSxcbiAgICAgICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IG1lcmdlUHJvZHVjdElkLnZhbCgpLFxuICAgICAgICAgICAgJ3JlcGxhY2VQcm9kdWN0SWQnOiByZXBsYWNlUHJvZHVjdElkLnZhbCgpXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc3RhdHVzIGNvbmZpZ3VyYXRpb24gaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2VTdGF0dXMoKSB7XG4gICAgICAgIGxldCBzZWxlY3RlZEFjdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzdGF0dXNcIl06Y2hlY2tlZCcpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzdGF0dXMnOiBzZWxlY3RlZEFjdGlvbi52YWwoKSxcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWc7XG4iLCJsZXQgU2VhcmNoRm9ybSA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWZvcm0nLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UnOiAnY2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdzdWJtaXQnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggZm9ybS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHByb3ZpZGVyQ29uZmlndXJlZCA9IHRoaXMuJGVsLmRhdGEoJ3Byb3ZpZGVyLWNvbmZpZ3VyZWQnKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCgncHJvdmlkZXJDb25maWd1cmVkJywgcHJvdmlkZXJDb25maWd1cmVkID09PSB0cnVlIHx8IHByb3ZpZGVyQ29uZmlndXJlZCA9PT0gJ3RydWUnKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtTZWFyY2hGb3JtfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGxldCBodG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWZvcm0tdGVtcGxhdGUnKS5odG1sKCksXG4gICAgICAgICAgICB0ZW1wbGF0ZSA9IF8udGVtcGxhdGUoaHRtbCk7XG5cbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3VibWl0IHRoZSBzZWFyY2ggZm9ybS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdWJtaXQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5jaGFuZ2UoKTtcbiAgICAgICAgdGhpcy5tb2RlbC5zdWJtaXQoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHNlYXJjaCBwYXJhbWV0ZXJzIGludG8gdGhlIG1vZGVsIG9uIGZvcm0gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2UoKSB7XG4gICAgICAgIGxldCB0ZXJtID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1cIl0nKSxcbiAgICAgICAgICAgIHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLFxuICAgICAgICAgICAgd2l0aFZhcmlhbnRzID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ3aXRoLXZhcmlhbnRzXCJdJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3Rlcm0nOiB0ZXJtLnZhbCgpLFxuICAgICAgICAgICAgJ3R5cGUnOiB0eXBlLnZhbCgpLFxuICAgICAgICAgICAgJ2NhdGVnb3J5JzogY2F0ZWdvcnkudmFsKCksXG4gICAgICAgICAgICAnd2l0aFZhcmlhbnRzJzogd2l0aFZhcmlhbnRzLnZhbCgpXG4gICAgICAgIH0pO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hMb2FkTW9yZSA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtbG9hZC1tb3JlJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2xpY2sgLmFmZi1hbWF6b24taW1wb3J0LWxvYWQtbW9yZS1idXR0b24nOiAnbG9hZCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCBsb2FkIG1vcmUuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggbG9hZCBtb3JlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm4ge1NlYXJjaExvYWRNb3JlfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGxldCBodG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtbG9hZC1tb3JlLXRlbXBsYXRlJykuaHRtbCgpLFxuICAgICAgICAgICAgdGVtcGxhdGUgPSBfLnRlbXBsYXRlKGh0bWwpO1xuXG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEVuYWJsZSB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMubW9kZWwubG9hZCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hMb2FkTW9yZTtcbiIsImxldCBTZWFyY2hSZXN1bHRzSXRlbSA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICB0YWdOYW1lOiAnZGl2JyxcblxuICAgIGNsYXNzTmFtZTogJycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NsaWNrIC5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJzogJ3Nob3dBbGwnXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm4ge1NlYXJjaFJlc3VsdHNJdGVtfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGxldCBodG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS10ZW1wbGF0ZScpLmh0bWwoKSxcbiAgICAgICAgICAgIHRlbXBsYXRlID0gXy50ZW1wbGF0ZShodG1sKTtcblxuICAgICAgICB0aGlzLnNldEVsZW1lbnQodGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYWxsIGhpZGRlbiB2YXJpYW50cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93QWxsKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJykuaGlkZSgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1pdGVtJykuc2hvdygpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0c0l0ZW07XG4iLCJpbXBvcnQgUHJvZHVjdFZpZXcgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uID0gb3B0aW9ucy5jb2xsZWN0aW9uO1xuXG4gICAgICAgIC8vIEJpbmQgdGhlIGNvbGxlY3Rpb24gZXZlbnRzXG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZXNldCcsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnYWRkJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZW1vdmUnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3N5bmMnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLl9hZGRBbGwoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIGFsbCBzZWFyY2ggcmVzdWx0cyBpdGVtcyB0byB0aGUgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRBbGwoKSB7XG4gICAgICAgIHRoaXMuJGVsLmVtcHR5KCk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5mb3JFYWNoKHRoaXMuX2FkZE9uZSwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBvbmUgc2VhcmNoIHJlc3VsdHMgaXRlbSB0byB0aGUgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRPbmUocHJvZHVjdCkge1xuICAgICAgICBsZXQgdmlldyA9IG5ldyBQcm9kdWN0Vmlldyh7XG4gICAgICAgICAgICBtb2RlbDogcHJvZHVjdCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy4kZWwuYXBwZW5kKHZpZXcucmVuZGVyKCkuZWwpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5mb3JtLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cyh7XG4gICAgICAgICAgICBjb2xsZWN0aW9uOiB0aGlzLm1vZGVsLnJlc3VsdHMsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMubG9hZE1vcmUgPSBuZXcgU2VhcmNoTG9hZE1vcmUoe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwubG9hZE1vcmUsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5mb3JtLnJlbmRlcigpO1xuICAgICAgICB0aGlzLnJlc3VsdHMucmVuZGVyKCk7XG5cbiAgICAgICAgaWYodGhpcy5tb2RlbC5nZXQoJ3N0YXJ0ZWQnKSkge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5yZW5kZXIoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoO1xuIl19
