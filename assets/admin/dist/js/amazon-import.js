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

},{"./model/import":7,"./view/import":18}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var ConfigAction = Backbone.Model.extend({
    defaults: {
        'action': 'new-product',
        'mergeProductId': null
    }
});

exports.default = ConfigAction;

},{}],3:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var ConfigShop = Backbone.Model.extend({
    defaults: {
        'shop': null,
        'newShopName': null,
        'addedShops': []
    },

    /**
     * Add a new shop to the config.
     *
     * @since 0.9.16
     * @public
     * @param {Object} shop
     */
    addShop: function addShop(shop) {
        var addedShops = this.get('addedShops');

        addedShops.push(shop);

        this.set({
            'shop': shop.slug,
            'newShopName': null,
            'addedShops': addedShops
        });
    }
});

exports.default = ConfigShop;

},{}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var ConfigStatus = Backbone.Model.extend({
    defaults: {
        'status': 'publish'
    }
});

exports.default = ConfigStatus;

},{}],5:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var ConfigTaxonomy = Backbone.Model.extend({
    defaults: {
        'taxonomy': null,
        'terms': null
    }
});

exports.default = ConfigTaxonomy;

},{}],6:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _configShop = require("./config-shop");

var _configShop2 = _interopRequireDefault(_configShop);

var _configStatus = require("./config-status");

var _configStatus2 = _interopRequireDefault(_configStatus);

var _configTaxonomy = require("./config-taxonomy");

var _configTaxonomy2 = _interopRequireDefault(_configTaxonomy);

var _configAction = require("./config-action");

var _configAction2 = _interopRequireDefault(_configAction);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Config = Backbone.Model.extend({

    /**
     * Initialize the config with all sub configs.
     *
     * @since 0.9.16
     * @public
     */
    initialize: function initialize() {
        this.shop = new _configShop2.default();
        this.status = new _configStatus2.default();
        this.taxonomy = new _configTaxonomy2.default();
        this.action = new _configAction2.default();
    },


    /**
     * Parse the config into an object.
     *
     * @since 0.9.16
     * @public
     * @returns {{shop, newShopName, status, taxonomy, term, action, mergeProductId}}
     */
    parse: function parse() {
        return {
            'shop': this.shop.get('shop'),
            'newShopName': this.shop.get('newShopName'),
            'status': this.status.get('status'),
            'taxonomy': this.taxonomy.get('taxonomy'),
            'terms': this.taxonomy.get('terms'),
            'action': this.action.get('action'),
            'mergeProductId': this.action.get('mergeProductId')
        };
    }
});

exports.default = Config;

},{"./config-action":2,"./config-shop":3,"./config-status":4,"./config-taxonomy":5}],7:[function(require,module,exports){
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
     * @param {SearchResultsItem} searchResultsItem
     * @public
     */
    import: function _import(searchResultsItem) {
        var _this = this;

        var data = {
            'product': {
                'name': searchResultsItem.get('name'),
                'type': searchResultsItem.get('type'),
                'shops': searchResultsItem.get('shops'),
                'custom_values': searchResultsItem.get('custom_values')
            },
            'config': this.config.parse(),
            'form': this.search.form.parse()
        };

        jQuery.ajax({
            type: 'POST',
            url: this._buildUrl(),
            data: data
        }).done(function (result) {
            var shopTemplate = ((result || {}).data || {}).shop_template || null;

            if (shopTemplate) {
                _this.config.shop.addShop(shopTemplate);
            }

            searchResultsItem.showSuccessMessage();
        }).fail(function (result) {
            var errorMessage = ((((result || {}).responseJSON || {}).data || {})[0] || {}).message || null;

            searchResultsItem.showErrorMessage(errorMessage);
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

},{"./config":6,"./search":12}],8:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchForm = Backbone.Model.extend({
    defaults: {
        'term': '',
        'type': 'keywords',
        'category': 'All',
        'minPrice': null,
        'maxPrice': null,
        'condition': 'New',
        'sort': '-price',
        'withVariants': 'no',
        'loading': false,
        'error': false,
        'errorMessage': null,
        'noResults': false,
        'noResultsMessage': null,
        'providerConfigured': false
    },

    /**
     * @since 0.9.16
     */
    parse: function parse() {
        return {
            'term': this.get('term'),
            'type': this.get('type'),
            'category': this.get('category'),
            'minPrice': this.get('minPrice'),
            'maxPrice': this.get('maxPrice'),
            'sort': this.get('sort'),
            'condition': this.get('condition'),
            'withVariants': this.get('withVariants')
        };
    },


    /**
     * Submit the form the form and trigger the loading animation.
     *
     * @since 0.9
     * @public
     */
    submit: function submit() {
        this.set({
            'loading': true,
            'error': false,
            'errorMessage': null,
            'noResults': false,
            'noResultsMessage': null
        });

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
    },


    /**
     * Finish the search submit with no results and stop the loading animation.
     *
     * @since 0.9.14
     * @param {string|null} message
     * @public
     */
    noResults: function noResults() {
        var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        this.set({
            'loading': false,
            'noResults': true,
            'noResultsMessage': message
        });

        this.trigger('aff:amazon-import:search:search-form:no-results', this);
    },


    /**
     * Show a submit error and stop the loading animation.
     *
     * @since 0.9
     * @param {string|null} message
     * @public
     */
    error: function error() {
        var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        this.set({
            'loading': false,
            'error': true,
            'errorMessage': message
        });

        this.trigger('aff:amazon-import:search:search-form:error', this);
    }
});

exports.default = SearchForm;

},{}],9:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchLoadMore = Backbone.Model.extend({
    defaults: {
        'enabled': true,
        'loading': false,
        'noResults': false,
        'error': false,
        'errorMessage': null
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
     * @param {boolean} enabled
     * @public
     */
    done: function done() {
        var enabled = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

        this.set({
            'loading': false,
            'enabled': enabled
        });

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
    },


    /**
     * Show a load more error and deactivate the spinner animation.
     *
     * @since 0.9
     * @param {string|null} message
     * @public
     */
    error: function error() {
        var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        this.set({
            'enabled': true,
            'loading': false,
            'error': true,
            'errorMessage': message
        });

        this.trigger('aff:amazon-import:search:load-more:error', this);
    }
});

exports.default = SearchLoadMore;

},{}],10:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchResultsItem = Backbone.Model.extend({
    defaults: {
        'loading': false,
        'success': false,
        'successMessage': null,
        'error': false,
        'errorMessage': null
    },

    /**
     * Import the search result item.
     *
     * @since 0.9
     * @public
     */
    import: function _import() {
        this.set('loading', true);

        this.trigger('aff:amazon-import:search:results:item:import', this);
    },


    /**
     * Successfully finish the import with an optional message.
     *
     * @since 0.9
     * @param {string|null} message
     * @public
     */
    showSuccessMessage: function showSuccessMessage() {
        var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        this.set({
            'loading': false,
            'success': true,
            'successMessage': message,
            'error': false,
            'errorMessage': null,
            'custom_values': {
                'already_imported': true
            }
        });

        this.trigger('aff:amazon-import:search:results:item:success', this);
    },


    /**
     * Display an error for import with an optional message.
     *
     * @since 0.9
     * @param {string|null} message
     * @public
     */
    showErrorMessage: function showErrorMessage() {
        var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        this.set({
            'loading': false,
            'success': false,
            'successMessage': null,
            'error': true,
            'errorMessage': message
        });

        this.trigger('aff:amazon-import:search:results:item:error', this);
    }
});

exports.default = SearchResultsItem;

},{}],11:[function(require,module,exports){
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
     * @public
     * @param {Array} response
     * @returns {Array}
     */
    parse: function parse(response) {
        return response && response.success ? response.data : [];
    },

    /**
     * Import the given item.
     *
     * @since 0.9
     * @public
     * @param {SearchResultsItem} searchResultsItem
     */
    importItem: function importItem(searchResultsItem) {
        this.trigger('aff:amazon-import:search:results:import-item', searchResultsItem);
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
     * @param {SearchResultsItem} searchResultsItem
     */
    _initImportListener: function _initImportListener(searchResultsItem) {
        searchResultsItem.on('aff:amazon-import:search:results:item:import', this.importItem, this);
    }
});

exports.default = SearchResults;

},{"./search-results-item":10}],12:[function(require,module,exports){
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

            if (_this._hasResults(results)) {
                _this.form.done();
            } else {
                _this.form.noResults();
            }
        }).fail(function (result) {
            var errorMessage = ((((result || {}).responseJSON || {}).data || {})[0] || {}).message || null;

            _this.form.error(errorMessage);
            _this.loadMore.set('enabled', false);
        }).always(function () {
            _this.set('started', true);
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
            _this2.loadMore.done(_this2._isLoadMoreEnabled(results));
        }).fail(function () {
            var errorMessage = ((((result || {}).responseJSON || {}).data || {})[0] || {}).message || null;

            _this2.loadMore.error(errorMessage);
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
        return affAdminAmazonImportUrls.ajax + ('?action=' + this.get('action')) + ('&term=' + this.form.get('term')) + ('&type=' + this.form.get('type')) + ('&category=' + this.form.get('category')) + ('&min-price=' + this.form.get('minPrice')) + ('&max-price=' + this.form.get('maxPrice')) + ('&condition=' + this.form.get('condition')) + ('&sort=' + this.form.get('sort')) + ('&with-variants=' + this.form.get('withVariants')) + ('&page=' + this.get('page'));
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
    },


    /**
     * Check if there are any other results.
     *
     * @since 1.1.2
     * @param {Array|null} results
     * @returns {bool}
     * @private
     */
    _hasResults: function _hasResults(results) {
        return results && results.data && results.data.length > 0;
    }
});

