(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _import = require('./model/import');

var _import2 = _interopRequireDefault(_import);

var _import3 = require('./view/import');

var _import4 = _interopRequireDefault(_import3);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var importModel = new _import2.default();
var importView = new _import4.default({ model: importModel });

importView.render();

},{"./model/import":3,"./view/import":10}],2:[function(require,module,exports){
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

var _search = require('./search');

var _search2 = _interopRequireDefault(_search);

var _config = require('./config');

var _config2 = _interopRequireDefault(_config);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Import = Backbone.Model.extend({
    defaults: {
        'action': 'aff_product_admin_amazon_import'
    },

    /**
     * Initialize the import.
     *
     * @since 0.9
     */
    initialize: function initialize() {
        this.search = new _search2.default();
        this.config = new _config2.default();

        this.search.on('aff:amazon-import:import-results-item', this.import, this);
    },


    /**
     * Import the product.
     *
     * @since 0.9
     * @param {Backbone.Model} product
     * @public
     */
    import: function _import(product) {
        var data = {
            'product': product.attributes,
            'config': this.config.attributes
        };

        jQuery.ajax({
            type: 'POST',
            url: this._buildUrl(),
            data: data,
            dataType: "application/json"
        }).done(function () {
            alert('Done');
        }).fail(function () {
            alert('Fail');
        });
    },


    /**
     * Build the import url based on the given parameters.
     *
     * @since 0.9
     * @returns {string}
     * @private
     */
    _buildUrl: function _buildUrl() {
        return affAdminAmazonImportUrls.ajax + ('?action=' + this.get('action'));
    }
});

exports.default = Import;

},{"./config":2,"./search":8}],4:[function(require,module,exports){
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

},{}],5:[function(require,module,exports){
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

},{}],6:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchResultsItem = Backbone.Model.extend({

    /**
     * Import the search result item.
     *
     * @since 0.9
     * @public
     */
    import: function _import() {
        this.trigger('aff:amazon-import:search:results:item:import', this);
    }
});

exports.default = SearchResultsItem;

},{}],7:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchResultsItem = require('./search-results-item');

var _searchResultsItem2 = _interopRequireDefault(_searchResultsItem);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var SearchResults = Backbone.Collection.extend({
    model: _searchResultsItem2.default,

    /**
     * Initialize the search results.
     *
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        this.on('sync', this.initImportListeners, this);
    },


    /**
     * Parse the Wordpress json Ajax response.
     *
     * @since 0.9
     * @param {Array} response
     * @returns {Array}
     * @public
     */
    parse: function parse(response) {
        return response && response.success ? response.data : [];
    },

    /**
     * Import the given item.
     *
     * @since 0.9
     * @param {Object} model
     * @public
     */
    importItem: function importItem(model) {
        this.trigger('aff:amazon-import:search:results:import-item', model);
    },


    /**
     * Init the import listeners for all results items.
     *
     * @since 0.9
     * @public
     */
    initImportListeners: function initImportListeners() {
        this.forEach(this._initImportListener, this);
    },


    /**
     * Init the import listeners for the result item.
     *
     * @since 0.9
     * @private
     */
    _initImportListener: function _initImportListener(model) {
        model.on('aff:amazon-import:search:results:item:import', this.importItem, this);
    }
});

exports.default = SearchResults;

},{"./search-results-item":6}],8:[function(require,module,exports){
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

        this.results.on('aff:amazon-import:search:results:import-item', this.import, this);
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

        this.results.fetch().done(function (results) {
            _this.loadMore.set('enabled', _this._isLoadMoreEnabled(results));
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

        this.results.fetch({ 'remove': false }).done(function (results) {
            _this2.loadMore.set('enabled', _this2._isLoadMoreEnabled(results));
            _this2.loadMore.done();
        });
    },


    /**
     * Import the given search results item.
     *
     * @since 0.9
     * @param {Object} model
     * @public
     */
    import: function _import(model) {
        this.trigger('aff:amazon-import:import-results-item', model);
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
    },


    /**
     * Check if the load more button is enabled (visible).
     *
     * @since 0.9
     * @param {Array|null} results
     * @returns {bool}
     * @private
     */
    _isLoadMoreEnabled: function _isLoadMoreEnabled(results) {
        return results && results.data && results.data.length > 0 && this.get('page') < 5 && this.form.get('type') === 'keywords';
    }
});

exports.default = Search;

},{"./search-form":4,"./search-load-more":5,"./search-results":7}],9:[function(require,module,exports){
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

},{}],10:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _search = require('./search');

