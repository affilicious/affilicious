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
    parse: function parse() {
        return {
            'term': this.get('term'),
            'type': this.get('type'),
            'category': this.get('type')
        };
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

        this.trigger('affebayiu:ebay-import:search:search-form:no-results', this);
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWctYWN0aW9uLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy1zaG9wLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy1zdGF0dXMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvY29uZmlnLXRheG9ub215LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWZvcm0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWxvYWQtbW9yZS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1yZXN1bHRzLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L2NvbmZpZy1hY3Rpb24uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWctc2hvcC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L2NvbmZpZy1zdGF0dXMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWctdGF4b25vbXkuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2guanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBOzs7O0FBQ0E7Ozs7OztBQUVBLElBQUksY0FBYyxzQkFBbEI7QUFDQSxJQUFJLGFBQWEscUJBQWUsRUFBQyxPQUFPLFdBQVIsRUFBZixDQUFqQjs7QUFFQSxXQUFXLE1BQVg7Ozs7Ozs7O0FDTkEsSUFBSSxlQUFlLFNBQVMsS0FBVCxDQUFlLE1BQWYsQ0FBc0I7QUFDckMsY0FBVTtBQUNOLGtCQUFVLGFBREo7QUFFTiwwQkFBa0I7QUFGWjtBQUQyQixDQUF0QixDQUFuQjs7a0JBT2UsWTs7Ozs7Ozs7QUNQZixJQUFJLGFBQWEsU0FBUyxLQUFULENBQWUsTUFBZixDQUFzQjtBQUNuQyxjQUFVO0FBQ04sZ0JBQVEsSUFERjtBQUVOLHVCQUFlLElBRlQ7QUFHTixzQkFBYztBQUhSLEtBRHlCOztBQU9uQzs7Ozs7OztBQU9BLFdBZG1DLG1CQWMzQixJQWQyQixFQWNyQjtBQUNWLFlBQUksYUFB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QSxXQS9DcUMsbUJBK0M3QixPQS9DNkIsRUErQ3BCO0FBQ2IsWUFBSSxPQUFPLGdDQUFzQjtBQUM3QixtQkFBTztBQURzQixTQUF0QixDQUFYOztBQUlBLGFBQUssR0FBTCxDQUFTLE1BQVQsQ0FBZ0IsS0FBSyxNQUFMLEdBQWMsRUFBOUI7QUFDSDtBQXJEb0MsQ0FBckIsQ0FBcEI7O2tCQXdEZSxhOzs7Ozs7Ozs7QUMxRGY7Ozs7QUFDQTs7OztBQUNBOzs7Ozs7QUFFQSxJQUFJLFNBQVMsU0FBUyxJQUFULENBQWMsTUFBZCxDQUFxQjtBQUM5QixRQUFJLDJCQUQwQjs7QUFHOUI7Ozs7OztBQU1BLGNBVDhCLHdCQVNqQjtBQUNULGFBQUssSUFBTCxHQUFZLHlCQUFlLEVBQUMsT0FBTyxLQUFLLEtBQUwsQ0FBVyxJQUFuQixFQUFmLENBQVo7QUFDQSxhQUFLLE9BQUwsR0FBZSw0QkFBa0IsRUFBQyxZQUFZLEtBQUssS0FBTCxDQUFXLE9BQXhCLEVBQWxCLENBQWY7QUFDQSxhQUFLLFFBQUwsR0FBZ0IsNkJBQW1CLEVBQUMsT0FBTyxLQUFLLEtBQUwsQ0FBVyxRQUFuQixFQUFuQixDQUFoQjs7QUFFQSxhQUFLLEtBQUwsQ0FBVyxFQUFYLENBQWMsUUFBZCxFQUF3QixLQUFLLE1BQTdCLEVBQXFDLElBQXJDO0FBQ0gsS0FmNkI7OztBQWlCOUI7Ozs7OztBQU1BLFVBdkI4QixvQkF1QnJCO0FBQ0wsYUFBSyxJQUFMLENBQVUsTUFBVjtBQUNBLGFBQUssT0FBTCxDQUFhLE1BQWI7O0FBRUEsWUFBRyxLQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWUsU0FBZixDQUFILEVBQThCO0FBQzFCLGlCQUFLLFFBQUwsQ0FBYyxNQUFkO0FBQ0g7O0FBRUQsZUFBTyxJQUFQO0FBQ0g7QUFoQzZCLENBQXJCLENBQWI7O2tCQW1DZSxNIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsImltcG9ydCBJbXBvcnQgZnJvbSAnLi9tb2RlbC9pbXBvcnQnO1xuaW1wb3J0IEltcG9ydFZpZXcgZnJvbSAnLi92aWV3L2ltcG9ydCc7XG5cbmxldCBpbXBvcnRNb2RlbCA9IG5ldyBJbXBvcnQoKTtcbmxldCBpbXBvcnRWaWV3ID0gbmV3IEltcG9ydFZpZXcoe21vZGVsOiBpbXBvcnRNb2RlbH0pO1xuXG5pbXBvcnRWaWV3LnJlbmRlcigpO1xuIiwibGV0IENvbmZpZ0FjdGlvbiA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2FjdGlvbic6ICduZXctcHJvZHVjdCcsXG4gICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IG51bGwsXG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdBY3Rpb247XG4iLCJsZXQgQ29uZmlnU2hvcCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3Nob3AnOiBudWxsLFxuICAgICAgICAnbmV3U2hvcE5hbWUnOiBudWxsLFxuICAgICAgICAnYWRkZWRTaG9wcyc6IFtdLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgYSBuZXcgc2hvcCB0byB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gc2hvcFxuICAgICAqL1xuICAgIGFkZFNob3Aoc2hvcCkge1xuICAgICAgICBsZXQgYWRkZWRTaG9wcyA9IHRoaXMuZ2V0KCdhZGRlZFNob3BzJyk7XG5cbiAgICAgICAgYWRkZWRTaG9wcy5wdXNoKHNob3ApO1xuXG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdzaG9wJzogc2hvcC5zbHVnLFxuICAgICAgICAgICAgJ25ld1Nob3BOYW1lJzogbnVsbCxcbiAgICAgICAgICAgICdhZGRlZFNob3BzJzogYWRkZWRTaG9wcyxcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdTaG9wO1xuIiwibGV0IENvbmZpZ1N0YXR1cyA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3N0YXR1cyc6ICdwdWJsaXNoJyxcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ1N0YXR1cztcbiIsImxldCBDb25maWdUYXhvbm9teSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3RheG9ub215JzogbnVsbCxcbiAgICAgICAgJ3Rlcm1zJzogbnVsbCxcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ1RheG9ub215O1xuIiwiaW1wb3J0IENvbmZpZ1Nob3AgZnJvbSBcIi4vY29uZmlnLXNob3BcIjtcbmltcG9ydCBDb25maWdTdGF0dXMgZnJvbSBcIi4vY29uZmlnLXN0YXR1c1wiO1xuaW1wb3J0IENvbmZpZ1RheG9ub215IGZyb20gXCIuL2NvbmZpZy10YXhvbm9teVwiO1xuaW1wb3J0IENvbmZpZ0FjdGlvbiBmcm9tIFwiLi9jb25maWctYWN0aW9uXCI7XG5cbmxldCBDb25maWcgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY29uZmlnIHdpdGggYWxsIHN1YiBjb25maWdzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNob3AgPSBuZXcgQ29uZmlnU2hvcCgpO1xuICAgICAgICB0aGlzLnN0YXR1cyA9IG5ldyBDb25maWdTdGF0dXMoKTtcbiAgICAgICAgdGhpcy50YXhvbm9teSA9IG5ldyBDb25maWdUYXhvbm9teSgpO1xuICAgICAgICB0aGlzLmFjdGlvbiA9IG5ldyBDb25maWdBY3Rpb24oKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUGFyc2UgdGhlIGNvbmZpZyBpbnRvIGFuIG9iamVjdC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICogQHJldHVybnMge3tzaG9wLCBuZXdTaG9wTmFtZSwgc3RhdHVzLCB0YXhvbm9teSwgdGVybSwgYWN0aW9uLCBtZXJnZVByb2R1Y3RJZH19XG4gICAgICovXG4gICAgcGFyc2UoKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAnc2hvcCc6IHRoaXMuc2hvcC5nZXQoJ3Nob3AnKSxcbiAgICAgICAgICAgICduZXdTaG9wTmFtZSc6IHRoaXMuc2hvcC5nZXQoJ25ld1Nob3BOYW1lJyksXG4gICAgICAgICAgICAnc3RhdHVzJzogdGhpcy5zdGF0dXMuZ2V0KCdzdGF0dXMnKSxcbiAgICAgICAgICAgICd0YXhvbm9teSc6IHRoaXMudGF4b25vbXkuZ2V0KCd0YXhvbm9teScpLFxuICAgICAgICAgICAgJ3Rlcm1zJzogdGhpcy50YXhvbm9teS5nZXQoJ3Rlcm1zJyksXG4gICAgICAgICAgICAnYWN0aW9uJzogdGhpcy5hY3Rpb24uZ2V0KCdhY3Rpb24nKSxcbiAgICAgICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IHRoaXMuYWN0aW9uLmdldCgnbWVyZ2VQcm9kdWN0SWQnKSxcbiAgICAgICAgfVxuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWc7XG4iLCJpbXBvcnQgU2VhcmNoIGZyb20gJy4vc2VhcmNoJztcbmltcG9ydCBDb25maWcgZnJvbSAnLi9jb25maWcnO1xuXG5sZXQgSW1wb3J0ID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnYWN0aW9uJzogJ2FmZl9wcm9kdWN0X2FkbWluX2FtYXpvbl9pbXBvcnQnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5zZWFyY2ggPSBuZXcgU2VhcmNoKCk7XG4gICAgICAgIHRoaXMuY29uZmlnID0gbmV3IENvbmZpZygpO1xuXG4gICAgICAgIHRoaXMuc2VhcmNoLm9uKCdhZmY6YW1hem9uLWltcG9ydDppbXBvcnQtcmVzdWx0cy1pdGVtJywgdGhpcy5pbXBvcnQsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIHByb2R1Y3QuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtTZWFyY2hSZXN1bHRzSXRlbX0gc2VhcmNoUmVzdWx0c0l0ZW1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0KHNlYXJjaFJlc3VsdHNJdGVtKSB7XG4gICAgICAgIGxldCBkYXRhID0ge1xuICAgICAgICAgICAgJ3Byb2R1Y3QnOiB7XG4gICAgICAgICAgICAgICAgJ25hbWUnIDogc2VhcmNoUmVzdWx0c0l0ZW0uZ2V0KCduYW1lJyksXG4gICAgICAgICAgICAgICAgJ3R5cGUnIDogc2VhcmNoUmVzdWx0c0l0ZW0uZ2V0KCd0eXBlJyksXG4gICAgICAgICAgICAgICAgJ3Nob3BzJyA6IHNlYXJjaFJlc3VsdHNJdGVtLmdldCgnc2hvcHMnKSxcbiAgICAgICAgICAgICAgICAnY3VzdG9tX3ZhbHVlcycgOiBzZWFyY2hSZXN1bHRzSXRlbS5nZXQoJ2N1c3RvbV92YWx1ZXMnKSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAnY29uZmlnJzogdGhpcy5jb25maWcucGFyc2UoKSxcbiAgICAgICAgICAgICdmb3JtJzogdGhpcy5zZWFyY2guZm9ybS5wYXJzZSgpLFxuICAgICAgICB9O1xuXG4gICAgICAgIGpRdWVyeS5hamF4KHtcbiAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgICAgICAgIHVybDogdGhpcy5fYnVpbGRVcmwoKSxcbiAgICAgICAgICAgIGRhdGE6IGRhdGEsXG4gICAgICAgIH0pLmRvbmUoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IHNob3BUZW1wbGF0ZSA9ICgocmVzdWx0IHx8IHt9KS5kYXRhIHx8IHt9KS5zaG9wX3RlbXBsYXRlIHx8IG51bGw7XG5cbiAgICAgICAgICAgIGlmKHNob3BUZW1wbGF0ZSkge1xuICAgICAgICAgICAgICAgIHRoaXMuY29uZmlnLnNob3AuYWRkU2hvcChzaG9wVGVtcGxhdGUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZWFyY2hSZXN1bHRzSXRlbS5zaG93U3VjY2Vzc01lc3NhZ2UoKTtcbiAgICAgICAgfSkuZmFpbCgocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICBsZXQgZXJyb3JNZXNzYWdlID0gKCgoKHJlc3VsdCB8fCB7fSkucmVzcG9uc2VKU09OIHx8IHt9KS5kYXRhIHx8IHt9KVswXSB8fCB7fSkubWVzc2FnZSB8fCBudWxsO1xuXG4gICAgICAgICAgICBzZWFyY2hSZXN1bHRzSXRlbS5zaG93RXJyb3JNZXNzYWdlKGVycm9yTWVzc2FnZSk7XG4gICAgICAgIH0pXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBpbXBvcnQgdXJsIGJhc2VkIG9uIHRoZSBnaXZlbiBwYXJhbWV0ZXJzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYnVpbGRVcmwoKSB7XG4gICAgICAgIHJldHVybiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYWpheFxuICAgICAgICAgICAgKyBgP2FjdGlvbj0ke3RoaXMuZ2V0KCdhY3Rpb24nKX1gXG4gICAgICAgIDtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IEltcG9ydDtcbiIsImxldCBTZWFyY2hGb3JtID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAndGVybSc6ICcnLFxuICAgICAgICAndHlwZSc6ICdrZXl3b3JkcycsXG4gICAgICAgICdjYXRlZ29yeSc6ICdBbGwnLFxuICAgICAgICAnbWluUHJpY2UnOiBudWxsLFxuICAgICAgICAnbWF4UHJpY2UnOiBudWxsLFxuICAgICAgICAnY29uZGl0aW9uJzogJ05ldycsXG4gICAgICAgICdzb3J0JzogJy1wcmljZScsXG4gICAgICAgICd3aXRoVmFyaWFudHMnOiAnbm8nLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnZXJyb3InOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICdub1Jlc3VsdHMnOiBmYWxzZSxcbiAgICAgICAgJ25vUmVzdWx0c01lc3NhZ2UnOiBudWxsLFxuICAgICAgICAncHJvdmlkZXJDb25maWd1cmVkJzogZmFsc2VcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3VibWl0IHRoZSBmb3JtIHRoZSBmb3JtIGFuZCB0cmlnZ2VyIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3VibWl0KCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3InOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgICAgICAgICAgJ25vUmVzdWx0cyc6IGZhbHNlLFxuICAgICAgICAgICAgJ25vUmVzdWx0c01lc3NhZ2UnOiBudWxsLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpzdWJtaXQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgcGFyc2UoKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAndGVybSc6IHRoaXMuZ2V0KCd0ZXJtJyksXG4gICAgICAgICAgICAndHlwZSc6IHRoaXMuZ2V0KCd0eXBlJyksXG4gICAgICAgICAgICAnY2F0ZWdvcnknOiB0aGlzLmdldCgndHlwZScpLFxuICAgICAgICB9O1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBGaW5pc2ggdGhlIHN1Ym1pdCBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgZmFsc2UpO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOmRvbmUnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRmluaXNoIHRoZSBzZWFyY2ggc3VibWl0IHdpdGggbm8gcmVzdWx0cyBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE0XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBub1Jlc3VsdHMobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiB0cnVlLFxuICAgICAgICAgICAgJ25vUmVzdWx0c01lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZmViYXlpdTplYmF5LWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06bm8tcmVzdWx0cycsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IGEgc3VibWl0IGVycm9yIGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGVycm9yKG1lc3NhZ2UgPSBudWxsKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3InOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOmVycm9yJywgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaEZvcm07XG4iLCJsZXQgU2VhcmNoTG9hZE1vcmUgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdlbmFibGVkJzogdHJ1ZSxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ25vUmVzdWx0cyc6IGZhbHNlLFxuICAgICAgICAnZXJyb3InOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG51bGwsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFjdGl2YXRlIHRoZSBsb2FkaW5nIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBsb2FkKCkge1xuICAgICAgICB0aGlzLnNldCgnbG9hZGluZycsIHRydWUpO1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bG9hZCcsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IHRoZSBsb2FkIG1vcmUgYnV0dG9uIGFuZCBkZWFjdGl2YXRlIHRoZSBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge2Jvb2xlYW59IGVuYWJsZWRcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZG9uZShlbmFibGVkID0gdHJ1ZSkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2VuYWJsZWQnOiBlbmFibGVkLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZG9uZScsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IHRoZSBubyByZXN1bHRzIG1lc3NhZ2UgYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBub1Jlc3VsdHMoKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJyA6IGZhbHNlLFxuICAgICAgICAgICAgJ25vUmVzdWx0cyc6IHRydWUsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpuby1yZXN1bHRzJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYSBsb2FkIG1vcmUgZXJyb3IgYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZXJyb3IobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2VuYWJsZWQnOiB0cnVlLFxuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmVycm9yJywgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaExvYWRNb3JlO1xuIiwibGV0IFNlYXJjaFJlc3VsdHNJdGVtID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnc3VjY2Vzcyc6IGZhbHNlLFxuICAgICAgICAnc3VjY2Vzc01lc3NhZ2UnOiBudWxsLFxuICAgICAgICAnZXJyb3InOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG51bGwsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgc2VhcmNoIHJlc3VsdCBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgdHJ1ZSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmltcG9ydCcsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWNjZXNzZnVsbHkgZmluaXNoIHRoZSBpbXBvcnQgd2l0aCBhbiBvcHRpb25hbCBtZXNzYWdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc2hvd1N1Y2Nlc3NNZXNzYWdlKG1lc3NhZ2UgPSBudWxsKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnc3VjY2Vzcyc6IHRydWUsXG4gICAgICAgICAgICAnc3VjY2Vzc01lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgICAgICdjdXN0b21fdmFsdWVzJzoge1xuICAgICAgICAgICAgICAgICdhbHJlYWR5X2ltcG9ydGVkJzogdHJ1ZSxcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOnN1Y2Nlc3MnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRGlzcGxheSBhbiBlcnJvciBmb3IgaW1wb3J0IHdpdGggYW4gb3B0aW9uYWwgbWVzc2FnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dFcnJvck1lc3NhZ2UobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdzdWNjZXNzJzogZmFsc2UsXG4gICAgICAgICAgICAnc3VjY2Vzc01lc3NhZ2UnOiBudWxsLFxuICAgICAgICAgICAgJ2Vycm9yJzogdHJ1ZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06ZXJyb3InLCB0aGlzKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdEl0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG4gICAgbW9kZWw6IFNlYXJjaFJlc3VsdEl0ZW0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5vbignc3luYycsIHRoaXMuaW5pdEltcG9ydExpc3RlbmVycywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFBhcnNlIHRoZSBXb3JkcHJlc3MganNvbiBBamF4IHJlc3BvbnNlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcGFyYW0ge0FycmF5fSByZXNwb25zZVxuICAgICAqIEByZXR1cm5zIHtBcnJheX1cbiAgICAgKi9cbiAgICBwYXJzZTogZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgcmV0dXJuIHJlc3BvbnNlICYmIHJlc3BvbnNlLnN1Y2Nlc3MgPyByZXNwb25zZS5kYXRhIDogW107XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgZ2l2ZW4gaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICogQHBhcmFtIHtTZWFyY2hSZXN1bHRzSXRlbX0gc2VhcmNoUmVzdWx0c0l0ZW1cbiAgICAgKi9cbiAgICBpbXBvcnRJdGVtKHNlYXJjaFJlc3VsdHNJdGVtKSB7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aW1wb3J0LWl0ZW0nLCBzZWFyY2hSZXN1bHRzSXRlbSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXQgdGhlIGltcG9ydCBsaXN0ZW5lcnMgZm9yIGFsbCByZXN1bHRzIGl0ZW1zLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0SW1wb3J0TGlzdGVuZXJzKCkge1xuICAgICAgICB0aGlzLmZvckVhY2godGhpcy5faW5pdEltcG9ydExpc3RlbmVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdCB0aGUgaW1wb3J0IGxpc3RlbmVycyBmb3IgdGhlIHJlc3VsdCBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHtTZWFyY2hSZXN1bHRzSXRlbX0gc2VhcmNoUmVzdWx0c0l0ZW1cbiAgICAgKi9cbiAgICBfaW5pdEltcG9ydExpc3RlbmVyKHNlYXJjaFJlc3VsdHNJdGVtKSB7XG4gICAgICAgIHNlYXJjaFJlc3VsdHNJdGVtLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmltcG9ydCcsIHRoaXMuaW1wb3J0SXRlbSwgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hMb2FkTW9yZSBmcm9tICcuL3NlYXJjaC1sb2FkLW1vcmUnO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzdGFydGVkJzogZmFsc2UsXG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX3NlYXJjaCcsXG4gICAgICAgICdwYWdlJyA6IDEsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCB3aXRoIHRoZSBnaXZlbiBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IG9wdGlvbnNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5mb3JtID0gbmV3IFNlYXJjaEZvcm0oKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzID0gbmV3IFNlYXJjaFJlc3VsdHMoKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSgpO1xuICAgICAgICB0aGlzLnBhZ2UgPSBvcHRpb25zICYmIG9wdGlvbnMucGFnZSA/IG9wdGlvbnMucGFnZSA6IDE7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppbXBvcnQtaXRlbScsIHRoaXMuaW1wb3J0LCB0aGlzKTtcbiAgICAgICAgdGhpcy5mb3JtLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcy5zdGFydCwgdGhpcyk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bG9hZCcsIHRoaXMubG9hZCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN0YXJ0IHRoZSBzZWFyY2ggd2l0aCB0aGUgZmlyc3QgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3RhcnQoKSB7XG4gICAgICAgIGlmKHRoaXMuZm9ybS5nZXQoJ3Rlcm0nKSA9PT0gbnVsbCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCAxKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnVybCA9IHRoaXMuX2J1aWxkVXJsKCk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLmZldGNoKCkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5zZXQoJ2VuYWJsZWQnLCB0aGlzLl9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSk7XG5cbiAgICAgICAgICAgIGlmKHRoaXMuX2hhc1Jlc3VsdHMocmVzdWx0cykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0uZG9uZSgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0ubm9SZXN1bHRzKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pLmZhaWwoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgdGhpcy5mb3JtLmVycm9yKGVycm9yTWVzc2FnZSk7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnNldCgnZW5hYmxlZCcsIGZhbHNlKTtcbiAgICAgICAgfSkuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2V0KCdzdGFydGVkJywgdHJ1ZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIG1vcmUgc2VhcmNoIHJlc3VsdHMgYnkgaW5jcmVhc2luZyB0aGUgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCB0aGlzLmdldCgncGFnZScpICsgMSk7XG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5mZXRjaCh7J3JlbW92ZSc6IGZhbHNlfSkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5kb25lKHRoaXMuX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpKTtcbiAgICAgICAgfSkuZmFpbCgoKSA9PiB7XG4gICAgICAgICAgICBsZXQgZXJyb3JNZXNzYWdlID0gKCgoKHJlc3VsdCB8fCB7fSkucmVzcG9uc2VKU09OIHx8IHt9KS5kYXRhIHx8IHt9KVswXSB8fCB7fSkubWVzc2FnZSB8fCBudWxsO1xuXG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLmVycm9yKGVycm9yTWVzc2FnZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChtb2RlbCkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCBtb2RlbCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBzZWFyY2ggQVBJIHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnRlcm09JHt0aGlzLmZvcm0uZ2V0KCd0ZXJtJyl9YFxuICAgICAgICAgICAgKyBgJnR5cGU9JHt0aGlzLmZvcm0uZ2V0KCd0eXBlJyl9YFxuICAgICAgICAgICAgKyBgJmNhdGVnb3J5PSR7dGhpcy5mb3JtLmdldCgnY2F0ZWdvcnknKX1gXG4gICAgICAgICAgICArIGAmbWluLXByaWNlPSR7dGhpcy5mb3JtLmdldCgnbWluUHJpY2UnKX1gXG4gICAgICAgICAgICArIGAmbWF4LXByaWNlPSR7dGhpcy5mb3JtLmdldCgnbWF4UHJpY2UnKX1gXG4gICAgICAgICAgICArIGAmY29uZGl0aW9uPSR7dGhpcy5mb3JtLmdldCgnY29uZGl0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnNvcnQ9JHt0aGlzLmZvcm0uZ2V0KCdzb3J0Jyl9YFxuICAgICAgICAgICAgKyBgJndpdGgtdmFyaWFudHM9JHt0aGlzLmZvcm0uZ2V0KCd3aXRoVmFyaWFudHMnKX1gXG4gICAgICAgICAgICArIGAmcGFnZT0ke3RoaXMuZ2V0KCdwYWdlJyl9YFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayBpZiB0aGUgbG9hZCBtb3JlIGJ1dHRvbiBpcyBlbmFibGVkICh2aXNpYmxlKS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge0FycmF5fG51bGx9IHJlc3VsdHNcbiAgICAgKiBAcmV0dXJucyB7Ym9vbH1cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSB7XG4gICAgICAgIHJldHVybiAocmVzdWx0cyAmJiByZXN1bHRzLmRhdGEgJiYgcmVzdWx0cy5kYXRhLmxlbmd0aCA+IDApXG4gICAgICAgICAgICAmJiB0aGlzLmdldCgncGFnZScpIDwgNVxuICAgICAgICAgICAgJiYgdGhpcy5mb3JtLmdldCgndHlwZScpID09PSAna2V5d29yZHMnO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayBpZiB0aGVyZSBhcmUgYW55IG90aGVyIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMS4xLjJcbiAgICAgKiBAcGFyYW0ge0FycmF5fG51bGx9IHJlc3VsdHNcbiAgICAgKiBAcmV0dXJucyB7Ym9vbH1cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9oYXNSZXN1bHRzKHJlc3VsdHMpIHtcbiAgICAgICAgcmV0dXJuIHJlc3VsdHNcbiAgICAgICAgICAgICYmIHJlc3VsdHMuZGF0YVxuICAgICAgICAgICAgJiYgcmVzdWx0cy5kYXRhLmxlbmd0aCA+IDA7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiIsImxldCBDb25maWdBY3Rpb24gPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1hY3Rpb24nLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cImFjdGlvblwiXSc6ICdfb25DaGFuZ2UnLFxuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJtZXJnZS1wcm9kdWN0LWlkXCJdJzogJ19vbkNoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnX29uQ2hhbmdlJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGUgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctYWN0aW9uLXRlbXBsYXRlJyk7XG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlLmh0bWwoKSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcmV0dXJucyB7Q29uZmlnQWN0aW9ufVxuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwudG9KU09OKCkpKTtcbiAgICAgICAgdGhpcy5fc2VsZWN0aXplKCk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIGN1cnJlbnQgY29uZmlnIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqIEBwYXJhbSB7RXZlbnR9IGVcbiAgICAgKi9cbiAgICBfb25DaGFuZ2UoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgbGV0IGFjdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJhY3Rpb25cIl06Y2hlY2tlZCcpO1xuICAgICAgICBsZXQgbWVyZ2VQcm9kdWN0SWQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWVyZ2UtcHJvZHVjdC1pZFwiXScpO1xuICAgICAgICBsZXQgbWVyZ2VTZWxlY3RpemUgPSBtZXJnZVByb2R1Y3RJZC5zZWxlY3RpemUoKVswXS5zZWxlY3RpemU7XG5cbiAgICAgICAgYWN0aW9uLnZhbCgpID09PSAnbWVyZ2UtcHJvZHVjdCcgPyBtZXJnZVNlbGVjdGl6ZS5lbmFibGUoKSA6IG1lcmdlU2VsZWN0aXplLmRpc2FibGUoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnYWN0aW9uJzogYWN0aW9uLnZhbCgpLFxuICAgICAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbWVyZ2VQcm9kdWN0SWQudmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTZWxlY3RpemUgdGhlIGlucHV0IGZvciBlbmFibGluZyBhdXRvLWNvbXBsZXRpb24gYW5kIHByb2R1Y3Qgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX3NlbGVjdGl6ZSgpIHtcbiAgICAgICAgbGV0IG1lcmdlUHJvZHVjdElkID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1lcmdlLXByb2R1Y3QtaWRcIl0nKTtcblxuICAgICAgICBtZXJnZVByb2R1Y3RJZC5zZWxlY3RpemUoe1xuICAgICAgICAgICAgbWF4SXRlbXM6IDEsXG4gICAgICAgICAgICB2YWx1ZUZpZWxkOiAnaWQnLFxuICAgICAgICAgICAgbGFiZWxGaWVsZDogJ25hbWUnLFxuICAgICAgICAgICAgc2VhcmNoRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIGNyZWF0ZTogZmFsc2UsXG4gICAgICAgICAgICBsb2FkKHF1ZXJ5LCBjYWxsYmFjaykge1xuICAgICAgICAgICAgICAgIGlmICghcXVlcnkubGVuZ3RoKSByZXR1cm4gY2FsbGJhY2soKTtcbiAgICAgICAgICAgICAgICBqUXVlcnkuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFwaVJvb3QgKyAnd3AvdjIvYWZmLXByb2R1Y3RzLz9zdGF0dXM9cHVibGlzaCxkcmFmdCZzZWFyY2g9JyArIHF1ZXJ5LFxuICAgICAgICAgICAgICAgICAgICB0eXBlOiAnR0VUJyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgICAgICAgICAgJ3Bvc3RfcGFyZW50JzogMCxcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgYmVmb3JlU2VuZCh4aHIpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHhoci5zZXRSZXF1ZXN0SGVhZGVyKCdYLVdQLU5vbmNlJywgYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLm5vbmNlKVxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBlcnJvcigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3MocmVzdWx0cykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0cyA9IHJlc3VsdHMubWFwKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnaWQnOiByZXN1bHQuaWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICduYW1lJzogcmVzdWx0LnRpdGxlLnJlbmRlcmVkXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKHJlc3VsdHMpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdBY3Rpb247XG4iLCJsZXQgQ29uZmlnU2hvcCA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLXNob3AnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInNob3BcIl0nOiAnX29uQ2hhbmdlJyxcbiAgICAgICAgJ2JsdXIgaW5wdXRbbmFtZT1cIm5ldy1zaG9wLW5hbWVcIl0nOiAnX29uQ2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdfb25DaGFuZ2UnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctc2hvcC10ZW1wbGF0ZScpLmh0bWwoKTtcbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcblxuICAgICAgICB0aGlzLmxpc3RlblRvKHRoaXMubW9kZWwsICdjaGFuZ2UnLCB0aGlzLnJlbmRlcik7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcmV0dXJucyB7Q29uZmlnU2hvcH1cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLnRvSlNPTigpKSk7XG4gICAgICAgIHRoaXMuX2luaXRTaG9wKCk7XG4gICAgICAgIHRoaXMuX2NoZWNrU2hvcCgpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBjdXJyZW50IGNvbmZpZyBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHByaXZhdGVcbiAgICAgKiBAcGFyYW0ge0V2ZW50fSBlXG4gICAgICovXG4gICAgX29uQ2hhbmdlKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIGxldCBzaG9wID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInNob3BcIl06Y2hlY2tlZCcpO1xuICAgICAgICBsZXQgbmV3U2hvcE5hbWUgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibmV3LXNob3AtbmFtZVwiXScpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzaG9wJzogc2hvcC52YWwoKSxcbiAgICAgICAgICAgICduZXdTaG9wTmFtZSc6IHNob3AudmFsKCkgPT09ICduZXctc2hvcCcgPyBuZXdTaG9wTmFtZS52YWwoKSA6IG51bGwsXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayB0aGUgc2VsZWN0ZWQgc2hvcC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9pbml0U2hvcCgpIHtcbiAgICAgICAgbGV0IHNob3BzID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInNob3BcIl0nKTtcblxuICAgICAgICBpZih0aGlzLm1vZGVsLmdldCgnc2hvcCcpID09IG51bGwpIHtcbiAgICAgICAgICAgIHRoaXMubW9kZWwuc2V0KCdzaG9wJywgc2hvcHMuZmlyc3QoKS52YWwoKSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQ2hlY2sgdGhlIHNlbGVjdGVkIHNob3AuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfY2hlY2tTaG9wKCkge1xuICAgICAgICBsZXQgc2hvcHMgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwic2hvcFwiXScpO1xuICAgICAgICBsZXQgc2hvcCA9IHRoaXMubW9kZWwuZ2V0KCdzaG9wJykgPT0gbnVsbCA/IHNob3BzLmZpcnN0KCkudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnc2hvcCcpO1xuXG4gICAgICAgIHNob3BzLnZhbChbc2hvcF0pO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdTaG9wO1xuIiwibGV0IENvbmZpZ1N0YXR1cyA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLXN0YXR1cycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwic3RhdHVzXCJdJzogJ19vbkNoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnX29uQ2hhbmdlJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLXN0YXR1cy10ZW1wbGF0ZScpLmh0bWwoKTtcbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcblxuICAgICAgICB0aGlzLmxpc3RlblRvKHRoaXMubW9kZWwsICdjaGFuZ2UnLCB0aGlzLnJlbmRlcik7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcmV0dXJucyB7Q29uZmlnU3RhdHVzfVxuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwudG9KU09OKCkpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgY3VycmVudCBjb25maWcgaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgICAqL1xuICAgIF9vbkNoYW5nZShlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICBsZXQgc3RhdHVzID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInN0YXR1c1wiXTpjaGVja2VkJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3N0YXR1cyc6IHN0YXR1cy52YWwoKSxcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdTdGF0dXM7XG4iLCJsZXQgQ29uZmlnVGF4b25vbXkgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy10YXhvbm9teScsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBzZWxlY3RbbmFtZT1cInRheG9ub215XCJdJzogJ19vbkNoYW5nZScsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInRlcm1zXCJdJzogJ19vbkNoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnX29uQ2hhbmdlJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGUgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctdGF4b25vbXktdGVtcGxhdGUnKTtcbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGUuaHRtbCgpKTtcblxuICAgICAgICB0aGlzLmxpc3RlblRvKHRoaXMubW9kZWwsICdjaGFuZ2U6dGF4b25vbXknLCB0aGlzLnJlbmRlcik7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcmV0dXJucyB7Q29uZmlnVGF4b25vbXl9XG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC50b0pTT04oKSkpO1xuICAgICAgICB0aGlzLl9zZWxlY3RpemUoKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgY3VycmVudCBjb25maWcgaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgICAqL1xuICAgIF9vbkNoYW5nZShlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICBsZXQgdGF4b25vbWllcyA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwidGF4b25vbXlcIl0nKTtcbiAgICAgICAgbGV0IHRlcm1zID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1zXCJdJyk7XG4gICAgICAgIGxldCBzZWxlY3RpemUgPSB0ZXJtcy5zZWxlY3RpemUoKVswXS5zZWxlY3RpemU7XG5cbiAgICAgICAgdGF4b25vbWllcy52YWwoKSA9PT0gbnVsbCB8fCB0YXhvbm9taWVzLnZhbCgpID09PSAnbm9uZScgPyBzZWxlY3RpemUuZGlzYWJsZSgpIDogc2VsZWN0aXplLmVuYWJsZSgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICd0YXhvbm9teSc6IHRheG9ub21pZXMudmFsKCkgIT09ICdub25lJyA/IHRheG9ub21pZXMudmFsKCkgOiBudWxsLFxuICAgICAgICAgICAgJ3Rlcm1zJzogdGVybXMudmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTZWxlY3RpemUgdGhlIGlucHV0IGZvciBlbmFibGluZyBhdXRvLWNvbXBsZXRpb24gYW5kIHByb2R1Y3Qgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX3NlbGVjdGl6ZSgpIHtcbiAgICAgICAgbGV0IGFwaVJvb3QgPSBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYXBpUm9vdDtcbiAgICAgICAgbGV0IG5vbmNlID0gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLm5vbmNlO1xuICAgICAgICBsZXQgdGVybXMgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwidGVybXNcIl0nKTtcblxuICAgICAgICB0ZXJtcy5zZWxlY3RpemUoe1xuICAgICAgICAgICAgZGVsaW1pdGVyOiAnLCcsXG4gICAgICAgICAgICB2YWx1ZUZpZWxkOiAnc2x1ZycsXG4gICAgICAgICAgICBsYWJlbEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBzZWFyY2hGaWVsZDogJ25hbWUnLFxuICAgICAgICAgICAgY3JlYXRlOiBmYWxzZSxcbiAgICAgICAgICAgIGxvYWQ6IChxdWVyeSwgY2FsbGJhY2spID0+IHtcbiAgICAgICAgICAgICAgICBsZXQgdGF4b25vbXkgPSB0aGlzLm1vZGVsLmdldCgndGF4b25vbXknKTtcblxuICAgICAgICAgICAgICAgIGlmICghcXVlcnkubGVuZ3RoIHx8ICF0YXhvbm9teSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGJhY2soKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBqUXVlcnkuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogYCR7YXBpUm9vdH13cC92Mi8ke3RheG9ub215fWAsXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgICAgICAgICAgICAgICBiZWZvcmVTZW5kKHhocikge1xuICAgICAgICAgICAgICAgICAgICAgICAgeGhyLnNldFJlcXVlc3RIZWFkZXIoJ1gtV1AtTm9uY2UnLCBub25jZSlcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgZXJyb3IoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBzdWNjZXNzKHJlc3VsdHMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdHMgPSByZXN1bHRzLm1hcCgocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2lkJzogcmVzdWx0LmlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnbmFtZSc6IHJlc3VsdC5uYW1lLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnc2x1Zyc6IHJlc3VsdC5zbHVnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAndGF4b25vbXknOiByZXN1bHQudGF4b25vbXksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKHJlc3VsdHMpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdUYXhvbm9teTtcbiIsImltcG9ydCBDb25maWdTaG9wIGZyb20gXCIuL2NvbmZpZy1zaG9wXCI7XG5pbXBvcnQgQ29uZmlnU3RhdHVzIGZyb20gXCIuL2NvbmZpZy1zdGF0dXNcIjtcbmltcG9ydCBDb25maWdUYXhvbm9teSBmcm9tIFwiLi9jb25maWctdGF4b25vbXlcIjtcbmltcG9ydCBDb25maWdBY3Rpb24gZnJvbSBcIi4vY29uZmlnLWFjdGlvblwiO1xuXG5sZXQgQ29uZmlnID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWcnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNob3AgPSBuZXcgQ29uZmlnU2hvcCh7bW9kZWw6IHRoaXMubW9kZWwuc2hvcH0pO1xuICAgICAgICB0aGlzLnN0YXR1cyA9IG5ldyBDb25maWdTdGF0dXMoe21vZGVsOiB0aGlzLm1vZGVsLnN0YXR1c30pO1xuICAgICAgICB0aGlzLnRheG9ub215ID0gbmV3IENvbmZpZ1RheG9ub215KHttb2RlbDogdGhpcy5tb2RlbC50YXhvbm9teX0pO1xuICAgICAgICB0aGlzLmFjdGlvbiA9IG5ldyBDb25maWdBY3Rpb24oe21vZGVsOiB0aGlzLm1vZGVsLmFjdGlvbn0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICogQHJldHVybnMge0NvbmZpZ31cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuc2hvcC5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5zdGF0dXMucmVuZGVyKCk7XG4gICAgICAgIHRoaXMudGF4b25vbXkucmVuZGVyKCk7XG4gICAgICAgIHRoaXMuYWN0aW9uLnJlbmRlcigpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnO1xuIiwiaW1wb3J0IFNlYXJjaCBmcm9tICcuL3NlYXJjaCc7XG5pbXBvcnQgQ29uZmlnIGZyb20gJy4vY29uZmlnJztcblxubGV0IEltcG9ydCA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydCcsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuc2VhcmNoID0gbmV3IFNlYXJjaCh7bW9kZWw6IHRoaXMubW9kZWwuc2VhcmNofSk7XG4gICAgICAgIHRoaXMuY29uZmlnID0gbmV3IENvbmZpZyh7bW9kZWw6IHRoaXMubW9kZWwuY29uZmlnfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuc2VhcmNoLnJlbmRlcigpO1xuICAgICAgICB0aGlzLmNvbmZpZy5yZW5kZXIoKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IEltcG9ydDtcbiIsImxldCBTZWFyY2hGb3JtID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybScsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBzZWxlY3RbbmFtZT1cInR5cGVcIl0nOiAnY2hhbmdlJyxcbiAgICAgICAgJ2NoYW5nZSBzZWxlY3RbbmFtZT1cImNhdGVnb3J5XCJdJzogJ2NoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnc3VibWl0JyxcbiAgICB9LFxuXG4gICAgaW5pdGlhbEZvY3VzOiBmYWxzZSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWZvcm0tdGVtcGxhdGUnKS5odG1sKCksXG4gICAgICAgICAgICBwcm92aWRlckNvbmZpZ3VyZWQgPSB0aGlzLiRlbC5kYXRhKCdwcm92aWRlci1jb25maWd1cmVkJyk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCgncHJvdmlkZXJDb25maWd1cmVkJywgcHJvdmlkZXJDb25maWd1cmVkID09PSB0cnVlIHx8IHByb3ZpZGVyQ29uZmlndXJlZCA9PT0gJ3RydWUnKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtTZWFyY2hGb3JtfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICBsZXQgdGVybSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJ0ZXJtXCJdJyksXG4gICAgICAgICAgICB0eXBlID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0eXBlXCJdJyksXG4gICAgICAgICAgICBjYXRlZ29yeSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nKSxcbiAgICAgICAgICAgIG1pblByaWNlID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1pbi1wcmljZVwiXScpLFxuICAgICAgICAgICAgbWF4UHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWF4LXByaWNlXCJdJyksXG4gICAgICAgICAgICBjb25kaXRpb24gPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNvbmRpdGlvblwiXScpLFxuICAgICAgICAgICAgc29ydCA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwic29ydFwiXScpLFxuICAgICAgICAgICAgd2l0aFZhcmlhbnRzID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ3aXRoLXZhcmlhbnRzXCJdJyk7XG5cbiAgICAgICAgaWYoIXRoaXMuaW5pdGlhbEZvY3VzKSB7XG4gICAgICAgICAgICB0ZXJtLmZvY3VzKCk7XG4gICAgICAgICAgICB0aGlzLmluaXRpYWxGb2N1cyA9IHRydWU7XG4gICAgICAgIH1cblxuICAgICAgICB0eXBlLnZhbCh0aGlzLm1vZGVsLmdldCgndHlwZScpKTtcbiAgICAgICAgY2F0ZWdvcnkudmFsKHRoaXMubW9kZWwuZ2V0KCdjYXRlZ29yeScpKTtcbiAgICAgICAgbWluUHJpY2UudmFsKHRoaXMubW9kZWwuZ2V0KCdtaW5QcmljZScpKTtcbiAgICAgICAgbWF4UHJpY2UudmFsKHRoaXMubW9kZWwuZ2V0KCdtYXhQcmljZScpKTtcbiAgICAgICAgY29uZGl0aW9uLnZhbCh0aGlzLm1vZGVsLmdldCgnY29uZGl0aW9uJykpO1xuICAgICAgICBzb3J0LnZhbCh0aGlzLm1vZGVsLmdldCgnc29ydCcpKTtcbiAgICAgICAgd2l0aFZhcmlhbnRzLnZhbCh0aGlzLm1vZGVsLmdldCgnd2l0aFZhcmlhbnRzJykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7RXZlbnR9IGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3VibWl0KGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuY2hhbmdlKCk7XG4gICAgICAgIHRoaXMubW9kZWwuc3VibWl0KCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzZWFyY2ggcGFyYW1ldGVycyBpbnRvIHRoZSBtb2RlbCBvbiBmb3JtIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlKCkge1xuICAgICAgICBsZXQgdGVybSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJ0ZXJtXCJdJyksXG4gICAgICAgICAgICB0eXBlID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0eXBlXCJdJyksXG4gICAgICAgICAgICBjYXRlZ29yeSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nKSxcbiAgICAgICAgICAgIG1pblByaWNlID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1pbi1wcmljZVwiXScpLFxuICAgICAgICAgICAgbWF4UHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWF4LXByaWNlXCJdJyksXG4gICAgICAgICAgICBjb25kaXRpb24gPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNvbmRpdGlvblwiXScpLFxuICAgICAgICAgICAgc29ydCA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwic29ydFwiXScpLFxuICAgICAgICAgICAgd2l0aFZhcmlhbnRzID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ3aXRoLXZhcmlhbnRzXCJdJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3Rlcm0nOiB0ZXJtLmxlbmd0aCAhPT0gMCA/IHRlcm0udmFsKCkgOiB0aGlzLm1vZGVsLmdldCgndGVybScpLFxuICAgICAgICAgICAgJ3R5cGUnOiB0eXBlLmxlbmd0aCAhPT0gMCA/IHR5cGUudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgndHlwZScpLFxuICAgICAgICAgICAgJ21pblByaWNlJzogbWluUHJpY2UubGVuZ3RoICE9PSAwID8gbWluUHJpY2UudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnbWluUHJpY2UnKSxcbiAgICAgICAgICAgICdtYXhQcmljZSc6IG1heFByaWNlLmxlbmd0aCAhPT0gMCA/IG1heFByaWNlLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ21heFByaWNlJyksXG4gICAgICAgICAgICAnY29uZGl0aW9uJzogY29uZGl0aW9uLmxlbmd0aCAhPT0gMCA/IGNvbmRpdGlvbi52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCdjb25kaXRpb24nKSxcbiAgICAgICAgICAgICdzb3J0Jzogc29ydC5sZW5ndGggIT09IDAgPyBzb3J0LnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ3NvcnQnKSxcbiAgICAgICAgICAgICdjYXRlZ29yeSc6IGNhdGVnb3J5Lmxlbmd0aCAhPT0gMCA/IGNhdGVnb3J5LnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ2NhdGVnb3J5JyksXG4gICAgICAgICAgICAnd2l0aFZhcmlhbnRzJzogd2l0aFZhcmlhbnRzLmxlbmd0aCAhPT0gMCA/IHdpdGhWYXJpYW50cy52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCd3aXRoVmFyaWFudHMnKSxcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLWxvYWQtbW9yZS1idXR0b24nOiAnbG9hZCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCBsb2FkIG1vcmUuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlLXRlbXBsYXRlJykuaHRtbCgpO1xuXG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggbG9hZCBtb3JlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm4ge1NlYXJjaExvYWRNb3JlfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRW5hYmxlIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5tb2RlbC5sb2FkKCk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaExvYWRNb3JlO1xuIiwibGV0IFNlYXJjaFJlc3VsdHNJdGVtID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIHRhZ05hbWU6ICdkaXYnLFxuXG4gICAgY2xhc3NOYW1lOiAnJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCc6ICdzaG93QWxsJyxcbiAgICAgICAgJ2NsaWNrIC5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tYWN0aW9ucy1pbXBvcnQnOiAnaW1wb3J0JyxcbiAgICAgICAgJ2NsaWNrIC5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tYWN0aW9ucy1yZWltcG9ydCc6ICdpbXBvcnQnXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXRlbXBsYXRlJykuaHRtbCgpO1xuXG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm4ge1NlYXJjaFJlc3VsdHNJdGVtfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhbGwgaGlkZGVuIHZhcmlhbnRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dBbGwoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCcpLmhpZGUoKTtcbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1pdGVtJykuc2hvdygpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIHNlYXJjaCByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5pbXBvcnQoKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0c0l0ZW07XG4iLCJpbXBvcnQgU2VhcmNoUmVzdWx0c0l0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uID0gb3B0aW9ucy5jb2xsZWN0aW9uO1xuXG4gICAgICAgIC8vIEJpbmQgdGhlIGNvbGxlY3Rpb24gZXZlbnRzXG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZXNldCcsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnYWRkJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZW1vdmUnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3N5bmMnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLl9hZGRBbGwoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIGFsbCBzZWFyY2ggcmVzdWx0cyBpdGVtcyB0byB0aGUgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRBbGwoKSB7XG4gICAgICAgIHRoaXMuJGVsLmVtcHR5KCk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5mb3JFYWNoKHRoaXMuX2FkZE9uZSwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBvbmUgc2VhcmNoIHJlc3VsdHMgaXRlbSB0byB0aGUgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRPbmUocHJvZHVjdCkge1xuICAgICAgICBsZXQgdmlldyA9IG5ldyBTZWFyY2hSZXN1bHRzSXRlbSh7XG4gICAgICAgICAgICBtb2RlbDogcHJvZHVjdCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy4kZWwuYXBwZW5kKHZpZXcucmVuZGVyKCkuZWwpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSh7bW9kZWw6IHRoaXMubW9kZWwuZm9ybX0pO1xuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cyh7Y29sbGVjdGlvbjogdGhpcy5tb2RlbC5yZXN1bHRzfSk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUgPSBuZXcgU2VhcmNoTG9hZE1vcmUoe21vZGVsOiB0aGlzLm1vZGVsLmxvYWRNb3JlfSk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLmZvcm0ucmVuZGVyKCk7XG4gICAgICAgIHRoaXMucmVzdWx0cy5yZW5kZXIoKTtcblxuICAgICAgICBpZih0aGlzLm1vZGVsLmdldCgnc3RhcnRlZCcpKSB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnJlbmRlcigpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2g7XG4iXX0=