exports.default = Search;

},{"./search-form":8,"./search-load-more":9,"./search-results":11}],13:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var ConfigAction = Backbone.View.extend({
    el: '#aff-amazon-import-config-action',

    events: {
        'change input[name="action"]': '_onChange',
        'change input[name="merge-product-id"]': '_onChange',
        'submit': '_onChange'
    },

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize: function initialize() {
        var template = jQuery('#aff-amazon-import-config-action-template');
        this.template = _.template(template.html());
    },


    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {ConfigAction}
     */
    render: function render() {
        this.$el.html(this.template(this.model.toJSON()));
        this._selectize();

        return this;
    },


    /**
     * Load the current config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChange: function _onChange(e) {
        e.preventDefault();

        var action = this.$el.find('input[name="action"]:checked');
        var mergeProductId = this.$el.find('input[name="merge-product-id"]');
        var mergeSelectize = mergeProductId.selectize()[0].selectize;

        action.val() === 'merge-product' ? mergeSelectize.enable() : mergeSelectize.disable();

        this.model.set({
            'action': action.val(),
            'mergeProductId': mergeProductId.val()
        });
    },


    /**
     * Selectize the input for enabling auto-completion and product search.
     *
     * @since 0.9.16
     * @private
     */
    _selectize: function _selectize() {
        var mergeProductId = this.$el.find('input[name="merge-product-id"]');

        mergeProductId.selectize({
            maxItems: 1,
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            create: false,
            load: function load(query, callback) {
                if (!query.length) return callback();
                jQuery.ajax({
                    url: affAdminAmazonImportUrls.apiRoot + 'wp/v2/aff-products/?status=publish,draft&search=' + query,
                    type: 'GET',
                    data: {
                        'post_parent': 0
                    },
                    beforeSend: function beforeSend(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', affAdminAmazonImportUrls.nonce);
                    },
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
    }
});

exports.default = ConfigAction;

},{}],14:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var ConfigShop = Backbone.View.extend({
    el: '#aff-amazon-import-config-shop',

    events: {
        'change input[name="shop"]': '_onChange',
        'blur input[name="new-shop-name"]': '_onChange',
        'submit': '_onChange'
    },

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize: function initialize() {
        var templateHtml = jQuery('#aff-amazon-import-config-shop-template').html();
        this.template = _.template(templateHtml);

        this.listenTo(this.model, 'change', this.render);
    },


    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {ConfigShop}
     */
    render: function render() {
        this.$el.html(this.template(this.model.toJSON()));
        this._initShop();
        this._checkShop();

        return this;
    },


    /**
     * Load the current config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChange: function _onChange(e) {
        e.preventDefault();

        var shop = this.$el.find('input[name="shop"]:checked');
        var newShopName = this.$el.find('input[name="new-shop-name"]');

        this.model.set({
            'shop': shop.val(),
            'newShopName': shop.val() === 'new-shop' ? newShopName.val() : null
        });
    },


    /**
     * Check the selected shop.
     *
     * @since 0.9.16
     * @private
     */
    _initShop: function _initShop() {
        var shops = this.$el.find('input[name="shop"]');

        if (this.model.get('shop') == null) {
            this.model.set('shop', shops.first().val());
        }

        return this;
    },


    /**
     * Check the selected shop.
     *
     * @since 0.9.16
     * @private
     */
    _checkShop: function _checkShop() {
        var shops = this.$el.find('input[name="shop"]');
        var shop = this.model.get('shop') == null ? shops.first().val() : this.model.get('shop');

        shops.val([shop]);

        return this;
    }
});

exports.default = ConfigShop;

},{}],15:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var ConfigStatus = Backbone.View.extend({
    el: '#aff-amazon-import-config-status',

    events: {
        'change input[name="status"]': '_onChange',
        'submit': '_onChange'
    },

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize: function initialize() {
        var templateHtml = jQuery('#aff-amazon-import-config-status-template').html();
        this.template = _.template(templateHtml);

        this.listenTo(this.model, 'change', this.render);
    },


    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {ConfigStatus}
     */
    render: function render() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    },


    /**
     * Load the current config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChange: function _onChange(e) {
        e.preventDefault();

        var status = this.$el.find('input[name="status"]:checked');

        this.model.set({
            'status': status.val()
        });
    }
});

exports.default = ConfigStatus;

},{}],16:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var ConfigTaxonomy = Backbone.View.extend({
    el: '#aff-amazon-import-config-taxonomy',

    events: {
        'change select[name="taxonomy"]': '_onChange',
        'change input[name="terms"]': '_onChange',
        'submit': '_onChange'
    },

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize: function initialize() {
        var template = jQuery('#aff-amazon-import-config-taxonomy-template');
        this.template = _.template(template.html());

        this.listenTo(this.model, 'change:taxonomy', this.render);
    },


    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {ConfigTaxonomy}
     */
    render: function render() {
        this.$el.html(this.template(this.model.toJSON()));
        this._selectize();

        return this;
    },


    /**
     * Load the current config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChange: function _onChange(e) {
        e.preventDefault();

        var taxonomies = this.$el.find('select[name="taxonomy"]');
        var terms = this.$el.find('input[name="terms"]');
        var selectize = terms.selectize()[0].selectize;

        taxonomies.val() === null || taxonomies.val() === 'none' ? selectize.disable() : selectize.enable();

        this.model.set({
            'taxonomy': taxonomies.val() !== 'none' ? taxonomies.val() : null,
            'terms': terms.val()
        });
    },


    /**
     * Selectize the input for enabling auto-completion and product search.
     *
     * @since 0.9.16
     * @private
     */
    _selectize: function _selectize() {
        var _this = this;

        var apiRoot = affAdminAmazonImportUrls.apiRoot;
        var nonce = affAdminAmazonImportUrls.nonce;
        var terms = this.$el.find('input[name="terms"]');

        terms.selectize({
            delimiter: ',',
            valueField: 'slug',
            labelField: 'name',
            searchField: 'name',
            create: false,
            load: function load(query, callback) {
                var taxonomy = _this.model.get('taxonomy');

                if (!query.length || !taxonomy) {
                    return callback();
                }

                jQuery.ajax({
                    url: apiRoot + 'wp/v2/' + taxonomy,
                    type: 'GET',
                    beforeSend: function beforeSend(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', nonce);
                    },
                    error: function error() {
                        callback();
                    },
                    success: function success(results) {
                        results = results.map(function (result) {
                            return {
                                'id': result.id,
                                'name': result.name,
                                'slug': result.slug,
                                'taxonomy': result.taxonomy
                            };
                        });

                        callback(results);
                    }
                });
            }
        });
    }
});

exports.default = ConfigTaxonomy;

},{}],17:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _configShop = require("./config-shop");

var _configShop2 = _interopRequireDefault(_configShop);

var _configStatus = require("./config-status");

var _configStatus2 = _interopRequireDefault(_configStatus);

var _configTaxonomy = require("./config-taxonomy");

var _configTaxonomy2 = _interopRequireDefault(_configTaxonomy);

var _configAction = require("./config-action");

var _configAction2 = _interopRequireDefault(_configAction);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Config = Backbone.View.extend({
    el: '#aff-amazon-import-config',

    /**
     * Initialize the config.
     *
     * @since 0.9.16
     * @public
     */
    initialize: function initialize() {
        this.shop = new _configShop2.default({ model: this.model.shop });
        this.status = new _configStatus2.default({ model: this.model.status });
        this.taxonomy = new _configTaxonomy2.default({ model: this.model.taxonomy });
        this.action = new _configAction2.default({ model: this.model.action });
    },


    /**
     * Render the config.
     *
     * @since 0.9.16
     * @public
     * @returns {Config}
     */
    render: function render() {
        this.shop.render();
        this.status.render();
        this.taxonomy.render();
        this.action.render();

        return this;
    }
});