var _search2 = _interopRequireDefault(_search);

var _config = require('./config');

var _config2 = _interopRequireDefault(_config);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Import = Backbone.View.extend({
    el: '.aff-amazon-import',

    /**
     * Initialize the import.
     *
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        this.search = new _search2.default({
            model: this.model.search
        });

        this.config = new _config2.default({
            model: this.model.config
        });
    },


    /**
     * Render the import.
     *
     * @since 0.9
     * @public
     */
    render: function render() {
        this.search.render();
        this.config.render();

        return this;
    }
});

exports.default = Import;

},{"./config":9,"./search":15}],11:[function(require,module,exports){
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

},{}],12:[function(require,module,exports){
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

},{}],13:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchResultsItem = Backbone.View.extend({
    tagName: 'div',

    className: '',

    events: {
        'click .aff-amazon-import-search-results-item-variants-show-all': 'showAll',
        'click .aff-amazon-import-search-results-item-actions-import': 'import'
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
    },


    /**
     * Import the search result item.
     *
     * @since 0.9
     * @param e
     * @public
     */
    import: function _import(e) {
        e.preventDefault();

        this.model.import();
    }
});

exports.default = SearchResultsItem;

},{}],14:[function(require,module,exports){
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

},{"./search-results-item":13}],15:[function(require,module,exports){
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

},{"./search-form":11,"./search-load-more":12,"./search-results":14}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvaW1wb3J0LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLXJlc3VsdHMtaXRlbS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2guanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9z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LENBQVcsRUFBWCxDQUFjLFFBQWQsRUFBd0IsS0FBSyxNQUE3QixFQUFxQyxJQUFyQztBQUNILEtBdkI2Qjs7O0FBeUI5Qjs7Ozs7O0FBTUEsVUEvQjhCLG9CQStCckI7QUFDTCxhQUFLLElBQUwsQ0FBVSxNQUFWO0FBQ0EsYUFBSyxPQUFMLENBQWEsTUFBYjs7QUFFQSxZQUFHLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxTQUFmLENBQUgsRUFBOEI7QUFDMUIsaUJBQUssUUFBTCxDQUFjLE1BQWQ7QUFDSDs7QUFFRCxlQUFPLElBQVA7QUFDSDtBQXhDNkIsQ0FBckIsQ0FBYjs7a0JBMkNlLE0iLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiaW1wb3J0IEltcG9ydCBmcm9tICcuL21vZGVsL2ltcG9ydCc7XG5pbXBvcnQgSW1wb3J0VmlldyBmcm9tICcuL3ZpZXcvaW1wb3J0JztcblxubGV0IGltcG9ydE1vZGVsID0gbmV3IEltcG9ydCgpO1xubGV0IGltcG9ydFZpZXcgPSBuZXcgSW1wb3J0Vmlldyh7bW9kZWw6IGltcG9ydE1vZGVsfSk7XG5cbmltcG9ydFZpZXcucmVuZGVyKCk7XG4iLCJsZXQgQ29uZmlnID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnc2VsZWN0ZWRTaG9wJzogJ2FtYXpvbicsXG4gICAgICAgICduZXdTaG9wTmFtZSc6IG51bGwsXG4gICAgICAgICdzZWxlY3RlZEFjdGlvbic6ICduZXctcHJvZHVjdCcsXG4gICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IG51bGwsXG4gICAgICAgICdyZXBsYWNlUHJvZHVjdElkJzogbnVsbCxcbiAgICAgICAgJ3N0YXR1cyc6ICdkcmFmdCcsXG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWc7XG4iLCJpbXBvcnQgU2VhcmNoIGZyb20gJy4vc2VhcmNoJztcbmltcG9ydCBDb25maWcgZnJvbSAnLi9jb25maWcnO1xuXG5sZXQgSW1wb3J0ID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnYWN0aW9uJzogJ2FmZl9wcm9kdWN0X2FkbWluX2FtYXpvbl9pbXBvcnQnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5zZWFyY2ggPSBuZXcgU2VhcmNoKCk7XG4gICAgICAgIHRoaXMuY29uZmlnID0gbmV3IENvbmZpZygpO1xuXG4gICAgICAgIHRoaXMuc2VhcmNoLm9uKCdhZmY6YW1hem9uLWltcG9ydDppbXBvcnQtcmVzdWx0cy1pdGVtJywgdGhpcy5pbXBvcnQsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIHByb2R1Y3QuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtCYWNrYm9uZS5Nb2RlbH0gcHJvZHVjdFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQocHJvZHVjdCkge1xuICAgICAgICBsZXQgZGF0YSA9IHtcbiAgICAgICAgICAgICdwcm9kdWN0JzogcHJvZHVjdC5hdHRyaWJ1dGVzLFxuICAgICAgICAgICAgJ2NvbmZpZyc6IHRoaXMuY29uZmlnLmF0dHJpYnV0ZXMsXG4gICAgICAgIH07XG5cbiAgICAgICAgalF1ZXJ5LmFqYXgoe1xuICAgICAgICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgICAgICAgdXJsOiB0aGlzLl9idWlsZFVybCgpLFxuICAgICAgICAgICAgZGF0YTogZGF0YSxcbiAgICAgICAgICAgIGRhdGFUeXBlOiBcImFwcGxpY2F0aW9uL2pzb25cIixcbiAgICAgICAgfSkuZG9uZShmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGFsZXJ0KCdEb25lJyk7XG4gICAgICAgIH0pLmZhaWwoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBhbGVydCgnRmFpbCcpO1xuICAgICAgICB9KVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgaW1wb3J0IHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICA7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBJbXBvcnQ7XG4iLCJsZXQgU2VhcmNoRm9ybSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3Rlcm0nOiAnJyxcbiAgICAgICAgJ3R5cGUnOiAna2V5d29yZHMnLFxuICAgICAgICAnY2F0ZWdvcnknOiAnQWxsJyxcbiAgICAgICAgJ3dpdGhWYXJpYW50cyc6ICdubycsXG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdwcm92aWRlckNvbmZpZ3VyZWQnOiBmYWxzZVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIGZvcm0gdGhlIGZvcm0gYW5kIHRyaWdnZXIgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdWJtaXQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgdHJ1ZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOnN1Ym1pdCcsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBGaW5pc2ggdGhlIHN1Ym1pdCBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgZmFsc2UpO1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpkb25lJywgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaEZvcm07XG4iLCJsZXQgU2VhcmNoTG9hZE1vcmUgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdlbmFibGVkJzogdHJ1ZSxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ25vUmVzdWx0cyc6IGZhbHNlLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBY3RpdmF0ZSB0aGUgbG9hZGluZyBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmxvYWQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyB0aGUgbG9hZCBtb3JlIGJ1dHRvbiBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgZmFsc2UpO1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZG9uZScsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IHRoZSBubyByZXN1bHRzIG1lc3NhZ2UgYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBub1Jlc3VsdHMoKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJyA6IGZhbHNlLFxuICAgICAgICAgICAgJ25vUmVzdWx0cyc6IHRydWUsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpuby1yZXN1bHRzJywgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaExvYWRNb3JlO1xuIiwibGV0IFNlYXJjaFJlc3VsdHNJdGVtID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgc2VhcmNoIHJlc3VsdCBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQoKSB7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTppbXBvcnQnLCB0aGlzKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdEl0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG4gICAgbW9kZWw6IFNlYXJjaFJlc3VsdEl0ZW0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5vbignc3luYycsIHRoaXMuaW5pdEltcG9ydExpc3RlbmVycywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFBhcnNlIHRoZSBXb3JkcHJlc3MganNvbiBBamF4IHJlc3BvbnNlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7QXJyYXl9IHJlc3BvbnNlXG4gICAgICogQHJldHVybnMge0FycmF5fVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBwYXJzZTogZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgcmV0dXJuIHJlc3BvbnNlICYmIHJlc3BvbnNlLnN1Y2Nlc3MgPyByZXNwb25zZS5kYXRhIDogW107XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgZ2l2ZW4gaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gbW9kZWxcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0SXRlbShtb2RlbCkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOmltcG9ydC1pdGVtJywgbW9kZWwpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0IHRoZSBpbXBvcnQgbGlzdGVuZXJzIGZvciBhbGwgcmVzdWx0cyBpdGVtcy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdEltcG9ydExpc3RlbmVycygpIHtcbiAgICAgICAgdGhpcy5mb3JFYWNoKHRoaXMuX2luaXRJbXBvcnRMaXN0ZW5lciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXQgdGhlIGltcG9ydCBsaXN0ZW5lcnMgZm9yIHRoZSByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9pbml0SW1wb3J0TGlzdGVuZXIobW9kZWwpIHtcbiAgICAgICAgbW9kZWwub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06aW1wb3J0JywgdGhpcy5pbXBvcnRJdGVtLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3N0YXJ0ZWQnOiBmYWxzZSxcbiAgICAgICAgJ2FjdGlvbic6ICdhZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25fc2VhcmNoJyxcbiAgICAgICAgJ3BhZ2UnIDogMSxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHdpdGggdGhlIGdpdmVuIG9wdGlvbnMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqL1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSgpO1xuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cygpO1xuICAgICAgICB0aGlzLmxvYWRNb3JlID0gbmV3IFNlYXJjaExvYWRNb3JlKCk7XG4gICAgICAgIHRoaXMucGFnZSA9IG9wdGlvbnMgJiYgb3B0aW9ucy5wYWdlID8gb3B0aW9ucy5wYWdlIDogMTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOmltcG9ydC1pdGVtJywgdGhpcy5pbXBvcnQsIHRoaXMpO1xuICAgICAgICB0aGlzLmZvcm0ub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpzdWJtaXQnLCB0aGlzLnN0YXJ0LCB0aGlzKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZS5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcy5sb2FkLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3RhcnQgdGhlIHNlYXJjaCB3aXRoIHRoZSBmaXJzdCBwYWdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdGFydCgpIHtcbiAgICAgICAgaWYodGhpcy5mb3JtLmdldCgndGVybScpID09PSBudWxsKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNldCgncGFnZScsIDEpO1xuICAgICAgICB0aGlzLnJlc3VsdHMudXJsID0gdGhpcy5fYnVpbGRVcmwoKTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goKS5kb25lKChyZXN1bHRzKSA9PiB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnNldCgnZW5hYmxlZCcsIHRoaXMuX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpKTtcbiAgICAgICAgICAgIHRoaXMuc2V0KCdzdGFydGVkJywgdHJ1ZSk7XG4gICAgICAgICAgICB0aGlzLmZvcm0uZG9uZSgpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCBtb3JlIHNlYXJjaCByZXN1bHRzIGJ5IGluY3JlYXNpbmcgdGhlIHBhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdwYWdlJywgdGhpcy5nZXQoJ3BhZ2UnKSArIDEpO1xuICAgICAgICB0aGlzLnJlc3VsdHMudXJsID0gdGhpcy5fYnVpbGRVcmwoKTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goeydyZW1vdmUnOiBmYWxzZX0pLmRvbmUoKHJlc3VsdHMpID0+IHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuc2V0KCdlbmFibGVkJywgdGhpcy5faXNMb2FkTW9yZUVuYWJsZWQocmVzdWx0cykpO1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5kb25lKCk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChtb2RlbCkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCBtb2RlbCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBzZWFyY2ggQVBJIHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnRlcm09JHt0aGlzLmZvcm0uZ2V0KCd0ZXJtJyl9YFxuICAgICAgICAgICAgKyBgJnR5cGU9JHt0aGlzLmZvcm0uZ2V0KCd0eXBlJyl9YFxuICAgICAgICAgICAgKyBgJmNhdGVnb3J5PSR7dGhpcy5mb3JtLmdldCgnY2F0ZWdvcnknKX1gXG4gICAgICAgICAgICArIGAmd2l0aC12YXJpYW50cz0ke3RoaXMuZm9ybS5nZXQoJ3dpdGhWYXJpYW50cycpfWBcbiAgICAgICAgICAgICsgYCZwYWdlPSR7dGhpcy5nZXQoJ3BhZ2UnKX1gXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIENoZWNrIGlmIHRoZSBsb2FkIG1vcmUgYnV0dG9uIGlzIGVuYWJsZWQgKHZpc2libGUpLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7QXJyYXl8bnVsbH0gcmVzdWx0c1xuICAgICAqIEByZXR1cm5zIHtib29sfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpIHtcbiAgICAgICAgcmV0dXJuIChyZXN1bHRzICYmIHJlc3VsdHMuZGF0YSAmJiByZXN1bHRzLmRhdGEubGVuZ3RoID4gMClcbiAgICAgICAgICAgICYmIHRoaXMuZ2V0KCdwYWdlJykgPCA1XG4gICAgICAgICAgICAmJiB0aGlzLmZvcm0uZ2V0KCd0eXBlJykgPT09ICdrZXl3b3Jkcyc7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiIsImxldCBDb25maWcgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LWNvbmZpZycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwic2hvcFwiXSc6ICdjaGFuZ2VTaG9wJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwiYWN0aW9uXCJdJzogJ2NoYW5nZUFjdGlvbicsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInN0YXR1c1wiXSc6ICdjaGFuZ2VTdGF0dXMnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctdGVtcGxhdGUnKS5odG1sKCk7XG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtDb25maWd9XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgbGV0IGh0bWwgPSB0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcyk7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwoaHRtbCk7XG5cbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1ncm91cC1vcHRpb24tbWVyZ2UtcHJvZHVjdC1pZCwgLmFmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1ncm91cC1vcHRpb24tcmVwbGFjZS1wcm9kdWN0LWlkJykuc2VsZWN0aXplKHtcbiAgICAgICAgICAgIG1heEl0ZW1zOiAxLFxuICAgICAgICAgICAgdmFsdWVGaWVsZDogJ2lkJyxcbiAgICAgICAgICAgIGxhYmVsRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIHNlYXJjaEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBjcmVhdGU6IGZhbHNlLFxuICAgICAgICAgICAgbG9hZDogZnVuY3Rpb24ocXVlcnksIGNhbGxiYWNrKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFxdWVyeS5sZW5ndGgpIHJldHVybiBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgIGpRdWVyeS5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiAnL3dwLWpzb24vd3AvdjIvYWZmLXByb2R1Y3RzLz9zZWFyY2g9JyArIHF1ZXJ5LFxuICAgICAgICAgICAgICAgICAgICB0eXBlOiAnR0VUJyxcbiAgICAgICAgICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2soKTtcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24ocmVzdWx0cykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0cyA9IHJlc3VsdHMubWFwKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnaWQnOiByZXN1bHQuaWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICduYW1lJzogcmVzdWx0LnRpdGxlLnJlbmRlcmVkXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKHJlc3VsdHMpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc2hvcCBjb25maWd1cmF0aW9uIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlU2hvcCgpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkU2hvcCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzaG9wXCJdOmNoZWNrZWQnKSxcbiAgICAgICAgICAgIG5ld1Nob3BOYW1lID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm5ldy1zaG9wLW5hbWVcIl0nKTtcblxuICAgICAgICBzZWxlY3RlZFNob3AudmFsKCkgPT09ICduZXctc2hvcCcgPyBuZXdTaG9wTmFtZS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpIDogbmV3U2hvcE5hbWUuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc2VsZWN0ZWRTaG9wJzogc2VsZWN0ZWRTaG9wLnZhbCgpLFxuICAgICAgICAgICAgJ25ld1Nob3BOYW1lJzogbmV3U2hvcE5hbWUudmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgYWN0aW9uIGNvbmZpZ3VyYXRpb24gaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2VBY3Rpb24oKSB7XG4gICAgICAgIGxldCBzZWxlY3RlZEFjdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJhY3Rpb25cIl06Y2hlY2tlZCcpLFxuICAgICAgICAgICAgbWVyZ2VQcm9kdWN0SWQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWVyZ2UtcHJvZHVjdC1pZFwiXScpLFxuICAgICAgICAgICAgcmVwbGFjZVByb2R1Y3RJZCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJyZXBsYWNlLXByb2R1Y3QtaWRcIl0nKSxcbiAgICAgICAgICAgIG1lcmdlU2VsZWN0aXplID0gbWVyZ2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplLFxuICAgICAgICAgICAgcmVwbGFjZVNlbGVjdGl6ZSA9IHJlcGxhY2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplO1xuXG4gICAgICAgIHNlbGVjdGVkQWN0aW9uLnZhbCgpID09PSAnbWVyZ2UtcHJvZHVjdCcgPyBtZXJnZVNlbGVjdGl6ZS5lbmFibGUoKSA6IG1lcmdlU2VsZWN0aXplLmRpc2FibGUoKTtcbiAgICAgICAgc2VsZWN0ZWRBY3Rpb24udmFsKCkgPT09ICdyZXBsYWNlLXByb2R1Y3QnID8gcmVwbGFjZVNlbGVjdGl6ZS5lbmFibGUoKSA6IHJlcGxhY2VTZWxlY3RpemUuZGlzYWJsZSgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzZWxlY3RlZEFjdGlvbic6IHNlbGVjdGVkQWN0aW9uLnZhbCgpLFxuICAgICAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbWVyZ2VQcm9kdWN0SWQudmFsKCksXG4gICAgICAgICAgICAncmVwbGFjZVByb2R1Y3RJZCc6IHJlcGxhY2VQcm9kdWN0SWQudmFsKClcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzdGF0dXMgY29uZmlndXJhdGlvbiBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZVN0YXR1cygpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkQWN0aW9uID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInN0YXR1c1wiXTpjaGVja2VkJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3N0YXR1cyc6IHNlbGVjdGVkQWN0aW9uLnZhbCgpLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9zZWFyY2gnO1xuaW1wb3J0IENvbmZpZyBmcm9tICcuL2NvbmZpZyc7XG5cbmxldCBJbXBvcnQgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNlYXJjaCA9IG5ldyBTZWFyY2goe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuc2VhcmNoLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmNvbmZpZyA9IG5ldyBDb25maWcoe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuY29uZmlnLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zZWFyY2gucmVuZGVyKCk7XG4gICAgICAgIHRoaXMuY29uZmlnLnJlbmRlcigpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgSW1wb3J0O1xuIiwibGV0IFNlYXJjaEZvcm0gPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlJzogJ2NoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnc3VibWl0JyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybS10ZW1wbGF0ZScpLmh0bWwoKSxcbiAgICAgICAgICAgIHByb3ZpZGVyQ29uZmlndXJlZCA9IHRoaXMuJGVsLmRhdGEoJ3Byb3ZpZGVyLWNvbmZpZ3VyZWQnKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdwcm92aWRlckNvbmZpZ3VyZWQnLCBwcm92aWRlckNvbmZpZ3VyZWQgPT09IHRydWUgfHwgcHJvdmlkZXJDb25maWd1cmVkID09PSAndHJ1ZScpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge1NlYXJjaEZvcm19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLmNoYW5nZSgpO1xuICAgICAgICB0aGlzLm1vZGVsLnN1Ym1pdCgpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc2VhcmNoIHBhcmFtZXRlcnMgaW50byB0aGUgbW9kZWwgb24gZm9ybSBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZSgpIHtcbiAgICAgICAgbGV0IHRlcm0gPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwidGVybVwiXScpLFxuICAgICAgICAgICAgdHlwZSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwidHlwZVwiXScpLFxuICAgICAgICAgICAgY2F0ZWdvcnkgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNhdGVnb3J5XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAndGVybSc6IHRlcm0udmFsKCksXG4gICAgICAgICAgICAndHlwZSc6IHR5cGUudmFsKCksXG4gICAgICAgICAgICAnY2F0ZWdvcnknOiBjYXRlZ29yeS52YWwoKSxcbiAgICAgICAgICAgICd3aXRoVmFyaWFudHMnOiB3aXRoVmFyaWFudHMudmFsKClcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1sb2FkLW1vcmUnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWFtYXpvbi1pbXBvcnQtbG9hZC1tb3JlLWJ1dHRvbic6ICdsb2FkJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWxvYWQtbW9yZS10ZW1wbGF0ZScpLmh0bWwoKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJuIHtTZWFyY2hMb2FkTW9yZX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEVuYWJsZSB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMubW9kZWwubG9hZCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hMb2FkTW9yZTtcbiIsImxldCBTZWFyY2hSZXN1bHRzSXRlbSA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICB0YWdOYW1lOiAnZGl2JyxcblxuICAgIGNsYXNzTmFtZTogJycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NsaWNrIC5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJzogJ3Nob3dBbGwnLFxuICAgICAgICAnY2xpY2sgLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tYWN0aW9ucy1pbXBvcnQnOiAnaW1wb3J0J1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS10ZW1wbGF0ZScpLmh0bWwoKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIHJlc3VsdHMgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJuIHtTZWFyY2hSZXN1bHRzSXRlbX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLnNldEVsZW1lbnQodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhbGwgaGlkZGVuIHZhcmlhbnRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dBbGwoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtc2hvdy1hbGwnKS5oaWRlKCk7XG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLWl0ZW0nKS5zaG93KCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgc2VhcmNoIHJlc3VsdCBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLmltcG9ydCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRzSXRlbSBmcm9tICcuL3NlYXJjaC1yZXN1bHRzLWl0ZW0nO1xuXG5sZXQgU2VhcmNoUmVzdWx0cyA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cycsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge2FycmF5fSBvcHRpb25zXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24gPSBvcHRpb25zLmNvbGxlY3Rpb247XG5cbiAgICAgICAgLy8gQmluZCB0aGUgY29sbGVjdGlvbiBldmVudHNcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3Jlc2V0JywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdhZGQnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3JlbW92ZScsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnc3luYycsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuX2FkZEFsbCgpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgYWxsIHNlYXJjaCByZXN1bHRzIGl0ZW1zIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2FkZEFsbCgpIHtcbiAgICAgICAgdGhpcy4kZWwuZW1wdHkoKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmZvckVhY2godGhpcy5fYWRkT25lLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIG9uZSBzZWFyY2ggcmVzdWx0cyBpdGVtIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2FkZE9uZShwcm9kdWN0KSB7XG4gICAgICAgIGxldCB2aWV3ID0gbmV3IFNlYXJjaFJlc3VsdHNJdGVtKHtcbiAgICAgICAgICAgIG1vZGVsOiBwcm9kdWN0LFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLiRlbC5hcHBlbmQodmlldy5yZW5kZXIoKS5lbCk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzO1xuIiwiaW1wb3J0IFNlYXJjaEZvcm0gZnJvbSAnLi9zZWFyY2gtZm9ybSc7XG5pbXBvcnQgU2VhcmNoTG9hZE1vcmUgZnJvbSAnLi9zZWFyY2gtbG9hZC1tb3JlJztcbmltcG9ydCBTZWFyY2hSZXN1bHRzIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMnO1xuXG5sZXQgU2VhcmNoID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaCcsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuZm9ybSA9IG5ldyBTZWFyY2hGb3JtKHtcbiAgICAgICAgICAgIG1vZGVsOiB0aGlzLm1vZGVsLmZvcm0sXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cyA9IG5ldyBTZWFyY2hSZXN1bHRzKHtcbiAgICAgICAgICAgIGNvbGxlY3Rpb246IHRoaXMubW9kZWwucmVzdWx0cyxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5sb2FkTW9yZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLmZvcm0ucmVuZGVyKCk7XG4gICAgICAgIHRoaXMucmVzdWx0cy5yZW5kZXIoKTtcblxuICAgICAgICBpZih0aGlzLm1vZGVsLmdldCgnc3RhcnRlZCcpKSB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnJlbmRlcigpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2g7XG4iXX0=
