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
        'status': 'publish'
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
            minPrice = this.$el.find('input[name="min-price"]'),
            maxPrice = this.$el.find('input[name="max-price"]'),
            condition = this.$el.find('select[name="condition"]'),
            sort = this.$el.find('select[name="sort"]'),
            withVariants = this.$el.find('select[name="with-variants"]');

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
            'term': term.val(),
            'type': type.val(),
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvaW1wb3J0LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLXJlc3VsdHMtaXRlbS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2guanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9z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QUFDYixZQUFJLE9BQU8sZ0NBQXNCO0FBQzdCLG1CQUFPO0FBRHNCLFNBQXRCLENBQVg7O0FBSUEsYUFBSyxHQUFMLENBQVMsTUFBVCxDQUFnQixLQUFLLE1BQUwsR0FBYyxFQUE5QjtBQUNIO0FBckRvQyxDQUFyQixDQUFwQjs7a0JBd0RlLGE7Ozs7Ozs7OztBQzFEZjs7OztBQUNBOzs7O0FBQ0E7Ozs7OztBQUVBLElBQUksU0FBUyxTQUFTLElBQVQsQ0FBYyxNQUFkLENBQXFCO0FBQzlCLFFBQUksMkJBRDBCOztBQUc5Qjs7Ozs7O0FBTUEsY0FUOEIsd0JBU2pCO0FBQ1QsYUFBSyxJQUFMLEdBQVkseUJBQWU7QUFDdkIsbUJBQU8sS0FBSyxLQUFMLENBQVc7QUFESyxTQUFmLENBQVo7O0FBSUEsYUFBSyxPQUFMLEdBQWUsNEJBQWtCO0FBQzdCLHdCQUFZLEtBQUssS0FBTCxDQUFXO0FBRE0sU0FBbEIsQ0FBZjs7QUFJQSxhQUFLLFFBQUwsR0FBZ0IsNkJBQW1CO0FBQy9CLG1CQUFPLEtBQUssS0FBTCxDQUFXO0FBRGEsU0FBbkIsQ0FBaEI7O0FBSUEsYUFBSyxLQUFMLENBQVcsRUFBWCxDQUFjLFFBQWQsRUFBd0IsS0FBSyxNQUE3QixFQUFxQyxJQUFyQztBQUNILEtBdkI2Qjs7O0FBeUI5Qjs7Ozs7O0FBTUEsVUEvQjhCLG9CQStCckI7QUFDTCxhQUFLLElBQUwsQ0FBVSxNQUFWO0FBQ0EsYUFBSyxPQUFMLENBQWEsTUFBYjs7QUFFQSxZQUFHLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxTQUFmLENBQUgsRUFBOEI7QUFDMUIsaUJBQUssUUFBTCxDQUFjLE1BQWQ7QUFDSDs7QUFFRCxlQUFPLElBQVA7QUFDSDtBQXhDNkIsQ0FBckIsQ0FBYjs7a0JBMkNlLE0iLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiaW1wb3J0IEltcG9ydCBmcm9tICcuL21vZGVsL2ltcG9ydCc7XG5pbXBvcnQgSW1wb3J0VmlldyBmcm9tICcuL3ZpZXcvaW1wb3J0JztcblxubGV0IGltcG9ydE1vZGVsID0gbmV3IEltcG9ydCgpO1xubGV0IGltcG9ydFZpZXcgPSBuZXcgSW1wb3J0Vmlldyh7bW9kZWw6IGltcG9ydE1vZGVsfSk7XG5cbmltcG9ydFZpZXcucmVuZGVyKCk7XG4iLCJsZXQgQ29uZmlnID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnc2VsZWN0ZWRTaG9wJzogJ2FtYXpvbicsXG4gICAgICAgICduZXdTaG9wTmFtZSc6IG51bGwsXG4gICAgICAgICdzZWxlY3RlZEFjdGlvbic6ICduZXctcHJvZHVjdCcsXG4gICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IG51bGwsXG4gICAgICAgICdyZXBsYWNlUHJvZHVjdElkJzogbnVsbCxcbiAgICAgICAgJ3N0YXR1cyc6ICdwdWJsaXNoJyxcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9zZWFyY2gnO1xuaW1wb3J0IENvbmZpZyBmcm9tICcuL2NvbmZpZyc7XG5cbmxldCBJbXBvcnQgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX2ltcG9ydCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNlYXJjaCA9IG5ldyBTZWFyY2goKTtcbiAgICAgICAgdGhpcy5jb25maWcgPSBuZXcgQ29uZmlnKCk7XG5cbiAgICAgICAgdGhpcy5zZWFyY2gub24oJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCB0aGlzLmltcG9ydCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgcHJvZHVjdC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gcHJvZHVjdFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQocHJvZHVjdCkge1xuICAgICAgICBsZXQgZGF0YSA9IHtcbiAgICAgICAgICAgICdwcm9kdWN0Jzoge1xuICAgICAgICAgICAgICAgICduYW1lJyA6IHByb2R1Y3QuYXR0cmlidXRlcy5uYW1lLFxuICAgICAgICAgICAgICAgICd0eXBlJyA6IHByb2R1Y3QuYXR0cmlidXRlcy50eXBlLFxuICAgICAgICAgICAgICAgICdzaG9wcycgOiBwcm9kdWN0LmF0dHJpYnV0ZXMuc2hvcHMsXG4gICAgICAgICAgICAgICAgJ2N1c3RvbV92YWx1ZXMnIDogcHJvZHVjdC5hdHRyaWJ1dGVzLmN1c3RvbV92YWx1ZXMsXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJ2NvbmZpZyc6IHRoaXMuY29uZmlnLmF0dHJpYnV0ZXMsXG4gICAgICAgICAgICAnZm9ybSc6IHRoaXMuc2VhcmNoLmZvcm0uYXR0cmlidXRlcyxcbiAgICAgICAgfTtcblxuICAgICAgICBqUXVlcnkuYWpheCh7XG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICB1cmw6IHRoaXMuX2J1aWxkVXJsKCksXG4gICAgICAgICAgICBkYXRhOiBkYXRhLFxuICAgICAgICB9KS5kb25lKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgIGxldCBzaG9wVGVtcGxhdGUgPSAoKHJlc3VsdCB8fCB7fSkuZGF0YSB8fCB7fSkuc2hvcF90ZW1wbGF0ZSB8fCBudWxsO1xuXG4gICAgICAgICAgICBpZihzaG9wVGVtcGxhdGUpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmNvbmZpZy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpjb25maWc6YWRkLXNob3AnLCBzaG9wVGVtcGxhdGUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBwcm9kdWN0LnNob3dTdWNjZXNzTWVzc2FnZSgpO1xuICAgICAgICB9KS5mYWlsKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgIGxldCBlcnJvck1lc3NhZ2UgPSAoKCgocmVzdWx0IHx8IHt9KS5yZXNwb25zZUpTT04gfHwge30pLmRhdGEgfHwge30pWzBdIHx8IHt9KS5tZXNzYWdlIHx8IG51bGw7XG5cbiAgICAgICAgICAgIHByb2R1Y3Quc2hvd0Vycm9yTWVzc2FnZShlcnJvck1lc3NhZ2UpO1xuICAgICAgICB9KVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgaW1wb3J0IHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICA7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBJbXBvcnQ7XG4iLCJsZXQgU2VhcmNoRm9ybSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3Rlcm0nOiAnJyxcbiAgICAgICAgJ3R5cGUnOiAna2V5d29yZHMnLFxuICAgICAgICAnY2F0ZWdvcnknOiAnQWxsJyxcbiAgICAgICAgJ21pblByaWNlJzogbnVsbCxcbiAgICAgICAgJ21heFByaWNlJzogbnVsbCxcbiAgICAgICAgJ2NvbmRpdGlvbic6ICdOZXcnLFxuICAgICAgICAnc29ydCc6ICctcHJpY2UnLFxuICAgICAgICAnd2l0aFZhcmlhbnRzJzogJ25vJyxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgJ3Byb3ZpZGVyQ29uZmlndXJlZCc6IGZhbHNlXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgZm9ybSB0aGUgZm9ybSBhbmQgdHJpZ2dlciB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdCgpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEZpbmlzaCB0aGUgc3VibWl0IGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZG9uZSgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCBmYWxzZSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZG9uZScsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBGaW5pc2ggdGhlIHNlYXJjaCBzdWJtaXQgd2l0aCBubyByZXN1bHRzIGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTRcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cyhtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ25vUmVzdWx0cyc6IHRydWUsXG4gICAgICAgICAgICAnbm9SZXN1bHRzTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmZWJheWl1OmViYXktaW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpuby1yZXN1bHRzJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYSBzdWJtaXQgZXJyb3IgYW5kIHN0b3AgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZXJyb3IobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hMb2FkTW9yZSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2VuYWJsZWQnOiB0cnVlLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWN0aXZhdGUgdGhlIGxvYWRpbmcgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgdHJ1ZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIGxvYWQgbW9yZSBidXR0b24gYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gZW5hYmxlZFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBkb25lKGVuYWJsZWQgPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZW5hYmxlZCc6IGVuYWJsZWQsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpkb25lJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIG5vIHJlc3VsdHMgbWVzc2FnZSBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cygpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnIDogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOm5vLXJlc3VsdHMnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhIGxvYWQgbW9yZSBlcnJvciBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBlcnJvcihtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnZW5hYmxlZCc6IHRydWUsXG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yJzogdHJ1ZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdzdWNjZXNzJzogZmFsc2UsXG4gICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBzZWFyY2ggcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06aW1wb3J0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Y2Nlc3NmdWxseSBmaW5pc2ggdGhlIGltcG9ydCB3aXRoIGFuIG9wdGlvbmFsIG1lc3NhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93U3VjY2Vzc01lc3NhZ2UobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdzdWNjZXNzJzogdHJ1ZSxcbiAgICAgICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG1lc3NhZ2VcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOnN1Y2Nlc3MnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRGlzcGxheSBhbiBlcnJvciBmb3IgaW1wb3J0IHdpdGggYW4gb3B0aW9uYWwgbWVzc2FnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dFcnJvck1lc3NhZ2UobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmVycm9yJywgdGhpcyk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRJdGVtIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuQ29sbGVjdGlvbi5leHRlbmQoe1xuICAgIG1vZGVsOiBTZWFyY2hSZXN1bHRJdGVtLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMub24oJ3N5bmMnLCB0aGlzLmluaXRJbXBvcnRMaXN0ZW5lcnMsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBQYXJzZSB0aGUgV29yZHByZXNzIGpzb24gQWpheCByZXNwb25zZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge0FycmF5fSByZXNwb25zZVxuICAgICAqIEByZXR1cm5zIHtBcnJheX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcGFyc2U6IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgIHJldHVybiByZXNwb25zZSAmJiByZXNwb25zZS5zdWNjZXNzID8gcmVzcG9uc2UuZGF0YSA6IFtdO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydEl0ZW0obW9kZWwpIHtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppbXBvcnQtaXRlbScsIG1vZGVsKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdCB0aGUgaW1wb3J0IGxpc3RlbmVycyBmb3IgYWxsIHJlc3VsdHMgaXRlbXMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRJbXBvcnRMaXN0ZW5lcnMoKSB7XG4gICAgICAgIHRoaXMuZm9yRWFjaCh0aGlzLl9pbml0SW1wb3J0TGlzdGVuZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0IHRoZSBpbXBvcnQgbGlzdGVuZXJzIGZvciB0aGUgcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaW5pdEltcG9ydExpc3RlbmVyKG1vZGVsKSB7XG4gICAgICAgIG1vZGVsLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmltcG9ydCcsIHRoaXMuaW1wb3J0SXRlbSwgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hMb2FkTW9yZSBmcm9tICcuL3NlYXJjaC1sb2FkLW1vcmUnO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzdGFydGVkJzogZmFsc2UsXG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX3NlYXJjaCcsXG4gICAgICAgICdwYWdlJyA6IDEsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCB3aXRoIHRoZSBnaXZlbiBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IG9wdGlvbnNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5mb3JtID0gbmV3IFNlYXJjaEZvcm0oKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzID0gbmV3IFNlYXJjaFJlc3VsdHMoKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSgpO1xuICAgICAgICB0aGlzLnBhZ2UgPSBvcHRpb25zICYmIG9wdGlvbnMucGFnZSA/IG9wdGlvbnMucGFnZSA6IDE7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppbXBvcnQtaXRlbScsIHRoaXMuaW1wb3J0LCB0aGlzKTtcbiAgICAgICAgdGhpcy5mb3JtLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcy5zdGFydCwgdGhpcyk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bG9hZCcsIHRoaXMubG9hZCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN0YXJ0IHRoZSBzZWFyY2ggd2l0aCB0aGUgZmlyc3QgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3RhcnQoKSB7XG4gICAgICAgIGlmKHRoaXMuZm9ybS5nZXQoJ3Rlcm0nKSA9PT0gbnVsbCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCAxKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnVybCA9IHRoaXMuX2J1aWxkVXJsKCk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLmZldGNoKCkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5zZXQoJ2VuYWJsZWQnLCB0aGlzLl9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSk7XG5cbiAgICAgICAgICAgIGlmKHRoaXMuX2hhc1Jlc3VsdHMocmVzdWx0cykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0uZG9uZSgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0ubm9SZXN1bHRzKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pLmZhaWwoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgdGhpcy5mb3JtLmVycm9yKGVycm9yTWVzc2FnZSk7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnNldCgnZW5hYmxlZCcsIGZhbHNlKTtcbiAgICAgICAgfSkuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2V0KCdzdGFydGVkJywgdHJ1ZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIG1vcmUgc2VhcmNoIHJlc3VsdHMgYnkgaW5jcmVhc2luZyB0aGUgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCB0aGlzLmdldCgncGFnZScpICsgMSk7XG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5mZXRjaCh7J3JlbW92ZSc6IGZhbHNlfSkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5kb25lKHRoaXMuX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpKTtcbiAgICAgICAgfSkuZmFpbCgoKSA9PiB7XG4gICAgICAgICAgICBsZXQgZXJyb3JNZXNzYWdlID0gKCgoKHJlc3VsdCB8fCB7fSkucmVzcG9uc2VKU09OIHx8IHt9KS5kYXRhIHx8IHt9KVswXSB8fCB7fSkubWVzc2FnZSB8fCBudWxsO1xuXG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLmVycm9yKGVycm9yTWVzc2FnZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChtb2RlbCkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCBtb2RlbCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBzZWFyY2ggQVBJIHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnRlcm09JHt0aGlzLmZvcm0uZ2V0KCd0ZXJtJyl9YFxuICAgICAgICAgICAgKyBgJnR5cGU9JHt0aGlzLmZvcm0uZ2V0KCd0eXBlJyl9YFxuICAgICAgICAgICAgKyBgJmNhdGVnb3J5PSR7dGhpcy5mb3JtLmdldCgnY2F0ZWdvcnknKX1gXG4gICAgICAgICAgICArIGAmbWluLXByaWNlPSR7dGhpcy5mb3JtLmdldCgnbWluUHJpY2UnKX1gXG4gICAgICAgICAgICArIGAmbWF4LXByaWNlPSR7dGhpcy5mb3JtLmdldCgnbWF4UHJpY2UnKX1gXG4gICAgICAgICAgICArIGAmY29uZGl0aW9uPSR7dGhpcy5mb3JtLmdldCgnY29uZGl0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnNvcnQ9JHt0aGlzLmZvcm0uZ2V0KCdzb3J0Jyl9YFxuICAgICAgICAgICAgKyBgJndpdGgtdmFyaWFudHM9JHt0aGlzLmZvcm0uZ2V0KCd3aXRoVmFyaWFudHMnKX1gXG4gICAgICAgICAgICArIGAmcGFnZT0ke3RoaXMuZ2V0KCdwYWdlJyl9YFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayBpZiB0aGUgbG9hZCBtb3JlIGJ1dHRvbiBpcyBlbmFibGVkICh2aXNpYmxlKS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge0FycmF5fG51bGx9IHJlc3VsdHNcbiAgICAgKiBAcmV0dXJucyB7Ym9vbH1cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSB7XG4gICAgICAgIHJldHVybiAocmVzdWx0cyAmJiByZXN1bHRzLmRhdGEgJiYgcmVzdWx0cy5kYXRhLmxlbmd0aCA+IDApXG4gICAgICAgICAgICAmJiB0aGlzLmdldCgncGFnZScpIDwgNVxuICAgICAgICAgICAgJiYgdGhpcy5mb3JtLmdldCgndHlwZScpID09PSAna2V5d29yZHMnO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayBpZiB0aGVyZSBhcmUgYW55IG90aGVyIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMS4xLjJcbiAgICAgKiBAcGFyYW0ge0FycmF5fG51bGx9IHJlc3VsdHNcbiAgICAgKiBAcmV0dXJucyB7Ym9vbH1cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9oYXNSZXN1bHRzKHJlc3VsdHMpIHtcbiAgICAgICAgcmV0dXJuIHJlc3VsdHNcbiAgICAgICAgICAgICYmIHJlc3VsdHMuZGF0YVxuICAgICAgICAgICAgJiYgcmVzdWx0cy5kYXRhLmxlbmd0aCA+IDA7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiIsImxldCBDb25maWcgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwic2hvcFwiXSc6ICdjaGFuZ2VTaG9wJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwibmV3LXNob3AtbmFtZVwiXSc6ICdjaGFuZ2VTaG9wJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwiYWN0aW9uXCJdJzogJ2NoYW5nZUFjdGlvbicsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cIm1lcmdlLXByb2R1Y3QtaWRcIl0nOiAnY2hhbmdlQWN0aW9uJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwicmVwbGFjZS1wcm9kdWN0LWlkXCJdJzogJ2NoYW5nZUFjdGlvbicsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInN0YXR1c1wiXSc6ICdjaGFuZ2VTdGF0dXMnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZSA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy10ZW1wbGF0ZScpO1xuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZS5odG1sKCkpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdzZWxlY3RlZFNob3AnLCBqUXVlcnkodGVtcGxhdGUuaHRtbCgpKS5maW5kKCdpbnB1dFtuYW1lPVwic2hvcFwiXScpLmZpcnN0KCkudmFsKCkpO1xuXG4gICAgICAgIHRoaXMubW9kZWwub24oJ2FmZjphbWF6b24taW1wb3J0OmNvbmZpZzphZGQtc2hvcCcsIHRoaXMuYWRkU2hvcCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtDb25maWd9XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgbGV0IGh0bWwgPSB0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcyk7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwoaHRtbCk7XG5cbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1ncm91cC1vcHRpb24tbWVyZ2UtcHJvZHVjdC1pZCwgLmFmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1ncm91cC1vcHRpb24tcmVwbGFjZS1wcm9kdWN0LWlkJykuc2VsZWN0aXplKHtcbiAgICAgICAgICAgIG1heEl0ZW1zOiAxLFxuICAgICAgICAgICAgdmFsdWVGaWVsZDogJ2lkJyxcbiAgICAgICAgICAgIGxhYmVsRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIHNlYXJjaEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBjcmVhdGU6IGZhbHNlLFxuICAgICAgICAgICAgbG9hZChxdWVyeSwgY2FsbGJhY2spIHtcbiAgICAgICAgICAgICAgICBpZiAoIXF1ZXJ5Lmxlbmd0aCkgcmV0dXJuIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgalF1ZXJ5LmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6IGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hcGlSb290ICsgJ3dwL3YyL2FmZi1wcm9kdWN0cy8/c3RhdHVzPXB1Ymxpc2gsZHJhZnQmc2VhcmNoPScgKyBxdWVyeSxcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdwb3N0X3BhcmVudCc6IDAsXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIGJlZm9yZVNlbmQoeGhyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB4aHIuc2V0UmVxdWVzdEhlYWRlcignWC1XUC1Ob25jZScsIGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5ub25jZSlcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgZXJyb3IoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBzdWNjZXNzKHJlc3VsdHMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdHMgPSByZXN1bHRzLm1hcCgocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2lkJzogcmVzdWx0LmlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnbmFtZSc6IHJlc3VsdC50aXRsZS5yZW5kZXJlZFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhyZXN1bHRzKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIGEgbmV3IHNob3BcbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gc2hvcFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBhZGRTaG9wKHNob3ApIHtcbiAgICAgICAgdGhpcy4kZWwuZmluZCgnaW5wdXRbdmFsdWU9XCJuZXctc2hvcFwiXScpLnBhcmVudCgpLmJlZm9yZShgXG4gICAgICAgICAgICA8bGFiZWwgY2xhc3M9XCJhZmYtaW1wb3J0LWNvbmZpZy1ncm91cC1sYWJlbFwiIGZvcj1cIiR7c2hvcC5zbHVnfVwiPlxuICAgICAgICAgICAgICAgIDxpbnB1dCBpZD1cIiR7c2hvcC5zbHVnfVwiIGNsYXNzPVwiYWZmLWltcG9ydC1jb25maWctZ3JvdXAtb3B0aW9uXCIgbmFtZT1cInNob3BcIiB0eXBlPVwicmFkaW9cIiB2YWx1ZT1cIiR7c2hvcC5zbHVnfVwiPlxuICAgICAgICAgICAgICAgICR7c2hvcC5uYW1lfSAgICAgICAgIFxuICAgICAgICAgICAgPC9sYWJlbD5cbiAgICAgICAgYCk7XG5cbiAgICAgICAgdGhpcy4kZWwuZmluZChgaW5wdXRbbmFtZT1cInNob3BcIl1bdmFsdWU9XCIke3Nob3Auc2x1Z31cIl1gKS5wcm9wKFwiY2hlY2tlZFwiLCB0cnVlKTtcbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ25ld1Nob3BOYW1lJzogbnVsbCxcbiAgICAgICAgICAgICdzZWxlY3RlZFNob3AnOiBzaG9wLnNsdWcsXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc2hvcCBjb25maWd1cmF0aW9uIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlU2hvcCgpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkU2hvcCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzaG9wXCJdOmNoZWNrZWQnKSxcbiAgICAgICAgICAgIG5ld1Nob3BOYW1lID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm5ldy1zaG9wLW5hbWVcIl0nKTtcblxuICAgICAgICBzZWxlY3RlZFNob3AudmFsKCkgPT09ICduZXctc2hvcCcgPyBuZXdTaG9wTmFtZS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpIDogbmV3U2hvcE5hbWUuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc2VsZWN0ZWRTaG9wJzogc2VsZWN0ZWRTaG9wLnZhbCgpLFxuICAgICAgICAgICAgJ25ld1Nob3BOYW1lJzogbmV3U2hvcE5hbWUudmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgYWN0aW9uIGNvbmZpZ3VyYXRpb24gaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2VBY3Rpb24oKSB7XG4gICAgICAgIGxldCBzZWxlY3RlZEFjdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJhY3Rpb25cIl06Y2hlY2tlZCcpLFxuICAgICAgICAgICAgbWVyZ2VQcm9kdWN0SWQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWVyZ2UtcHJvZHVjdC1pZFwiXScpLFxuICAgICAgICAgICAgcmVwbGFjZVByb2R1Y3RJZCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJyZXBsYWNlLXByb2R1Y3QtaWRcIl0nKSxcbiAgICAgICAgICAgIG1lcmdlU2VsZWN0aXplID0gbWVyZ2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplLFxuICAgICAgICAgICAgcmVwbGFjZVNlbGVjdGl6ZSA9IHJlcGxhY2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplO1xuXG4gICAgICAgIHNlbGVjdGVkQWN0aW9uLnZhbCgpID09PSAnbWVyZ2UtcHJvZHVjdCcgPyBtZXJnZVNlbGVjdGl6ZS5lbmFibGUoKSA6IG1lcmdlU2VsZWN0aXplLmRpc2FibGUoKTtcbiAgICAgICAgc2VsZWN0ZWRBY3Rpb24udmFsKCkgPT09ICdyZXBsYWNlLXByb2R1Y3QnID8gcmVwbGFjZVNlbGVjdGl6ZS5lbmFibGUoKSA6IHJlcGxhY2VTZWxlY3RpemUuZGlzYWJsZSgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzZWxlY3RlZEFjdGlvbic6IHNlbGVjdGVkQWN0aW9uLnZhbCgpLFxuICAgICAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbWVyZ2VQcm9kdWN0SWQudmFsKCksXG4gICAgICAgICAgICAncmVwbGFjZVByb2R1Y3RJZCc6IHJlcGxhY2VQcm9kdWN0SWQudmFsKClcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzdGF0dXMgY29uZmlndXJhdGlvbiBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZVN0YXR1cygpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkU3RhdHVzID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInN0YXR1c1wiXTpjaGVja2VkJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3N0YXR1cyc6IHNlbGVjdGVkU3RhdHVzLnZhbCgpLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9zZWFyY2gnO1xuaW1wb3J0IENvbmZpZyBmcm9tICcuL2NvbmZpZyc7XG5cbmxldCBJbXBvcnQgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNlYXJjaCA9IG5ldyBTZWFyY2goe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuc2VhcmNoLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmNvbmZpZyA9IG5ldyBDb25maWcoe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuY29uZmlnLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zZWFyY2gucmVuZGVyKCk7XG4gICAgICAgIHRoaXMuY29uZmlnLnJlbmRlcigpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgSW1wb3J0O1xuIiwibGV0IFNlYXJjaEZvcm0gPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwidHlwZVwiXSc6ICdjaGFuZ2UnLFxuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nOiAnY2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdzdWJtaXQnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggZm9ybS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtLXRlbXBsYXRlJykuaHRtbCgpLFxuICAgICAgICAgICAgcHJvdmlkZXJDb25maWd1cmVkID0gdGhpcy4kZWwuZGF0YSgncHJvdmlkZXItY29uZmlndXJlZCcpO1xuXG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoJ3Byb3ZpZGVyQ29uZmlndXJlZCcsIHByb3ZpZGVyQ29uZmlndXJlZCA9PT0gdHJ1ZSB8fCBwcm92aWRlckNvbmZpZ3VyZWQgPT09ICd0cnVlJyk7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggZm9ybS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7U2VhcmNoRm9ybX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgbGV0IHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLFxuICAgICAgICAgICAgbWluUHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWluLXByaWNlXCJdJyksXG4gICAgICAgICAgICBtYXhQcmljZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtYXgtcHJpY2VcIl0nKSxcbiAgICAgICAgICAgIGNvbmRpdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY29uZGl0aW9uXCJdJyksXG4gICAgICAgICAgICBzb3J0ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJzb3J0XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICB0eXBlLnZhbCh0aGlzLm1vZGVsLmdldCgndHlwZScpKTtcbiAgICAgICAgY2F0ZWdvcnkudmFsKHRoaXMubW9kZWwuZ2V0KCdjYXRlZ29yeScpKTtcbiAgICAgICAgbWluUHJpY2UudmFsKHRoaXMubW9kZWwuZ2V0KCdtaW5QcmljZScpKTtcbiAgICAgICAgbWF4UHJpY2UudmFsKHRoaXMubW9kZWwuZ2V0KCdtYXhQcmljZScpKTtcbiAgICAgICAgY29uZGl0aW9uLnZhbCh0aGlzLm1vZGVsLmdldCgnY29uZGl0aW9uJykpO1xuICAgICAgICBzb3J0LnZhbCh0aGlzLm1vZGVsLmdldCgnc29ydCcpKTtcbiAgICAgICAgd2l0aFZhcmlhbnRzLnZhbCh0aGlzLm1vZGVsLmdldCgnd2l0aFZhcmlhbnRzJykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7RXZlbnR9IGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3VibWl0KGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuY2hhbmdlKCk7XG4gICAgICAgIHRoaXMubW9kZWwuc3VibWl0KCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzZWFyY2ggcGFyYW1ldGVycyBpbnRvIHRoZSBtb2RlbCBvbiBmb3JtIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlKCkge1xuICAgICAgICBsZXQgdGVybSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJ0ZXJtXCJdJyksXG4gICAgICAgICAgICB0eXBlID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0eXBlXCJdJyksXG4gICAgICAgICAgICBjYXRlZ29yeSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nKSxcbiAgICAgICAgICAgIG1pblByaWNlID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1pbi1wcmljZVwiXScpLFxuICAgICAgICAgICAgbWF4UHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWF4LXByaWNlXCJdJyksXG4gICAgICAgICAgICBjb25kaXRpb24gPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNvbmRpdGlvblwiXScpLFxuICAgICAgICAgICAgc29ydCA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwic29ydFwiXScpLFxuICAgICAgICAgICAgd2l0aFZhcmlhbnRzID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ3aXRoLXZhcmlhbnRzXCJdJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3Rlcm0nOiB0ZXJtLnZhbCgpLFxuICAgICAgICAgICAgJ3R5cGUnOiB0eXBlLnZhbCgpLFxuICAgICAgICAgICAgJ21pblByaWNlJzogbWluUHJpY2UubGVuZ3RoICE9PSAwID8gbWluUHJpY2UudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnbWluUHJpY2UnKSxcbiAgICAgICAgICAgICdtYXhQcmljZSc6IG1heFByaWNlLmxlbmd0aCAhPT0gMCA/IG1heFByaWNlLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ21heFByaWNlJyksXG4gICAgICAgICAgICAnY29uZGl0aW9uJzogY29uZGl0aW9uLmxlbmd0aCAhPT0gMCA/IGNvbmRpdGlvbi52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCdjb25kaXRpb24nKSxcbiAgICAgICAgICAgICdzb3J0Jzogc29ydC5sZW5ndGggIT09IDAgPyBzb3J0LnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ3NvcnQnKSxcbiAgICAgICAgICAgICdjYXRlZ29yeSc6IGNhdGVnb3J5Lmxlbmd0aCAhPT0gMCA/IGNhdGVnb3J5LnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ2NhdGVnb3J5JyksXG4gICAgICAgICAgICAnd2l0aFZhcmlhbnRzJzogd2l0aFZhcmlhbnRzLmxlbmd0aCAhPT0gMCA/IHdpdGhWYXJpYW50cy52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCd3aXRoVmFyaWFudHMnKSxcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLWxvYWQtbW9yZS1idXR0b24nOiAnbG9hZCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCBsb2FkIG1vcmUuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlLXRlbXBsYXRlJykuaHRtbCgpO1xuXG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggbG9hZCBtb3JlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm4ge1NlYXJjaExvYWRNb3JlfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRW5hYmxlIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5tb2RlbC5sb2FkKCk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaExvYWRNb3JlO1xuIiwibGV0IFNlYXJjaFJlc3VsdHNJdGVtID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIHRhZ05hbWU6ICdkaXYnLFxuXG4gICAgY2xhc3NOYW1lOiAnJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCc6ICdzaG93QWxsJyxcbiAgICAgICAgJ2NsaWNrIC5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tYWN0aW9ucy1pbXBvcnQnOiAnaW1wb3J0J1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS10ZW1wbGF0ZScpLmh0bWwoKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIHJlc3VsdHMgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJuIHtTZWFyY2hSZXN1bHRzSXRlbX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYWxsIGhpZGRlbiB2YXJpYW50cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93QWxsKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtc2hvdy1hbGwnKS5oaWRlKCk7XG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtaXRlbScpLnNob3coKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBzZWFyY2ggcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0KGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuaW1wb3J0KCk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdHNJdGVtIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzJyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IG9wdGlvbnNcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbiA9IG9wdGlvbnMuY29sbGVjdGlvbjtcblxuICAgICAgICAvLyBCaW5kIHRoZSBjb2xsZWN0aW9uIGV2ZW50c1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgncmVzZXQnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ2FkZCcsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgncmVtb3ZlJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdzeW5jJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5fYWRkQWxsKCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBhbGwgc2VhcmNoIHJlc3VsdHMgaXRlbXMgdG8gdGhlIHZpZXcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYWRkQWxsKCkge1xuICAgICAgICB0aGlzLiRlbC5lbXB0eSgpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uZm9yRWFjaCh0aGlzLl9hZGRPbmUsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgb25lIHNlYXJjaCByZXN1bHRzIGl0ZW0gdG8gdGhlIHZpZXcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYWRkT25lKHByb2R1Y3QpIHtcbiAgICAgICAgbGV0IHZpZXcgPSBuZXcgU2VhcmNoUmVzdWx0c0l0ZW0oe1xuICAgICAgICAgICAgbW9kZWw6IHByb2R1Y3QsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuJGVsLmFwcGVuZCh2aWV3LnJlbmRlcigpLmVsKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hMb2FkTW9yZSBmcm9tICcuL3NlYXJjaC1sb2FkLW1vcmUnO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoJyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5mb3JtID0gbmV3IFNlYXJjaEZvcm0oe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuZm9ybSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzID0gbmV3IFNlYXJjaFJlc3VsdHMoe1xuICAgICAgICAgICAgY29sbGVjdGlvbjogdGhpcy5tb2RlbC5yZXN1bHRzLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmxvYWRNb3JlID0gbmV3IFNlYXJjaExvYWRNb3JlKHtcbiAgICAgICAgICAgIG1vZGVsOiB0aGlzLm1vZGVsLmxvYWRNb3JlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuZm9ybS5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnJlbmRlcigpO1xuXG4gICAgICAgIGlmKHRoaXMubW9kZWwuZ2V0KCdzdGFydGVkJykpIHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUucmVuZGVyKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiJdfQ==