exports.default = Config;

},{"./config-action":13,"./config-shop":14,"./config-status":15,"./config-taxonomy":16}],18:[function(require,module,exports){
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
    el: '#aff-amazon-import',

    /**
     * Initialize the import.
     *
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        this.search = new _search2.default({ model: this.model.search });
        this.config = new _config2.default({ model: this.model.config });
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

},{"./config":17,"./search":23}],19:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchForm = Backbone.View.extend({
    el: '#aff-amazon-import-search-form',

    events: {
        'change select[name="type"]': 'change',
        'change select[name="category"]': 'change',
        'submit': 'submit'
    },

    initialFocus: false,

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

        var term = this.$el.find('input[name="term"]'),
            type = this.$el.find('select[name="type"]'),
            category = this.$el.find('select[name="category"]'),
            minPrice = this.$el.find('input[name="min-price"]'),
            maxPrice = this.$el.find('input[name="max-price"]'),
            condition = this.$el.find('select[name="condition"]'),
            sort = this.$el.find('select[name="sort"]'),
            withVariants = this.$el.find('select[name="with-variants"]');

        if (!this.initialFocus) {
            term.focus();
            this.initialFocus = true;
        }

        type.val(this.model.get('type'));
        category.val(this.model.get('category'));
        minPrice.val(this.model.get('minPrice'));
        maxPrice.val(this.model.get('maxPrice'));
        condition.val(this.model.get('condition'));
        sort.val(this.model.get('sort'));
        withVariants.val(this.model.get('withVariants'));

        return this;
    },


    /**
     * Submit the search form.
     *
     * @since 0.9
     * @param {Event} e
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
            minPrice = this.$el.find('input[name="min-price"]'),
            maxPrice = this.$el.find('input[name="max-price"]'),
            condition = this.$el.find('select[name="condition"]'),
            sort = this.$el.find('select[name="sort"]'),
            withVariants = this.$el.find('select[name="with-variants"]');

        this.model.set({
            'term': term.length !== 0 ? term.val() : this.model.get('term'),
            'type': type.length !== 0 ? type.val() : this.model.get('type'),
            'minPrice': minPrice.length !== 0 ? minPrice.val() : this.model.get('minPrice'),
            'maxPrice': maxPrice.length !== 0 ? maxPrice.val() : this.model.get('maxPrice'),
            'condition': condition.length !== 0 ? condition.val() : this.model.get('condition'),
            'sort': sort.length !== 0 ? sort.val() : this.model.get('sort'),
            'category': category.length !== 0 ? category.val() : this.model.get('category'),
            'withVariants': withVariants.length !== 0 ? withVariants.val() : this.model.get('withVariants')
        });
    }
});

exports.default = SearchForm;

},{}],20:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchLoadMore = Backbone.View.extend({
    el: '#aff-amazon-import-search-load-more',

    events: {
        'click .aff-import-search-load-more-button': 'load'
    },

    /**
     * Initialize the search load more.
     *
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        var templateHtml = jQuery('#aff-amazon-import-search-load-more-template').html();

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

},{}],21:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchResultsItem = Backbone.View.extend({
    tagName: 'div',

    className: '',

    events: {
        'click .aff-import-search-results-item-variants-show-all': 'showAll',
        'click .aff-import-search-results-item-actions-import': 'import',
        'click .aff-import-search-results-item-actions-reimport': 'import'
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
        this.$el.html(this.template(this.model.attributes));

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

        this.$el.find('.aff-import-search-results-item-variants-show-all').hide();
        this.$el.find('.aff-import-search-results-item-variants-item').show();
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

},{}],22:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _searchResultsItem = require('./search-results-item');

var _searchResultsItem2 = _interopRequireDefault(_searchResultsItem);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var SearchResults = Backbone.View.extend({
    el: '#aff-amazon-import-search-results',

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

},{"./search-results-item":21}],23:[function(require,module,exports){
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
    el: '#aff-amazon-import-search',

    /**
     * Initialize the search.
     *
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        this.form = new _searchForm2.default({ model: this.model.form });
        this.results = new _searchResults2.default({ collection: this.model.results });
        this.loadMore = new _searchLoadMore2.default({ model: this.model.loadMore });

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

},{"./search-form":19,"./search-load-more":20,"./search-results":22}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWctYWN0aW9uLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy1zaG9wLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy1zdGF0dXMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvY29uZmlnLXRheG9ub215LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWZvcm0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWxvYWQtbW9yZS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1yZXN1bHRzLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L2NvbmZpZy1hY3Rpb24uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWctc2hvcC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L2NvbmZpZy1zdGF0dXMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWctdGF4b25vbXkuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2guanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBOzs7O0FBQ0E7Ozs7OztBQUVBLElBQUksY0FBYyxzQkFBbEI7QUFDQSxJQUFJLGFBQWEscUJBQWUsRUFBQyxPQUFPLFdBQVIsRUFBZixDQUFqQjs7QUFFQSxXQUFXLE1BQVg7Ozs7Ozs7O0FDTkEsSUFBSSxlQUFlLFNBQVMsS0FBVCxDQUFlLE1BQWYsQ0FBc0I7QUFDckMsY0FBVTtBQUNOLGtCQUFVLGFBREo7QUFFTiwwQkFBa0I7QUFGWjtBQUQyQixDQUF0QixDQUFuQjs7a0JBT2UsWTs7Ozs7Ozs7QUNQZixJQUFJLGFBQWEsU0FBUyxLQUFULENBQWUsTUFBZixDQUFzQjtBQUNuQyxjQUFVO0FBQ04sZ0JBQVEsSUFERjtBQUVOLHVCQUFlLElBRlQ7QUFHTixzQkFBYztBQUhSLEtBRHlCOztBQU9uQzs7Ozs7OztBQU9BLFdBZG1DLG1CQWMzQixJQWQyQixFQWNyQjtBQUNWLFlBQUksYUFB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QjtBQUMxQixpQkFBSyxRQUFMLENBQWMsTUFBZDtBQUNIOztBQUVELGVBQU8sSUFBUDtBQUNIO0FBaEM2QixDQUFyQixDQUFiOztrQkFtQ2UsTSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCJpbXBvcnQgSW1wb3J0IGZyb20gJy4vbW9kZWwvaW1wb3J0JztcbmltcG9ydCBJbXBvcnRWaWV3IGZyb20gJy4vdmlldy9pbXBvcnQnO1xuXG5sZXQgaW1wb3J0TW9kZWwgPSBuZXcgSW1wb3J0KCk7XG5sZXQgaW1wb3J0VmlldyA9IG5ldyBJbXBvcnRWaWV3KHttb2RlbDogaW1wb3J0TW9kZWx9KTtcblxuaW1wb3J0Vmlldy5yZW5kZXIoKTtcbiIsImxldCBDb25maWdBY3Rpb24gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdhY3Rpb24nOiAnbmV3LXByb2R1Y3QnLFxuICAgICAgICAnbWVyZ2VQcm9kdWN0SWQnOiBudWxsLFxuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnQWN0aW9uO1xuIiwibGV0IENvbmZpZ1Nob3AgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzaG9wJzogbnVsbCxcbiAgICAgICAgJ25ld1Nob3BOYW1lJzogbnVsbCxcbiAgICAgICAgJ2FkZGVkU2hvcHMnOiBbXSxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIGEgbmV3IHNob3AgdG8gdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICogQHBhcmFtIHtPYmplY3R9IHNob3BcbiAgICAgKi9cbiAgICBhZGRTaG9wKHNob3ApIHtcbiAgICAgICAgbGV0IGFkZGVkU2hvcHMgPSB0aGlzLmdldCgnYWRkZWRTaG9wcycpO1xuXG4gICAgICAgIGFkZGVkU2hvcHMucHVzaChzaG9wKTtcblxuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnc2hvcCc6IHNob3Auc2x1ZyxcbiAgICAgICAgICAgICduZXdTaG9wTmFtZSc6IG51bGwsXG4gICAgICAgICAgICAnYWRkZWRTaG9wcyc6IGFkZGVkU2hvcHMsXG4gICAgICAgIH0pO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnU2hvcDtcbiIsImxldCBDb25maWdTdGF0dXMgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzdGF0dXMnOiAncHVibGlzaCcsXG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdTdGF0dXM7XG4iLCJsZXQgQ29uZmlnVGF4b25vbXkgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICd0YXhvbm9teSc6IG51bGwsXG4gICAgICAgICd0ZXJtcyc6IG51bGwsXG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdUYXhvbm9teTtcbiIsImltcG9ydCBDb25maWdTaG9wIGZyb20gXCIuL2NvbmZpZy1zaG9wXCI7XG5pbXBvcnQgQ29uZmlnU3RhdHVzIGZyb20gXCIuL2NvbmZpZy1zdGF0dXNcIjtcbmltcG9ydCBDb25maWdUYXhvbm9teSBmcm9tIFwiLi9jb25maWctdGF4b25vbXlcIjtcbmltcG9ydCBDb25maWdBY3Rpb24gZnJvbSBcIi4vY29uZmlnLWFjdGlvblwiO1xuXG5sZXQgQ29uZmlnID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGNvbmZpZyB3aXRoIGFsbCBzdWIgY29uZmlncy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5zaG9wID0gbmV3IENvbmZpZ1Nob3AoKTtcbiAgICAgICAgdGhpcy5zdGF0dXMgPSBuZXcgQ29uZmlnU3RhdHVzKCk7XG4gICAgICAgIHRoaXMudGF4b25vbXkgPSBuZXcgQ29uZmlnVGF4b25vbXkoKTtcbiAgICAgICAgdGhpcy5hY3Rpb24gPSBuZXcgQ29uZmlnQWN0aW9uKCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFBhcnNlIHRoZSBjb25maWcgaW50byBhbiBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqIEByZXR1cm5zIHt7c2hvcCwgbmV3U2hvcE5hbWUsIHN0YXR1cywgdGF4b25vbXksIHRlcm0sIGFjdGlvbiwgbWVyZ2VQcm9kdWN0SWR9fVxuICAgICAqL1xuICAgIHBhcnNlKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgJ3Nob3AnOiB0aGlzLnNob3AuZ2V0KCdzaG9wJyksXG4gICAgICAgICAgICAnbmV3U2hvcE5hbWUnOiB0aGlzLnNob3AuZ2V0KCduZXdTaG9wTmFtZScpLFxuICAgICAgICAgICAgJ3N0YXR1cyc6IHRoaXMuc3RhdHVzLmdldCgnc3RhdHVzJyksXG4gICAgICAgICAgICAndGF4b25vbXknOiB0aGlzLnRheG9ub215LmdldCgndGF4b25vbXknKSxcbiAgICAgICAgICAgICd0ZXJtcyc6IHRoaXMudGF4b25vbXkuZ2V0KCd0ZXJtcycpLFxuICAgICAgICAgICAgJ2FjdGlvbic6IHRoaXMuYWN0aW9uLmdldCgnYWN0aW9uJyksXG4gICAgICAgICAgICAnbWVyZ2VQcm9kdWN0SWQnOiB0aGlzLmFjdGlvbi5nZXQoJ21lcmdlUHJvZHVjdElkJyksXG4gICAgICAgIH1cbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnO1xuIiwiaW1wb3J0IFNlYXJjaCBmcm9tICcuL3NlYXJjaCc7XG5pbXBvcnQgQ29uZmlnIGZyb20gJy4vY29uZmlnJztcblxubGV0IEltcG9ydCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2FjdGlvbic6ICdhZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25faW1wb3J0JyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuc2VhcmNoID0gbmV3IFNlYXJjaCgpO1xuICAgICAgICB0aGlzLmNvbmZpZyA9IG5ldyBDb25maWcoKTtcblxuICAgICAgICB0aGlzLnNlYXJjaC5vbignYWZmOmFtYXpvbi1pbXBvcnQ6aW1wb3J0LXJlc3VsdHMtaXRlbScsIHRoaXMuaW1wb3J0LCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBwcm9kdWN0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7U2VhcmNoUmVzdWx0c0l0ZW19IHNlYXJjaFJlc3VsdHNJdGVtXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChzZWFyY2hSZXN1bHRzSXRlbSkge1xuICAgICAgICBsZXQgZGF0YSA9IHtcbiAgICAgICAgICAgICdwcm9kdWN0Jzoge1xuICAgICAgICAgICAgICAgICduYW1lJyA6IHNlYXJjaFJlc3VsdHNJdGVtLmdldCgnbmFtZScpLFxuICAgICAgICAgICAgICAgICd0eXBlJyA6IHNlYXJjaFJlc3VsdHNJdGVtLmdldCgndHlwZScpLFxuICAgICAgICAgICAgICAgICdzaG9wcycgOiBzZWFyY2hSZXN1bHRzSXRlbS5nZXQoJ3Nob3BzJyksXG4gICAgICAgICAgICAgICAgJ2N1c3RvbV92YWx1ZXMnIDogc2VhcmNoUmVzdWx0c0l0ZW0uZ2V0KCdjdXN0b21fdmFsdWVzJyksXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJ2NvbmZpZyc6IHRoaXMuY29uZmlnLnBhcnNlKCksXG4gICAgICAgICAgICAnZm9ybSc6IHRoaXMuc2VhcmNoLmZvcm0ucGFyc2UoKSxcbiAgICAgICAgfTtcblxuICAgICAgICBqUXVlcnkuYWpheCh7XG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICB1cmw6IHRoaXMuX2J1aWxkVXJsKCksXG4gICAgICAgICAgICBkYXRhOiBkYXRhLFxuICAgICAgICB9KS5kb25lKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgIGxldCBzaG9wVGVtcGxhdGUgPSAoKHJlc3VsdCB8fCB7fSkuZGF0YSB8fCB7fSkuc2hvcF90ZW1wbGF0ZSB8fCBudWxsO1xuXG4gICAgICAgICAgICBpZihzaG9wVGVtcGxhdGUpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmNvbmZpZy5zaG9wLmFkZFNob3Aoc2hvcFRlbXBsYXRlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgc2VhcmNoUmVzdWx0c0l0ZW0uc2hvd1N1Y2Nlc3NNZXNzYWdlKCk7XG4gICAgICAgIH0pLmZhaWwoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgc2VhcmNoUmVzdWx0c0l0ZW0uc2hvd0Vycm9yTWVzc2FnZShlcnJvck1lc3NhZ2UpO1xuICAgICAgICB9KVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgaW1wb3J0IHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICA7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBJbXBvcnQ7XG4iLCJsZXQgU2VhcmNoRm9ybSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3Rlcm0nOiAnJyxcbiAgICAgICAgJ3R5cGUnOiAna2V5d29yZHMnLFxuICAgICAgICAnY2F0ZWdvcnknOiAnQWxsJyxcbiAgICAgICAgJ21pblByaWNlJzogbnVsbCxcbiAgICAgICAgJ21heFByaWNlJzogbnVsbCxcbiAgICAgICAgJ2NvbmRpdGlvbic6ICdOZXcnLFxuICAgICAgICAnc29ydCc6ICctcHJpY2UnLFxuICAgICAgICAnd2l0aFZhcmlhbnRzJzogJ25vJyxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgJ3Byb3ZpZGVyQ29uZmlndXJlZCc6IGZhbHNlXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKi9cbiAgICBwYXJzZSgpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICd0ZXJtJzogdGhpcy5nZXQoJ3Rlcm0nKSxcbiAgICAgICAgICAgICd0eXBlJzogdGhpcy5nZXQoJ3R5cGUnKSxcbiAgICAgICAgICAgICdjYXRlZ29yeSc6IHRoaXMuZ2V0KCdjYXRlZ29yeScpLFxuICAgICAgICAgICAgJ21pblByaWNlJzogdGhpcy5nZXQoJ21pblByaWNlJyksXG4gICAgICAgICAgICAnbWF4UHJpY2UnOiB0aGlzLmdldCgnbWF4UHJpY2UnKSxcbiAgICAgICAgICAgICdzb3J0JzogdGhpcy5nZXQoJ3NvcnQnKSxcbiAgICAgICAgICAgICdjb25kaXRpb24nOiB0aGlzLmdldCgnY29uZGl0aW9uJyksXG4gICAgICAgICAgICAnd2l0aFZhcmlhbnRzJzogdGhpcy5nZXQoJ3dpdGhWYXJpYW50cycpLFxuICAgICAgICB9O1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIGZvcm0gdGhlIGZvcm0gYW5kIHRyaWdnZXIgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdWJtaXQoKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogdHJ1ZSxcbiAgICAgICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzTWVzc2FnZSc6IG51bGwsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOnN1Ym1pdCcsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBGaW5pc2ggdGhlIHN1Ym1pdCBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgZmFsc2UpO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOmRvbmUnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRmluaXNoIHRoZSBzZWFyY2ggc3VibWl0IHdpdGggbm8gcmVzdWx0cyBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE0XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBub1Jlc3VsdHMobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiB0cnVlLFxuICAgICAgICAgICAgJ25vUmVzdWx0c01lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpuby1yZXN1bHRzJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYSBzdWJtaXQgZXJyb3IgYW5kIHN0b3AgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZXJyb3IobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hMb2FkTW9yZSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2VuYWJsZWQnOiB0cnVlLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWN0aXZhdGUgdGhlIGxvYWRpbmcgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgdHJ1ZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIGxvYWQgbW9yZSBidXR0b24gYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gZW5hYmxlZFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBkb25lKGVuYWJsZWQgPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZW5hYmxlZCc6IGVuYWJsZWQsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpkb25lJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIG5vIHJlc3VsdHMgbWVzc2FnZSBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cygpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnIDogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOm5vLXJlc3VsdHMnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhIGxvYWQgbW9yZSBlcnJvciBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBlcnJvcihtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnZW5hYmxlZCc6IHRydWUsXG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yJzogdHJ1ZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdzdWNjZXNzJzogZmFsc2UsXG4gICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBzZWFyY2ggcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06aW1wb3J0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Y2Nlc3NmdWxseSBmaW5pc2ggdGhlIGltcG9ydCB3aXRoIGFuIG9wdGlvbmFsIG1lc3NhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93U3VjY2Vzc01lc3NhZ2UobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdzdWNjZXNzJzogdHJ1ZSxcbiAgICAgICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgICAgICAnZXJyb3InOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgICAgICAgICAgJ2N1c3RvbV92YWx1ZXMnOiB7XG4gICAgICAgICAgICAgICAgJ2FscmVhZHlfaW1wb3J0ZWQnOiB0cnVlLFxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06c3VjY2VzcycsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBEaXNwbGF5IGFuIGVycm9yIGZvciBpbXBvcnQgd2l0aCBhbiBvcHRpb25hbCBtZXNzYWdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc2hvd0Vycm9yTWVzc2FnZShtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ3N1Y2Nlc3MnOiBmYWxzZSxcbiAgICAgICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICAgICAnZXJyb3InOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTplcnJvcicsIHRoaXMpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0c0l0ZW07XG4iLCJpbXBvcnQgU2VhcmNoUmVzdWx0SXRlbSBmcm9tICcuL3NlYXJjaC1yZXN1bHRzLWl0ZW0nO1xuXG5sZXQgU2VhcmNoUmVzdWx0cyA9IEJhY2tib25lLkNvbGxlY3Rpb24uZXh0ZW5kKHtcbiAgICBtb2RlbDogU2VhcmNoUmVzdWx0SXRlbSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLm9uKCdzeW5jJywgdGhpcy5pbml0SW1wb3J0TGlzdGVuZXJzLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUGFyc2UgdGhlIFdvcmRwcmVzcyBqc29uIEFqYXggcmVzcG9uc2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqIEBwYXJhbSB7QXJyYXl9IHJlc3BvbnNlXG4gICAgICogQHJldHVybnMge0FycmF5fVxuICAgICAqL1xuICAgIHBhcnNlOiBmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICByZXR1cm4gcmVzcG9uc2UgJiYgcmVzcG9uc2Uuc3VjY2VzcyA/IHJlc3BvbnNlLmRhdGEgOiBbXTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBnaXZlbiBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcGFyYW0ge1NlYXJjaFJlc3VsdHNJdGVtfSBzZWFyY2hSZXN1bHRzSXRlbVxuICAgICAqL1xuICAgIGltcG9ydEl0ZW0oc2VhcmNoUmVzdWx0c0l0ZW0pIHtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppbXBvcnQtaXRlbScsIHNlYXJjaFJlc3VsdHNJdGVtKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdCB0aGUgaW1wb3J0IGxpc3RlbmVycyBmb3IgYWxsIHJlc3VsdHMgaXRlbXMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRJbXBvcnRMaXN0ZW5lcnMoKSB7XG4gICAgICAgIHRoaXMuZm9yRWFjaCh0aGlzLl9pbml0SW1wb3J0TGlzdGVuZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0IHRoZSBpbXBvcnQgbGlzdGVuZXJzIGZvciB0aGUgcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKiBAcGFyYW0ge1NlYXJjaFJlc3VsdHNJdGVtfSBzZWFyY2hSZXN1bHRzSXRlbVxuICAgICAqL1xuICAgIF9pbml0SW1wb3J0TGlzdGVuZXIoc2VhcmNoUmVzdWx0c0l0ZW0pIHtcbiAgICAgICAgc2VhcmNoUmVzdWx0c0l0ZW0ub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06aW1wb3J0JywgdGhpcy5pbXBvcnRJdGVtLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3N0YXJ0ZWQnOiBmYWxzZSxcbiAgICAgICAgJ2FjdGlvbic6ICdhZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25fc2VhcmNoJyxcbiAgICAgICAgJ3BhZ2UnIDogMSxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHdpdGggdGhlIGdpdmVuIG9wdGlvbnMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqL1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSgpO1xuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cygpO1xuICAgICAgICB0aGlzLmxvYWRNb3JlID0gbmV3IFNlYXJjaExvYWRNb3JlKCk7XG4gICAgICAgIHRoaXMucGFnZSA9IG9wdGlvbnMgJiYgb3B0aW9ucy5wYWdlID8gb3B0aW9ucy5wYWdlIDogMTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOmltcG9ydC1pdGVtJywgdGhpcy5pbXBvcnQsIHRoaXMpO1xuICAgICAgICB0aGlzLmZvcm0ub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpzdWJtaXQnLCB0aGlzLnN0YXJ0LCB0aGlzKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZS5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcy5sb2FkLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3RhcnQgdGhlIHNlYXJjaCB3aXRoIHRoZSBmaXJzdCBwYWdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdGFydCgpIHtcbiAgICAgICAgaWYodGhpcy5mb3JtLmdldCgndGVybScpID09PSBudWxsKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNldCgncGFnZScsIDEpO1xuICAgICAgICB0aGlzLnJlc3VsdHMudXJsID0gdGhpcy5fYnVpbGRVcmwoKTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goKS5kb25lKChyZXN1bHRzKSA9PiB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnNldCgnZW5hYmxlZCcsIHRoaXMuX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpKTtcblxuICAgICAgICAgICAgaWYodGhpcy5faGFzUmVzdWx0cyhyZXN1bHRzKSkge1xuICAgICAgICAgICAgICAgIHRoaXMuZm9ybS5kb25lKCk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHRoaXMuZm9ybS5ub1Jlc3VsdHMoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSkuZmFpbCgocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICBsZXQgZXJyb3JNZXNzYWdlID0gKCgoKHJlc3VsdCB8fCB7fSkucmVzcG9uc2VKU09OIHx8IHt9KS5kYXRhIHx8IHt9KVswXSB8fCB7fSkubWVzc2FnZSB8fCBudWxsO1xuXG4gICAgICAgICAgICB0aGlzLmZvcm0uZXJyb3IoZXJyb3JNZXNzYWdlKTtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuc2V0KCdlbmFibGVkJywgZmFsc2UpO1xuICAgICAgICB9KS5hbHdheXMoKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5zZXQoJ3N0YXJ0ZWQnLCB0cnVlKTtcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgbW9yZSBzZWFyY2ggcmVzdWx0cyBieSBpbmNyZWFzaW5nIHRoZSBwYWdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBsb2FkKCkge1xuICAgICAgICB0aGlzLnNldCgncGFnZScsIHRoaXMuZ2V0KCdwYWdlJykgKyAxKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnVybCA9IHRoaXMuX2J1aWxkVXJsKCk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLmZldGNoKHsncmVtb3ZlJzogZmFsc2V9KS5kb25lKChyZXN1bHRzKSA9PiB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLmRvbmUodGhpcy5faXNMb2FkTW9yZUVuYWJsZWQocmVzdWx0cykpO1xuICAgICAgICB9KS5mYWlsKCgpID0+IHtcbiAgICAgICAgICAgIGxldCBlcnJvck1lc3NhZ2UgPSAoKCgocmVzdWx0IHx8IHt9KS5yZXNwb25zZUpTT04gfHwge30pLmRhdGEgfHwge30pWzBdIHx8IHt9KS5tZXNzYWdlIHx8IG51bGw7XG5cbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuZXJyb3IoZXJyb3JNZXNzYWdlKTtcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgZ2l2ZW4gc2VhcmNoIHJlc3VsdHMgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gbW9kZWxcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0KG1vZGVsKSB7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6aW1wb3J0LXJlc3VsdHMtaXRlbScsIG1vZGVsKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQnVpbGQgdGhlIHNlYXJjaCBBUEkgdXJsIGJhc2VkIG9uIHRoZSBnaXZlbiBwYXJhbWV0ZXJzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYnVpbGRVcmwoKSB7XG4gICAgICAgIHJldHVybiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYWpheFxuICAgICAgICAgICAgKyBgP2FjdGlvbj0ke3RoaXMuZ2V0KCdhY3Rpb24nKX1gXG4gICAgICAgICAgICArIGAmdGVybT0ke3RoaXMuZm9ybS5nZXQoJ3Rlcm0nKX1gXG4gICAgICAgICAgICArIGAmdHlwZT0ke3RoaXMuZm9ybS5nZXQoJ3R5cGUnKX1gXG4gICAgICAgICAgICArIGAmY2F0ZWdvcnk9JHt0aGlzLmZvcm0uZ2V0KCdjYXRlZ29yeScpfWBcbiAgICAgICAgICAgICsgYCZtaW4tcHJpY2U9JHt0aGlzLmZvcm0uZ2V0KCdtaW5QcmljZScpfWBcbiAgICAgICAgICAgICsgYCZtYXgtcHJpY2U9JHt0aGlzLmZvcm0uZ2V0KCdtYXhQcmljZScpfWBcbiAgICAgICAgICAgICsgYCZjb25kaXRpb249JHt0aGlzLmZvcm0uZ2V0KCdjb25kaXRpb24nKX1gXG4gICAgICAgICAgICArIGAmc29ydD0ke3RoaXMuZm9ybS5nZXQoJ3NvcnQnKX1gXG4gICAgICAgICAgICArIGAmd2l0aC12YXJpYW50cz0ke3RoaXMuZm9ybS5nZXQoJ3dpdGhWYXJpYW50cycpfWBcbiAgICAgICAgICAgICsgYCZwYWdlPSR7dGhpcy5nZXQoJ3BhZ2UnKX1gXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIENoZWNrIGlmIHRoZSBsb2FkIG1vcmUgYnV0dG9uIGlzIGVuYWJsZWQgKHZpc2libGUpLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7QXJyYXl8bnVsbH0gcmVzdWx0c1xuICAgICAqIEByZXR1cm5zIHtib29sfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpIHtcbiAgICAgICAgcmV0dXJuIChyZXN1bHRzICYmIHJlc3VsdHMuZGF0YSAmJiByZXN1bHRzLmRhdGEubGVuZ3RoID4gMClcbiAgICAgICAgICAgICYmIHRoaXMuZ2V0KCdwYWdlJykgPCA1XG4gICAgICAgICAgICAmJiB0aGlzLmZvcm0uZ2V0KCd0eXBlJykgPT09ICdrZXl3b3Jkcyc7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIENoZWNrIGlmIHRoZXJlIGFyZSBhbnkgb3RoZXIgcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAxLjEuMlxuICAgICAqIEBwYXJhbSB7QXJyYXl8bnVsbH0gcmVzdWx0c1xuICAgICAqIEByZXR1cm5zIHtib29sfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2hhc1Jlc3VsdHMocmVzdWx0cykge1xuICAgICAgICByZXR1cm4gcmVzdWx0c1xuICAgICAgICAgICAgJiYgcmVzdWx0cy5kYXRhXG4gICAgICAgICAgICAmJiByZXN1bHRzLmRhdGEubGVuZ3RoID4gMDtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoO1xuIiwibGV0IENvbmZpZ0FjdGlvbiA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLWFjdGlvbicsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwiYWN0aW9uXCJdJzogJ19vbkNoYW5nZScsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cIm1lcmdlLXByb2R1Y3QtaWRcIl0nOiAnX29uQ2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdfb25DaGFuZ2UnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZSA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1hY3Rpb24tdGVtcGxhdGUnKTtcbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGUuaHRtbCgpKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqIEByZXR1cm5zIHtDb25maWdBY3Rpb259XG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC50b0pTT04oKSkpO1xuICAgICAgICB0aGlzLl9zZWxlY3RpemUoKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgY3VycmVudCBjb25maWcgaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgICAqL1xuICAgIF9vbkNoYW5nZShlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICBsZXQgYWN0aW9uID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cImFjdGlvblwiXTpjaGVja2VkJyk7XG4gICAgICAgIGxldCBtZXJnZVByb2R1Y3RJZCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtZXJnZS1wcm9kdWN0LWlkXCJdJyk7XG4gICAgICAgIGxldCBtZXJnZVNlbGVjdGl6ZSA9IG1lcmdlUHJvZHVjdElkLnNlbGVjdGl6ZSgpWzBdLnNlbGVjdGl6ZTtcblxuICAgICAgICBhY3Rpb24udmFsKCkgPT09ICdtZXJnZS1wcm9kdWN0JyA/IG1lcmdlU2VsZWN0aXplLmVuYWJsZSgpIDogbWVyZ2VTZWxlY3RpemUuZGlzYWJsZSgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdhY3Rpb24nOiBhY3Rpb24udmFsKCksXG4gICAgICAgICAgICAnbWVyZ2VQcm9kdWN0SWQnOiBtZXJnZVByb2R1Y3RJZC52YWwoKSxcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNlbGVjdGl6ZSB0aGUgaW5wdXQgZm9yIGVuYWJsaW5nIGF1dG8tY29tcGxldGlvbiBhbmQgcHJvZHVjdCBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfc2VsZWN0aXplKCkge1xuICAgICAgICBsZXQgbWVyZ2VQcm9kdWN0SWQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWVyZ2UtcHJvZHVjdC1pZFwiXScpO1xuXG4gICAgICAgIG1lcmdlUHJvZHVjdElkLnNlbGVjdGl6ZSh7XG4gICAgICAgICAgICBtYXhJdGVtczogMSxcbiAgICAgICAgICAgIHZhbHVlRmllbGQ6ICdpZCcsXG4gICAgICAgICAgICBsYWJlbEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBzZWFyY2hGaWVsZDogJ25hbWUnLFxuICAgICAgICAgICAgY3JlYXRlOiBmYWxzZSxcbiAgICAgICAgICAgIGxvYWQocXVlcnksIGNhbGxiYWNrKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFxdWVyeS5sZW5ndGgpIHJldHVybiBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgIGpRdWVyeS5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYXBpUm9vdCArICd3cC92Mi9hZmYtcHJvZHVjdHMvP3N0YXR1cz1wdWJsaXNoLGRyYWZ0JnNlYXJjaD0nICsgcXVlcnksXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgICAgICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAncG9zdF9wYXJlbnQnOiAwLFxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBiZWZvcmVTZW5kKHhocikge1xuICAgICAgICAgICAgICAgICAgICAgICAgeGhyLnNldFJlcXVlc3RIZWFkZXIoJ1gtV1AtTm9uY2UnLCBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMubm9uY2UpXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIGVycm9yKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2soKTtcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgc3VjY2VzcyhyZXN1bHRzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHRzID0gcmVzdWx0cy5tYXAoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdpZCc6IHJlc3VsdC5pZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ25hbWUnOiByZXN1bHQudGl0bGUucmVuZGVyZWRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2socmVzdWx0cyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ0FjdGlvbjtcbiIsImxldCBDb25maWdTaG9wID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctc2hvcCcsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwic2hvcFwiXSc6ICdfb25DaGFuZ2UnLFxuICAgICAgICAnYmx1ciBpbnB1dFtuYW1lPVwibmV3LXNob3AtbmFtZVwiXSc6ICdfb25DaGFuZ2UnLFxuICAgICAgICAnc3VibWl0JzogJ19vbkNoYW5nZScsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1zaG9wLXRlbXBsYXRlJykuaHRtbCgpO1xuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuXG4gICAgICAgIHRoaXMubGlzdGVuVG8odGhpcy5tb2RlbCwgJ2NoYW5nZScsIHRoaXMucmVuZGVyKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqIEByZXR1cm5zIHtDb25maWdTaG9wfVxuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwudG9KU09OKCkpKTtcbiAgICAgICAgdGhpcy5faW5pdFNob3AoKTtcbiAgICAgICAgdGhpcy5fY2hlY2tTaG9wKCk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIGN1cnJlbnQgY29uZmlnIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqIEBwYXJhbSB7RXZlbnR9IGVcbiAgICAgKi9cbiAgICBfb25DaGFuZ2UoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgbGV0IHNob3AgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwic2hvcFwiXTpjaGVja2VkJyk7XG4gICAgICAgIGxldCBuZXdTaG9wTmFtZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJuZXctc2hvcC1uYW1lXCJdJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3Nob3AnOiBzaG9wLnZhbCgpLFxuICAgICAgICAgICAgJ25ld1Nob3BOYW1lJzogc2hvcC52YWwoKSA9PT0gJ25ldy1zaG9wJyA/IG5ld1Nob3BOYW1lLnZhbCgpIDogbnVsbCxcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIENoZWNrIHRoZSBzZWxlY3RlZCBzaG9wLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2luaXRTaG9wKCkge1xuICAgICAgICBsZXQgc2hvcHMgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwic2hvcFwiXScpO1xuXG4gICAgICAgIGlmKHRoaXMubW9kZWwuZ2V0KCdzaG9wJykgPT0gbnVsbCkge1xuICAgICAgICAgICAgdGhpcy5tb2RlbC5zZXQoJ3Nob3AnLCBzaG9wcy5maXJzdCgpLnZhbCgpKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayB0aGUgc2VsZWN0ZWQgc2hvcC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9jaGVja1Nob3AoKSB7XG4gICAgICAgIGxldCBzaG9wcyA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzaG9wXCJdJyk7XG4gICAgICAgIGxldCBzaG9wID0gdGhpcy5tb2RlbC5nZXQoJ3Nob3AnKSA9PSBudWxsID8gc2hvcHMuZmlyc3QoKS52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCdzaG9wJyk7XG5cbiAgICAgICAgc2hvcHMudmFsKFtzaG9wXSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ1Nob3A7XG4iLCJsZXQgQ29uZmlnU3RhdHVzID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctc3RhdHVzJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJzdGF0dXNcIl0nOiAnX29uQ2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdfb25DaGFuZ2UnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctc3RhdHVzLXRlbXBsYXRlJykuaHRtbCgpO1xuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuXG4gICAgICAgIHRoaXMubGlzdGVuVG8odGhpcy5tb2RlbCwgJ2NoYW5nZScsIHRoaXMucmVuZGVyKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqIEByZXR1cm5zIHtDb25maWdTdGF0dXN9XG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC50b0pTT04oKSkpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBjdXJyZW50IGNvbmZpZyBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHByaXZhdGVcbiAgICAgKiBAcGFyYW0ge0V2ZW50fSBlXG4gICAgICovXG4gICAgX29uQ2hhbmdlKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIGxldCBzdGF0dXMgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwic3RhdHVzXCJdOmNoZWNrZWQnKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc3RhdHVzJzogc3RhdHVzLnZhbCgpLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ1N0YXR1cztcbiIsImxldCBDb25maWdUYXhvbm9teSA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLXRheG9ub215JyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwidGF4b25vbXlcIl0nOiAnX29uQ2hhbmdlJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwidGVybXNcIl0nOiAnX29uQ2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdfb25DaGFuZ2UnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZSA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy10YXhvbm9teS10ZW1wbGF0ZScpO1xuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZS5odG1sKCkpO1xuXG4gICAgICAgIHRoaXMubGlzdGVuVG8odGhpcy5tb2RlbCwgJ2NoYW5nZTp0YXhvbm9teScsIHRoaXMucmVuZGVyKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqIEByZXR1cm5zIHtDb25maWdUYXhvbm9teX1cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLnRvSlNPTigpKSk7XG4gICAgICAgIHRoaXMuX3NlbGVjdGl6ZSgpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBjdXJyZW50IGNvbmZpZyBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHByaXZhdGVcbiAgICAgKiBAcGFyYW0ge0V2ZW50fSBlXG4gICAgICovXG4gICAgX29uQ2hhbmdlKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIGxldCB0YXhvbm9taWVzID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0YXhvbm9teVwiXScpO1xuICAgICAgICBsZXQgdGVybXMgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwidGVybXNcIl0nKTtcbiAgICAgICAgbGV0IHNlbGVjdGl6ZSA9IHRlcm1zLnNlbGVjdGl6ZSgpWzBdLnNlbGVjdGl6ZTtcblxuICAgICAgICB0YXhvbm9taWVzLnZhbCgpID09PSBudWxsIHx8IHRheG9ub21pZXMudmFsKCkgPT09ICdub25lJyA/IHNlbGVjdGl6ZS5kaXNhYmxlKCkgOiBzZWxlY3RpemUuZW5hYmxlKCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3RheG9ub215JzogdGF4b25vbWllcy52YWwoKSAhPT0gJ25vbmUnID8gdGF4b25vbWllcy52YWwoKSA6IG51bGwsXG4gICAgICAgICAgICAndGVybXMnOiB0ZXJtcy52YWwoKSxcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNlbGVjdGl6ZSB0aGUgaW5wdXQgZm9yIGVuYWJsaW5nIGF1dG8tY29tcGxldGlvbiBhbmQgcHJvZHVjdCBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfc2VsZWN0aXplKCkge1xuICAgICAgICBsZXQgYXBpUm9vdCA9IGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hcGlSb290O1xuICAgICAgICBsZXQgbm9uY2UgPSBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMubm9uY2U7XG4gICAgICAgIGxldCB0ZXJtcyA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJ0ZXJtc1wiXScpO1xuXG4gICAgICAgIHRlcm1zLnNlbGVjdGl6ZSh7XG4gICAgICAgICAgICBkZWxpbWl0ZXI6ICcsJyxcbiAgICAgICAgICAgIHZhbHVlRmllbGQ6ICdzbHVnJyxcbiAgICAgICAgICAgIGxhYmVsRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIHNlYXJjaEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBjcmVhdGU6IGZhbHNlLFxuICAgICAgICAgICAgbG9hZDogKHF1ZXJ5LCBjYWxsYmFjaykgPT4ge1xuICAgICAgICAgICAgICAgIGxldCB0YXhvbm9teSA9IHRoaXMubW9kZWwuZ2V0KCd0YXhvbm9teScpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCFxdWVyeS5sZW5ndGggfHwgIXRheG9ub215KSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGpRdWVyeS5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBgJHthcGlSb290fXdwL3YyLyR7dGF4b25vbXl9YCxcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgICAgICAgICAgIGJlZm9yZVNlbmQoeGhyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB4aHIuc2V0UmVxdWVzdEhlYWRlcignWC1XUC1Ob25jZScsIG5vbmNlKVxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBlcnJvcigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3MocmVzdWx0cykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0cyA9IHJlc3VsdHMubWFwKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnaWQnOiByZXN1bHQuaWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICduYW1lJzogcmVzdWx0Lm5hbWUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdzbHVnJzogcmVzdWx0LnNsdWcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICd0YXhvbm9teSc6IHJlc3VsdC50YXhvbm9teSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2socmVzdWx0cyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ1RheG9ub215O1xuIiwiaW1wb3J0IENvbmZpZ1Nob3AgZnJvbSBcIi4vY29uZmlnLXNob3BcIjtcbmltcG9ydCBDb25maWdTdGF0dXMgZnJvbSBcIi4vY29uZmlnLXN0YXR1c1wiO1xuaW1wb3J0IENvbmZpZ1RheG9ub215IGZyb20gXCIuL2NvbmZpZy10YXhvbm9teVwiO1xuaW1wb3J0IENvbmZpZ0FjdGlvbiBmcm9tIFwiLi9jb25maWctYWN0aW9uXCI7XG5cbmxldCBDb25maWcgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZycsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuc2hvcCA9IG5ldyBDb25maWdTaG9wKHttb2RlbDogdGhpcy5tb2RlbC5zaG9wfSk7XG4gICAgICAgIHRoaXMuc3RhdHVzID0gbmV3IENvbmZpZ1N0YXR1cyh7bW9kZWw6IHRoaXMubW9kZWwuc3RhdHVzfSk7XG4gICAgICAgIHRoaXMudGF4b25vbXkgPSBuZXcgQ29uZmlnVGF4b25vbXkoe21vZGVsOiB0aGlzLm1vZGVsLnRheG9ub215fSk7XG4gICAgICAgIHRoaXMuYWN0aW9uID0gbmV3IENvbmZpZ0FjdGlvbih7bW9kZWw6IHRoaXMubW9kZWwuYWN0aW9ufSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcmV0dXJucyB7Q29uZmlnfVxuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zaG9wLnJlbmRlcigpO1xuICAgICAgICB0aGlzLnN0YXR1cy5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy50YXhvbm9teS5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5hY3Rpb24ucmVuZGVyKCk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWc7XG4iLCJpbXBvcnQgU2VhcmNoIGZyb20gJy4vc2VhcmNoJztcbmltcG9ydCBDb25maWcgZnJvbSAnLi9jb25maWcnO1xuXG5sZXQgSW1wb3J0ID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0JyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5zZWFyY2ggPSBuZXcgU2VhcmNoKHttb2RlbDogdGhpcy5tb2RlbC5zZWFyY2h9KTtcbiAgICAgICAgdGhpcy5jb25maWcgPSBuZXcgQ29uZmlnKHttb2RlbDogdGhpcy5tb2RlbC5jb25maWd9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zZWFyY2gucmVuZGVyKCk7XG4gICAgICAgIHRoaXMuY29uZmlnLnJlbmRlcigpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgSW1wb3J0O1xuIiwibGV0IFNlYXJjaEZvcm0gPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwidHlwZVwiXSc6ICdjaGFuZ2UnLFxuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nOiAnY2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdzdWJtaXQnLFxuICAgIH0sXG5cbiAgICBpbml0aWFsRm9jdXM6IGZhbHNlLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybS10ZW1wbGF0ZScpLmh0bWwoKSxcbiAgICAgICAgICAgIHByb3ZpZGVyQ29uZmlndXJlZCA9IHRoaXMuJGVsLmRhdGEoJ3Byb3ZpZGVyLWNvbmZpZ3VyZWQnKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdwcm92aWRlckNvbmZpZ3VyZWQnLCBwcm92aWRlckNvbmZpZ3VyZWQgPT09IHRydWUgfHwgcHJvdmlkZXJDb25maWd1cmVkID09PSAndHJ1ZScpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge1NlYXJjaEZvcm19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIGxldCB0ZXJtID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1cIl0nKSxcbiAgICAgICAgICAgIHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLFxuICAgICAgICAgICAgbWluUHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWluLXByaWNlXCJdJyksXG4gICAgICAgICAgICBtYXhQcmljZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtYXgtcHJpY2VcIl0nKSxcbiAgICAgICAgICAgIGNvbmRpdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY29uZGl0aW9uXCJdJyksXG4gICAgICAgICAgICBzb3J0ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJzb3J0XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICBpZighdGhpcy5pbml0aWFsRm9jdXMpIHtcbiAgICAgICAgICAgIHRlcm0uZm9jdXMoKTtcbiAgICAgICAgICAgIHRoaXMuaW5pdGlhbEZvY3VzID0gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHR5cGUudmFsKHRoaXMubW9kZWwuZ2V0KCd0eXBlJykpO1xuICAgICAgICBjYXRlZ29yeS52YWwodGhpcy5tb2RlbC5nZXQoJ2NhdGVnb3J5JykpO1xuICAgICAgICBtaW5QcmljZS52YWwodGhpcy5tb2RlbC5nZXQoJ21pblByaWNlJykpO1xuICAgICAgICBtYXhQcmljZS52YWwodGhpcy5tb2RlbC5nZXQoJ21heFByaWNlJykpO1xuICAgICAgICBjb25kaXRpb24udmFsKHRoaXMubW9kZWwuZ2V0KCdjb25kaXRpb24nKSk7XG4gICAgICAgIHNvcnQudmFsKHRoaXMubW9kZWwuZ2V0KCdzb3J0JykpO1xuICAgICAgICB3aXRoVmFyaWFudHMudmFsKHRoaXMubW9kZWwuZ2V0KCd3aXRoVmFyaWFudHMnKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdWJtaXQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5jaGFuZ2UoKTtcbiAgICAgICAgdGhpcy5tb2RlbC5zdWJtaXQoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHNlYXJjaCBwYXJhbWV0ZXJzIGludG8gdGhlIG1vZGVsIG9uIGZvcm0gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2UoKSB7XG4gICAgICAgIGxldCB0ZXJtID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1cIl0nKSxcbiAgICAgICAgICAgIHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLFxuICAgICAgICAgICAgbWluUHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWluLXByaWNlXCJdJyksXG4gICAgICAgICAgICBtYXhQcmljZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtYXgtcHJpY2VcIl0nKSxcbiAgICAgICAgICAgIGNvbmRpdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY29uZGl0aW9uXCJdJyksXG4gICAgICAgICAgICBzb3J0ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJzb3J0XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAndGVybSc6IHRlcm0ubGVuZ3RoICE9PSAwID8gdGVybS52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCd0ZXJtJyksXG4gICAgICAgICAgICAndHlwZSc6IHR5cGUubGVuZ3RoICE9PSAwID8gdHlwZS52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCd0eXBlJyksXG4gICAgICAgICAgICAnbWluUHJpY2UnOiBtaW5QcmljZS5sZW5ndGggIT09IDAgPyBtaW5QcmljZS52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCdtaW5QcmljZScpLFxuICAgICAgICAgICAgJ21heFByaWNlJzogbWF4UHJpY2UubGVuZ3RoICE9PSAwID8gbWF4UHJpY2UudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnbWF4UHJpY2UnKSxcbiAgICAgICAgICAgICdjb25kaXRpb24nOiBjb25kaXRpb24ubGVuZ3RoICE9PSAwID8gY29uZGl0aW9uLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ2NvbmRpdGlvbicpLFxuICAgICAgICAgICAgJ3NvcnQnOiBzb3J0Lmxlbmd0aCAhPT0gMCA/IHNvcnQudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnc29ydCcpLFxuICAgICAgICAgICAgJ2NhdGVnb3J5JzogY2F0ZWdvcnkubGVuZ3RoICE9PSAwID8gY2F0ZWdvcnkudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnY2F0ZWdvcnknKSxcbiAgICAgICAgICAgICd3aXRoVmFyaWFudHMnOiB3aXRoVmFyaWFudHMubGVuZ3RoICE9PSAwID8gd2l0aFZhcmlhbnRzLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ3dpdGhWYXJpYW50cycpLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaEZvcm07XG4iLCJsZXQgU2VhcmNoTG9hZE1vcmUgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1sb2FkLW1vcmUnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlLWJ1dHRvbic6ICdsb2FkJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1sb2FkLW1vcmUtdGVtcGxhdGUnKS5odG1sKCk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCBsb2FkIG1vcmUuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybiB7U2VhcmNoTG9hZE1vcmV9XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBFbmFibGUgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBsb2FkKCkge1xuICAgICAgICB0aGlzLm1vZGVsLmxvYWQoKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgdGFnTmFtZTogJ2RpdicsXG5cbiAgICBjbGFzc05hbWU6ICcnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJzogJ3Nob3dBbGwnLFxuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS1hY3Rpb25zLWltcG9ydCc6ICdpbXBvcnQnLFxuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS1hY3Rpb25zLXJlaW1wb3J0JzogJ2ltcG9ydCdcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdGVtcGxhdGUnKS5odG1sKCk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybiB7U2VhcmNoUmVzdWx0c0l0ZW19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IGFsbCBoaWRkZW4gdmFyaWFudHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc2hvd0FsbChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJykuaGlkZSgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLWl0ZW0nKS5zaG93KCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgc2VhcmNoIHJlc3VsdCBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLmltcG9ydCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRzSXRlbSBmcm9tICcuL3NlYXJjaC1yZXN1bHRzLWl0ZW0nO1xuXG5sZXQgU2VhcmNoUmVzdWx0cyA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cycsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge2FycmF5fSBvcHRpb25zXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24gPSBvcHRpb25zLmNvbGxlY3Rpb247XG5cbiAgICAgICAgLy8gQmluZCB0aGUgY29sbGVjdGlvbiBldmVudHNcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3Jlc2V0JywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdhZGQnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3JlbW92ZScsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnc3luYycsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuX2FkZEFsbCgpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgYWxsIHNlYXJjaCByZXN1bHRzIGl0ZW1zIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2FkZEFsbCgpIHtcbiAgICAgICAgdGhpcy4kZWwuZW1wdHkoKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmZvckVhY2godGhpcy5fYWRkT25lLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIG9uZSBzZWFyY2ggcmVzdWx0cyBpdGVtIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2FkZE9uZShwcm9kdWN0KSB7XG4gICAgICAgIGxldCB2aWV3ID0gbmV3IFNlYXJjaFJlc3VsdHNJdGVtKHtcbiAgICAgICAgICAgIG1vZGVsOiBwcm9kdWN0LFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLiRlbC5hcHBlbmQodmlldy5yZW5kZXIoKS5lbCk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzO1xuIiwiaW1wb3J0IFNlYXJjaEZvcm0gZnJvbSAnLi9zZWFyY2gtZm9ybSc7XG5pbXBvcnQgU2VhcmNoTG9hZE1vcmUgZnJvbSAnLi9zZWFyY2gtbG9hZC1tb3JlJztcbmltcG9ydCBTZWFyY2hSZXN1bHRzIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMnO1xuXG5sZXQgU2VhcmNoID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaCcsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuZm9ybSA9IG5ldyBTZWFyY2hGb3JtKHttb2RlbDogdGhpcy5tb2RlbC5mb3JtfSk7XG4gICAgICAgIHRoaXMucmVzdWx0cyA9IG5ldyBTZWFyY2hSZXN1bHRzKHtjb2xsZWN0aW9uOiB0aGlzLm1vZGVsLnJlc3VsdHN9KTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSh7bW9kZWw6IHRoaXMubW9kZWwubG9hZE1vcmV9KTtcblxuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuZm9ybS5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnJlbmRlcigpO1xuXG4gICAgICAgIGlmKHRoaXMubW9kZWwuZ2V0KCdzdGFydGVkJykpIHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUucmVuZGVyKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiJdfQ==
