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
     * @param product
     * @public
     */
    import: function _import(product) {
        var _this = this;

        var data = {
            'product': {
                'name': product.attributes.name,
                'type': product.attributes.type,
                'shops': product.attributes.shops,
                'custom_values': product.attributes.custom_values
            },
            'config': this.config.attributes,
            'form': this.search.form.attributes
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

            product.showSuccessMessage();
        }).fail(function (result) {
            var errorMessage = ((((result || {}).responseJSON || {}).data || {})[0] || {}).message || null;

            product.showErrorMessage(errorMessage);
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

},{}],5:[function(require,module,exports){
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

},{}],6:[function(require,module,exports){
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
            'successMessage': message
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
            'error': true,
            'errorMessage': message
        });

        this.trigger('aff:amazon-import:search:results:item:error', this);
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

},{"./search-form":4,"./search-load-more":5,"./search-results":7}],9:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var Config = Backbone.View.extend({
    el: '#aff-amazon-import-config',

    events: {
        'change input[name="shop"]': 'changeShop',
        'change input[name="new-shop-name"]': 'changeShop',
        'change input[name="action"]': 'changeAction',
        'change input[name="merge-product-id"]': 'changeAction',
        'change input[name="replace-product-id"]': 'changeAction',
        'change input[name="status"]': 'changeStatus'
    },

    /**
     * Initialize the config.
     *
     * @since 0.9
     * @public
     */
    initialize: function initialize() {
        var template = jQuery('#aff-amazon-import-config-template');
        this.template = _.template(template.html());

        this.model.set('selectedShop', jQuery(template.html()).find('input[name="shop"]').first().val());

        this.model.on('aff:amazon-import:config:add-shop', this.addShop, this);
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

        return this;
    },


    /**
     * Add a new shop
     *
     * @since 0.9
     * @param {Object} shop
     * @public
     */
    addShop: function addShop(shop) {
        this.$el.find('input[value="new-shop"]').parent().before('\n            <label class="aff-import-config-group-label" for="' + shop.slug + '">\n                <input id="' + shop.slug + '" class="aff-import-config-group-option" name="shop" type="radio" value="' + shop.slug + '">\n                ' + shop.name + '         \n            </label>\n        ');

        this.$el.find('input[name="shop"][value="' + shop.slug + '"]').prop("checked", true);
        this.model.set({
            'newShopName': null,
            'selectedShop': shop.slug
        });
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
        var selectedStatus = this.$el.find('input[name="status"]:checked');

        this.model.set({
            'status': selectedStatus.val()
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
    el: '#aff-amazon-import',

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
    el: '#aff-amazon-import-search-form',

    events: {
        'change select[name="type"]': 'change',
        'change select[name="category"]': 'change',
        'change select[name="with-variants"]': 'change',
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

        var type = this.$el.find('select[name="type"]'),
            category = this.$el.find('select[name="category"]'),
            withVariants = this.$el.find('select[name="with-variants"]');

        type.val(this.model.get('type'));
        category.val(this.model.get('category'));
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
            withVariants = this.$el.find('select[name="with-variants"]');

        this.model.set({
            'term': term.val(),
            'type': type.val(),
            'category': category.length !== 0 ? category.val() : this.model.get('category'),
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

},{}],13:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var SearchResultsItem = Backbone.View.extend({
    tagName: 'div',

    className: '',

    events: {
        'click .aff-import-search-results-item-variants-show-all': 'showAll',
        'click .aff-import-search-results-item-actions-import': 'import'
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

},{}],14:[function(require,module,exports){
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
    el: '#aff-amazon-import-search',

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvaW1wb3J0LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLXJlc3VsdHMtaXRlbS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2guanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9z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O0FBSUEsYUFBSyxRQUFMLEdBQWdCLDZCQUFtQjtBQUMvQixtQkFBTyxLQUFLLEtBQUwsQ0FBVztBQURhLFNBQW5CLENBQWhCOztBQUlBLGFBQUssS0FBTCxDQUFXLEVBQVgsQ0FBYyxRQUFkLEVBQXdCLEtBQUssTUFBN0IsRUFBcUMsSUFBckM7QUFDSCxLQXZCNkI7OztBQXlCOUI7Ozs7OztBQU1BLFVBL0I4QixvQkErQnJCO0FBQ0wsYUFBSyxJQUFMLENBQVUsTUFBVjtBQUNBLGFBQUssT0FBTCxDQUFhLE1BQWI7O0FBRUEsWUFBRyxLQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWUsU0FBZixDQUFILEVBQThCO0FBQzFCLGlCQUFLLFFBQUwsQ0FBYyxNQUFkO0FBQ0g7O0FBRUQsZUFBTyxJQUFQO0FBQ0g7QUF4QzZCLENBQXJCLENBQWI7O2tCQTJDZSxNIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsImltcG9ydCBJbXBvcnQgZnJvbSAnLi9tb2RlbC9pbXBvcnQnO1xuaW1wb3J0IEltcG9ydFZpZXcgZnJvbSAnLi92aWV3L2ltcG9ydCc7XG5cbmxldCBpbXBvcnRNb2RlbCA9IG5ldyBJbXBvcnQoKTtcbmxldCBpbXBvcnRWaWV3ID0gbmV3IEltcG9ydFZpZXcoe21vZGVsOiBpbXBvcnRNb2RlbH0pO1xuXG5pbXBvcnRWaWV3LnJlbmRlcigpO1xuIiwibGV0IENvbmZpZyA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3NlbGVjdGVkU2hvcCc6ICdhbWF6b24nLFxuICAgICAgICAnbmV3U2hvcE5hbWUnOiBudWxsLFxuICAgICAgICAnc2VsZWN0ZWRBY3Rpb24nOiAnbmV3LXByb2R1Y3QnLFxuICAgICAgICAnbWVyZ2VQcm9kdWN0SWQnOiBudWxsLFxuICAgICAgICAncmVwbGFjZVByb2R1Y3RJZCc6IG51bGwsXG4gICAgICAgICdzdGF0dXMnOiAnZHJhZnQnLFxuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnO1xuIiwiaW1wb3J0IFNlYXJjaCBmcm9tICcuL3NlYXJjaCc7XG5pbXBvcnQgQ29uZmlnIGZyb20gJy4vY29uZmlnJztcblxubGV0IEltcG9ydCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2FjdGlvbic6ICdhZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25faW1wb3J0JyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuc2VhcmNoID0gbmV3IFNlYXJjaCgpO1xuICAgICAgICB0aGlzLmNvbmZpZyA9IG5ldyBDb25maWcoKTtcblxuICAgICAgICB0aGlzLnNlYXJjaC5vbignYWZmOmFtYXpvbi1pbXBvcnQ6aW1wb3J0LXJlc3VsdHMtaXRlbScsIHRoaXMuaW1wb3J0LCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBwcm9kdWN0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBwcm9kdWN0XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChwcm9kdWN0KSB7XG4gICAgICAgIGxldCBkYXRhID0ge1xuICAgICAgICAgICAgJ3Byb2R1Y3QnOiB7XG4gICAgICAgICAgICAgICAgJ25hbWUnIDogcHJvZHVjdC5hdHRyaWJ1dGVzLm5hbWUsXG4gICAgICAgICAgICAgICAgJ3R5cGUnIDogcHJvZHVjdC5hdHRyaWJ1dGVzLnR5cGUsXG4gICAgICAgICAgICAgICAgJ3Nob3BzJyA6IHByb2R1Y3QuYXR0cmlidXRlcy5zaG9wcyxcbiAgICAgICAgICAgICAgICAnY3VzdG9tX3ZhbHVlcycgOiBwcm9kdWN0LmF0dHJpYnV0ZXMuY3VzdG9tX3ZhbHVlcyxcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAnY29uZmlnJzogdGhpcy5jb25maWcuYXR0cmlidXRlcyxcbiAgICAgICAgICAgICdmb3JtJzogdGhpcy5zZWFyY2guZm9ybS5hdHRyaWJ1dGVzLFxuICAgICAgICB9O1xuXG4gICAgICAgIGpRdWVyeS5hamF4KHtcbiAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgICAgICAgIHVybDogdGhpcy5fYnVpbGRVcmwoKSxcbiAgICAgICAgICAgIGRhdGE6IGRhdGEsXG4gICAgICAgIH0pLmRvbmUoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IHNob3BUZW1wbGF0ZSA9ICgocmVzdWx0IHx8IHt9KS5kYXRhIHx8IHt9KS5zaG9wX3RlbXBsYXRlIHx8IG51bGw7XG5cbiAgICAgICAgICAgIGlmKHNob3BUZW1wbGF0ZSkge1xuICAgICAgICAgICAgICAgIHRoaXMuY29uZmlnLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OmNvbmZpZzphZGQtc2hvcCcsIHNob3BUZW1wbGF0ZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHByb2R1Y3Quc2hvd1N1Y2Nlc3NNZXNzYWdlKCk7XG4gICAgICAgIH0pLmZhaWwoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgcHJvZHVjdC5zaG93RXJyb3JNZXNzYWdlKGVycm9yTWVzc2FnZSk7XG4gICAgICAgIH0pXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBpbXBvcnQgdXJsIGJhc2VkIG9uIHRoZSBnaXZlbiBwYXJhbWV0ZXJzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYnVpbGRVcmwoKSB7XG4gICAgICAgIHJldHVybiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYWpheFxuICAgICAgICAgICAgKyBgP2FjdGlvbj0ke3RoaXMuZ2V0KCdhY3Rpb24nKX1gXG4gICAgICAgIDtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IEltcG9ydDtcbiIsImxldCBTZWFyY2hGb3JtID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAndGVybSc6ICcnLFxuICAgICAgICAndHlwZSc6ICdrZXl3b3JkcycsXG4gICAgICAgICdjYXRlZ29yeSc6ICdBbGwnLFxuICAgICAgICAnd2l0aFZhcmlhbnRzJzogJ25vJyxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgJ3Byb3ZpZGVyQ29uZmlndXJlZCc6IGZhbHNlXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgZm9ybSB0aGUgZm9ybSBhbmQgdHJpZ2dlciB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdCgpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEZpbmlzaCB0aGUgc3VibWl0IGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZG9uZSgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCBmYWxzZSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZG9uZScsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBGaW5pc2ggdGhlIHNlYXJjaCBzdWJtaXQgd2l0aCBubyByZXN1bHRzIGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTRcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cyhtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ25vUmVzdWx0cyc6IHRydWUsXG4gICAgICAgICAgICAnbm9SZXN1bHRzTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmZWJheWl1OmViYXktaW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpuby1yZXN1bHRzJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYSBzdWJtaXQgZXJyb3IgYW5kIHN0b3AgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZXJyb3IobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hMb2FkTW9yZSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2VuYWJsZWQnOiB0cnVlLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWN0aXZhdGUgdGhlIGxvYWRpbmcgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgdHJ1ZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIGxvYWQgbW9yZSBidXR0b24gYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gZW5hYmxlZFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBkb25lKGVuYWJsZWQgPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZW5hYmxlZCc6IGVuYWJsZWQsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpkb25lJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIG5vIHJlc3VsdHMgbWVzc2FnZSBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cygpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnIDogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOm5vLXJlc3VsdHMnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhIGxvYWQgbW9yZSBlcnJvciBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBlcnJvcihtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnZW5hYmxlZCc6IHRydWUsXG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yJzogdHJ1ZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdzdWNjZXNzJzogZmFsc2UsXG4gICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBzZWFyY2ggcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06aW1wb3J0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Y2Nlc3NmdWxseSBmaW5pc2ggdGhlIGltcG9ydCB3aXRoIGFuIG9wdGlvbmFsIG1lc3NhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93U3VjY2Vzc01lc3NhZ2UobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdzdWNjZXNzJzogdHJ1ZSxcbiAgICAgICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG1lc3NhZ2VcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOnN1Y2Nlc3MnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRGlzcGxheSBhbiBlcnJvciBmb3IgaW1wb3J0IHdpdGggYW4gb3B0aW9uYWwgbWVzc2FnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dFcnJvck1lc3NhZ2UobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmVycm9yJywgdGhpcyk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRJdGVtIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuQ29sbGVjdGlvbi5leHRlbmQoe1xuICAgIG1vZGVsOiBTZWFyY2hSZXN1bHRJdGVtLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMub24oJ3N5bmMnLCB0aGlzLmluaXRJbXBvcnRMaXN0ZW5lcnMsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBQYXJzZSB0aGUgV29yZHByZXNzIGpzb24gQWpheCByZXNwb25zZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge0FycmF5fSByZXNwb25zZVxuICAgICAqIEByZXR1cm5zIHtBcnJheX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcGFyc2U6IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgIHJldHVybiByZXNwb25zZSAmJiByZXNwb25zZS5zdWNjZXNzID8gcmVzcG9uc2UuZGF0YSA6IFtdO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydEl0ZW0obW9kZWwpIHtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppbXBvcnQtaXRlbScsIG1vZGVsKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdCB0aGUgaW1wb3J0IGxpc3RlbmVycyBmb3IgYWxsIHJlc3VsdHMgaXRlbXMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRJbXBvcnRMaXN0ZW5lcnMoKSB7XG4gICAgICAgIHRoaXMuZm9yRWFjaCh0aGlzLl9pbml0SW1wb3J0TGlzdGVuZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0IHRoZSBpbXBvcnQgbGlzdGVuZXJzIGZvciB0aGUgcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaW5pdEltcG9ydExpc3RlbmVyKG1vZGVsKSB7XG4gICAgICAgIG1vZGVsLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmltcG9ydCcsIHRoaXMuaW1wb3J0SXRlbSwgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hMb2FkTW9yZSBmcm9tICcuL3NlYXJjaC1sb2FkLW1vcmUnO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzdGFydGVkJzogZmFsc2UsXG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX3NlYXJjaCcsXG4gICAgICAgICdwYWdlJyA6IDEsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCB3aXRoIHRoZSBnaXZlbiBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IG9wdGlvbnNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5mb3JtID0gbmV3IFNlYXJjaEZvcm0oKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzID0gbmV3IFNlYXJjaFJlc3VsdHMoKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSgpO1xuICAgICAgICB0aGlzLnBhZ2UgPSBvcHRpb25zICYmIG9wdGlvbnMucGFnZSA/IG9wdGlvbnMucGFnZSA6IDE7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppbXBvcnQtaXRlbScsIHRoaXMuaW1wb3J0LCB0aGlzKTtcbiAgICAgICAgdGhpcy5mb3JtLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcy5zdGFydCwgdGhpcyk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bG9hZCcsIHRoaXMubG9hZCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN0YXJ0IHRoZSBzZWFyY2ggd2l0aCB0aGUgZmlyc3QgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3RhcnQoKSB7XG4gICAgICAgIGlmKHRoaXMuZm9ybS5nZXQoJ3Rlcm0nKSA9PT0gbnVsbCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCAxKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnVybCA9IHRoaXMuX2J1aWxkVXJsKCk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLmZldGNoKCkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5zZXQoJ2VuYWJsZWQnLCB0aGlzLl9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSk7XG5cbiAgICAgICAgICAgIGlmKHRoaXMuX2hhc1Jlc3VsdHMocmVzdWx0cykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0uZG9uZSgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0ubm9SZXN1bHRzKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pLmZhaWwoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgdGhpcy5mb3JtLmVycm9yKGVycm9yTWVzc2FnZSk7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnNldCgnZW5hYmxlZCcsIGZhbHNlKTtcbiAgICAgICAgfSkuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2V0KCdzdGFydGVkJywgdHJ1ZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIG1vcmUgc2VhcmNoIHJlc3VsdHMgYnkgaW5jcmVhc2luZyB0aGUgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCB0aGlzLmdldCgncGFnZScpICsgMSk7XG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5mZXRjaCh7J3JlbW92ZSc6IGZhbHNlfSkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5kb25lKHRoaXMuX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpKTtcbiAgICAgICAgfSkuZmFpbCgoKSA9PiB7XG4gICAgICAgICAgICBsZXQgZXJyb3JNZXNzYWdlID0gKCgoKHJlc3VsdCB8fCB7fSkucmVzcG9uc2VKU09OIHx8IHt9KS5kYXRhIHx8IHt9KVswXSB8fCB7fSkubWVzc2FnZSB8fCBudWxsO1xuXG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLmVycm9yKGVycm9yTWVzc2FnZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChtb2RlbCkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCBtb2RlbCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBzZWFyY2ggQVBJIHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnRlcm09JHt0aGlzLmZvcm0uZ2V0KCd0ZXJtJyl9YFxuICAgICAgICAgICAgKyBgJnR5cGU9JHt0aGlzLmZvcm0uZ2V0KCd0eXBlJyl9YFxuICAgICAgICAgICAgKyBgJmNhdGVnb3J5PSR7dGhpcy5mb3JtLmdldCgnY2F0ZWdvcnknKX1gXG4gICAgICAgICAgICArIGAmd2l0aC12YXJpYW50cz0ke3RoaXMuZm9ybS5nZXQoJ3dpdGhWYXJpYW50cycpfWBcbiAgICAgICAgICAgICsgYCZwYWdlPSR7dGhpcy5nZXQoJ3BhZ2UnKX1gXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIENoZWNrIGlmIHRoZSBsb2FkIG1vcmUgYnV0dG9uIGlzIGVuYWJsZWQgKHZpc2libGUpLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7QXJyYXl8bnVsbH0gcmVzdWx0c1xuICAgICAqIEByZXR1cm5zIHtib29sfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpIHtcbiAgICAgICAgcmV0dXJuIChyZXN1bHRzICYmIHJlc3VsdHMuZGF0YSAmJiByZXN1bHRzLmRhdGEubGVuZ3RoID4gMClcbiAgICAgICAgICAgICYmIHRoaXMuZ2V0KCdwYWdlJykgPCA1XG4gICAgICAgICAgICAmJiB0aGlzLmZvcm0uZ2V0KCd0eXBlJykgPT09ICdrZXl3b3Jkcyc7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIENoZWNrIGlmIHRoZXJlIGFyZSBhbnkgb3RoZXIgcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAxLjEuMlxuICAgICAqIEBwYXJhbSB7QXJyYXl8bnVsbH0gcmVzdWx0c1xuICAgICAqIEByZXR1cm5zIHtib29sfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2hhc1Jlc3VsdHMocmVzdWx0cykge1xuICAgICAgICByZXR1cm4gcmVzdWx0c1xuICAgICAgICAgICAgJiYgcmVzdWx0cy5kYXRhXG4gICAgICAgICAgICAmJiByZXN1bHRzLmRhdGEubGVuZ3RoID4gMDtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoO1xuIiwibGV0IENvbmZpZyA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJzaG9wXCJdJzogJ2NoYW5nZVNob3AnLFxuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJuZXctc2hvcC1uYW1lXCJdJzogJ2NoYW5nZVNob3AnLFxuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJhY3Rpb25cIl0nOiAnY2hhbmdlQWN0aW9uJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwibWVyZ2UtcHJvZHVjdC1pZFwiXSc6ICdjaGFuZ2VBY3Rpb24nLFxuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJyZXBsYWNlLXByb2R1Y3QtaWRcIl0nOiAnY2hhbmdlQWN0aW9uJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwic3RhdHVzXCJdJzogJ2NoYW5nZVN0YXR1cycsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLXRlbXBsYXRlJyk7XG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlLmh0bWwoKSk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoJ3NlbGVjdGVkU2hvcCcsIGpRdWVyeSh0ZW1wbGF0ZS5odG1sKCkpLmZpbmQoJ2lucHV0W25hbWU9XCJzaG9wXCJdJykuZmlyc3QoKS52YWwoKSk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5vbignYWZmOmFtYXpvbi1pbXBvcnQ6Y29uZmlnOmFkZC1zaG9wJywgdGhpcy5hZGRTaG9wLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge0NvbmZpZ31cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBsZXQgaHRtbCA9IHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKTtcbiAgICAgICAgdGhpcy4kZWwuaHRtbChodG1sKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLWdyb3VwLW9wdGlvbi1tZXJnZS1wcm9kdWN0LWlkLCAuYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLWdyb3VwLW9wdGlvbi1yZXBsYWNlLXByb2R1Y3QtaWQnKS5zZWxlY3RpemUoe1xuICAgICAgICAgICAgbWF4SXRlbXM6IDEsXG4gICAgICAgICAgICB2YWx1ZUZpZWxkOiAnaWQnLFxuICAgICAgICAgICAgbGFiZWxGaWVsZDogJ25hbWUnLFxuICAgICAgICAgICAgc2VhcmNoRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIGNyZWF0ZTogZmFsc2UsXG4gICAgICAgICAgICBsb2FkKHF1ZXJ5LCBjYWxsYmFjaykge1xuICAgICAgICAgICAgICAgIGlmICghcXVlcnkubGVuZ3RoKSByZXR1cm4gY2FsbGJhY2soKTtcbiAgICAgICAgICAgICAgICBqUXVlcnkuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFwaVJvb3QgKyAnd3AvdjIvYWZmLXByb2R1Y3RzLz9zdGF0dXM9cHVibGlzaCxkcmFmdCZzZWFyY2g9JyArIHF1ZXJ5LFxuICAgICAgICAgICAgICAgICAgICB0eXBlOiAnR0VUJyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgICAgICAgICAgJ3Bvc3RfcGFyZW50JzogMCxcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgYmVmb3JlU2VuZCh4aHIpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHhoci5zZXRSZXF1ZXN0SGVhZGVyKCdYLVdQLU5vbmNlJywgYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLm5vbmNlKVxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBlcnJvcigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3MocmVzdWx0cykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0cyA9IHJlc3VsdHMubWFwKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnaWQnOiByZXN1bHQuaWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICduYW1lJzogcmVzdWx0LnRpdGxlLnJlbmRlcmVkXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKHJlc3VsdHMpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgYSBuZXcgc2hvcFxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBzaG9wXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGFkZFNob3Aoc2hvcCkge1xuICAgICAgICB0aGlzLiRlbC5maW5kKCdpbnB1dFt2YWx1ZT1cIm5ldy1zaG9wXCJdJykucGFyZW50KCkuYmVmb3JlKGBcbiAgICAgICAgICAgIDxsYWJlbCBjbGFzcz1cImFmZi1pbXBvcnQtY29uZmlnLWdyb3VwLWxhYmVsXCIgZm9yPVwiJHtzaG9wLnNsdWd9XCI+XG4gICAgICAgICAgICAgICAgPGlucHV0IGlkPVwiJHtzaG9wLnNsdWd9XCIgY2xhc3M9XCJhZmYtaW1wb3J0LWNvbmZpZy1ncm91cC1vcHRpb25cIiBuYW1lPVwic2hvcFwiIHR5cGU9XCJyYWRpb1wiIHZhbHVlPVwiJHtzaG9wLnNsdWd9XCI+XG4gICAgICAgICAgICAgICAgJHtzaG9wLm5hbWV9ICAgICAgICAgXG4gICAgICAgICAgICA8L2xhYmVsPlxuICAgICAgICBgKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKGBpbnB1dFtuYW1lPVwic2hvcFwiXVt2YWx1ZT1cIiR7c2hvcC5zbHVnfVwiXWApLnByb3AoXCJjaGVja2VkXCIsIHRydWUpO1xuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnbmV3U2hvcE5hbWUnOiBudWxsLFxuICAgICAgICAgICAgJ3NlbGVjdGVkU2hvcCc6IHNob3Auc2x1ZyxcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzaG9wIGNvbmZpZ3VyYXRpb24gaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2VTaG9wKCkge1xuICAgICAgICBsZXQgc2VsZWN0ZWRTaG9wID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInNob3BcIl06Y2hlY2tlZCcpLFxuICAgICAgICAgICAgbmV3U2hvcE5hbWUgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibmV3LXNob3AtbmFtZVwiXScpO1xuXG4gICAgICAgIHNlbGVjdGVkU2hvcC52YWwoKSA9PT0gJ25ldy1zaG9wJyA/IG5ld1Nob3BOYW1lLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJykgOiBuZXdTaG9wTmFtZS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzZWxlY3RlZFNob3AnOiBzZWxlY3RlZFNob3AudmFsKCksXG4gICAgICAgICAgICAnbmV3U2hvcE5hbWUnOiBuZXdTaG9wTmFtZS52YWwoKSxcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBhY3Rpb24gY29uZmlndXJhdGlvbiBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZUFjdGlvbigpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkQWN0aW9uID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cImFjdGlvblwiXTpjaGVja2VkJyksXG4gICAgICAgICAgICBtZXJnZVByb2R1Y3RJZCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtZXJnZS1wcm9kdWN0LWlkXCJdJyksXG4gICAgICAgICAgICByZXBsYWNlUHJvZHVjdElkID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInJlcGxhY2UtcHJvZHVjdC1pZFwiXScpLFxuICAgICAgICAgICAgbWVyZ2VTZWxlY3RpemUgPSBtZXJnZVByb2R1Y3RJZC5zZWxlY3RpemUoKVswXS5zZWxlY3RpemUsXG4gICAgICAgICAgICByZXBsYWNlU2VsZWN0aXplID0gcmVwbGFjZVByb2R1Y3RJZC5zZWxlY3RpemUoKVswXS5zZWxlY3RpemU7XG5cbiAgICAgICAgc2VsZWN0ZWRBY3Rpb24udmFsKCkgPT09ICdtZXJnZS1wcm9kdWN0JyA/IG1lcmdlU2VsZWN0aXplLmVuYWJsZSgpIDogbWVyZ2VTZWxlY3RpemUuZGlzYWJsZSgpO1xuICAgICAgICBzZWxlY3RlZEFjdGlvbi52YWwoKSA9PT0gJ3JlcGxhY2UtcHJvZHVjdCcgPyByZXBsYWNlU2VsZWN0aXplLmVuYWJsZSgpIDogcmVwbGFjZVNlbGVjdGl6ZS5kaXNhYmxlKCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3NlbGVjdGVkQWN0aW9uJzogc2VsZWN0ZWRBY3Rpb24udmFsKCksXG4gICAgICAgICAgICAnbWVyZ2VQcm9kdWN0SWQnOiBtZXJnZVByb2R1Y3RJZC52YWwoKSxcbiAgICAgICAgICAgICdyZXBsYWNlUHJvZHVjdElkJzogcmVwbGFjZVByb2R1Y3RJZC52YWwoKVxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHN0YXR1cyBjb25maWd1cmF0aW9uIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlU3RhdHVzKCkge1xuICAgICAgICBsZXQgc2VsZWN0ZWRTdGF0dXMgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwic3RhdHVzXCJdOmNoZWNrZWQnKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc3RhdHVzJzogc2VsZWN0ZWRTdGF0dXMudmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnO1xuIiwiaW1wb3J0IFNlYXJjaCBmcm9tICcuL3NlYXJjaCc7XG5pbXBvcnQgQ29uZmlnIGZyb20gJy4vY29uZmlnJztcblxubGV0IEltcG9ydCA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydCcsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuc2VhcmNoID0gbmV3IFNlYXJjaCh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5zZWFyY2gsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuY29uZmlnID0gbmV3IENvbmZpZyh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5jb25maWcsXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLnNlYXJjaC5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5jb25maWcucmVuZGVyKCk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBJbXBvcnQ7XG4iLCJsZXQgU2VhcmNoRm9ybSA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWZvcm0nLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2Ugc2VsZWN0W25hbWU9XCJ0eXBlXCJdJzogJ2NoYW5nZScsXG4gICAgICAgICdjaGFuZ2Ugc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXSc6ICdjaGFuZ2UnLFxuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwid2l0aC12YXJpYW50c1wiXSc6ICdjaGFuZ2UnLFxuICAgICAgICAnc3VibWl0JzogJ3N1Ym1pdCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWZvcm0tdGVtcGxhdGUnKS5odG1sKCksXG4gICAgICAgICAgICBwcm92aWRlckNvbmZpZ3VyZWQgPSB0aGlzLiRlbC5kYXRhKCdwcm92aWRlci1jb25maWd1cmVkJyk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCgncHJvdmlkZXJDb25maWd1cmVkJywgcHJvdmlkZXJDb25maWd1cmVkID09PSB0cnVlIHx8IHByb3ZpZGVyQ29uZmlndXJlZCA9PT0gJ3RydWUnKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtTZWFyY2hGb3JtfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICBsZXQgdHlwZSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwidHlwZVwiXScpLFxuICAgICAgICAgICAgY2F0ZWdvcnkgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNhdGVnb3J5XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICB0eXBlLnZhbCh0aGlzLm1vZGVsLmdldCgndHlwZScpKTtcbiAgICAgICAgY2F0ZWdvcnkudmFsKHRoaXMubW9kZWwuZ2V0KCdjYXRlZ29yeScpKTtcbiAgICAgICAgd2l0aFZhcmlhbnRzLnZhbCh0aGlzLm1vZGVsLmdldCgnd2l0aFZhcmlhbnRzJykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7RXZlbnR9IGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3VibWl0KGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuY2hhbmdlKCk7XG4gICAgICAgIHRoaXMubW9kZWwuc3VibWl0KCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzZWFyY2ggcGFyYW1ldGVycyBpbnRvIHRoZSBtb2RlbCBvbiBmb3JtIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlKCkge1xuICAgICAgICBsZXQgdGVybSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJ0ZXJtXCJdJyksXG4gICAgICAgICAgICB0eXBlID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0eXBlXCJdJyksXG4gICAgICAgICAgICBjYXRlZ29yeSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nKSxcbiAgICAgICAgICAgIHdpdGhWYXJpYW50cyA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwid2l0aC12YXJpYW50c1wiXScpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICd0ZXJtJzogdGVybS52YWwoKSxcbiAgICAgICAgICAgICd0eXBlJzogdHlwZS52YWwoKSxcbiAgICAgICAgICAgICdjYXRlZ29yeSc6IGNhdGVnb3J5Lmxlbmd0aCAhPT0gMCA/IGNhdGVnb3J5LnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ2NhdGVnb3J5JyksXG4gICAgICAgICAgICAnd2l0aFZhcmlhbnRzJzogd2l0aFZhcmlhbnRzLnZhbCgpXG4gICAgICAgIH0pO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hMb2FkTW9yZSA9ICBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWxvYWQtbW9yZScsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NsaWNrIC5hZmYtaW1wb3J0LXNlYXJjaC1sb2FkLW1vcmUtYnV0dG9uJzogJ2xvYWQnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggbG9hZCBtb3JlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWxvYWQtbW9yZS10ZW1wbGF0ZScpLmh0bWwoKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJuIHtTZWFyY2hMb2FkTW9yZX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEVuYWJsZSB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMubW9kZWwubG9hZCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hMb2FkTW9yZTtcbiIsImxldCBTZWFyY2hSZXN1bHRzSXRlbSA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICB0YWdOYW1lOiAnZGl2JyxcblxuICAgIGNsYXNzTmFtZTogJycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NsaWNrIC5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtc2hvdy1hbGwnOiAnc2hvd0FsbCcsXG4gICAgICAgICdjbGljayAuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLWFjdGlvbnMtaW1wb3J0JzogJ2ltcG9ydCdcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdGVtcGxhdGUnKS5odG1sKCk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybiB7U2VhcmNoUmVzdWx0c0l0ZW19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IGFsbCBoaWRkZW4gdmFyaWFudHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc2hvd0FsbChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJykuaGlkZSgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLWl0ZW0nKS5zaG93KCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgc2VhcmNoIHJlc3VsdCBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLmltcG9ydCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRzSXRlbSBmcm9tICcuL3NlYXJjaC1yZXN1bHRzLWl0ZW0nO1xuXG5sZXQgU2VhcmNoUmVzdWx0cyA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cycsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge2FycmF5fSBvcHRpb25zXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24gPSBvcHRpb25zLmNvbGxlY3Rpb247XG5cbiAgICAgICAgLy8gQmluZCB0aGUgY29sbGVjdGlvbiBldmVudHNcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3Jlc2V0JywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdhZGQnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3JlbW92ZScsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnc3luYycsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuX2FkZEFsbCgpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgYWxsIHNlYXJjaCByZXN1bHRzIGl0ZW1zIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2FkZEFsbCgpIHtcbiAgICAgICAgdGhpcy4kZWwuZW1wdHkoKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmZvckVhY2godGhpcy5fYWRkT25lLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIG9uZSBzZWFyY2ggcmVzdWx0cyBpdGVtIHRvIHRoZSB2aWV3LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2FkZE9uZShwcm9kdWN0KSB7XG4gICAgICAgIGxldCB2aWV3ID0gbmV3IFNlYXJjaFJlc3VsdHNJdGVtKHtcbiAgICAgICAgICAgIG1vZGVsOiBwcm9kdWN0LFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLiRlbC5hcHBlbmQodmlldy5yZW5kZXIoKS5lbCk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzO1xuIiwiaW1wb3J0IFNlYXJjaEZvcm0gZnJvbSAnLi9zZWFyY2gtZm9ybSc7XG5pbXBvcnQgU2VhcmNoTG9hZE1vcmUgZnJvbSAnLi9zZWFyY2gtbG9hZC1tb3JlJztcbmltcG9ydCBTZWFyY2hSZXN1bHRzIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMnO1xuXG5sZXQgU2VhcmNoID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaCcsXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuZm9ybSA9IG5ldyBTZWFyY2hGb3JtKHtcbiAgICAgICAgICAgIG1vZGVsOiB0aGlzLm1vZGVsLmZvcm0sXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cyA9IG5ldyBTZWFyY2hSZXN1bHRzKHtcbiAgICAgICAgICAgIGNvbGxlY3Rpb246IHRoaXMubW9kZWwucmVzdWx0cyxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5sb2FkTW9yZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLmZvcm0ucmVuZGVyKCk7XG4gICAgICAgIHRoaXMucmVzdWx0cy5yZW5kZXIoKTtcblxuICAgICAgICBpZih0aGlzLm1vZGVsLmdldCgnc3RhcnRlZCcpKSB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnJlbmRlcigpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2g7XG4iXX0=
