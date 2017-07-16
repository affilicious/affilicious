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
        var templateHtml = jQuery('#aff-amazon-import-search-form-template').html(),
            providerConfigured = this.$el.data('provider-configured');

        this.template = _.template(templateHtml);

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
        this.$el.html(this.template(this.model.attributes));

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
        var templateHtml = jQuery('#aff-amazon-import-load-more-template').html();

        this.template = _.template(templateHtml);
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
        this.$el.html(this.template(this.model.attributes));

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
        var templateHtml = jQuery('#aff-amazon-import-search-results-item-template').html();

        this.template = _.template(templateHtml);
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
        this.setElement(this.template(this.model.attributes));

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWZvcm0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWxvYWQtbW9yZS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1yZXN1bHRzLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L2NvbmZpZy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLWxvYWQtbW9yZS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1yZXN1bHRzLWl0ZW0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7O0FDQUE7Ozs7QUFDQTs7OztBQU9BOzs7O0FBQ0E7Ozs7OztBQU5BLElBQUksU0FBUyxzQkFBYjtBQUNBLElB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LE1BQVQsQ0FBZ0IsS0FBSyxNQUFMLEdBQWMsRUFBOUI7QUFDSDtBQXJEb0MsQ0FBckIsQ0FBcEI7O2tCQXdEZSxhOzs7Ozs7Ozs7QUMxRGY7Ozs7QUFDQTs7OztBQUNBOzs7Ozs7QUFFQSxJQUFJLFNBQVMsU0FBUyxJQUFULENBQWMsTUFBZCxDQUFxQjtBQUM5QixRQUFJLDJCQUQwQjs7QUFHOUI7Ozs7OztBQU1BLGNBVDhCLHdCQVNqQjtBQUNULGFBQUssSUFBTCxHQUFZLHlCQUFlO0FBQ3ZCLG1CQUFPLEtBQUssS0FBTCxDQUFXO0FBREssU0FBZixDQUFaOztBQUlBLGFBQUssT0FBTCxHQUFlLDRCQUFrQjtBQUM3Qix3QkFBWSxLQUFLLEtBQUwsQ0FBVztBQURNLFNBQWxCLENBQWY7O0FBSUEsYUFBSyxRQUFMLEdBQWdCLDZCQUFtQjtBQUMvQixtQkFBTyxLQUFLLEtBQUwsQ0FBVztBQURhLFNBQW5CLENBQWhCOztBQUlBLGFBQUssS0FBTCxDQUFXLEVBQVgsQ0FBYyxRQUFkLEVBQXdCLEtBQUssTUFBN0IsRUFBcUMsSUFBckM7QUFDSCxLQXZCNkI7OztBQXlCOUI7Ozs7OztBQU1BLFVBL0I4QixvQkErQnJCO0FBQ0wsYUFBSyxJQUFMLENBQVUsTUFBVjtBQUNBLGFBQUssT0FBTCxDQUFhLE1BQWI7O0FBRUEsWUFBRyxLQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWUsU0FBZixDQUFILEVBQThCO0FBQzFCLGlCQUFLLFFBQUwsQ0FBYyxNQUFkO0FBQ0g7O0FBRUQsZUFBTyxJQUFQO0FBQ0g7QUF4QzZCLENBQXJCLENBQWI7O2tCQTJDZSxNIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9tb2RlbC9zZWFyY2gnO1xuaW1wb3J0IFNlYXJjaFZpZXcgZnJvbSAnLi92aWV3L3NlYXJjaCc7XG5cbmxldCBzZWFyY2ggPSBuZXcgU2VhcmNoKCk7XG5sZXQgc2VhcmNoVmlldyA9IG5ldyBTZWFyY2hWaWV3KHttb2RlbDogc2VhcmNofSk7XG5cbnNlYXJjaFZpZXcucmVuZGVyKCk7XG5cbmltcG9ydCBDb25maWcgZnJvbSAnLi9tb2RlbC9jb25maWcnO1xuaW1wb3J0IENvbmZpZ1ZpZXcgZnJvbSAnLi92aWV3L2NvbmZpZyc7XG5cbmxldCBjb25maWcgPSBuZXcgQ29uZmlnKCk7XG5sZXQgY29uZmlnVmlldyA9IG5ldyBDb25maWdWaWV3KHttb2RlbDogY29uZmlnfSk7XG5cbmNvbmZpZ1ZpZXcucmVuZGVyKCk7XG4iLCJsZXQgQ29uZmlnID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnc2VsZWN0ZWRTaG9wJzogJ2FtYXpvbicsXG4gICAgICAgICduZXdTaG9wTmFtZSc6IG51bGwsXG4gICAgICAgICdzZWxlY3RlZEFjdGlvbic6ICduZXctcHJvZHVjdCcsXG4gICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IG51bGwsXG4gICAgICAgICdyZXBsYWNlUHJvZHVjdElkJzogbnVsbCxcbiAgICAgICAgJ3N0YXR1cyc6ICdkcmFmdCcsXG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWc7XG4iLCJsZXQgU2VhcmNoRm9ybSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3Rlcm0nOiAnJyxcbiAgICAgICAgJ3R5cGUnOiAna2V5d29yZHMnLFxuICAgICAgICAnY2F0ZWdvcnknOiAnQWxsJyxcbiAgICAgICAgJ3dpdGhWYXJpYW50cyc6ICdubycsXG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdwcm92aWRlckNvbmZpZ3VyZWQnOiBmYWxzZVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIGZvcm0gdGhlIGZvcm0gYW5kIHRyaWdnZXIgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdWJtaXQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgdHJ1ZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOnN1Ym1pdCcsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBGaW5pc2ggdGhlIHN1Ym1pdCBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgZmFsc2UpO1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpkb25lJywgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaEZvcm07XG4iLCJsZXQgU2VhcmNoTG9hZE1vcmUgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdlbmFibGVkJzogdHJ1ZSxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ25vUmVzdWx0cyc6IGZhbHNlLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBY3RpdmF0ZSB0aGUgbG9hZGluZyBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmxvYWQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyB0aGUgbG9hZCBtb3JlIGJ1dHRvbiBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgZmFsc2UpO1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZG9uZScsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IHRoZSBubyByZXN1bHRzIG1lc3NhZ2UgYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBub1Jlc3VsdHMoKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJyA6IGZhbHNlLFxuICAgICAgICAgICAgJ25vUmVzdWx0cyc6IHRydWUsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpuby1yZXN1bHRzJywgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaExvYWRNb3JlO1xuIiwibGV0IFNlYXJjaFJlc3VsdHNJdGVtID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRJdGVtIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuQ29sbGVjdGlvbi5leHRlbmQoe1xuICAgIG1vZGVsOiBTZWFyY2hSZXN1bHRJdGVtLFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hMb2FkTW9yZSBmcm9tICcuL3NlYXJjaC1sb2FkLW1vcmUnO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzdGFydGVkJzogZmFsc2UsXG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX3NlYXJjaCcsXG4gICAgICAgICdwYWdlJyA6IDEsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCB3aXRoIHRoZSBnaXZlbiBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IG9wdGlvbnNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5mb3JtID0gbmV3IFNlYXJjaEZvcm0oKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzID0gbmV3IFNlYXJjaFJlc3VsdHMoKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSgpO1xuICAgICAgICB0aGlzLnBhZ2UgPSBvcHRpb25zICYmIG9wdGlvbnMucGFnZSA/IG9wdGlvbnMucGFnZSA6IDE7XG5cbiAgICAgICAgdGhpcy5mb3JtLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcy5zdGFydCwgdGhpcyk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bG9hZCcsIHRoaXMubG9hZCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN0YXJ0IHRoZSBzZWFyY2ggd2l0aCB0aGUgZmlyc3QgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3RhcnQoKSB7XG4gICAgICAgIGlmKHRoaXMuZm9ybS5nZXQoJ3Rlcm0nKSA9PT0gbnVsbCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCAxKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnVybCA9IHRoaXMuX2J1aWxkVXJsKCk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLmZldGNoKCkuZG9uZSgoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnNldCgnZW5hYmxlZCcsIHRoaXMuZm9ybS5nZXQoJ3R5cGUnKSA9PT0gJ2tleXdvcmRzJyk7XG4gICAgICAgICAgICB0aGlzLnNldCgnc3RhcnRlZCcsIHRydWUpO1xuICAgICAgICAgICAgdGhpcy5mb3JtLmRvbmUoKTtcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgbW9yZSBzZWFyY2ggcmVzdWx0cyBieSBpbmNyZWFzaW5nIHRoZSBwYWdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBsb2FkKCkge1xuICAgICAgICB0aGlzLnNldCgncGFnZScsIHRoaXMuZ2V0KCdwYWdlJykgKyAxKTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMudXJsID0gdGhpcy5fYnVpbGRVcmwoKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLmZldGNoKHsncmVtb3ZlJzogZmFsc2V9KS5kb25lKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuZG9uZSgpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQnVpbGQgdGhlIHNlYXJjaCBBUEkgdXJsIGJhc2VkIG9uIHRoZSBnaXZlbiBwYXJhbWV0ZXJzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYnVpbGRVcmwoKSB7XG4gICAgICAgIHJldHVybiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYWpheFxuICAgICAgICAgICAgKyBgP2FjdGlvbj0ke3RoaXMuZ2V0KCdhY3Rpb24nKX1gXG4gICAgICAgICAgICArIGAmdGVybT0ke3RoaXMuZm9ybS5nZXQoJ3Rlcm0nKX1gXG4gICAgICAgICAgICArIGAmdHlwZT0ke3RoaXMuZm9ybS5nZXQoJ3R5cGUnKX1gXG4gICAgICAgICAgICArIGAmY2F0ZWdvcnk9JHt0aGlzLmZvcm0uZ2V0KCdjYXRlZ29yeScpfWBcbiAgICAgICAgICAgICsgYCZ3aXRoLXZhcmlhbnRzPSR7dGhpcy5mb3JtLmdldCgnd2l0aFZhcmlhbnRzJyl9YFxuICAgICAgICAgICAgKyBgJnBhZ2U9JHt0aGlzLmdldCgncGFnZScpfWBcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoO1xuIiwibGV0IENvbmZpZyA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJzaG9wXCJdJzogJ2NoYW5nZVNob3AnLFxuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJhY3Rpb25cIl0nOiAnY2hhbmdlQWN0aW9uJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwic3RhdHVzXCJdJzogJ2NoYW5nZVN0YXR1cycsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy10ZW1wbGF0ZScpLmh0bWwoKTtcbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge0NvbmZpZ31cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBsZXQgaHRtbCA9IHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKTtcbiAgICAgICAgdGhpcy4kZWwuaHRtbChodG1sKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLWdyb3VwLW9wdGlvbi1tZXJnZS1wcm9kdWN0LWlkLCAuYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLWdyb3VwLW9wdGlvbi1yZXBsYWNlLXByb2R1Y3QtaWQnKS5zZWxlY3RpemUoe1xuICAgICAgICAgICAgbWF4SXRlbXM6IDEsXG4gICAgICAgICAgICB2YWx1ZUZpZWxkOiAnaWQnLFxuICAgICAgICAgICAgbGFiZWxGaWVsZDogJ25hbWUnLFxuICAgICAgICAgICAgc2VhcmNoRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIGNyZWF0ZTogZmFsc2UsXG4gICAgICAgICAgICBsb2FkOiBmdW5jdGlvbihxdWVyeSwgY2FsbGJhY2spIHtcbiAgICAgICAgICAgICAgICBpZiAoIXF1ZXJ5Lmxlbmd0aCkgcmV0dXJuIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgalF1ZXJ5LmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6ICcvd3AtanNvbi93cC92Mi9hZmYtcHJvZHVjdHMvP3NlYXJjaD0nICsgcXVlcnksXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgICAgICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihyZXN1bHRzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHRzID0gcmVzdWx0cy5tYXAoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdpZCc6IHJlc3VsdC5pZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ25hbWUnOiByZXN1bHQudGl0bGUucmVuZGVyZWRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2socmVzdWx0cyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzaG9wIGNvbmZpZ3VyYXRpb24gaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2VTaG9wKCkge1xuICAgICAgICBsZXQgc2VsZWN0ZWRTaG9wID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInNob3BcIl06Y2hlY2tlZCcpLFxuICAgICAgICAgICAgbmV3U2hvcE5hbWUgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibmV3LXNob3AtbmFtZVwiXScpO1xuXG4gICAgICAgIHNlbGVjdGVkU2hvcC52YWwoKSA9PT0gJ25ldy1zaG9wJyA/IG5ld1Nob3BOYW1lLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJykgOiBuZXdTaG9wTmFtZS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzZWxlY3RlZFNob3AnOiBzZWxlY3RlZFNob3AudmFsKCksXG4gICAgICAgICAgICAnbmV3U2hvcE5hbWUnOiBuZXdTaG9wTmFtZS52YWwoKSxcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBhY3Rpb24gY29uZmlndXJhdGlvbiBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZUFjdGlvbigpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkQWN0aW9uID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cImFjdGlvblwiXTpjaGVja2VkJyksXG4gICAgICAgICAgICBtZXJnZVByb2R1Y3RJZCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtZXJnZS1wcm9kdWN0LWlkXCJdJyksXG4gICAgICAgICAgICByZXBsYWNlUHJvZHVjdElkID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInJlcGxhY2UtcHJvZHVjdC1pZFwiXScpLFxuICAgICAgICAgICAgbWVyZ2VTZWxlY3RpemUgPSBtZXJnZVByb2R1Y3RJZC5zZWxlY3RpemUoKVswXS5zZWxlY3RpemUsXG4gICAgICAgICAgICByZXBsYWNlU2VsZWN0aXplID0gcmVwbGFjZVByb2R1Y3RJZC5zZWxlY3RpemUoKVswXS5zZWxlY3RpemU7XG5cbiAgICAgICAgc2VsZWN0ZWRBY3Rpb24udmFsKCkgPT09ICdtZXJnZS1wcm9kdWN0JyA/IG1lcmdlU2VsZWN0aXplLmVuYWJsZSgpIDogbWVyZ2VTZWxlY3RpemUuZGlzYWJsZSgpO1xuICAgICAgICBzZWxlY3RlZEFjdGlvbi52YWwoKSA9PT0gJ3JlcGxhY2UtcHJvZHVjdCcgPyByZXBsYWNlU2VsZWN0aXplLmVuYWJsZSgpIDogcmVwbGFjZVNlbGVjdGl6ZS5kaXNhYmxlKCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3NlbGVjdGVkQWN0aW9uJzogc2VsZWN0ZWRBY3Rpb24udmFsKCksXG4gICAgICAgICAgICAnbWVyZ2VQcm9kdWN0SWQnOiBtZXJnZVByb2R1Y3RJZC52YWwoKSxcbiAgICAgICAgICAgICdyZXBsYWNlUHJvZHVjdElkJzogcmVwbGFjZVByb2R1Y3RJZC52YWwoKVxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHN0YXR1cyBjb25maWd1cmF0aW9uIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlU3RhdHVzKCkge1xuICAgICAgICBsZXQgc2VsZWN0ZWRBY3Rpb24gPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwic3RhdHVzXCJdOmNoZWNrZWQnKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc3RhdHVzJzogc2VsZWN0ZWRBY3Rpb24udmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnO1xuIiwibGV0IFNlYXJjaEZvcm0gPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlJzogJ2NoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnc3VibWl0JyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybS10ZW1wbGF0ZScpLmh0bWwoKSxcbiAgICAgICAgICAgIHByb3ZpZGVyQ29uZmlndXJlZCA9IHRoaXMuJGVsLmRhdGEoJ3Byb3ZpZGVyLWNvbmZpZ3VyZWQnKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdwcm92aWRlckNvbmZpZ3VyZWQnLCBwcm92aWRlckNvbmZpZ3VyZWQgPT09IHRydWUgfHwgcHJvdmlkZXJDb25maWd1cmVkID09PSAndHJ1ZScpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge1NlYXJjaEZvcm19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLmNoYW5nZSgpO1xuICAgICAgICB0aGlzLm1vZGVsLnN1Ym1pdCgpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc2VhcmNoIHBhcmFtZXRlcnMgaW50byB0aGUgbW9kZWwgb24gZm9ybSBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZSgpIHtcbiAgICAgICAgbGV0IHRlcm0gPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwidGVybVwiXScpLFxuICAgICAgICAgICAgdHlwZSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwidHlwZVwiXScpLFxuICAgICAgICAgICAgY2F0ZWdvcnkgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNhdGVnb3J5XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAndGVybSc6IHRlcm0udmFsKCksXG4gICAgICAgICAgICAndHlwZSc6IHR5cGUudmFsKCksXG4gICAgICAgICAgICAnY2F0ZWdvcnknOiBjYXRlZ29yeS52YWwoKSxcbiAgICAgICAgICAgICd3aXRoVmFyaWFudHMnOiB3aXRoVmFyaWFudHMudmFsKClcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1sb2FkLW1vcmUnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWFtYXpvbi1pbXBvcnQtbG9hZC1tb3JlLWJ1dHRvbic6ICdsb2FkJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWxvYWQtbW9yZS10ZW1wbGF0ZScpLmh0bWwoKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJuIHtTZWFyY2hMb2FkTW9yZX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEVuYWJsZSB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMubW9kZWwubG9hZCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hMb2FkTW9yZTtcbiIsImxldCBTZWFyY2hSZXN1bHRzSXRlbSA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICB0YWdOYW1lOiAnZGl2JyxcblxuICAgIGNsYXNzTmFtZTogJycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NsaWNrIC5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJzogJ3Nob3dBbGwnXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXRlbXBsYXRlJykuaHRtbCgpO1xuXG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm4ge1NlYXJjaFJlc3VsdHNJdGVtfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuc2V0RWxlbWVudCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IGFsbCBoaWRkZW4gdmFyaWFudHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc2hvd0FsbChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCcpLmhpZGUoKTtcbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtaXRlbScpLnNob3coKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdHNJdGVtIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzJyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IG9wdGlvbnNcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbiA9IG9wdGlvbnMuY29sbGVjdGlvbjtcblxuICAgICAgICAvLyBCaW5kIHRoZSBjb2xsZWN0aW9uIGV2ZW50c1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgncmVzZXQnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ2FkZCcsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgncmVtb3ZlJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdzeW5jJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5fYWRkQWxsKCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBhbGwgc2VhcmNoIHJlc3VsdHMgaXRlbXMgdG8gdGhlIHZpZXcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYWRkQWxsKCkge1xuICAgICAgICB0aGlzLiRlbC5lbXB0eSgpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uZm9yRWFjaCh0aGlzLl9hZGRPbmUsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgb25lIHNlYXJjaCByZXN1bHRzIGl0ZW0gdG8gdGhlIHZpZXcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYWRkT25lKHByb2R1Y3QpIHtcbiAgICAgICAgbGV0IHZpZXcgPSBuZXcgU2VhcmNoUmVzdWx0c0l0ZW0oe1xuICAgICAgICAgICAgbW9kZWw6IHByb2R1Y3QsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuJGVsLmFwcGVuZCh2aWV3LnJlbmRlcigpLmVsKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hMb2FkTW9yZSBmcm9tICcuL3NlYXJjaC1sb2FkLW1vcmUnO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoJyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5mb3JtID0gbmV3IFNlYXJjaEZvcm0oe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuZm9ybSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzID0gbmV3IFNlYXJjaFJlc3VsdHMoe1xuICAgICAgICAgICAgY29sbGVjdGlvbjogdGhpcy5tb2RlbC5yZXN1bHRzLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmxvYWRNb3JlID0gbmV3IFNlYXJjaExvYWRNb3JlKHtcbiAgICAgICAgICAgIG1vZGVsOiB0aGlzLm1vZGVsLmxvYWRNb3JlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuZm9ybS5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnJlbmRlcigpO1xuXG4gICAgICAgIGlmKHRoaXMubW9kZWwuZ2V0KCdzdGFydGVkJykpIHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUucmVuZGVyKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiJdfQ==
