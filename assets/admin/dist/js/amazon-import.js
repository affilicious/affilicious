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
        var addedShops = this.model.get('addedShops');

        addedShops.push(shop);

        this.model.set({
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
        'term': null
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
            'term': this.taxonomy.get('term'),
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
                _this.config.trigger('aff:amazon-import:config:add-shop', shopTemplate);
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
        'change select[name="taxonomy"]': '_onChangeTaxonomy',
        'change select[name="term"]': '_onChangeTerm',
        'submit': '_onChangeTerm'
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

        this.listenTo(this.model, 'change', this.render);
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

        return this;
    },


    /**
     * Load the current taxonomy config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChangeTaxonomy: function _onChangeTaxonomy(e) {
        e.preventDefault();

        var taxonomies = this.$el.find('select[name="taxonomy"]');

        this.model.set({
            'taxonomy': taxonomies.val() !== 'none' ? taxonomies.val() : null,
            'term': null
        });
    },


    /**
     * Load the current term config into the model on change.
     *
     * @since 0.9.16
     * @private
     * @param {Event} e
     */
    _onChangeTerm: function _onChangeTerm(e) {
        e.preventDefault();

        var terms = this.$el.find('select[name="term"]');

        this.model.set({
            'term': terms.val() !== 'none' ? terms.val() : null
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWctYWN0aW9uLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy1zaG9wLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy1zdGF0dXMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvY29uZmlnLXRheG9ub215LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL2NvbmZpZy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWZvcm0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLWxvYWQtbW9yZS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1yZXN1bHRzLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L2NvbmZpZy1hY3Rpb24uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWctc2hvcC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L2NvbmZpZy1zdGF0dXMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWctdGF4b25vbXkuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2guanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBOzs7O0FBQ0E7Ozs7OztBQUVBLElBQUksY0FBYyxzQkFBbEI7QUFDQSxJQUFJLGFBQWEscUJBQWUsRUFBQyxPQUFPLFdBQVIsRUFBZixDQUFqQjs7QUFFQSxXQUFXLE1BQVg7Ozs7Ozs7O0FDTkEsSUFBSSxlQUFlLFNBQVMsS0FBVCxDQUFlLE1BQWYsQ0FBc0I7QUFDckMsY0FBVTtBQUNOLGtCQUFVLGFBREo7QUFFTiwwQkFBa0I7QUFGWjtBQUQyQixDQUF0QixDQUFuQjs7a0JBT2UsWTs7Ozs7Ozs7QUNQZixJQUFJLGFBQWEsU0FBUyxLQUFULENBQWUsTUFBZixDQUFzQjtBQUNuQyxjQUFVO0FBQ04sZ0JBQVEsSUFERjtBQUVOLHVCQUFlLElBRlQ7QUFHTixzQkFBYztBQUhSLEtBRHlCOztBQU9uQzs7Ozs7OztBQU9BLFdBZG1DLG1CQWMzQixJQWQyQixFQWNyQjtBQUNWLFlBQUksYUFB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LmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiaW1wb3J0IEltcG9ydCBmcm9tICcuL21vZGVsL2ltcG9ydCc7XG5pbXBvcnQgSW1wb3J0VmlldyBmcm9tICcuL3ZpZXcvaW1wb3J0JztcblxubGV0IGltcG9ydE1vZGVsID0gbmV3IEltcG9ydCgpO1xubGV0IGltcG9ydFZpZXcgPSBuZXcgSW1wb3J0Vmlldyh7bW9kZWw6IGltcG9ydE1vZGVsfSk7XG5cbmltcG9ydFZpZXcucmVuZGVyKCk7XG4iLCJsZXQgQ29uZmlnQWN0aW9uID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnYWN0aW9uJzogJ25ldy1wcm9kdWN0JyxcbiAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbnVsbCxcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ0FjdGlvbjtcbiIsImxldCBDb25maWdTaG9wID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnc2hvcCc6IG51bGwsXG4gICAgICAgICduZXdTaG9wTmFtZSc6IG51bGwsXG4gICAgICAgICdhZGRlZFNob3BzJzogW10sXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBhIG5ldyBzaG9wIHRvIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBzaG9wXG4gICAgICovXG4gICAgYWRkU2hvcChzaG9wKSB7XG4gICAgICAgIGxldCBhZGRlZFNob3BzID0gdGhpcy5tb2RlbC5nZXQoJ2FkZGVkU2hvcHMnKTtcblxuICAgICAgICBhZGRlZFNob3BzLnB1c2goc2hvcCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3Nob3AnOiBzaG9wLnNsdWcsXG4gICAgICAgICAgICAnbmV3U2hvcE5hbWUnOiBudWxsLFxuICAgICAgICAgICAgJ2FkZGVkU2hvcHMnOiBhZGRlZFNob3BzLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ1Nob3A7XG4iLCJsZXQgQ29uZmlnU3RhdHVzID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnc3RhdHVzJzogJ3B1Ymxpc2gnLFxuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnU3RhdHVzO1xuIiwibGV0IENvbmZpZ1RheG9ub215ID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAndGF4b25vbXknOiBudWxsLFxuICAgICAgICAndGVybSc6IG51bGwsXG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWdUYXhvbm9teTtcbiIsImltcG9ydCBDb25maWdTaG9wIGZyb20gXCIuL2NvbmZpZy1zaG9wXCI7XG5pbXBvcnQgQ29uZmlnU3RhdHVzIGZyb20gXCIuL2NvbmZpZy1zdGF0dXNcIjtcbmltcG9ydCBDb25maWdUYXhvbm9teSBmcm9tIFwiLi9jb25maWctdGF4b25vbXlcIjtcbmltcG9ydCBDb25maWdBY3Rpb24gZnJvbSBcIi4vY29uZmlnLWFjdGlvblwiO1xuXG5sZXQgQ29uZmlnID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGNvbmZpZyB3aXRoIGFsbCBzdWIgY29uZmlncy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5zaG9wID0gbmV3IENvbmZpZ1Nob3AoKTtcbiAgICAgICAgdGhpcy5zdGF0dXMgPSBuZXcgQ29uZmlnU3RhdHVzKCk7XG4gICAgICAgIHRoaXMudGF4b25vbXkgPSBuZXcgQ29uZmlnVGF4b25vbXkoKTtcbiAgICAgICAgdGhpcy5hY3Rpb24gPSBuZXcgQ29uZmlnQWN0aW9uKCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFBhcnNlIHRoZSBjb25maWcgaW50byBhbiBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqIEByZXR1cm5zIHt7c2hvcCwgbmV3U2hvcE5hbWUsIHN0YXR1cywgdGF4b25vbXksIHRlcm0sIGFjdGlvbiwgbWVyZ2VQcm9kdWN0SWR9fVxuICAgICAqL1xuICAgIHBhcnNlKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgJ3Nob3AnOiB0aGlzLnNob3AuZ2V0KCdzaG9wJyksXG4gICAgICAgICAgICAnbmV3U2hvcE5hbWUnOiB0aGlzLnNob3AuZ2V0KCduZXdTaG9wTmFtZScpLFxuICAgICAgICAgICAgJ3N0YXR1cyc6IHRoaXMuc3RhdHVzLmdldCgnc3RhdHVzJyksXG4gICAgICAgICAgICAndGF4b25vbXknOiB0aGlzLnRheG9ub215LmdldCgndGF4b25vbXknKSxcbiAgICAgICAgICAgICd0ZXJtJzogdGhpcy50YXhvbm9teS5nZXQoJ3Rlcm0nKSxcbiAgICAgICAgICAgICdhY3Rpb24nOiB0aGlzLmFjdGlvbi5nZXQoJ2FjdGlvbicpLFxuICAgICAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogdGhpcy5hY3Rpb24uZ2V0KCdtZXJnZVByb2R1Y3RJZCcpLFxuICAgICAgICB9XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9zZWFyY2gnO1xuaW1wb3J0IENvbmZpZyBmcm9tICcuL2NvbmZpZyc7XG5cbmxldCBJbXBvcnQgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX2ltcG9ydCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNlYXJjaCA9IG5ldyBTZWFyY2goKTtcbiAgICAgICAgdGhpcy5jb25maWcgPSBuZXcgQ29uZmlnKCk7XG5cbiAgICAgICAgdGhpcy5zZWFyY2gub24oJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCB0aGlzLmltcG9ydCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgcHJvZHVjdC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge1NlYXJjaFJlc3VsdHNJdGVtfSBzZWFyY2hSZXN1bHRzSXRlbVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQoc2VhcmNoUmVzdWx0c0l0ZW0pIHtcbiAgICAgICAgbGV0IGRhdGEgPSB7XG4gICAgICAgICAgICAncHJvZHVjdCc6IHtcbiAgICAgICAgICAgICAgICAnbmFtZScgOiBzZWFyY2hSZXN1bHRzSXRlbS5nZXQoJ25hbWUnKSxcbiAgICAgICAgICAgICAgICAndHlwZScgOiBzZWFyY2hSZXN1bHRzSXRlbS5nZXQoJ3R5cGUnKSxcbiAgICAgICAgICAgICAgICAnc2hvcHMnIDogc2VhcmNoUmVzdWx0c0l0ZW0uZ2V0KCdzaG9wcycpLFxuICAgICAgICAgICAgICAgICdjdXN0b21fdmFsdWVzJyA6IHNlYXJjaFJlc3VsdHNJdGVtLmdldCgnY3VzdG9tX3ZhbHVlcycpLFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICdjb25maWcnOiB0aGlzLmNvbmZpZy5wYXJzZSgpLFxuICAgICAgICAgICAgJ2Zvcm0nOiB0aGlzLnNlYXJjaC5mb3JtLnBhcnNlKCksXG4gICAgICAgIH07XG5cbiAgICAgICAgalF1ZXJ5LmFqYXgoe1xuICAgICAgICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgICAgICAgdXJsOiB0aGlzLl9idWlsZFVybCgpLFxuICAgICAgICAgICAgZGF0YTogZGF0YSxcbiAgICAgICAgfSkuZG9uZSgocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICBsZXQgc2hvcFRlbXBsYXRlID0gKChyZXN1bHQgfHwge30pLmRhdGEgfHwge30pLnNob3BfdGVtcGxhdGUgfHwgbnVsbDtcblxuICAgICAgICAgICAgaWYoc2hvcFRlbXBsYXRlKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5jb25maWcudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6Y29uZmlnOmFkZC1zaG9wJywgc2hvcFRlbXBsYXRlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgc2VhcmNoUmVzdWx0c0l0ZW0uc2hvd1N1Y2Nlc3NNZXNzYWdlKCk7XG4gICAgICAgIH0pLmZhaWwoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgc2VhcmNoUmVzdWx0c0l0ZW0uc2hvd0Vycm9yTWVzc2FnZShlcnJvck1lc3NhZ2UpO1xuICAgICAgICB9KVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgaW1wb3J0IHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICA7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBJbXBvcnQ7XG4iLCJsZXQgU2VhcmNoRm9ybSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3Rlcm0nOiAnJyxcbiAgICAgICAgJ3R5cGUnOiAna2V5d29yZHMnLFxuICAgICAgICAnY2F0ZWdvcnknOiAnQWxsJyxcbiAgICAgICAgJ21pblByaWNlJzogbnVsbCxcbiAgICAgICAgJ21heFByaWNlJzogbnVsbCxcbiAgICAgICAgJ2NvbmRpdGlvbic6ICdOZXcnLFxuICAgICAgICAnc29ydCc6ICctcHJpY2UnLFxuICAgICAgICAnd2l0aFZhcmlhbnRzJzogJ25vJyxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgJ3Byb3ZpZGVyQ29uZmlndXJlZCc6IGZhbHNlXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgZm9ybSB0aGUgZm9ybSBhbmQgdHJpZ2dlciB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdCgpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIHBhcnNlKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgJ3Rlcm0nOiB0aGlzLmdldCgndGVybScpLFxuICAgICAgICAgICAgJ3R5cGUnOiB0aGlzLmdldCgndHlwZScpLFxuICAgICAgICAgICAgJ2NhdGVnb3J5JzogdGhpcy5nZXQoJ3R5cGUnKSxcbiAgICAgICAgfTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRmluaXNoIHRoZSBzdWJtaXQgYW5kIHN0b3AgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBkb25lKCkge1xuICAgICAgICB0aGlzLnNldCgnbG9hZGluZycsIGZhbHNlKTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpkb25lJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEZpbmlzaCB0aGUgc2VhcmNoIHN1Ym1pdCB3aXRoIG5vIHJlc3VsdHMgYW5kIHN0b3AgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNFxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbm9SZXN1bHRzKG1lc3NhZ2UgPSBudWxsKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogdHJ1ZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmZlYmF5aXU6ZWJheS1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOm5vLXJlc3VsdHMnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhIHN1Ym1pdCBlcnJvciBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBlcnJvcihtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yJzogdHJ1ZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTplcnJvcicsIHRoaXMpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnZW5hYmxlZCc6IHRydWUsXG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdub1Jlc3VsdHMnOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBY3RpdmF0ZSB0aGUgbG9hZGluZyBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmxvYWQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyB0aGUgbG9hZCBtb3JlIGJ1dHRvbiBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtib29sZWFufSBlbmFibGVkXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoZW5hYmxlZCA9IHRydWUpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlbmFibGVkJzogZW5hYmxlZCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmRvbmUnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyB0aGUgbm8gcmVzdWx0cyBtZXNzYWdlIGFuZCBkZWFjdGl2YXRlIHRoZSBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbm9SZXN1bHRzKCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZycgOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bm8tcmVzdWx0cycsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IGEgbG9hZCBtb3JlIGVycm9yIGFuZCBkZWFjdGl2YXRlIHRoZSBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGVycm9yKG1lc3NhZ2UgPSBudWxsKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdlbmFibGVkJzogdHJ1ZSxcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3InOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTplcnJvcicsIHRoaXMpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hMb2FkTW9yZTtcbiIsImxldCBTZWFyY2hSZXN1bHRzSXRlbSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ3N1Y2Nlc3MnOiBmYWxzZSxcbiAgICAgICAgJ3N1Y2Nlc3NNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIHNlYXJjaCByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0KCkge1xuICAgICAgICB0aGlzLnNldCgnbG9hZGluZycsIHRydWUpO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTppbXBvcnQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3VjY2Vzc2Z1bGx5IGZpbmlzaCB0aGUgaW1wb3J0IHdpdGggYW4gb3B0aW9uYWwgbWVzc2FnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dTdWNjZXNzTWVzc2FnZShtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ3N1Y2Nlc3MnOiB0cnVlLFxuICAgICAgICAgICAgJ3N1Y2Nlc3NNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICAgICAnY3VzdG9tX3ZhbHVlcyc6IHtcbiAgICAgICAgICAgICAgICAnYWxyZWFkeV9pbXBvcnRlZCc6IHRydWUsXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTpzdWNjZXNzJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIERpc3BsYXkgYW4gZXJyb3IgZm9yIGltcG9ydCB3aXRoIGFuIG9wdGlvbmFsIG1lc3NhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93RXJyb3JNZXNzYWdlKG1lc3NhZ2UgPSBudWxsKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnc3VjY2Vzcyc6IGZhbHNlLFxuICAgICAgICAgICAgJ3N1Y2Nlc3NNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmVycm9yJywgdGhpcyk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRJdGVtIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuQ29sbGVjdGlvbi5leHRlbmQoe1xuICAgIG1vZGVsOiBTZWFyY2hSZXN1bHRJdGVtLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMub24oJ3N5bmMnLCB0aGlzLmluaXRJbXBvcnRMaXN0ZW5lcnMsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBQYXJzZSB0aGUgV29yZHByZXNzIGpzb24gQWpheCByZXNwb25zZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICogQHBhcmFtIHtBcnJheX0gcmVzcG9uc2VcbiAgICAgKiBAcmV0dXJucyB7QXJyYXl9XG4gICAgICovXG4gICAgcGFyc2U6IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgIHJldHVybiByZXNwb25zZSAmJiByZXNwb25zZS5zdWNjZXNzID8gcmVzcG9uc2UuZGF0YSA6IFtdO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqIEBwYXJhbSB7U2VhcmNoUmVzdWx0c0l0ZW19IHNlYXJjaFJlc3VsdHNJdGVtXG4gICAgICovXG4gICAgaW1wb3J0SXRlbShzZWFyY2hSZXN1bHRzSXRlbSkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOmltcG9ydC1pdGVtJywgc2VhcmNoUmVzdWx0c0l0ZW0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0IHRoZSBpbXBvcnQgbGlzdGVuZXJzIGZvciBhbGwgcmVzdWx0cyBpdGVtcy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdEltcG9ydExpc3RlbmVycygpIHtcbiAgICAgICAgdGhpcy5mb3JFYWNoKHRoaXMuX2luaXRJbXBvcnRMaXN0ZW5lciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXQgdGhlIGltcG9ydCBsaXN0ZW5lcnMgZm9yIHRoZSByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqIEBwYXJhbSB7U2VhcmNoUmVzdWx0c0l0ZW19IHNlYXJjaFJlc3VsdHNJdGVtXG4gICAgICovXG4gICAgX2luaXRJbXBvcnRMaXN0ZW5lcihzZWFyY2hSZXN1bHRzSXRlbSkge1xuICAgICAgICBzZWFyY2hSZXN1bHRzSXRlbS5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTppbXBvcnQnLCB0aGlzLmltcG9ydEl0ZW0sIHRoaXMpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzO1xuIiwiaW1wb3J0IFNlYXJjaEZvcm0gZnJvbSAnLi9zZWFyY2gtZm9ybSc7XG5pbXBvcnQgU2VhcmNoTG9hZE1vcmUgZnJvbSAnLi9zZWFyY2gtbG9hZC1tb3JlJztcbmltcG9ydCBTZWFyY2hSZXN1bHRzIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMnO1xuXG5sZXQgU2VhcmNoID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnc3RhcnRlZCc6IGZhbHNlLFxuICAgICAgICAnYWN0aW9uJzogJ2FmZl9wcm9kdWN0X2FkbWluX2FtYXpvbl9zZWFyY2gnLFxuICAgICAgICAncGFnZScgOiAxLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggd2l0aCB0aGUgZ2l2ZW4gb3B0aW9ucy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge2FycmF5fSBvcHRpb25zXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuZm9ybSA9IG5ldyBTZWFyY2hGb3JtKCk7XG4gICAgICAgIHRoaXMucmVzdWx0cyA9IG5ldyBTZWFyY2hSZXN1bHRzKCk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUgPSBuZXcgU2VhcmNoTG9hZE1vcmUoKTtcbiAgICAgICAgdGhpcy5wYWdlID0gb3B0aW9ucyAmJiBvcHRpb25zLnBhZ2UgPyBvcHRpb25zLnBhZ2UgOiAxO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aW1wb3J0LWl0ZW0nLCB0aGlzLmltcG9ydCwgdGhpcyk7XG4gICAgICAgIHRoaXMuZm9ybS5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOnN1Ym1pdCcsIHRoaXMuc3RhcnQsIHRoaXMpO1xuICAgICAgICB0aGlzLmxvYWRNb3JlLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmxvYWQnLCB0aGlzLmxvYWQsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdGFydCB0aGUgc2VhcmNoIHdpdGggdGhlIGZpcnN0IHBhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN0YXJ0KCkge1xuICAgICAgICBpZih0aGlzLmZvcm0uZ2V0KCd0ZXJtJykgPT09IG51bGwpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc2V0KCdwYWdlJywgMSk7XG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5mZXRjaCgpLmRvbmUoKHJlc3VsdHMpID0+IHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuc2V0KCdlbmFibGVkJywgdGhpcy5faXNMb2FkTW9yZUVuYWJsZWQocmVzdWx0cykpO1xuXG4gICAgICAgICAgICBpZih0aGlzLl9oYXNSZXN1bHRzKHJlc3VsdHMpKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5mb3JtLmRvbmUoKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhpcy5mb3JtLm5vUmVzdWx0cygpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KS5mYWlsKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgIGxldCBlcnJvck1lc3NhZ2UgPSAoKCgocmVzdWx0IHx8IHt9KS5yZXNwb25zZUpTT04gfHwge30pLmRhdGEgfHwge30pWzBdIHx8IHt9KS5tZXNzYWdlIHx8IG51bGw7XG5cbiAgICAgICAgICAgIHRoaXMuZm9ybS5lcnJvcihlcnJvck1lc3NhZ2UpO1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5zZXQoJ2VuYWJsZWQnLCBmYWxzZSk7XG4gICAgICAgIH0pLmFsd2F5cygoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLnNldCgnc3RhcnRlZCcsIHRydWUpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCBtb3JlIHNlYXJjaCByZXN1bHRzIGJ5IGluY3JlYXNpbmcgdGhlIHBhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdwYWdlJywgdGhpcy5nZXQoJ3BhZ2UnKSArIDEpO1xuICAgICAgICB0aGlzLnJlc3VsdHMudXJsID0gdGhpcy5fYnVpbGRVcmwoKTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goeydyZW1vdmUnOiBmYWxzZX0pLmRvbmUoKHJlc3VsdHMpID0+IHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuZG9uZSh0aGlzLl9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSk7XG4gICAgICAgIH0pLmZhaWwoKCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5lcnJvcihlcnJvck1lc3NhZ2UpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBnaXZlbiBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBtb2RlbFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQobW9kZWwpIHtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDppbXBvcnQtcmVzdWx0cy1pdGVtJywgbW9kZWwpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgc2VhcmNoIEFQSSB1cmwgYmFzZWQgb24gdGhlIGdpdmVuIHBhcmFtZXRlcnMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge3N0cmluZ31cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9idWlsZFVybCgpIHtcbiAgICAgICAgcmV0dXJuIGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hamF4XG4gICAgICAgICAgICArIGA/YWN0aW9uPSR7dGhpcy5nZXQoJ2FjdGlvbicpfWBcbiAgICAgICAgICAgICsgYCZ0ZXJtPSR7dGhpcy5mb3JtLmdldCgndGVybScpfWBcbiAgICAgICAgICAgICsgYCZ0eXBlPSR7dGhpcy5mb3JtLmdldCgndHlwZScpfWBcbiAgICAgICAgICAgICsgYCZjYXRlZ29yeT0ke3RoaXMuZm9ybS5nZXQoJ2NhdGVnb3J5Jyl9YFxuICAgICAgICAgICAgKyBgJm1pbi1wcmljZT0ke3RoaXMuZm9ybS5nZXQoJ21pblByaWNlJyl9YFxuICAgICAgICAgICAgKyBgJm1heC1wcmljZT0ke3RoaXMuZm9ybS5nZXQoJ21heFByaWNlJyl9YFxuICAgICAgICAgICAgKyBgJmNvbmRpdGlvbj0ke3RoaXMuZm9ybS5nZXQoJ2NvbmRpdGlvbicpfWBcbiAgICAgICAgICAgICsgYCZzb3J0PSR7dGhpcy5mb3JtLmdldCgnc29ydCcpfWBcbiAgICAgICAgICAgICsgYCZ3aXRoLXZhcmlhbnRzPSR7dGhpcy5mb3JtLmdldCgnd2l0aFZhcmlhbnRzJyl9YFxuICAgICAgICAgICAgKyBgJnBhZ2U9JHt0aGlzLmdldCgncGFnZScpfWBcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQ2hlY2sgaWYgdGhlIGxvYWQgbW9yZSBidXR0b24gaXMgZW5hYmxlZCAodmlzaWJsZSkuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtBcnJheXxudWxsfSByZXN1bHRzXG4gICAgICogQHJldHVybnMge2Jvb2x9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaXNMb2FkTW9yZUVuYWJsZWQocmVzdWx0cykge1xuICAgICAgICByZXR1cm4gKHJlc3VsdHMgJiYgcmVzdWx0cy5kYXRhICYmIHJlc3VsdHMuZGF0YS5sZW5ndGggPiAwKVxuICAgICAgICAgICAgJiYgdGhpcy5nZXQoJ3BhZ2UnKSA8IDVcbiAgICAgICAgICAgICYmIHRoaXMuZm9ybS5nZXQoJ3R5cGUnKSA9PT0gJ2tleXdvcmRzJztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQ2hlY2sgaWYgdGhlcmUgYXJlIGFueSBvdGhlciByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDEuMS4yXG4gICAgICogQHBhcmFtIHtBcnJheXxudWxsfSByZXN1bHRzXG4gICAgICogQHJldHVybnMge2Jvb2x9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaGFzUmVzdWx0cyhyZXN1bHRzKSB7XG4gICAgICAgIHJldHVybiByZXN1bHRzXG4gICAgICAgICAgICAmJiByZXN1bHRzLmRhdGFcbiAgICAgICAgICAgICYmIHJlc3VsdHMuZGF0YS5sZW5ndGggPiAwO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2g7XG4iLCJsZXQgQ29uZmlnQWN0aW9uID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctYWN0aW9uJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJhY3Rpb25cIl0nOiAnX29uQ2hhbmdlJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwibWVyZ2UtcHJvZHVjdC1pZFwiXSc6ICdfb25DaGFuZ2UnLFxuICAgICAgICAnc3VibWl0JzogJ19vbkNoYW5nZScsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLWFjdGlvbi10ZW1wbGF0ZScpO1xuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZS5odG1sKCkpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICogQHJldHVybnMge0NvbmZpZ0FjdGlvbn1cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLnRvSlNPTigpKSk7XG4gICAgICAgIHRoaXMuX3NlbGVjdGl6ZSgpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBjdXJyZW50IGNvbmZpZyBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHByaXZhdGVcbiAgICAgKiBAcGFyYW0ge0V2ZW50fSBlXG4gICAgICovXG4gICAgX29uQ2hhbmdlKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIGxldCBhY3Rpb24gPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwiYWN0aW9uXCJdOmNoZWNrZWQnKTtcbiAgICAgICAgbGV0IG1lcmdlUHJvZHVjdElkID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1lcmdlLXByb2R1Y3QtaWRcIl0nKTtcbiAgICAgICAgbGV0IG1lcmdlU2VsZWN0aXplID0gbWVyZ2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplO1xuXG4gICAgICAgIGFjdGlvbi52YWwoKSA9PT0gJ21lcmdlLXByb2R1Y3QnID8gbWVyZ2VTZWxlY3RpemUuZW5hYmxlKCkgOiBtZXJnZVNlbGVjdGl6ZS5kaXNhYmxlKCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ2FjdGlvbic6IGFjdGlvbi52YWwoKSxcbiAgICAgICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IG1lcmdlUHJvZHVjdElkLnZhbCgpLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2VsZWN0aXplIHRoZSBpbnB1dCBmb3IgZW5hYmxpbmcgYXV0by1jb21wbGV0aW9uIGFuZCBwcm9kdWN0IHNlYXJjaC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9zZWxlY3RpemUoKSB7XG4gICAgICAgIGxldCBtZXJnZVByb2R1Y3RJZCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtZXJnZS1wcm9kdWN0LWlkXCJdJyk7XG5cbiAgICAgICAgbWVyZ2VQcm9kdWN0SWQuc2VsZWN0aXplKHtcbiAgICAgICAgICAgIG1heEl0ZW1zOiAxLFxuICAgICAgICAgICAgdmFsdWVGaWVsZDogJ2lkJyxcbiAgICAgICAgICAgIGxhYmVsRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIHNlYXJjaEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBjcmVhdGU6IGZhbHNlLFxuICAgICAgICAgICAgbG9hZChxdWVyeSwgY2FsbGJhY2spIHtcbiAgICAgICAgICAgICAgICBpZiAoIXF1ZXJ5Lmxlbmd0aCkgcmV0dXJuIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgalF1ZXJ5LmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6IGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hcGlSb290ICsgJ3dwL3YyL2FmZi1wcm9kdWN0cy8/c3RhdHVzPXB1Ymxpc2gsZHJhZnQmc2VhcmNoPScgKyBxdWVyeSxcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdwb3N0X3BhcmVudCc6IDAsXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIGJlZm9yZVNlbmQoeGhyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB4aHIuc2V0UmVxdWVzdEhlYWRlcignWC1XUC1Ob25jZScsIGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5ub25jZSlcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgZXJyb3IoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBzdWNjZXNzKHJlc3VsdHMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdHMgPSByZXN1bHRzLm1hcCgocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2lkJzogcmVzdWx0LmlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnbmFtZSc6IHJlc3VsdC50aXRsZS5yZW5kZXJlZFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhyZXN1bHRzKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnQWN0aW9uO1xuIiwibGV0IENvbmZpZ1Nob3AgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1zaG9wJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJzaG9wXCJdJzogJ19vbkNoYW5nZScsXG4gICAgICAgICdibHVyIGlucHV0W25hbWU9XCJuZXctc2hvcC1uYW1lXCJdJzogJ19vbkNoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnX29uQ2hhbmdlJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLXNob3AtdGVtcGxhdGUnKS5odG1sKCk7XG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG5cbiAgICAgICAgdGhpcy5saXN0ZW5Ubyh0aGlzLm1vZGVsLCAnY2hhbmdlJywgdGhpcy5yZW5kZXIpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICogQHJldHVybnMge0NvbmZpZ1Nob3B9XG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC50b0pTT04oKSkpO1xuICAgICAgICB0aGlzLl9pbml0U2hvcCgpO1xuICAgICAgICB0aGlzLl9jaGVja1Nob3AoKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgY3VycmVudCBjb25maWcgaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgICAqL1xuICAgIF9vbkNoYW5nZShlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICBsZXQgc2hvcCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzaG9wXCJdOmNoZWNrZWQnKTtcbiAgICAgICAgbGV0IG5ld1Nob3BOYW1lID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm5ldy1zaG9wLW5hbWVcIl0nKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc2hvcCc6IHNob3AudmFsKCksXG4gICAgICAgICAgICAnbmV3U2hvcE5hbWUnOiBzaG9wLnZhbCgpID09PSAnbmV3LXNob3AnID8gbmV3U2hvcE5hbWUudmFsKCkgOiBudWxsLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQ2hlY2sgdGhlIHNlbGVjdGVkIHNob3AuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaW5pdFNob3AoKSB7XG4gICAgICAgIGxldCBzaG9wcyA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzaG9wXCJdJyk7XG5cbiAgICAgICAgaWYodGhpcy5tb2RlbC5nZXQoJ3Nob3AnKSA9PSBudWxsKSB7XG4gICAgICAgICAgICB0aGlzLm1vZGVsLnNldCgnc2hvcCcsIHNob3BzLmZpcnN0KCkudmFsKCkpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIENoZWNrIHRoZSBzZWxlY3RlZCBzaG9wLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2NoZWNrU2hvcCgpIHtcbiAgICAgICAgbGV0IHNob3BzID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInNob3BcIl0nKTtcbiAgICAgICAgbGV0IHNob3AgPSB0aGlzLm1vZGVsLmdldCgnc2hvcCcpID09IG51bGwgPyBzaG9wcy5maXJzdCgpLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ3Nob3AnKTtcblxuICAgICAgICBzaG9wcy52YWwoW3Nob3BdKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnU2hvcDtcbiIsImxldCBDb25maWdTdGF0dXMgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1zdGF0dXMnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInN0YXR1c1wiXSc6ICdfb25DaGFuZ2UnLFxuICAgICAgICAnc3VibWl0JzogJ19vbkNoYW5nZScsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1zdGF0dXMtdGVtcGxhdGUnKS5odG1sKCk7XG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG5cbiAgICAgICAgdGhpcy5saXN0ZW5Ubyh0aGlzLm1vZGVsLCAnY2hhbmdlJywgdGhpcy5yZW5kZXIpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHVibGljXG4gICAgICogQHJldHVybnMge0NvbmZpZ1N0YXR1c31cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLnRvSlNPTigpKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIGN1cnJlbnQgY29uZmlnIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqIEBwYXJhbSB7RXZlbnR9IGVcbiAgICAgKi9cbiAgICBfb25DaGFuZ2UoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgbGV0IHN0YXR1cyA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzdGF0dXNcIl06Y2hlY2tlZCcpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzdGF0dXMnOiBzdGF0dXMudmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnU3RhdHVzO1xuIiwibGV0IENvbmZpZ1RheG9ub215ID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctdGF4b25vbXknLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2Ugc2VsZWN0W25hbWU9XCJ0YXhvbm9teVwiXSc6ICdfb25DaGFuZ2VUYXhvbm9teScsXG4gICAgICAgICdjaGFuZ2Ugc2VsZWN0W25hbWU9XCJ0ZXJtXCJdJzogJ19vbkNoYW5nZVRlcm0nLFxuICAgICAgICAnc3VibWl0JzogJ19vbkNoYW5nZVRlcm0nLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZSA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy10YXhvbm9teS10ZW1wbGF0ZScpO1xuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZS5odG1sKCkpO1xuXG4gICAgICAgIHRoaXMubGlzdGVuVG8odGhpcy5tb2RlbCwgJ2NoYW5nZScsIHRoaXMucmVuZGVyKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqIEByZXR1cm5zIHtDb25maWdUYXhvbm9teX1cbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLnRvSlNPTigpKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIGN1cnJlbnQgdGF4b25vbXkgY29uZmlnIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTZcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqIEBwYXJhbSB7RXZlbnR9IGVcbiAgICAgKi9cbiAgICBfb25DaGFuZ2VUYXhvbm9teShlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICBsZXQgdGF4b25vbWllcyA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwidGF4b25vbXlcIl0nKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAndGF4b25vbXknOiB0YXhvbm9taWVzLnZhbCgpICE9PSAnbm9uZScgPyB0YXhvbm9taWVzLnZhbCgpIDogbnVsbCxcbiAgICAgICAgICAgICd0ZXJtJzogbnVsbCxcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIGN1cnJlbnQgdGVybSBjb25maWcgaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgICAqL1xuICAgIF9vbkNoYW5nZVRlcm0oZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgbGV0IHRlcm1zID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0ZXJtXCJdJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3Rlcm0nOiB0ZXJtcy52YWwoKSAhPT0gJ25vbmUnID8gdGVybXMudmFsKCkgOiBudWxsLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZ1RheG9ub215O1xuIiwiaW1wb3J0IENvbmZpZ1Nob3AgZnJvbSBcIi4vY29uZmlnLXNob3BcIjtcbmltcG9ydCBDb25maWdTdGF0dXMgZnJvbSBcIi4vY29uZmlnLXN0YXR1c1wiO1xuaW1wb3J0IENvbmZpZ1RheG9ub215IGZyb20gXCIuL2NvbmZpZy10YXhvbm9teVwiO1xuaW1wb3J0IENvbmZpZ0FjdGlvbiBmcm9tIFwiLi9jb25maWctYWN0aW9uXCI7XG5cbmxldCBDb25maWcgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZycsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45LjE2XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuc2hvcCA9IG5ldyBDb25maWdTaG9wKHttb2RlbDogdGhpcy5tb2RlbC5zaG9wfSk7XG4gICAgICAgIHRoaXMuc3RhdHVzID0gbmV3IENvbmZpZ1N0YXR1cyh7bW9kZWw6IHRoaXMubW9kZWwuc3RhdHVzfSk7XG4gICAgICAgIHRoaXMudGF4b25vbXkgPSBuZXcgQ29uZmlnVGF4b25vbXkoe21vZGVsOiB0aGlzLm1vZGVsLnRheG9ub215fSk7XG4gICAgICAgIHRoaXMuYWN0aW9uID0gbmV3IENvbmZpZ0FjdGlvbih7bW9kZWw6IHRoaXMubW9kZWwuYWN0aW9ufSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcmV0dXJucyB7Q29uZmlnfVxuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zaG9wLnJlbmRlcigpO1xuICAgICAgICB0aGlzLnN0YXR1cy5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy50YXhvbm9teS5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5hY3Rpb24ucmVuZGVyKCk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWc7XG4iLCJpbXBvcnQgU2VhcmNoIGZyb20gJy4vc2VhcmNoJztcbmltcG9ydCBDb25maWcgZnJvbSAnLi9jb25maWcnO1xuXG5sZXQgSW1wb3J0ID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0JyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5zZWFyY2ggPSBuZXcgU2VhcmNoKHttb2RlbDogdGhpcy5tb2RlbC5zZWFyY2h9KTtcbiAgICAgICAgdGhpcy5jb25maWcgPSBuZXcgQ29uZmlnKHttb2RlbDogdGhpcy5tb2RlbC5jb25maWd9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zZWFyY2gucmVuZGVyKCk7XG4gICAgICAgIHRoaXMuY29uZmlnLnJlbmRlcigpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgSW1wb3J0O1xuIiwibGV0IFNlYXJjaEZvcm0gPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwidHlwZVwiXSc6ICdjaGFuZ2UnLFxuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nOiAnY2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdzdWJtaXQnLFxuICAgIH0sXG5cbiAgICBpbml0aWFsRm9jdXM6IGZhbHNlLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybS10ZW1wbGF0ZScpLmh0bWwoKSxcbiAgICAgICAgICAgIHByb3ZpZGVyQ29uZmlndXJlZCA9IHRoaXMuJGVsLmRhdGEoJ3Byb3ZpZGVyLWNvbmZpZ3VyZWQnKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdwcm92aWRlckNvbmZpZ3VyZWQnLCBwcm92aWRlckNvbmZpZ3VyZWQgPT09IHRydWUgfHwgcHJvdmlkZXJDb25maWd1cmVkID09PSAndHJ1ZScpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge1NlYXJjaEZvcm19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIGxldCB0ZXJtID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1cIl0nKSxcbiAgICAgICAgICAgIHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLFxuICAgICAgICAgICAgbWluUHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWluLXByaWNlXCJdJyksXG4gICAgICAgICAgICBtYXhQcmljZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtYXgtcHJpY2VcIl0nKSxcbiAgICAgICAgICAgIGNvbmRpdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY29uZGl0aW9uXCJdJyksXG4gICAgICAgICAgICBzb3J0ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJzb3J0XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICBpZighdGhpcy5pbml0aWFsRm9jdXMpIHtcbiAgICAgICAgICAgIHRlcm0uZm9jdXMoKTtcbiAgICAgICAgICAgIHRoaXMuaW5pdGlhbEZvY3VzID0gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHR5cGUudmFsKHRoaXMubW9kZWwuZ2V0KCd0eXBlJykpO1xuICAgICAgICBjYXRlZ29yeS52YWwodGhpcy5tb2RlbC5nZXQoJ2NhdGVnb3J5JykpO1xuICAgICAgICBtaW5QcmljZS52YWwodGhpcy5tb2RlbC5nZXQoJ21pblByaWNlJykpO1xuICAgICAgICBtYXhQcmljZS52YWwodGhpcy5tb2RlbC5nZXQoJ21heFByaWNlJykpO1xuICAgICAgICBjb25kaXRpb24udmFsKHRoaXMubW9kZWwuZ2V0KCdjb25kaXRpb24nKSk7XG4gICAgICAgIHNvcnQudmFsKHRoaXMubW9kZWwuZ2V0KCdzb3J0JykpO1xuICAgICAgICB3aXRoVmFyaWFudHMudmFsKHRoaXMubW9kZWwuZ2V0KCd3aXRoVmFyaWFudHMnKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdWJtaXQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5jaGFuZ2UoKTtcbiAgICAgICAgdGhpcy5tb2RlbC5zdWJtaXQoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHNlYXJjaCBwYXJhbWV0ZXJzIGludG8gdGhlIG1vZGVsIG9uIGZvcm0gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2UoKSB7XG4gICAgICAgIGxldCB0ZXJtID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1cIl0nKSxcbiAgICAgICAgICAgIHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLFxuICAgICAgICAgICAgbWluUHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWluLXByaWNlXCJdJyksXG4gICAgICAgICAgICBtYXhQcmljZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtYXgtcHJpY2VcIl0nKSxcbiAgICAgICAgICAgIGNvbmRpdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY29uZGl0aW9uXCJdJyksXG4gICAgICAgICAgICBzb3J0ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJzb3J0XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAndGVybSc6IHRlcm0ubGVuZ3RoICE9PSAwID8gdGVybS52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCd0ZXJtJyksXG4gICAgICAgICAgICAndHlwZSc6IHR5cGUubGVuZ3RoICE9PSAwID8gdHlwZS52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCd0eXBlJyksXG4gICAgICAgICAgICAnbWluUHJpY2UnOiBtaW5QcmljZS5sZW5ndGggIT09IDAgPyBtaW5QcmljZS52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCdtaW5QcmljZScpLFxuICAgICAgICAgICAgJ21heFByaWNlJzogbWF4UHJpY2UubGVuZ3RoICE9PSAwID8gbWF4UHJpY2UudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnbWF4UHJpY2UnKSxcbiAgICAgICAgICAgICdjb25kaXRpb24nOiBjb25kaXRpb24ubGVuZ3RoICE9PSAwID8gY29uZGl0aW9uLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ2NvbmRpdGlvbicpLFxuICAgICAgICAgICAgJ3NvcnQnOiBzb3J0Lmxlbmd0aCAhPT0gMCA/IHNvcnQudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnc29ydCcpLFxuICAgICAgICAgICAgJ2NhdGVnb3J5JzogY2F0ZWdvcnkubGVuZ3RoICE9PSAwID8gY2F0ZWdvcnkudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnY2F0ZWdvcnknKSxcbiAgICAgICAgICAgICd3aXRoVmFyaWFudHMnOiB3aXRoVmFyaWFudHMubGVuZ3RoICE9PSAwID8gd2l0aFZhcmlhbnRzLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ3dpdGhWYXJpYW50cycpLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaEZvcm07XG4iLCJsZXQgU2VhcmNoTG9hZE1vcmUgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1sb2FkLW1vcmUnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlLWJ1dHRvbic6ICdsb2FkJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1sb2FkLW1vcmUtdGVtcGxhdGUnKS5odG1sKCk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCBsb2FkIG1vcmUuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybiB7U2VhcmNoTG9hZE1vcmV9XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBFbmFibGUgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBsb2FkKCkge1xuICAgICAgICB0aGlzLm1vZGVsLmxvYWQoKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgdGFnTmFtZTogJ2RpdicsXG5cbiAgICBjbGFzc05hbWU6ICcnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJzogJ3Nob3dBbGwnLFxuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS1hY3Rpb25zLWltcG9ydCc6ICdpbXBvcnQnLFxuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS1hY3Rpb25zLXJlaW1wb3J0JzogJ2ltcG9ydCdcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdGVtcGxhdGUnKS5odG1sKCk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybiB7U2VhcmNoUmVzdWx0c0l0ZW19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IGFsbCBoaWRkZW4gdmFyaWFudHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc2hvd0FsbChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJykuaGlkZSgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLWl0ZW0nKS5zaG93KCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgc2VhcmNoIHJlc3VsdCBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLmltcG9ydCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRzSXRlbSBmcm9tICcuL3NlYXJjaC1yZXN1bHRzLWl0ZW0nO1xuXG5sZXQgU2VhcmNoUmVzdWx0cyA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cycsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge2FycmF5fSBvcHRpb25zXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24gPSBvcHRpb25zLmNvbGxlY3Rpb247XG5cbiAgICAgICAgLy8gQmluZCB0aGUgY29sbGVjdGlvbiBldmVudHNcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3Jlc2V0JywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdhZGQnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3JlbW92ZScsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnc3luYycsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuX2FkZEFsbCgpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgYWxsIHNlYXJjaCByZXN1bHRzIGl0ZW1zIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2FkZEFsbCgpIHtcbiAgICAgICAgdGhpcy4kZWwuZW1wdHkoKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmZvckVhY2godGhpcy5fYWRkT25lLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIG9uZSBzZWFyY2ggcmVzdWx0cyBpdGVtIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2FkZE9uZShwcm9kdWN0KSB7XG4gICAgICAgIGxldCB2aWV3ID0gbmV3IFNlYXJjaFJlc3VsdHNJdGVtKHtcbiAgICAgICAgICAgIG1vZGVsOiBwcm9kdWN0LFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLiRlbC5hcHBlbmQodmlldy5yZW5kZXIoKS5lbCk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzO1xuIiwiaW1wb3J0IFNlYXJjaEZvcm0gZnJvbSAnLi9zZWFyY2gtZm9ybSc7XG5pbXBvcnQgU2VhcmNoTG9hZE1vcmUgZnJvbSAnLi9zZWFyY2gtbG9hZC1tb3JlJztcbmltcG9ydCBTZWFyY2hSZXN1bHRzIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMnO1xuXG5sZXQgU2VhcmNoID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaCcsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuZm9ybSA9IG5ldyBTZWFyY2hGb3JtKHttb2RlbDogdGhpcy5tb2RlbC5mb3JtfSk7XG4gICAgICAgIHRoaXMucmVzdWx0cyA9IG5ldyBTZWFyY2hSZXN1bHRzKHtjb2xsZWN0aW9uOiB0aGlzLm1vZGVsLnJlc3VsdHN9KTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSh7bW9kZWw6IHRoaXMubW9kZWwubG9hZE1vcmV9KTtcblxuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuZm9ybS5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnJlbmRlcigpO1xuXG4gICAgICAgIGlmKHRoaXMubW9kZWwuZ2V0KCdzdGFydGVkJykpIHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUucmVuZGVyKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiJdfQ==
