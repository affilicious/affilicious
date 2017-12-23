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
            'successMessage': message,
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
        'click .aff-import-search-results-item-actions-import': 'import',
        'click .aff-import-search-results-item-actions-retry-import': 'import'
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvaW1wb3J0LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLXJlc3VsdHMtaXRlbS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2guanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2guanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7OztBQ0FBOzs7O0FBQ0E7Ozs7OztBQUVBLElBQUksY0FBYyxzQkFBbEI7QUFDQSxJQUFJLGFBQWEscUJBQWUsRUFBQyxPQUFPLFdBQVIsRUFBZixDQUFqQjs7QUFFQSxXQUFXLE1BQVg7Ozs7Ozs7O0FDTkEsSUFBSSxTQUFTLFNBQVMsS0FBVCxDQUFlLE1BQWYsQ0FBc0I7QUFDL0IsY0FBVTtBQUNOLHdCQUFnQixRQURWO0FBRU4sdUJBQWUsSUFGVDtBQUdOLDBCQUFrQixhQUhaO0FBSU4sMEJBQWtCLElBSlo7QUFLTiw0QkFBb0IsSUFMZDtBQU1OLGtCQUFVO0FBTko7QUFEcUIsQ0FBdEIsQ0FBYjs7a0JBV2UsTTs7Ozs7Ozs7O0FDWGY7Ozs7QUFDQTs7Ozs7O0FBRUEsSUFBSSxTQUFTLFNBQVMsS0FBVCxDQUFlLE1BQWYsQ0FBc0I7QUFDL0IsY0FBVTtBQUNOLGtCQUFVO0FBREosS0FEcUI7O0FBSy9COzs7OztBQUtBLGNBVitCLHdCQVVsQjtBQUNULGFBQUssTUFBTCxHQUFjLHNCQUFkO0FBQ0EsYUFBSyxNQUFMLEdBQWMsc0JBQWQ7O0FBRUEsYUFBSyxNQUFMLENBQVksRUFBWixDQUFlLHVDQUFmLEVBQXdELEtBQUssTUFBN0QsRUFBcUUsSUFBckU7QUFDSCxLQWY4Qjs7O0FBaUIvQjs7Ozs7OztBQU9BLFVBeEIrQixtQkF3QnhCLE9BeEJ3QixFQXdCZjtBQUFBOztBQUNaLFlBQUksT0FBTztBQUNQLHVCQUFXO0FBQ1Asd0JBQVMsUUFBUSxVQUFSLENBQW1CLElBRHJCO0FBRVAsd0JBQVMsUUFBUSxVQUFSLENBQW1CLElBRnJCO0FBR1AseUJBQVUsUUFBUSxVQUFSLENBQW1CLEtBSHRCO0FBSVAsaUNBQWtCLFFBQVEsVUFBUixDQUFtQjtBQUo5QixhQURKO0FBT1Asc0JBQVUsS0FBSyxNQUFMLENBQVksVUFQZjtBQVFQLG9CQUFRLEtBQUssTUFBTCxDQUFZLElBQVosQ0FBaUI7QUFSbEIsU0FBWDs7QUFXQSxlQUFPLElBQVAsQ0FBWTtBQUNSLGtCQUFNLE1BREU7QUFFUixpQkFBSyxLQUFLLFNBQUwsRUFGRztBQUdSLGtCQUFNO0FBSEUsU0FBWixFQUlHLElBSkgsQ0FJUSxVQUFDLE1BQUQsRUFBWTtBQUNoQixnQkFBSSxlQUFlLENBQUMsQ0FBQyxVQUFVLEVBQVgsRUFBZSxJQUFmLElBQXVCLEVBQXhCLEVBQTRCLGFBQTVCLElBQTZDLElBQWhFOztBQUVBLGdCQUFHLFlBQUgsRUFBaUI7QUFDYixzQkFBSyxNQUFMLENBQVksT0FBWixDQUFvQixtQ0FBcEIsRUFBeUQsWUFBekQ7QUFDSDs7QUFFRCxvQkFBUSxrQkFBUjtBQUNILFNBWkQsRUFZRyxJQVpILENBWVEsVUFBQyxNQUFELEVBQVk7QUFDaEIsZ0JBQUksZUFBZSxDQUFDLENBQUMsQ0FBQyxDQUFDLFVBQVUsRUFBWCxFQUFlLFlBQWYsSUFBK0IsRUFBaEMsRUFBb0MsSUFBcEMsSUFBNEMsRUFBN0MsRUFBaUQsQ0FBakQsS0FBdUQsRUFBeEQsRUFBNEQsT0FBNUQsSUFBdUUsSUFBMUY7O0FBRUEsb0JBQVEsZ0JBQVIsQ0FBeUIsWUFBekI7QUFDSCxTQWhCRDtBQWlCSCxLQXJEOEI7OztBQXVEL0I7Ozs7Ozs7QUFPQSxhQTlEK0IsdUJBOERuQjtBQUNSLGVBQU8seUJBQXlCLElBQXpCLGlCQUNVLEtBQUssR0FBTCxDQUFTLFFBQVQsQ0FEVixDQUFQO0FBR0g7QUFsRThCLENBQXRCLENBQWI7O2tCQXFFZSxNOzs7Ozs7OztBQ3hFZixJQUFJLGFBQWEsU0FBUyxLQUFULENBQWUsTUFBZixDQUFzQjtBQUNuQyxjQUFVO0FBQ04sZ0JBQVEsRUFERjtBQUVOLGdCQUFRLFVBRkY7QUFHTixvQkFBWSxLQUhOO0FBSU4sb0JBQVksSUFKTjtBQUtOLG9CQUFZLElBTE47QUFNTixxQkFBYSxLQU5QO0FBT04sZ0JBQVEsUUFQRjtBQVFOLHdCQUFnQixJQVJWO0FBU04sbUJBQVcsS0FUTDtBQVVOLGlCQUFTLEtBVkg7QUFXTix3QkFBZ0IsSUFYVjtBQVlOLHFCQUFhLEtBWlA7QUFhTiw0QkFBb0IsSUFiZDtBQWNOLDhCQUFzQjtBQWRoQixLQUR5Qjs7QUFrQm5DOzs7Ozs7QUFNQSxVQXhCbUMsb0JBd0IxQjtBQUNMLGFBQUssR0FBTCxDQUFTO0FBQ0wsdUJBQVcsSUFETjtBQUVMLHFCQUFTLEtBRko7QUFHTCw0QkFBZ0IsSUFIWDtBQUlMLHlCQUFhLEtBSlI7QUFLTCxnQ0FBb0I7QUFMZixTQUFUOztBQVFBLGFBQUssT0FBTCxDQUFhLDZDQUFiLEVBQTRELElBQTVEO0FBQ0gsS0FsQ2tDOzs7QUFvQ25DOzs7Ozs7QUFNQSxRQTFDbUMsa0JBMEM1QjtBQUNILGFBQUssR0FBTCxDQUFTLFNBQVQsRUFBb0IsS0FBcEI7O0FBRUEsYUFBSyxPQUFMLENBQWEsMkNBQWIsRUFBMEQsSUFBMUQ7QUFDSCxLQTlDa0M7OztBQWdEbkM7Ozs7Ozs7QUFPQSxhQXZEbUMsdUJBdURUO0FBQUEsWUFBaEIsT0FBZ0IsdUVBQU4sSUFBTTs7QUFDdEIsYUFBSyxHQUFMLENBQVM7QUFDTCx1QkFBVyxLQUROO0FBRUwseUJBQWEsSUFGUjtBQUdMLGdDQUFvQjtBQUhmLFNBQVQ7O0FBTUEsYUFBSyxPQUFMLENBQWEscURBQWIsRUFBb0UsSUFBcEU7QUFDSCxLQS9Ea0M7OztBQWlFbkM7Ozs7Ozs7QUFPQSxTQXhFbUMsbUJBd0ViO0FBQUEsWUFBaEIsT0FBZ0IsdUVBQU4sSUFBTTs7QUFDbEIsYUFBSyxHQUFMLENBQVM7QUFDTCx1QkFBVyxLQUROO0FBRUwscUJBQVMsSUFGSjtBQUdMLDRCQUFnQjtBQUhYLFNBQVQ7O0FBTUEsYUFBSyxPQUFMLENBQWEsNENBQWIsRUFBMkQsSUFBM0Q7QUFDSDtBQWhGa0MsQ0FBdEIsQ0FBakI7O2tCQW1GZSxVOzs7Ozs7OztBQ25GZixJQUFJLGlCQUFpQixTQUFTLEtBQVQsQ0FBZSxNQUFmLENBQXNCO0FBQ3ZDLGNBQVU7QUFDTixtQkFBVyxJQURMO0FBRU4sbUJBQVcsS0FGTDtBQUdOLHFCQUFhLEtBSFA7QUFJTixpQkFBUyxLQUpIO0FBS04sd0JBQWdCO0FBTFYsS0FENkI7O0FBU3ZDOzs7Ozs7QUFNQSxRQWZ1QyxrQkFlaEM7QUFDSCxhQUFLLEdBQUwsQ0FBUyxTQUFULEVBQW9CLElBQXBCO0FBQ0EsYUFBSyxPQUFMLENBQWEseUNBQWIsRUFBd0QsSUFBeEQ7QUFDSCxLQWxCc0M7OztBQW9CdkM7Ozs7Ozs7QUFPQSxRQTNCdUMsa0JBMkJsQjtBQUFBLFlBQWhCLE9BQWdCLHVFQUFOLElBQU07O0FBQ2pCLGFBQUssR0FBTCxDQUFTO0FBQ0wsdUJBQVcsS0FETjtBQUVMLHVCQUFXO0FBRk4sU0FBVDs7QUFLQSxhQUFLLE9BQUwsQ0FBYSx5Q0FBYixFQUF3RCxJQUF4RDtBQUNILEtBbENzQzs7O0FBb0N2Qzs7Ozs7O0FBTUEsYUExQ3VDLHVCQTBDM0I7QUFDUixhQUFLLEdBQUwsQ0FBUztBQUNMLHVCQUFZLEtBRFA7QUFFTCx5QkFBYTtBQUZSLFNBQVQ7O0FBS0EsYUFBSyxPQUFMLENBQWEsK0NBQWIsRUFBOEQsSUFBOUQ7QUFDSCxLQWpEc0M7OztBQW1EdkM7Ozs7Ozs7QUFPQSxTQTFEdUMsbUJBMERqQjtBQUFBLFlBQWhCLE9BQWdCLHVFQUFOLElBQU07O0FBQ2xCLGFBQUssR0FBTCxDQUFTO0FBQ0wsdUJBQVcsSUFETjtBQUVMLHVCQUFXLEtBRk47QUFHTCxxQkFBUyxJQUhKO0FBSUwsNEJBQWdCO0FBSlgsU0FBVDs7QUFPQSxhQUFLLE9BQUwsQ0FBYSwwQ0FBYixFQUF5RCxJQUF6RDtBQUNIO0FBbkVzQyxDQUF0QixDQUFyQjs7a0JBc0VlLGM7Ozs7Ozs7O0FDdEVmLElBQUksb0JBQW9CLFNBQVMsS0FBVCxDQUFlLE1BQWYsQ0FBc0I7QUFDMUMsY0FBVTtBQUNOLG1CQUFXLEtBREw7QUFFTixtQkFBVyxLQUZMO0FBR04sMEJBQWtCLElBSFo7QUFJTixpQkFBUyxLQUpIO0FBS04sd0JBQWdCO0FBTFYsS0FEZ0M7O0FBUzFDOzs7Ozs7QUFNQSxVQWYwQyxxQkFlakM7QUFDTCxhQUFLLEdBQUwsQ0FBUyxTQUFULEVBQW9CLElBQXBCOztBQUVBLGFBQUssT0FBTCxDQUFhLDhDQUFiLEVBQTZELElBQTdEO0FBQ0gsS0FuQnlDOzs7QUFxQjFDOzs7Ozs7O0FBT0Esc0JBNUIwQyxnQ0E0QlA7QUFBQSxZQUFoQixPQUFnQix1RUFBTixJQUFNOztBQUMvQixhQUFLLEdBQUwsQ0FBUztBQUNMLHVCQUFXLEtBRE47QUFFTCx1QkFBVyxJQUZOO0FBR0wsOEJBQWtCLE9BSGI7QUFJTCw2QkFBaUI7QUFDYixvQ0FBb0I7QUFEUDtBQUpaLFNBQVQ7O0FBU0EsYUFBSyxPQUFMLENBQWEsK0NBQWIsRUFBOEQsSUFBOUQ7QUFDSCxLQXZDeUM7OztBQXlDMUM7Ozs7Ozs7QUFPQSxvQkFoRDBDLDhCQWdEVDtBQUFBLFlBQWhCLE9BQWdCLHVFQUFOLElBQU07O0FBQzdCLGFBQUssR0FBTCxDQUFTO0FBQ0wsdUJBQVcsS0FETjtBQUVMLHFCQUFTLElBRko7QUFHTCw0QkFBZ0I7QUFIWCxTQUFUOztBQU1BLGFBQUssT0FBTCxDQUFhLDZDQUFiLEVBQTRELElBQTVEO0FBQ0g7QUF4RHlDLENBQXRCLENBQXhCOztrQkEyRGUsaUI7Ozs7Ozs7OztBQzNEZjs7Ozs7O0FBRUEsSUFBSSxnQkFBZ0IsU0FBUyxVQUFULENBQW9CLE1BQXBCLENBQTJCO0FBQzNDLHNDQUQyQzs7QUFHM0M7Ozs7OztBQU1BLGNBVDJDLHdCQVM5QjtBQUNULGFBQUssRUFBTCxDQUFRLE1BQVIsRUFBZ0IsS0FBSyxtQkFBckIsRUFBMEMsSUFBMUM7QUFDSCxLQVgwQzs7O0FBYTNDOzs7Ozs7OztBQVFBLFdBQU8sZUFBUyxRQUFULEVBQW1CO0FBQ3RCLGVBQU8sWUFBWSxTQUFTLE9BQXJCLEdBQStCLFNBQVMsSUFBeEMsR0FBK0MsRUFBdEQ7QUFDSCxLQXZCMEM7O0FBeUIzQzs7Ozs7OztBQU9BLGNBaEMyQyxzQkFnQ2hDLEtBaENnQyxFQWdDekI7QUFDZCxhQUFLLE9BQUwsQ0FBYSw4Q0FBYixFQUE2RCxLQUE3RDtBQUNILEtBbEMwQzs7O0FBb0MzQzs7Ozs7O0FBTUEsdUJBMUMyQyxpQ0EwQ3JCO0FBQ2xCLGFBQUssT0FBTCxDQUFhLEtBQUssbUJBQWxCLEVBQXVDLElBQXZDO0FBQ0gsS0E1QzBDOzs7QUE4QzNDOzs7Ozs7QUFNQSx1QkFwRDJDLCtCQW9EdkIsS0FwRHVCLEVBb0RoQjtBQUN2QixjQUFNLEVBQU4sQ0FBUyw4Q0FBVCxFQUF5RCxLQUFLLFVBQTlELEVBQTBFLElBQTFFO0FBQ0g7QUF0RDBDLENBQTNCLENBQXBCOztrQkF5RGUsYTs7Ozs7Ozs7O0FDM0RmOzs7O0FBQ0E7Ozs7QUFDQTs7Ozs7O0FBRUEsSUFBSSxTQUFTLFNBQVMsS0FBVCxDQUFlLE1BQWYsQ0FBc0I7QUFDL0IsY0FBVTtBQUNOLG1CQUFXLEtBREw7QUFFTixrQkFBVSxpQ0FGSjtBQUdOLGdCQUFTO0FBSEgsS0FEcUI7O0FBTy9COzs7Ozs7QUFNQSxjQWIrQixzQkFhcEIsT0Fib0IsRUFhWDtBQUNoQixhQUFLLElBQUwsR0FBWSwwQkFBWjtBQUNBLGFBQUssT0FBTCxHQUFlLDZCQUFmO0FBQ0EsYUFBSyxRQUFMLEdBQWdCLDhCQUFoQjtBQUNBLGFBQUssSUFBTCxHQUFZLFdBQVcsUUFBUSxJQUFuQixHQUEwQixRQUFRLElBQWxDLEdBQXlDLENBQXJEOztBQUVBLGFBQUssT0FBTCxDQUFhLEVBQWIsQ0FBZ0IsOENBQWhCLEVBQWdFLEtBQUssTUFBckUsRUFBNkUsSUFBN0U7QUFDQSxhQUFLLElBQUwsQ0FBVSxFQUFWLENBQWEsNkNBQWIsRUFBNEQsS0FBSyxLQUFqRSxFQUF3RSxJQUF4RTtBQUNBLGFBQUssUUFBTCxDQUFjLEVBQWQsQ0FBaUIseUNBQWpCLEVBQTRELEtBQUssSUFBakUsRUFBdUUsSUFBdkU7QUFDSCxLQXRCOEI7OztBQXdCL0I7Ozs7OztBQU1BLFNBOUIrQixtQkE4QnZCO0FBQUE7O0FBQ0osWUFBRyxLQUFLLElBQUwsQ0FBVSxHQUFWLENBQWMsTUFBZCxNQUEwQixJQUE3QixFQUFtQztBQUMvQjtBQUNIOztBQUVELGFBQUssR0FBTCxDQUFTLE1BQVQsRUFBaUIsQ0FBakI7QUFDQSxhQUFLLE9BQUwsQ0FBYSxHQUFiLEdBQW1CLEtBQUssU0FBTCxFQUFuQjs7QUFFQSxhQUFLLE9BQUwsQ0FBYSxLQUFiLEdBQXFCLElBQXJCLENBQTBCLFVBQUMsT0FBRCxFQUFhO0FBQ25DLGtCQUFLLFFBQUwsQ0FBYyxHQUFkLENBQWtCLFNBQWxCLEVBQTZCLE1BQUssa0JBQUwsQ0FBd0IsT0FBeEIsQ0FBN0I7O0FBRUEsZ0JBQUcsTUFBSyxXQUFMLENBQWlCLE9BQWpCLENBQUgsRUFBOEI7QUFDMUIsc0JBQUssSUFBTCxDQUFVLElBQVY7QUFDSCxhQUZELE1BRU87QUFDSCxzQkFBSyxJQUFMLENBQVUsU0FBVjtBQUNIO0FBQ0osU0FSRCxFQVFHLElBUkgsQ0FRUSxVQUFDLE1BQUQsRUFBWTtBQUNoQixnQkFBSSxlQUFlLENBQUMsQ0FBQyxDQUFDLENBQUMsVUFBVSxFQUFYLEVBQWUsWUFBZixJQUErQixFQUFoQyxFQUFvQyxJQUFwQyxJQUE0QyxFQUE3QyxFQUFpRCxDQUFqRCxLQUF1RCxFQUF4RCxFQUE0RCxPQUE1RCxJQUF1RSxJQUExRjs7QUFFQSxrQkFBSyxJQUFMLENBQVUsS0FBVixDQUFnQixZQUFoQjtBQUNBLGtCQUFLLFFBQUwsQ0FBYyxHQUFkLENBQWtCLFNBQWxCLEVBQTZCLEtBQTdCO0FBQ0gsU0FiRCxFQWFHLE1BYkgsQ0FhVSxZQUFNO0FBQ1osa0JBQUssR0FBTCxDQUFTLFNBQVQsRUFBb0IsSUFBcEI7QUFDSCxTQWZEO0FBZ0JILEtBdEQ4Qjs7O0FBd0QvQjs7Ozs7O0FBTUEsUUE5RCtCLGtCQThEeEI7QUFBQTs7QUFDSCxhQUFLLEdBQUwsQ0FBUyxNQUFULEVBQWlCLEtBQUssR0FBTCxDQUFTLE1BQVQsSUFBbUIsQ0FBcEM7QUFDQSxhQUFLLE9BQUwsQ0FBYSxHQUFiLEdBQW1CLEtBQUssU0FBTCxFQUFuQjs7QUFFQSxhQUFLLE9BQUwsQ0FBYSxLQUFiLENBQW1CLEVBQUMsVUFBVSxLQUFYLEVBQW5CLEVBQXNDLElBQXRDLENBQTJDLFVBQUMsT0FBRCxFQUFhO0FBQ3BELG1CQUFLLFFBQUwsQ0FBYyxJQUFkLENBQW1CLE9BQUssa0JBQUwsQ0FBd0IsT0FBeEIsQ0FBbkI7QUFDSCxTQUZELEVBRUcsSUFGSCxDQUVRLFlBQU07QUFDVixnQkFBSSxlQUFlLENBQUMsQ0FBQyxDQUFDLENBQUMsVUFBVSxFQUFYLEVBQWUsWUFBZixJQUErQixFQUFoQyxFQUFvQyxJQUFwQyxJQUE0QyxFQUE3QyxFQUFpRCxDQUFqRCxLQUF1RCxFQUF4RCxFQUE0RCxPQUE1RCxJQUF1RSxJQUExRjs7QUFFQSxtQkFBSyxRQUFMLENBQWMsS0FBZCxDQUFvQixZQUFwQjtBQUNILFNBTkQ7QUFPSCxLQXpFOEI7OztBQTJFL0I7Ozs7Ozs7QUFPQSxVQWxGK0IsbUJBa0Z4QixLQWxGd0IsRUFrRmpCO0FBQ1YsYUFBSyxPQUFMLENBQWEsdUNBQWIsRUFBc0QsS0FBdEQ7QUFDSCxLQXBGOEI7OztBQXNGL0I7Ozs7Ozs7QUFPQSxhQTdGK0IsdUJBNkZuQjtBQUNSLGVBQU8seUJBQXlCLElBQXpCLGlCQUNVLEtBQUssR0FBTCxDQUFTLFFBQVQsQ0FEVixnQkFFUSxLQUFLLElBQUwsQ0FBVSxHQUFWLENBQWMsTUFBZCxDQUZSLGdCQUdRLEtBQUssSUFBTCxDQUFVLEdBQVYsQ0FBYyxNQUFkLENBSFIsb0JBSVksS0FBSyxJQUFMLENBQVUsR0FBVixDQUFjLFVBQWQsQ0FKWixxQkFLYSxLQUFLLElBQUwsQ0FBVSxHQUFWLENBQWMsVUFBZCxDQUxiLHFCQU1hLEtBQUssSUFBTCxDQUFVLEdBQVYsQ0FBYyxVQUFkLENBTmIscUJBT2EsS0FBSyxJQUFMLENBQVUsR0FBVixDQUFjLFdBQWQsQ0FQYixnQkFRUSxLQUFLLElBQUwsQ0FBVSxHQUFWLENBQWMsTUFBZCxDQVJSLHlCQVNpQixLQUFLLElBQUwsQ0FBVSxHQUFWLENBQWMsY0FBZCxDQVRqQixnQkFVUSxLQUFLLEdBQUwsQ0FBUyxNQUFULENBVlIsQ0FBUDtBQVdILEtBekc4Qjs7O0FBMkcvQjs7Ozs7Ozs7QUFRQSxzQkFuSCtCLDhCQW1IWixPQW5IWSxFQW1ISDtBQUN4QixlQUFRLFdBQVcsUUFBUSxJQUFuQixJQUEyQixRQUFRLElBQVIsQ0FBYSxNQUFiLEdBQXNCLENBQWxELElBQ0EsS0FBSyxHQUFMLENBQVMsTUFBVCxJQUFtQixDQURuQixJQUVBLEtBQUssSUFBTCxDQUFVLEdBQVYsQ0FBYyxNQUFkLE1BQTBCLFVBRmpDO0FBR0gsS0F2SDhCOzs7QUF5SC9COzs7Ozs7OztBQVFBLGVBakkrQix1QkFpSW5CLE9BakltQixFQWlJVjtBQUNqQixlQUFPLFdBQ0EsUUFBUSxJQURSLElBRUEsUUFBUSxJQUFSLENBQWEsTUFBYixHQUFzQixDQUY3QjtBQUdIO0FBckk4QixDQUF0QixDQUFiOztrQkF3SWUsTTs7Ozs7Ozs7QUM1SWYsSUFBSSxTQUFVLFNBQVMsSUFBVCxDQUFjLE1BQWQsQ0FBcUI7QUFDL0IsUUFBSSwyQkFEMkI7O0FBRy9CLFlBQVE7QUFDSixxQ0FBNkIsWUFEekI7QUFFSiw4Q0FBc0MsWUFGbEM7QUFHSix1Q0FBK0IsY0FIM0I7QUFJSixpREFBeUMsY0FKckM7QUFLSixtREFBMkMsY0FMdkM7QUFNSix1Q0FBK0I7QUFOM0IsS0FIdUI7O0FBWS9COzs7Ozs7QUFNQSxjQWxCK0Isd0JBa0JsQjtBQUNULFlBQUksV0FBVyxPQUFPLG9DQUFQLENBQWY7QUFDQSxhQUFLLFFBQUwsR0FBZ0IsRUFBRSxRQUFGLENBQVcsU0FBUyxJQUFULEVBQVgsQ0FBaEI7O0FBRUEsYUFBSyxLQUFMLENBQVcsR0FBWCxDQUFlLGNBQWYsRUFBK0IsT0FBTyxTQUFTLElBQVQsRUFBUCxFQUF3QixJQUF4QixDQUE2QixvQkFBN0IsRUFBbUQsS0FBbkQsR0FBMkQsR0FBM0QsRUFBL0I7O0FBRUEsYUFBSyxLQUFMLENBQVcsRUFBWCxDQUFjLG1DQUFkLEVBQW1ELEtBQUssT0FBeEQsRUFBaUUsSUFBakU7QUFDSCxLQXpCOEI7OztBQTJCL0I7Ozs7Ozs7QUFPQSxVQWxDK0Isb0JBa0N0QjtBQUNMLFlBQUksT0FBTyxLQUFLLFFBQUwsQ0FBYyxLQUFLLEtBQUwsQ0FBVyxVQUF6QixDQUFYO0FBQ0EsYUFBSyxHQUFMLENBQVMsSUFBVCxDQUFjLElBQWQ7O0FBRUEsYUFBSyxHQUFMLENBQVMsSUFBVCxDQUFjLG9IQUFkLEVBQW9JLFNBQXBJLENBQThJO0FBQzFJLHNCQUFVLENBRGdJO0FBRTFJLHdCQUFZLElBRjhIO0FBRzFJLHdCQUFZLE1BSDhIO0FBSTFJLHlCQUFhLE1BSjZIO0FBSzFJLG9CQUFRLEtBTGtJO0FBTTFJLGdCQU4wSSxnQkFNckksS0FOcUksRUFNOUgsUUFOOEgsRUFNcEg7QUFDbEIsb0JBQUksQ0FBQyxNQUFNLE1BQVgsRUFBbUIsT0FBTyxVQUFQO0FBQ25CLHVCQUFPLElBQVAsQ0FBWTtBQUNSLHlCQUFLLHlCQUF5QixPQUF6QixHQUFtQyxrREFBbkMsR0FBd0YsS0FEckY7QUFFUiwwQkFBTSxLQUZFO0FBR1IsMEJBQU07QUFDRix1Q0FBZTtBQURiLHFCQUhFO0FBTVIsOEJBTlEsc0JBTUcsR0FOSCxFQU1RO0FBQ1osNEJBQUksZ0JBQUosQ0FBcUIsWUFBckIsRUFBbUMseUJBQXlCLEtBQTVEO0FBQ0gscUJBUk87QUFTUix5QkFUUSxtQkFTQTtBQUNKO0FBQ0gscUJBWE87QUFZUiwyQkFaUSxtQkFZQSxPQVpBLEVBWVM7QUFDYixrQ0FBVSxRQUFRLEdBQVIsQ0FBWSxVQUFDLE1BQUQsRUFBWTtBQUM5QixtQ0FBTztBQUNILHNDQUFNLE9BQU8sRUFEVjtBQUVILHdDQUFRLE9BQU8sS0FBUCxDQUFhO0FBRmxCLDZCQUFQO0FBSUgseUJBTFMsQ0FBVjs7QUFPQSxpQ0FBUyxPQUFUO0FBQ0g7QUFyQk8saUJBQVo7QUF1Qkg7QUEvQnlJLFNBQTlJOztBQWtDQSxlQUFPLElBQVA7QUFDSCxLQXpFOEI7OztBQTJFL0I7Ozs7Ozs7QUFPQSxXQWxGK0IsbUJBa0Z2QixJQWxGdUIsRUFrRmpCO0FBQ1YsYUFBSyxHQUFMLENBQVMsSUFBVCxDQUFjLHlCQUFkLEVBQXlDLE1BQXpDLEdBQWtELE1BQWxELHNFQUN3RCxLQUFLLElBRDdELHVDQUVxQixLQUFLLElBRjFCLGlGQUUwRyxLQUFLLElBRi9HLDRCQUdVLEtBQUssSUFIZjs7QUFPQSxhQUFLLEdBQUwsQ0FBUyxJQUFULGdDQUEyQyxLQUFLLElBQWhELFNBQTBELElBQTFELENBQStELFNBQS9ELEVBQTBFLElBQTFFO0FBQ0EsYUFBSyxLQUFMLENBQVcsR0FBWCxDQUFlO0FBQ1gsMkJBQWUsSUFESjtBQUVYLDRCQUFnQixLQUFLO0FBRlYsU0FBZjtBQUlILEtBL0Y4Qjs7O0FBaUcvQjs7Ozs7O0FBTUEsY0F2RytCLHdCQXVHbEI7QUFDVCxZQUFJLGVBQWUsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLDRCQUFkLENBQW5CO0FBQUEsWUFDSSxjQUFjLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyw2QkFBZCxDQURsQjs7QUFHQSxxQkFBYSxHQUFiLE9BQXVCLFVBQXZCLEdBQW9DLFlBQVksVUFBWixDQUF1QixVQUF2QixDQUFwQyxHQUF5RSxZQUFZLElBQVosQ0FBaUIsVUFBakIsRUFBNkIsVUFBN0IsQ0FBekU7O0FBRUEsYUFBSyxLQUFMLENBQVcsR0FBWCxDQUFlO0FBQ1gsNEJBQWdCLGFBQWEsR0FBYixFQURMO0FBRVgsMkJBQWUsWUFBWSxHQUFaO0FBRkosU0FBZjtBQUlILEtBakg4Qjs7O0FBbUgvQjs7Ozs7O0FBTUEsZ0JBekgrQiwwQkF5SGhCO0FBQ1gsWUFBSSxpQkFBaUIsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLDhCQUFkLENBQXJCO0FBQUEsWUFDSSxpQkFBaUIsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLGdDQUFkLENBRHJCO0FBQUEsWUFFSSxtQkFBbUIsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLGtDQUFkLENBRnZCO0FBQUEsWUFHSSxpQkFBaUIsZUFBZSxTQUFmLEdBQTJCLENBQTNCLEVBQThCLFNBSG5EO0FBQUEsWUFJSSxtQkFBbUIsaUJBQWlCLFNBQWpCLEdBQTZCLENBQTdCLEVBQWdDLFNBSnZEOztBQU1BLHVCQUFlLEdBQWYsT0FBeUIsZUFBekIsR0FBMkMsZUFBZSxNQUFmLEVBQTNDLEdBQXFFLGVBQWUsT0FBZixFQUFyRTtBQUNBLHVCQUFlLEdBQWYsT0FBeUIsaUJBQXpCLEdBQTZDLGlCQUFpQixNQUFqQixFQUE3QyxHQUF5RSxpQkFBaUIsT0FBakIsRUFBekU7O0FBRUEsYUFBSyxLQUFMLENBQVcsR0FBWCxDQUFlO0FBQ1gsOEJBQWtCLGVBQWUsR0FBZixFQURQO0FBRVgsOEJBQWtCLGVBQWUsR0FBZixFQUZQO0FBR1gsZ0NBQW9CLGlCQUFpQixHQUFqQjtBQUhULFNBQWY7QUFLSCxLQXhJOEI7OztBQTBJL0I7Ozs7OztBQU1BLGdCQWhKK0IsMEJBZ0poQjtBQUNYLFlBQUksaUJBQWlCLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyw4QkFBZCxDQUFyQjs7QUFFQSxhQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWU7QUFDWCxzQkFBVSxlQUFlLEdBQWY7QUFEQyxTQUFmO0FBR0g7QUF0SjhCLENBQXJCLENBQWQ7O2tCQXlKZSxNOzs7Ozs7Ozs7QUN6SmY7Ozs7QUFDQTs7Ozs7O0FBRUEsSUFBSSxTQUFTLFNBQVMsSUFBVCxDQUFjLE1BQWQsQ0FBcUI7QUFDOUIsUUFBSSxvQkFEMEI7O0FBRzlCOzs7Ozs7QUFNQSxjQVQ4Qix3QkFTakI7QUFDVCxhQUFLLE1BQUwsR0FBYyxxQkFBVztBQUNyQixtQkFBTyxLQUFLLEtBQUwsQ0FBVztBQURHLFNBQVgsQ0FBZDs7QUFJQSxhQUFLLE1BQUwsR0FBYyxxQkFBVztBQUNyQixtQkFBTyxLQUFLLEtBQUwsQ0FBVztBQURHLFNBQVgsQ0FBZDtBQUdILEtBakI2Qjs7O0FBbUI5Qjs7Ozs7O0FBTUEsVUF6QjhCLG9CQXlCckI7QUFDTCxhQUFLLE1BQUwsQ0FBWSxNQUFaO0FBQ0EsYUFBSyxNQUFMLENBQVksTUFBWjs7QUFFQSxlQUFPLElBQVA7QUFDSDtBQTlCNkIsQ0FBckIsQ0FBYjs7a0JBaUNlLE07Ozs7Ozs7O0FDcENmLElBQUksYUFBYyxTQUFTLElBQVQsQ0FBYyxNQUFkLENBQXFCO0FBQ25DLFFBQUksZ0NBRCtCOztBQUduQyxZQUFRO0FBQ0osc0NBQThCLFFBRDFCO0FBRUosMENBQWtDLFFBRjlCO0FBR0osa0JBQVU7QUFITixLQUgyQjs7QUFTbkMsa0JBQWMsS0FUcUI7O0FBV25DOzs7Ozs7QUFNQSxjQWpCbUMsd0JBaUJ0QjtBQUNULFlBQUksZUFBZSxPQUFPLHlDQUFQLEVBQWtELElBQWxELEVBQW5CO0FBQUEsWUFDSSxxQkFBcUIsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLHFCQUFkLENBRHpCOztBQUdBLGFBQUssUUFBTCxHQUFnQixFQUFFLFFBQUYsQ0FBVyxZQUFYLENBQWhCOztBQUVBLGFBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxvQkFBZixFQUFxQyx1QkFBdUIsSUFBdkIsSUFBK0IsdUJBQXVCLE1BQTNGO0FBQ0EsYUFBSyxLQUFMLENBQVcsRUFBWCxDQUFjLFFBQWQsRUFBd0IsS0FBSyxNQUE3QixFQUFxQyxJQUFyQztBQUNILEtBekJrQzs7O0FBMkJuQzs7Ozs7OztBQU9BLFVBbENtQyxvQkFrQzFCO0FBQ0wsYUFBSyxHQUFMLENBQVMsSUFBVCxDQUFjLEtBQUssUUFBTCxDQUFjLEtBQUssS0FBTCxDQUFXLFVBQXpCLENBQWQ7O0FBRUEsWUFBSSxPQUFPLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyxvQkFBZCxDQUFYO0FBQUEsWUFDSSxPQUFPLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyxxQkFBZCxDQURYO0FBQUEsWUFFSSxXQUFXLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyx5QkFBZCxDQUZmO0FBQUEsWUFHSSxXQUFXLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyx5QkFBZCxDQUhmO0FBQUEsWUFJSSxXQUFXLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyx5QkFBZCxDQUpmO0FBQUEsWUFLSSxZQUFZLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYywwQkFBZCxDQUxoQjtBQUFBLFlBTUksT0FBTyxLQUFLLEdBQUwsQ0FBUyxJQUFULENBQWMscUJBQWQsQ0FOWDtBQUFBLFlBT0ksZUFBZSxLQUFLLEdBQUwsQ0FBUyxJQUFULENBQWMsOEJBQWQsQ0FQbkI7O0FBU0EsWUFBRyxDQUFDLEtBQUssWUFBVCxFQUF1QjtBQUNuQixpQkFBSyxLQUFMO0FBQ0EsaUJBQUssWUFBTCxHQUFvQixJQUFwQjtBQUNIOztBQUVELGFBQUssR0FBTCxDQUFTLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxNQUFmLENBQVQ7QUFDQSxpQkFBUyxHQUFULENBQWEsS0FBSyxLQUFMLENBQVcsR0FBWCxDQUFlLFVBQWYsQ0FBYjtBQUNBLGlCQUFTLEdBQVQsQ0FBYSxLQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWUsVUFBZixDQUFiO0FBQ0EsaUJBQVMsR0FBVCxDQUFhLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxVQUFmLENBQWI7QUFDQSxrQkFBVSxHQUFWLENBQWMsS0FBSyxLQUFMLENBQVcsR0FBWCxDQUFlLFdBQWYsQ0FBZDtBQUNBLGFBQUssR0FBTCxDQUFTLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxNQUFmLENBQVQ7QUFDQSxxQkFBYSxHQUFiLENBQWlCLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxjQUFmLENBQWpCOztBQUVBLGVBQU8sSUFBUDtBQUNILEtBNURrQzs7O0FBOERuQzs7Ozs7OztBQU9BLFVBckVtQyxrQkFxRTVCLENBckU0QixFQXFFekI7QUFDTixVQUFFLGNBQUY7O0FBRUEsYUFBSyxNQUFMO0FBQ0EsYUFBSyxLQUFMLENBQVcsTUFBWDtBQUNILEtBMUVrQzs7O0FBNEVuQzs7Ozs7O0FBTUEsVUFsRm1DLG9CQWtGMUI7QUFDTCxZQUFJLE9BQU8sS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLG9CQUFkLENBQVg7QUFBQSxZQUNJLE9BQU8sS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLHFCQUFkLENBRFg7QUFBQSxZQUVJLFdBQVcsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLHlCQUFkLENBRmY7QUFBQSxZQUdJLFdBQVcsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLHlCQUFkLENBSGY7QUFBQSxZQUlJLFdBQVcsS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLHlCQUFkLENBSmY7QUFBQSxZQUtJLFlBQVksS0FBSyxHQUFMLENBQVMsSUFBVCxDQUFjLDBCQUFkLENBTGhCO0FBQUEsWUFNSSxPQUFPLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyxxQkFBZCxDQU5YO0FBQUEsWUFPSSxlQUFlLEtBQUssR0FBTCxDQUFTLElBQVQsQ0FBYyw4QkFBZCxDQVBuQjs7QUFTQSxhQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWU7QUFDWCxvQkFBUSxLQUFLLEdBQUwsRUFERztBQUVYLG9CQUFRLEtBQUssR0FBTCxFQUZHO0FBR1gsd0JBQVksU0FBUyxNQUFULEtBQW9CLENBQXBCLEdBQXdCLFNBQVMsR0FBVCxFQUF4QixHQUF5QyxLQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWUsVUFBZixDQUgxQztBQUlYLHdCQUFZLFNBQVMsTUFBVCxLQUFvQixDQUFwQixHQUF3QixTQUFTLEdBQVQsRUFBeEIsR0FBeUMsS0FBSyxLQUFMLENBQVcsR0FBWCxDQUFlLFVBQWYsQ0FKMUM7QUFLWCx5QkFBYSxVQUFVLE1BQVYsS0FBcUIsQ0FBckIsR0FBeUIsVUFBVSxHQUFWLEVBQXpCLEdBQTJDLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxXQUFmLENBTDdDO0FBTVgsb0JBQVEsS0FBSyxNQUFMLEtBQWdCLENBQWhCLEdBQW9CLEtBQUssR0FBTCxFQUFwQixHQUFpQyxLQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWUsTUFBZixDQU45QjtBQU9YLHdCQUFZLFNBQVMsTUFBVCxLQUFvQixDQUFwQixHQUF3QixTQUFTLEdBQVQsRUFBeEIsR0FBeUMsS0FBSyxLQUFMLENBQVcsR0FBWCxDQUFlLFVBQWYsQ0FQMUM7QUFRWCw0QkFBZ0IsYUFBYSxNQUFiLEtBQXdCLENBQXhCLEdBQTRCLGFBQWEsR0FBYixFQUE1QixHQUFpRCxLQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWUsY0FBZjtBQVJ0RCxTQUFmO0FBVUg7QUF0R2tDLENBQXJCLENBQWxCOztrQkF5R2UsVTs7Ozs7Ozs7QUN6R2YsSUFBSSxpQkFBa0IsU0FBUyxJQUFULENBQWMsTUFBZCxDQUFxQjtBQUN2QyxRQUFJLHFDQURtQzs7QUFHdkMsWUFBUTtBQUNKLHFEQUE2QztBQUR6QyxLQUgrQjs7QUFPdkM7Ozs7OztBQU1BLGNBYnVDLHdCQWExQjtBQUNULFlBQUksZUFBZSxPQUFPLDhDQUFQLEVBQXVELElBQXZELEVBQW5COztBQUVBLGFBQUssUUFBTCxHQUFnQixFQUFFLFFBQUYsQ0FBVyxZQUFYLENBQWhCO0FBQ0EsYUFBSyxLQUFMLENBQVcsRUFBWCxDQUFjLFFBQWQsRUFBd0IsS0FBSyxNQUE3QixFQUFxQyxJQUFyQztBQUNILEtBbEJzQzs7O0FBb0J2Qzs7Ozs7OztBQU9BLFVBM0J1QyxvQkEyQjlCO0FBQ0wsYUFBSyxHQUFMLENBQVMsSUFBVCxDQUFjLEtBQUssUUFBTCxDQUFjLEtBQUssS0FBTCxDQUFXLFVBQXpCLENBQWQ7O0FBRUEsZUFBTyxJQUFQO0FBQ0gsS0EvQnNDOzs7QUFpQ3ZDOzs7Ozs7QUFNQSxRQXZDdUMsa0JBdUNoQztBQUNILGFBQUssS0FBTCxDQUFXLElBQVg7QUFDSDtBQXpDc0MsQ0FBckIsQ0FBdEI7O2tCQTRDZSxjOzs7Ozs7OztBQzVDZixJQUFJLG9CQUFvQixTQUFTLElBQVQsQ0FBYyxNQUFkLENBQXFCO0FBQ3pDLGFBQVMsS0FEZ0M7O0FBR3pDLGVBQVcsRUFIOEI7O0FBS3pDLFlBQVE7QUFDSixtRUFBMkQsU0FEdkQ7QUFFSixnRUFBd0QsUUFGcEQ7QUFHSixzRUFBOEQ7QUFIMUQsS0FMaUM7O0FBV3pDOzs7Ozs7QUFNQSxjQWpCeUMsd0JBaUI1QjtBQUNULFlBQUksZUFBZSxPQUFPLGlEQUFQLEVBQTBELElBQTFELEVBQW5COztBQUVBLGFBQUssUUFBTCxHQUFnQixFQUFFLFFBQUYsQ0FBVyxZQUFYLENBQWhCO0FBQ0EsYUFBSyxLQUFMLENBQVcsRUFBWCxDQUFjLFFBQWQsRUFBd0IsS0FBSyxNQUE3QixFQUFxQyxJQUFyQztBQUNILEtBdEJ3Qzs7O0FBd0J6Qzs7Ozs7OztBQU9BLFVBL0J5QyxvQkErQmhDO0FBQ0wsYUFBSyxHQUFMLENBQVMsSUFBVCxDQUFjLEtBQUssUUFBTCxDQUFjLEtBQUssS0FBTCxDQUFXLFVBQXpCLENBQWQ7O0FBRUEsZUFBTyxJQUFQO0FBQ0gsS0FuQ3dDOzs7QUFxQ3pDOzs7Ozs7O0FBT0EsV0E1Q3lDLG1CQTRDakMsQ0E1Q2lDLEVBNEM5QjtBQUNQLFVBQUUsY0FBRjs7QUFFQSxhQUFLLEdBQUwsQ0FBUyxJQUFULENBQWMsbURBQWQsRUFBbUUsSUFBbkU7QUFDQSxhQUFLLEdBQUwsQ0FBUyxJQUFULENBQWMsK0NBQWQsRUFBK0QsSUFBL0Q7QUFDSCxLQWpEd0M7OztBQW1EekM7Ozs7Ozs7QUFPQSxVQTFEeUMsbUJBMERsQyxDQTFEa0MsRUEwRC9CO0FBQ04sVUFBRSxjQUFGOztBQUVBLGFBQUssS0FBTCxDQUFXLE1BQVg7QUFDSDtBQTlEd0MsQ0FBckIsQ0FBeEI7O2tCQWlFZSxpQjs7Ozs7Ozs7O0FDakVmOzs7Ozs7QUFFQSxJQUFJLGdCQUFnQixTQUFTLElBQVQsQ0FBYyxNQUFkLENBQXFCO0FBQ3JDLFFBQUksbUNBRGlDOztBQUdyQzs7Ozs7OztBQU9BLGNBVnFDLHNCQVUxQixPQVYwQixFQVVqQjtBQUFBOztBQUNoQixhQUFLLFVBQUwsR0FBa0IsUUFBUSxVQUExQjs7QUFFQTtBQUNBLGFBQUssVUFBTCxDQUFnQixJQUFoQixDQUFxQixPQUFyQixFQUE4QjtBQUFBLG1CQUFNLE1BQUssTUFBTCxFQUFOO0FBQUEsU0FBOUI7QUFDQSxhQUFLLFVBQUwsQ0FBZ0IsSUFBaEIsQ0FBcUIsS0FBckIsRUFBNEI7QUFBQSxtQkFBTSxNQUFLLE1BQUwsRUFBTjtBQUFBLFNBQTVCO0FBQ0EsYUFBSyxVQUFMLENBQWdCLElBQWhCLENBQXFCLFFBQXJCLEVBQStCO0FBQUEsbUJBQU0sTUFBSyxNQUFMLEVBQU47QUFBQSxTQUEvQjtBQUNBLGFBQUssVUFBTCxDQUFnQixJQUFoQixDQUFxQixNQUFyQixFQUE2QjtBQUFBLG1CQUFNLE1BQUssTUFBTCxFQUFOO0FBQUEsU0FBN0I7QUFDSCxLQWxCb0M7OztBQW9CckM7Ozs7OztBQU1BLFVBMUJxQyxvQkEwQjVCO0FBQ0wsYUFBSyxPQUFMO0FBQ0gsS0E1Qm9DOzs7QUE4QnJDOzs7Ozs7QUFNQSxXQXBDcUMscUJBb0MzQjtBQUNOLGFBQUssR0FBTCxDQUFTLEtBQVQ7QUFDQSxhQUFLLFVBQUwsQ0FBZ0IsT0FBaEIsQ0FBd0IsS0FBSyxPQUE3QixFQUFzQyxJQUF0QztBQUNILEtBdkNvQzs7O0FBeUNyQzs7Ozs7O0FBTUEsV0EvQ3FDLG1CQStDN0IsT0EvQzZCLEVBK0NwQjtBQUNiLFlBQUksT0FBTyxnQ0FBc0I7QUFDN0IsbUJBQU87QUFEc0IsU0FBdEIsQ0FBWDs7QUFJQSxhQUFLLEdBQUwsQ0FBUyxNQUFULENBQWdCLEtBQUssTUFBTCxHQUFjLEVBQTlCO0FBQ0g7QUFyRG9DLENBQXJCLENBQXBCOztrQkF3RGUsYTs7Ozs7Ozs7O0FDMURmOzs7O0FBQ0E7Ozs7QUFDQTs7Ozs7O0FBRUEsSUFBSSxTQUFTLFNBQVMsSUFBVCxDQUFjLE1BQWQsQ0FBcUI7QUFDOUIsUUFBSSwyQkFEMEI7O0FBRzlCOzs7Ozs7QUFNQSxjQVQ4Qix3QkFTakI7QUFDVCxhQUFLLElBQUwsR0FBWSx5QkFBZTtBQUN2QixtQkFBTyxLQUFLLEtBQUwsQ0FBVztBQURLLFNBQWYsQ0FBWjs7QUFJQSxhQUFLLE9BQUwsR0FBZSw0QkFBa0I7QUFDN0Isd0JBQVksS0FBSyxLQUFMLENBQVc7QUFETSxTQUFsQixDQUFmOztBQUlBLGFBQUssUUFBTCxHQUFnQiw2QkFBbUI7QUFDL0IsbUJBQU8sS0FBSyxLQUFMLENBQVc7QUFEYSxTQUFuQixDQUFoQjs7QUFJQSxhQUFLLEtBQUwsQ0FBVyxFQUFYLENBQWMsUUFBZCxFQUF3QixLQUFLLE1BQTdCLEVBQXFDLElBQXJDO0FBQ0gsS0F2QjZCOzs7QUF5QjlCOzs7Ozs7QUFNQSxVQS9COEIsb0JBK0JyQjtBQUNMLGFBQUssSUFBTCxDQUFVLE1BQVY7QUFDQSxhQUFLLE9BQUwsQ0FBYSxNQUFiOztBQUVBLFlBQUcsS0FBSyxLQUFMLENBQVcsR0FBWCxDQUFlLFNBQWYsQ0FBSCxFQUE4QjtBQUMxQixpQkFBSyxRQUFMLENBQWMsTUFBZDtBQUNIOztBQUVELGVBQU8sSUFBUDtBQUNIO0FBeEM2QixDQUFyQixDQUFiOztrQkEyQ2UsTSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCJpbXBvcnQgSW1wb3J0IGZyb20gJy4vbW9kZWwvaW1wb3J0JztcbmltcG9ydCBJbXBvcnRWaWV3IGZyb20gJy4vdmlldy9pbXBvcnQnO1xuXG5sZXQgaW1wb3J0TW9kZWwgPSBuZXcgSW1wb3J0KCk7XG5sZXQgaW1wb3J0VmlldyA9IG5ldyBJbXBvcnRWaWV3KHttb2RlbDogaW1wb3J0TW9kZWx9KTtcblxuaW1wb3J0Vmlldy5yZW5kZXIoKTtcbiIsImxldCBDb25maWcgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzZWxlY3RlZFNob3AnOiAnYW1hem9uJyxcbiAgICAgICAgJ25ld1Nob3BOYW1lJzogbnVsbCxcbiAgICAgICAgJ3NlbGVjdGVkQWN0aW9uJzogJ25ldy1wcm9kdWN0JyxcbiAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbnVsbCxcbiAgICAgICAgJ3JlcGxhY2VQcm9kdWN0SWQnOiBudWxsLFxuICAgICAgICAnc3RhdHVzJzogJ3B1Ymxpc2gnLFxuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgQ29uZmlnO1xuIiwiaW1wb3J0IFNlYXJjaCBmcm9tICcuL3NlYXJjaCc7XG5pbXBvcnQgQ29uZmlnIGZyb20gJy4vY29uZmlnJztcblxubGV0IEltcG9ydCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2FjdGlvbic6ICdhZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25faW1wb3J0JyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMuc2VhcmNoID0gbmV3IFNlYXJjaCgpO1xuICAgICAgICB0aGlzLmNvbmZpZyA9IG5ldyBDb25maWcoKTtcblxuICAgICAgICB0aGlzLnNlYXJjaC5vbignYWZmOmFtYXpvbi1pbXBvcnQ6aW1wb3J0LXJlc3VsdHMtaXRlbScsIHRoaXMuaW1wb3J0LCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBwcm9kdWN0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBwcm9kdWN0XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChwcm9kdWN0KSB7XG4gICAgICAgIGxldCBkYXRhID0ge1xuICAgICAgICAgICAgJ3Byb2R1Y3QnOiB7XG4gICAgICAgICAgICAgICAgJ25hbWUnIDogcHJvZHVjdC5hdHRyaWJ1dGVzLm5hbWUsXG4gICAgICAgICAgICAgICAgJ3R5cGUnIDogcHJvZHVjdC5hdHRyaWJ1dGVzLnR5cGUsXG4gICAgICAgICAgICAgICAgJ3Nob3BzJyA6IHByb2R1Y3QuYXR0cmlidXRlcy5zaG9wcyxcbiAgICAgICAgICAgICAgICAnY3VzdG9tX3ZhbHVlcycgOiBwcm9kdWN0LmF0dHJpYnV0ZXMuY3VzdG9tX3ZhbHVlcyxcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAnY29uZmlnJzogdGhpcy5jb25maWcuYXR0cmlidXRlcyxcbiAgICAgICAgICAgICdmb3JtJzogdGhpcy5zZWFyY2guZm9ybS5hdHRyaWJ1dGVzLFxuICAgICAgICB9O1xuXG4gICAgICAgIGpRdWVyeS5hamF4KHtcbiAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgICAgICAgIHVybDogdGhpcy5fYnVpbGRVcmwoKSxcbiAgICAgICAgICAgIGRhdGE6IGRhdGEsXG4gICAgICAgIH0pLmRvbmUoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IHNob3BUZW1wbGF0ZSA9ICgocmVzdWx0IHx8IHt9KS5kYXRhIHx8IHt9KS5zaG9wX3RlbXBsYXRlIHx8IG51bGw7XG5cbiAgICAgICAgICAgIGlmKHNob3BUZW1wbGF0ZSkge1xuICAgICAgICAgICAgICAgIHRoaXMuY29uZmlnLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OmNvbmZpZzphZGQtc2hvcCcsIHNob3BUZW1wbGF0ZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHByb2R1Y3Quc2hvd1N1Y2Nlc3NNZXNzYWdlKCk7XG4gICAgICAgIH0pLmZhaWwoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgcHJvZHVjdC5zaG93RXJyb3JNZXNzYWdlKGVycm9yTWVzc2FnZSk7XG4gICAgICAgIH0pXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBpbXBvcnQgdXJsIGJhc2VkIG9uIHRoZSBnaXZlbiBwYXJhbWV0ZXJzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYnVpbGRVcmwoKSB7XG4gICAgICAgIHJldHVybiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYWpheFxuICAgICAgICAgICAgKyBgP2FjdGlvbj0ke3RoaXMuZ2V0KCdhY3Rpb24nKX1gXG4gICAgICAgIDtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IEltcG9ydDtcbiIsImxldCBTZWFyY2hGb3JtID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAndGVybSc6ICcnLFxuICAgICAgICAndHlwZSc6ICdrZXl3b3JkcycsXG4gICAgICAgICdjYXRlZ29yeSc6ICdBbGwnLFxuICAgICAgICAnbWluUHJpY2UnOiBudWxsLFxuICAgICAgICAnbWF4UHJpY2UnOiBudWxsLFxuICAgICAgICAnY29uZGl0aW9uJzogJ05ldycsXG4gICAgICAgICdzb3J0JzogJy1wcmljZScsXG4gICAgICAgICd3aXRoVmFyaWFudHMnOiAnbm8nLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnZXJyb3InOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICdub1Jlc3VsdHMnOiBmYWxzZSxcbiAgICAgICAgJ25vUmVzdWx0c01lc3NhZ2UnOiBudWxsLFxuICAgICAgICAncHJvdmlkZXJDb25maWd1cmVkJzogZmFsc2VcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3VibWl0IHRoZSBmb3JtIHRoZSBmb3JtIGFuZCB0cmlnZ2VyIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3VibWl0KCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3InOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgICAgICAgICAgJ25vUmVzdWx0cyc6IGZhbHNlLFxuICAgICAgICAgICAgJ25vUmVzdWx0c01lc3NhZ2UnOiBudWxsLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpzdWJtaXQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRmluaXNoIHRoZSBzdWJtaXQgYW5kIHN0b3AgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBkb25lKCkge1xuICAgICAgICB0aGlzLnNldCgnbG9hZGluZycsIGZhbHNlKTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpkb25lJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEZpbmlzaCB0aGUgc2VhcmNoIHN1Ym1pdCB3aXRoIG5vIHJlc3VsdHMgYW5kIHN0b3AgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOS4xNFxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbm9SZXN1bHRzKG1lc3NhZ2UgPSBudWxsKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogdHJ1ZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmZlYmF5aXU6ZWJheS1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOm5vLXJlc3VsdHMnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhIHN1Ym1pdCBlcnJvciBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBlcnJvcihtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yJzogdHJ1ZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTplcnJvcicsIHRoaXMpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnZW5hYmxlZCc6IHRydWUsXG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdub1Jlc3VsdHMnOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBY3RpdmF0ZSB0aGUgbG9hZGluZyBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmxvYWQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyB0aGUgbG9hZCBtb3JlIGJ1dHRvbiBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtib29sZWFufSBlbmFibGVkXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoZW5hYmxlZCA9IHRydWUpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlbmFibGVkJzogZW5hYmxlZCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmRvbmUnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyB0aGUgbm8gcmVzdWx0cyBtZXNzYWdlIGFuZCBkZWFjdGl2YXRlIHRoZSBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbm9SZXN1bHRzKCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZycgOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bm8tcmVzdWx0cycsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IGEgbG9hZCBtb3JlIGVycm9yIGFuZCBkZWFjdGl2YXRlIHRoZSBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGVycm9yKG1lc3NhZ2UgPSBudWxsKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdlbmFibGVkJzogdHJ1ZSxcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3InOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTplcnJvcicsIHRoaXMpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hMb2FkTW9yZTtcbiIsImxldCBTZWFyY2hSZXN1bHRzSXRlbSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ3N1Y2Nlc3MnOiBmYWxzZSxcbiAgICAgICAgJ3N1Y2Nlc3NNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIHNlYXJjaCByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0KCkge1xuICAgICAgICB0aGlzLnNldCgnbG9hZGluZycsIHRydWUpO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTppbXBvcnQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3VjY2Vzc2Z1bGx5IGZpbmlzaCB0aGUgaW1wb3J0IHdpdGggYW4gb3B0aW9uYWwgbWVzc2FnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dTdWNjZXNzTWVzc2FnZShtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ3N1Y2Nlc3MnOiB0cnVlLFxuICAgICAgICAgICAgJ3N1Y2Nlc3NNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgICAgICdjdXN0b21fdmFsdWVzJzoge1xuICAgICAgICAgICAgICAgICdhbHJlYWR5X2ltcG9ydGVkJzogdHJ1ZSxcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOnN1Y2Nlc3MnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRGlzcGxheSBhbiBlcnJvciBmb3IgaW1wb3J0IHdpdGggYW4gb3B0aW9uYWwgbWVzc2FnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dFcnJvck1lc3NhZ2UobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmVycm9yJywgdGhpcyk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzSXRlbTtcbiIsImltcG9ydCBTZWFyY2hSZXN1bHRJdGVtIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuQ29sbGVjdGlvbi5leHRlbmQoe1xuICAgIG1vZGVsOiBTZWFyY2hSZXN1bHRJdGVtLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIHRoaXMub24oJ3N5bmMnLCB0aGlzLmluaXRJbXBvcnRMaXN0ZW5lcnMsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBQYXJzZSB0aGUgV29yZHByZXNzIGpzb24gQWpheCByZXNwb25zZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge0FycmF5fSByZXNwb25zZVxuICAgICAqIEByZXR1cm5zIHtBcnJheX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcGFyc2U6IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgIHJldHVybiByZXNwb25zZSAmJiByZXNwb25zZS5zdWNjZXNzID8gcmVzcG9uc2UuZGF0YSA6IFtdO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydEl0ZW0obW9kZWwpIHtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppbXBvcnQtaXRlbScsIG1vZGVsKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdCB0aGUgaW1wb3J0IGxpc3RlbmVycyBmb3IgYWxsIHJlc3VsdHMgaXRlbXMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRJbXBvcnRMaXN0ZW5lcnMoKSB7XG4gICAgICAgIHRoaXMuZm9yRWFjaCh0aGlzLl9pbml0SW1wb3J0TGlzdGVuZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0IHRoZSBpbXBvcnQgbGlzdGVuZXJzIGZvciB0aGUgcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaW5pdEltcG9ydExpc3RlbmVyKG1vZGVsKSB7XG4gICAgICAgIG1vZGVsLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppdGVtOmltcG9ydCcsIHRoaXMuaW1wb3J0SXRlbSwgdGhpcyk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hMb2FkTW9yZSBmcm9tICcuL3NlYXJjaC1sb2FkLW1vcmUnO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzdGFydGVkJzogZmFsc2UsXG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX3NlYXJjaCcsXG4gICAgICAgICdwYWdlJyA6IDEsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCB3aXRoIHRoZSBnaXZlbiBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IG9wdGlvbnNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5mb3JtID0gbmV3IFNlYXJjaEZvcm0oKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzID0gbmV3IFNlYXJjaFJlc3VsdHMoKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZSA9IG5ldyBTZWFyY2hMb2FkTW9yZSgpO1xuICAgICAgICB0aGlzLnBhZ2UgPSBvcHRpb25zICYmIG9wdGlvbnMucGFnZSA/IG9wdGlvbnMucGFnZSA6IDE7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6cmVzdWx0czppbXBvcnQtaXRlbScsIHRoaXMuaW1wb3J0LCB0aGlzKTtcbiAgICAgICAgdGhpcy5mb3JtLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcy5zdGFydCwgdGhpcyk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bG9hZCcsIHRoaXMubG9hZCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN0YXJ0IHRoZSBzZWFyY2ggd2l0aCB0aGUgZmlyc3QgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3RhcnQoKSB7XG4gICAgICAgIGlmKHRoaXMuZm9ybS5nZXQoJ3Rlcm0nKSA9PT0gbnVsbCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCAxKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnVybCA9IHRoaXMuX2J1aWxkVXJsKCk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLmZldGNoKCkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5zZXQoJ2VuYWJsZWQnLCB0aGlzLl9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSk7XG5cbiAgICAgICAgICAgIGlmKHRoaXMuX2hhc1Jlc3VsdHMocmVzdWx0cykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0uZG9uZSgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0ubm9SZXN1bHRzKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pLmZhaWwoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgdGhpcy5mb3JtLmVycm9yKGVycm9yTWVzc2FnZSk7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnNldCgnZW5hYmxlZCcsIGZhbHNlKTtcbiAgICAgICAgfSkuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2V0KCdzdGFydGVkJywgdHJ1ZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIG1vcmUgc2VhcmNoIHJlc3VsdHMgYnkgaW5jcmVhc2luZyB0aGUgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCB0aGlzLmdldCgncGFnZScpICsgMSk7XG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5mZXRjaCh7J3JlbW92ZSc6IGZhbHNlfSkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5kb25lKHRoaXMuX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpKTtcbiAgICAgICAgfSkuZmFpbCgoKSA9PiB7XG4gICAgICAgICAgICBsZXQgZXJyb3JNZXNzYWdlID0gKCgoKHJlc3VsdCB8fCB7fSkucmVzcG9uc2VKU09OIHx8IHt9KS5kYXRhIHx8IHt9KVswXSB8fCB7fSkubWVzc2FnZSB8fCBudWxsO1xuXG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLmVycm9yKGVycm9yTWVzc2FnZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChtb2RlbCkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCBtb2RlbCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBzZWFyY2ggQVBJIHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnRlcm09JHt0aGlzLmZvcm0uZ2V0KCd0ZXJtJyl9YFxuICAgICAgICAgICAgKyBgJnR5cGU9JHt0aGlzLmZvcm0uZ2V0KCd0eXBlJyl9YFxuICAgICAgICAgICAgKyBgJmNhdGVnb3J5PSR7dGhpcy5mb3JtLmdldCgnY2F0ZWdvcnknKX1gXG4gICAgICAgICAgICArIGAmbWluLXByaWNlPSR7dGhpcy5mb3JtLmdldCgnbWluUHJpY2UnKX1gXG4gICAgICAgICAgICArIGAmbWF4LXByaWNlPSR7dGhpcy5mb3JtLmdldCgnbWF4UHJpY2UnKX1gXG4gICAgICAgICAgICArIGAmY29uZGl0aW9uPSR7dGhpcy5mb3JtLmdldCgnY29uZGl0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnNvcnQ9JHt0aGlzLmZvcm0uZ2V0KCdzb3J0Jyl9YFxuICAgICAgICAgICAgKyBgJndpdGgtdmFyaWFudHM9JHt0aGlzLmZvcm0uZ2V0KCd3aXRoVmFyaWFudHMnKX1gXG4gICAgICAgICAgICArIGAmcGFnZT0ke3RoaXMuZ2V0KCdwYWdlJyl9YFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayBpZiB0aGUgbG9hZCBtb3JlIGJ1dHRvbiBpcyBlbmFibGVkICh2aXNpYmxlKS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge0FycmF5fG51bGx9IHJlc3VsdHNcbiAgICAgKiBAcmV0dXJucyB7Ym9vbH1cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSB7XG4gICAgICAgIHJldHVybiAocmVzdWx0cyAmJiByZXN1bHRzLmRhdGEgJiYgcmVzdWx0cy5kYXRhLmxlbmd0aCA+IDApXG4gICAgICAgICAgICAmJiB0aGlzLmdldCgncGFnZScpIDwgNVxuICAgICAgICAgICAgJiYgdGhpcy5mb3JtLmdldCgndHlwZScpID09PSAna2V5d29yZHMnO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBDaGVjayBpZiB0aGVyZSBhcmUgYW55IG90aGVyIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMS4xLjJcbiAgICAgKiBAcGFyYW0ge0FycmF5fG51bGx9IHJlc3VsdHNcbiAgICAgKiBAcmV0dXJucyB7Ym9vbH1cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9oYXNSZXN1bHRzKHJlc3VsdHMpIHtcbiAgICAgICAgcmV0dXJuIHJlc3VsdHNcbiAgICAgICAgICAgICYmIHJlc3VsdHMuZGF0YVxuICAgICAgICAgICAgJiYgcmVzdWx0cy5kYXRhLmxlbmd0aCA+IDA7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiIsImxldCBDb25maWcgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwic2hvcFwiXSc6ICdjaGFuZ2VTaG9wJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwibmV3LXNob3AtbmFtZVwiXSc6ICdjaGFuZ2VTaG9wJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwiYWN0aW9uXCJdJzogJ2NoYW5nZUFjdGlvbicsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cIm1lcmdlLXByb2R1Y3QtaWRcIl0nOiAnY2hhbmdlQWN0aW9uJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwicmVwbGFjZS1wcm9kdWN0LWlkXCJdJzogJ2NoYW5nZUFjdGlvbicsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInN0YXR1c1wiXSc6ICdjaGFuZ2VTdGF0dXMnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZSA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWNvbmZpZy10ZW1wbGF0ZScpO1xuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZS5odG1sKCkpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdzZWxlY3RlZFNob3AnLCBqUXVlcnkodGVtcGxhdGUuaHRtbCgpKS5maW5kKCdpbnB1dFtuYW1lPVwic2hvcFwiXScpLmZpcnN0KCkudmFsKCkpO1xuXG4gICAgICAgIHRoaXMubW9kZWwub24oJ2FmZjphbWF6b24taW1wb3J0OmNvbmZpZzphZGQtc2hvcCcsIHRoaXMuYWRkU2hvcCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtDb25maWd9XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgbGV0IGh0bWwgPSB0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcyk7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwoaHRtbCk7XG5cbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1ncm91cC1vcHRpb24tbWVyZ2UtcHJvZHVjdC1pZCwgLmFmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1ncm91cC1vcHRpb24tcmVwbGFjZS1wcm9kdWN0LWlkJykuc2VsZWN0aXplKHtcbiAgICAgICAgICAgIG1heEl0ZW1zOiAxLFxuICAgICAgICAgICAgdmFsdWVGaWVsZDogJ2lkJyxcbiAgICAgICAgICAgIGxhYmVsRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIHNlYXJjaEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBjcmVhdGU6IGZhbHNlLFxuICAgICAgICAgICAgbG9hZChxdWVyeSwgY2FsbGJhY2spIHtcbiAgICAgICAgICAgICAgICBpZiAoIXF1ZXJ5Lmxlbmd0aCkgcmV0dXJuIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgalF1ZXJ5LmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6IGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hcGlSb290ICsgJ3dwL3YyL2FmZi1wcm9kdWN0cy8/c3RhdHVzPXB1Ymxpc2gsZHJhZnQmc2VhcmNoPScgKyBxdWVyeSxcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdwb3N0X3BhcmVudCc6IDAsXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIGJlZm9yZVNlbmQoeGhyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB4aHIuc2V0UmVxdWVzdEhlYWRlcignWC1XUC1Ob25jZScsIGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5ub25jZSlcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgZXJyb3IoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBzdWNjZXNzKHJlc3VsdHMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdHMgPSByZXN1bHRzLm1hcCgocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2lkJzogcmVzdWx0LmlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnbmFtZSc6IHJlc3VsdC50aXRsZS5yZW5kZXJlZFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhyZXN1bHRzKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIGEgbmV3IHNob3BcbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gc2hvcFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBhZGRTaG9wKHNob3ApIHtcbiAgICAgICAgdGhpcy4kZWwuZmluZCgnaW5wdXRbdmFsdWU9XCJuZXctc2hvcFwiXScpLnBhcmVudCgpLmJlZm9yZShgXG4gICAgICAgICAgICA8bGFiZWwgY2xhc3M9XCJhZmYtaW1wb3J0LWNvbmZpZy1ncm91cC1sYWJlbFwiIGZvcj1cIiR7c2hvcC5zbHVnfVwiPlxuICAgICAgICAgICAgICAgIDxpbnB1dCBpZD1cIiR7c2hvcC5zbHVnfVwiIGNsYXNzPVwiYWZmLWltcG9ydC1jb25maWctZ3JvdXAtb3B0aW9uXCIgbmFtZT1cInNob3BcIiB0eXBlPVwicmFkaW9cIiB2YWx1ZT1cIiR7c2hvcC5zbHVnfVwiPlxuICAgICAgICAgICAgICAgICR7c2hvcC5uYW1lfSAgICAgICAgIFxuICAgICAgICAgICAgPC9sYWJlbD5cbiAgICAgICAgYCk7XG5cbiAgICAgICAgdGhpcy4kZWwuZmluZChgaW5wdXRbbmFtZT1cInNob3BcIl1bdmFsdWU9XCIke3Nob3Auc2x1Z31cIl1gKS5wcm9wKFwiY2hlY2tlZFwiLCB0cnVlKTtcbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ25ld1Nob3BOYW1lJzogbnVsbCxcbiAgICAgICAgICAgICdzZWxlY3RlZFNob3AnOiBzaG9wLnNsdWcsXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc2hvcCBjb25maWd1cmF0aW9uIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlU2hvcCgpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkU2hvcCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzaG9wXCJdOmNoZWNrZWQnKSxcbiAgICAgICAgICAgIG5ld1Nob3BOYW1lID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm5ldy1zaG9wLW5hbWVcIl0nKTtcblxuICAgICAgICBzZWxlY3RlZFNob3AudmFsKCkgPT09ICduZXctc2hvcCcgPyBuZXdTaG9wTmFtZS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpIDogbmV3U2hvcE5hbWUuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc2VsZWN0ZWRTaG9wJzogc2VsZWN0ZWRTaG9wLnZhbCgpLFxuICAgICAgICAgICAgJ25ld1Nob3BOYW1lJzogbmV3U2hvcE5hbWUudmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgYWN0aW9uIGNvbmZpZ3VyYXRpb24gaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2VBY3Rpb24oKSB7XG4gICAgICAgIGxldCBzZWxlY3RlZEFjdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJhY3Rpb25cIl06Y2hlY2tlZCcpLFxuICAgICAgICAgICAgbWVyZ2VQcm9kdWN0SWQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWVyZ2UtcHJvZHVjdC1pZFwiXScpLFxuICAgICAgICAgICAgcmVwbGFjZVByb2R1Y3RJZCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJyZXBsYWNlLXByb2R1Y3QtaWRcIl0nKSxcbiAgICAgICAgICAgIG1lcmdlU2VsZWN0aXplID0gbWVyZ2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplLFxuICAgICAgICAgICAgcmVwbGFjZVNlbGVjdGl6ZSA9IHJlcGxhY2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplO1xuXG4gICAgICAgIHNlbGVjdGVkQWN0aW9uLnZhbCgpID09PSAnbWVyZ2UtcHJvZHVjdCcgPyBtZXJnZVNlbGVjdGl6ZS5lbmFibGUoKSA6IG1lcmdlU2VsZWN0aXplLmRpc2FibGUoKTtcbiAgICAgICAgc2VsZWN0ZWRBY3Rpb24udmFsKCkgPT09ICdyZXBsYWNlLXByb2R1Y3QnID8gcmVwbGFjZVNlbGVjdGl6ZS5lbmFibGUoKSA6IHJlcGxhY2VTZWxlY3RpemUuZGlzYWJsZSgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzZWxlY3RlZEFjdGlvbic6IHNlbGVjdGVkQWN0aW9uLnZhbCgpLFxuICAgICAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbWVyZ2VQcm9kdWN0SWQudmFsKCksXG4gICAgICAgICAgICAncmVwbGFjZVByb2R1Y3RJZCc6IHJlcGxhY2VQcm9kdWN0SWQudmFsKClcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzdGF0dXMgY29uZmlndXJhdGlvbiBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZVN0YXR1cygpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkU3RhdHVzID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInN0YXR1c1wiXTpjaGVja2VkJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3N0YXR1cyc6IHNlbGVjdGVkU3RhdHVzLnZhbCgpLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9zZWFyY2gnO1xuaW1wb3J0IENvbmZpZyBmcm9tICcuL2NvbmZpZyc7XG5cbmxldCBJbXBvcnQgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNlYXJjaCA9IG5ldyBTZWFyY2goe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuc2VhcmNoLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmNvbmZpZyA9IG5ldyBDb25maWcoe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuY29uZmlnLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zZWFyY2gucmVuZGVyKCk7XG4gICAgICAgIHRoaXMuY29uZmlnLnJlbmRlcigpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgSW1wb3J0O1xuIiwibGV0IFNlYXJjaEZvcm0gPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwidHlwZVwiXSc6ICdjaGFuZ2UnLFxuICAgICAgICAnY2hhbmdlIHNlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nOiAnY2hhbmdlJyxcbiAgICAgICAgJ3N1Ym1pdCc6ICdzdWJtaXQnLFxuICAgIH0sXG5cbiAgICBpbml0aWFsRm9jdXM6IGZhbHNlLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybS10ZW1wbGF0ZScpLmh0bWwoKSxcbiAgICAgICAgICAgIHByb3ZpZGVyQ29uZmlndXJlZCA9IHRoaXMuJGVsLmRhdGEoJ3Byb3ZpZGVyLWNvbmZpZ3VyZWQnKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdwcm92aWRlckNvbmZpZ3VyZWQnLCBwcm92aWRlckNvbmZpZ3VyZWQgPT09IHRydWUgfHwgcHJvdmlkZXJDb25maWd1cmVkID09PSAndHJ1ZScpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge1NlYXJjaEZvcm19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIGxldCB0ZXJtID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1cIl0nKSxcbiAgICAgICAgICAgIHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLFxuICAgICAgICAgICAgbWluUHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWluLXByaWNlXCJdJyksXG4gICAgICAgICAgICBtYXhQcmljZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtYXgtcHJpY2VcIl0nKSxcbiAgICAgICAgICAgIGNvbmRpdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY29uZGl0aW9uXCJdJyksXG4gICAgICAgICAgICBzb3J0ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJzb3J0XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICBpZighdGhpcy5pbml0aWFsRm9jdXMpIHtcbiAgICAgICAgICAgIHRlcm0uZm9jdXMoKTtcbiAgICAgICAgICAgIHRoaXMuaW5pdGlhbEZvY3VzID0gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHR5cGUudmFsKHRoaXMubW9kZWwuZ2V0KCd0eXBlJykpO1xuICAgICAgICBjYXRlZ29yeS52YWwodGhpcy5tb2RlbC5nZXQoJ2NhdGVnb3J5JykpO1xuICAgICAgICBtaW5QcmljZS52YWwodGhpcy5tb2RlbC5nZXQoJ21pblByaWNlJykpO1xuICAgICAgICBtYXhQcmljZS52YWwodGhpcy5tb2RlbC5nZXQoJ21heFByaWNlJykpO1xuICAgICAgICBjb25kaXRpb24udmFsKHRoaXMubW9kZWwuZ2V0KCdjb25kaXRpb24nKSk7XG4gICAgICAgIHNvcnQudmFsKHRoaXMubW9kZWwuZ2V0KCdzb3J0JykpO1xuICAgICAgICB3aXRoVmFyaWFudHMudmFsKHRoaXMubW9kZWwuZ2V0KCd3aXRoVmFyaWFudHMnKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdWJtaXQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5jaGFuZ2UoKTtcbiAgICAgICAgdGhpcy5tb2RlbC5zdWJtaXQoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHNlYXJjaCBwYXJhbWV0ZXJzIGludG8gdGhlIG1vZGVsIG9uIGZvcm0gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2UoKSB7XG4gICAgICAgIGxldCB0ZXJtID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1cIl0nKSxcbiAgICAgICAgICAgIHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLFxuICAgICAgICAgICAgbWluUHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWluLXByaWNlXCJdJyksXG4gICAgICAgICAgICBtYXhQcmljZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJtYXgtcHJpY2VcIl0nKSxcbiAgICAgICAgICAgIGNvbmRpdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY29uZGl0aW9uXCJdJyksXG4gICAgICAgICAgICBzb3J0ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJzb3J0XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAndGVybSc6IHRlcm0udmFsKCksXG4gICAgICAgICAgICAndHlwZSc6IHR5cGUudmFsKCksXG4gICAgICAgICAgICAnbWluUHJpY2UnOiBtaW5QcmljZS5sZW5ndGggIT09IDAgPyBtaW5QcmljZS52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCdtaW5QcmljZScpLFxuICAgICAgICAgICAgJ21heFByaWNlJzogbWF4UHJpY2UubGVuZ3RoICE9PSAwID8gbWF4UHJpY2UudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnbWF4UHJpY2UnKSxcbiAgICAgICAgICAgICdjb25kaXRpb24nOiBjb25kaXRpb24ubGVuZ3RoICE9PSAwID8gY29uZGl0aW9uLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ2NvbmRpdGlvbicpLFxuICAgICAgICAgICAgJ3NvcnQnOiBzb3J0Lmxlbmd0aCAhPT0gMCA/IHNvcnQudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnc29ydCcpLFxuICAgICAgICAgICAgJ2NhdGVnb3J5JzogY2F0ZWdvcnkubGVuZ3RoICE9PSAwID8gY2F0ZWdvcnkudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnY2F0ZWdvcnknKSxcbiAgICAgICAgICAgICd3aXRoVmFyaWFudHMnOiB3aXRoVmFyaWFudHMubGVuZ3RoICE9PSAwID8gd2l0aFZhcmlhbnRzLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ3dpdGhWYXJpYW50cycpLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaEZvcm07XG4iLCJsZXQgU2VhcmNoTG9hZE1vcmUgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1sb2FkLW1vcmUnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlLWJ1dHRvbic6ICdsb2FkJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1sb2FkLW1vcmUtdGVtcGxhdGUnKS5odG1sKCk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCBsb2FkIG1vcmUuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybiB7U2VhcmNoTG9hZE1vcmV9XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBFbmFibGUgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBsb2FkKCkge1xuICAgICAgICB0aGlzLm1vZGVsLmxvYWQoKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgdGFnTmFtZTogJ2RpdicsXG5cbiAgICBjbGFzc05hbWU6ICcnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJzogJ3Nob3dBbGwnLFxuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS1hY3Rpb25zLWltcG9ydCc6ICdpbXBvcnQnLFxuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS1hY3Rpb25zLXJldHJ5LWltcG9ydCc6ICdpbXBvcnQnXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXRlbXBsYXRlJykuaHRtbCgpO1xuXG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm4ge1NlYXJjaFJlc3VsdHNJdGVtfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhbGwgaGlkZGVuIHZhcmlhbnRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHNob3dBbGwoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCcpLmhpZGUoKTtcbiAgICAgICAgdGhpcy4kZWwuZmluZCgnLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1pdGVtJykuc2hvdygpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIHNlYXJjaCByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5pbXBvcnQoKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0c0l0ZW07XG4iLCJpbXBvcnQgU2VhcmNoUmVzdWx0c0l0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uID0gb3B0aW9ucy5jb2xsZWN0aW9uO1xuXG4gICAgICAgIC8vIEJpbmQgdGhlIGNvbGxlY3Rpb24gZXZlbnRzXG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZXNldCcsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnYWRkJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZW1vdmUnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3N5bmMnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLl9hZGRBbGwoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIGFsbCBzZWFyY2ggcmVzdWx0cyBpdGVtcyB0byB0aGUgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRBbGwoKSB7XG4gICAgICAgIHRoaXMuJGVsLmVtcHR5KCk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5mb3JFYWNoKHRoaXMuX2FkZE9uZSwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBvbmUgc2VhcmNoIHJlc3VsdHMgaXRlbSB0byB0aGUgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRPbmUocHJvZHVjdCkge1xuICAgICAgICBsZXQgdmlldyA9IG5ldyBTZWFyY2hSZXN1bHRzSXRlbSh7XG4gICAgICAgICAgICBtb2RlbDogcHJvZHVjdCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy4kZWwuYXBwZW5kKHZpZXcucmVuZGVyKCkuZWwpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5mb3JtLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cyh7XG4gICAgICAgICAgICBjb2xsZWN0aW9uOiB0aGlzLm1vZGVsLnJlc3VsdHMsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMubG9hZE1vcmUgPSBuZXcgU2VhcmNoTG9hZE1vcmUoe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwubG9hZE1vcmUsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5mb3JtLnJlbmRlcigpO1xuICAgICAgICB0aGlzLnJlc3VsdHMucmVuZGVyKCk7XG5cbiAgICAgICAgaWYodGhpcy5tb2RlbC5nZXQoJ3N0YXJ0ZWQnKSkge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5yZW5kZXIoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoO1xuIl19
