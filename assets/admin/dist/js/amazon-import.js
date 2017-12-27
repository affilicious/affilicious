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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvaW1wb3J0LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLXJlc3VsdHMtaXRlbS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2guanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9z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O0FBeUI5Qjs7Ozs7O0FBTUEsVUEvQjhCLG9CQStCckI7QUFDTCxhQUFLLElBQUwsQ0FBVSxNQUFWO0FBQ0EsYUFBSyxPQUFMLENBQWEsTUFBYjs7QUFFQSxZQUFHLEtBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxTQUFmLENBQUgsRUFBOEI7QUFDMUIsaUJBQUssUUFBTCxDQUFjLE1BQWQ7QUFDSDs7QUFFRCxlQUFPLElBQVA7QUFDSDtBQXhDNkIsQ0FBckIsQ0FBYjs7a0JBMkNlLE0iLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiaW1wb3J0IEltcG9ydCBmcm9tICcuL21vZGVsL2ltcG9ydCc7XG5pbXBvcnQgSW1wb3J0VmlldyBmcm9tICcuL3ZpZXcvaW1wb3J0JztcblxubGV0IGltcG9ydE1vZGVsID0gbmV3IEltcG9ydCgpO1xubGV0IGltcG9ydFZpZXcgPSBuZXcgSW1wb3J0Vmlldyh7bW9kZWw6IGltcG9ydE1vZGVsfSk7XG5cbmltcG9ydFZpZXcucmVuZGVyKCk7XG4iLCJsZXQgQ29uZmlnID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnc2VsZWN0ZWRTaG9wJzogJ2FtYXpvbicsXG4gICAgICAgICduZXdTaG9wTmFtZSc6IG51bGwsXG4gICAgICAgICdzZWxlY3RlZEFjdGlvbic6ICduZXctcHJvZHVjdCcsXG4gICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IG51bGwsXG4gICAgICAgICdyZXBsYWNlUHJvZHVjdElkJzogbnVsbCxcbiAgICAgICAgJ3N0YXR1cyc6ICdwdWJsaXNoJyxcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9zZWFyY2gnO1xuaW1wb3J0IENvbmZpZyBmcm9tICcuL2NvbmZpZyc7XG5cbmxldCBJbXBvcnQgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX2ltcG9ydCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNlYXJjaCA9IG5ldyBTZWFyY2goKTtcbiAgICAgICAgdGhpcy5jb25maWcgPSBuZXcgQ29uZmlnKCk7XG5cbiAgICAgICAgdGhpcy5zZWFyY2gub24oJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCB0aGlzLmltcG9ydCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgcHJvZHVjdC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gcHJvZHVjdFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQocHJvZHVjdCkge1xuICAgICAgICBsZXQgZGF0YSA9IHtcbiAgICAgICAgICAgICdwcm9kdWN0Jzoge1xuICAgICAgICAgICAgICAgICduYW1lJyA6IHByb2R1Y3QuYXR0cmlidXRlcy5uYW1lLFxuICAgICAgICAgICAgICAgICd0eXBlJyA6IHByb2R1Y3QuYXR0cmlidXRlcy50eXBlLFxuICAgICAgICAgICAgICAgICdzaG9wcycgOiBwcm9kdWN0LmF0dHJpYnV0ZXMuc2hvcHMsXG4gICAgICAgICAgICAgICAgJ2N1c3RvbV92YWx1ZXMnIDogcHJvZHVjdC5hdHRyaWJ1dGVzLmN1c3RvbV92YWx1ZXMsXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJ2NvbmZpZyc6IHRoaXMuY29uZmlnLmF0dHJpYnV0ZXMsXG4gICAgICAgICAgICAnZm9ybSc6IHRoaXMuc2VhcmNoLmZvcm0uYXR0cmlidXRlcyxcbiAgICAgICAgfTtcblxuICAgICAgICBqUXVlcnkuYWpheCh7XG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICB1cmw6IHRoaXMuX2J1aWxkVXJsKCksXG4gICAgICAgICAgICBkYXRhOiBkYXRhLFxuICAgICAgICB9KS5kb25lKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgIGxldCBzaG9wVGVtcGxhdGUgPSAoKHJlc3VsdCB8fCB7fSkuZGF0YSB8fCB7fSkuc2hvcF90ZW1wbGF0ZSB8fCBudWxsO1xuXG4gICAgICAgICAgICBpZihzaG9wVGVtcGxhdGUpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmNvbmZpZy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpjb25maWc6YWRkLXNob3AnLCBzaG9wVGVtcGxhdGUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBwcm9kdWN0LnNob3dTdWNjZXNzTWVzc2FnZSgpO1xuICAgICAgICB9KS5mYWlsKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgIGxldCBlcnJvck1lc3NhZ2UgPSAoKCgocmVzdWx0IHx8IHt9KS5yZXNwb25zZUpTT04gfHwge30pLmRhdGEgfHwge30pWzBdIHx8IHt9KS5tZXNzYWdlIHx8IG51bGw7XG5cbiAgICAgICAgICAgIHByb2R1Y3Quc2hvd0Vycm9yTWVzc2FnZShlcnJvck1lc3NhZ2UpO1xuICAgICAgICB9KVxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgaW1wb3J0IHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICA7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBJbXBvcnQ7XG4iLCJsZXQgU2VhcmNoRm9ybSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3Rlcm0nOiAnJyxcbiAgICAgICAgJ3R5cGUnOiAna2V5d29yZHMnLFxuICAgICAgICAnY2F0ZWdvcnknOiAnQWxsJyxcbiAgICAgICAgJ21pblByaWNlJzogbnVsbCxcbiAgICAgICAgJ21heFByaWNlJzogbnVsbCxcbiAgICAgICAgJ2NvbmRpdGlvbic6ICdOZXcnLFxuICAgICAgICAnc29ydCc6ICctcHJpY2UnLFxuICAgICAgICAnd2l0aFZhcmlhbnRzJzogJ25vJyxcbiAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICdlcnJvck1lc3NhZ2UnOiBudWxsLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgJ3Byb3ZpZGVyQ29uZmlndXJlZCc6IGZhbHNlXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgZm9ybSB0aGUgZm9ybSBhbmQgdHJpZ2dlciB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdCgpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHNNZXNzYWdlJzogbnVsbCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEZpbmlzaCB0aGUgc3VibWl0IGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZG9uZSgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCBmYWxzZSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZG9uZScsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBGaW5pc2ggdGhlIHNlYXJjaCBzdWJtaXQgd2l0aCBubyByZXN1bHRzIGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjkuMTRcbiAgICAgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBtZXNzYWdlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cyhtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ25vUmVzdWx0cyc6IHRydWUsXG4gICAgICAgICAgICAnbm9SZXN1bHRzTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmZWJheWl1OmViYXktaW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpuby1yZXN1bHRzJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYSBzdWJtaXQgZXJyb3IgYW5kIHN0b3AgdGhlIGxvYWRpbmcgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7c3RyaW5nfG51bGx9IG1lc3NhZ2VcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZXJyb3IobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlcnJvcic6IHRydWUsXG4gICAgICAgICAgICAnZXJyb3JNZXNzYWdlJzogbWVzc2FnZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hMb2FkTW9yZSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2VuYWJsZWQnOiB0cnVlLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWN0aXZhdGUgdGhlIGxvYWRpbmcgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdsb2FkaW5nJywgdHJ1ZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIGxvYWQgbW9yZSBidXR0b24gYW5kIGRlYWN0aXZhdGUgdGhlIHNwaW5uZXIgYW5pbWF0aW9uLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gZW5hYmxlZFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBkb25lKGVuYWJsZWQgPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZW5hYmxlZCc6IGVuYWJsZWQsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpkb25lJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgdGhlIG5vIHJlc3VsdHMgbWVzc2FnZSBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIG5vUmVzdWx0cygpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnIDogZmFsc2UsXG4gICAgICAgICAgICAnbm9SZXN1bHRzJzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOm5vLXJlc3VsdHMnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhIGxvYWQgbW9yZSBlcnJvciBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBlcnJvcihtZXNzYWdlID0gbnVsbCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnZW5hYmxlZCc6IHRydWUsXG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yJzogdHJ1ZSxcbiAgICAgICAgICAgICdlcnJvck1lc3NhZ2UnOiBtZXNzYWdlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdzdWNjZXNzJzogZmFsc2UsXG4gICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG51bGwsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgICAgICAnZXJyb3JNZXNzYWdlJzogbnVsbCxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBzZWFyY2ggcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06aW1wb3J0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Y2Nlc3NmdWxseSBmaW5pc2ggdGhlIGltcG9ydCB3aXRoIGFuIG9wdGlvbmFsIG1lc3NhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93U3VjY2Vzc01lc3NhZ2UobWVzc2FnZSA9IG51bGwpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdzdWNjZXNzJzogdHJ1ZSxcbiAgICAgICAgICAgICdzdWNjZXNzTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgICAgICAnY3VzdG9tX3ZhbHVlcyc6IHtcbiAgICAgICAgICAgICAgICAnYWxyZWFkeV9pbXBvcnRlZCc6IHRydWUsXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTpzdWNjZXNzJywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIERpc3BsYXkgYW4gZXJyb3IgZm9yIGltcG9ydCB3aXRoIGFuIG9wdGlvbmFsIG1lc3NhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtzdHJpbmd8bnVsbH0gbWVzc2FnZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93RXJyb3JNZXNzYWdlKG1lc3NhZ2UgPSBudWxsKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3InOiB0cnVlLFxuICAgICAgICAgICAgJ2Vycm9yTWVzc2FnZSc6IG1lc3NhZ2UsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTplcnJvcicsIHRoaXMpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0c0l0ZW07XG4iLCJpbXBvcnQgU2VhcmNoUmVzdWx0SXRlbSBmcm9tICcuL3NlYXJjaC1yZXN1bHRzLWl0ZW0nO1xuXG5sZXQgU2VhcmNoUmVzdWx0cyA9IEJhY2tib25lLkNvbGxlY3Rpb24uZXh0ZW5kKHtcbiAgICBtb2RlbDogU2VhcmNoUmVzdWx0SXRlbSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLm9uKCdzeW5jJywgdGhpcy5pbml0SW1wb3J0TGlzdGVuZXJzLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUGFyc2UgdGhlIFdvcmRwcmVzcyBqc29uIEFqYXggcmVzcG9uc2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtBcnJheX0gcmVzcG9uc2VcbiAgICAgKiBAcmV0dXJucyB7QXJyYXl9XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHBhcnNlOiBmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICByZXR1cm4gcmVzcG9uc2UgJiYgcmVzcG9uc2Uuc3VjY2VzcyA/IHJlc3BvbnNlLmRhdGEgOiBbXTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBnaXZlbiBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBtb2RlbFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnRJdGVtKG1vZGVsKSB7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aW1wb3J0LWl0ZW0nLCBtb2RlbCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXQgdGhlIGltcG9ydCBsaXN0ZW5lcnMgZm9yIGFsbCByZXN1bHRzIGl0ZW1zLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0SW1wb3J0TGlzdGVuZXJzKCkge1xuICAgICAgICB0aGlzLmZvckVhY2godGhpcy5faW5pdEltcG9ydExpc3RlbmVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdCB0aGUgaW1wb3J0IGxpc3RlbmVycyBmb3IgdGhlIHJlc3VsdCBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2luaXRJbXBvcnRMaXN0ZW5lcihtb2RlbCkge1xuICAgICAgICBtb2RlbC5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTppbXBvcnQnLCB0aGlzLmltcG9ydEl0ZW0sIHRoaXMpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hSZXN1bHRzO1xuIiwiaW1wb3J0IFNlYXJjaEZvcm0gZnJvbSAnLi9zZWFyY2gtZm9ybSc7XG5pbXBvcnQgU2VhcmNoTG9hZE1vcmUgZnJvbSAnLi9zZWFyY2gtbG9hZC1tb3JlJztcbmltcG9ydCBTZWFyY2hSZXN1bHRzIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMnO1xuXG5sZXQgU2VhcmNoID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcbiAgICBkZWZhdWx0czoge1xuICAgICAgICAnc3RhcnRlZCc6IGZhbHNlLFxuICAgICAgICAnYWN0aW9uJzogJ2FmZl9wcm9kdWN0X2FkbWluX2FtYXpvbl9zZWFyY2gnLFxuICAgICAgICAncGFnZScgOiAxLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggd2l0aCB0aGUgZ2l2ZW4gb3B0aW9ucy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge2FycmF5fSBvcHRpb25zXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuZm9ybSA9IG5ldyBTZWFyY2hGb3JtKCk7XG4gICAgICAgIHRoaXMucmVzdWx0cyA9IG5ldyBTZWFyY2hSZXN1bHRzKCk7XG4gICAgICAgIHRoaXMubG9hZE1vcmUgPSBuZXcgU2VhcmNoTG9hZE1vcmUoKTtcbiAgICAgICAgdGhpcy5wYWdlID0gb3B0aW9ucyAmJiBvcHRpb25zLnBhZ2UgPyBvcHRpb25zLnBhZ2UgOiAxO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aW1wb3J0LWl0ZW0nLCB0aGlzLmltcG9ydCwgdGhpcyk7XG4gICAgICAgIHRoaXMuZm9ybS5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOnN1Ym1pdCcsIHRoaXMuc3RhcnQsIHRoaXMpO1xuICAgICAgICB0aGlzLmxvYWRNb3JlLm9uKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmxvYWQnLCB0aGlzLmxvYWQsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdGFydCB0aGUgc2VhcmNoIHdpdGggdGhlIGZpcnN0IHBhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN0YXJ0KCkge1xuICAgICAgICBpZih0aGlzLmZvcm0uZ2V0KCd0ZXJtJykgPT09IG51bGwpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc2V0KCdwYWdlJywgMSk7XG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5mZXRjaCgpLmRvbmUoKHJlc3VsdHMpID0+IHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuc2V0KCdlbmFibGVkJywgdGhpcy5faXNMb2FkTW9yZUVuYWJsZWQocmVzdWx0cykpO1xuXG4gICAgICAgICAgICBpZih0aGlzLl9oYXNSZXN1bHRzKHJlc3VsdHMpKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5mb3JtLmRvbmUoKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhpcy5mb3JtLm5vUmVzdWx0cygpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KS5mYWlsKChyZXN1bHQpID0+IHtcbiAgICAgICAgICAgIGxldCBlcnJvck1lc3NhZ2UgPSAoKCgocmVzdWx0IHx8IHt9KS5yZXNwb25zZUpTT04gfHwge30pLmRhdGEgfHwge30pWzBdIHx8IHt9KS5tZXNzYWdlIHx8IG51bGw7XG5cbiAgICAgICAgICAgIHRoaXMuZm9ybS5lcnJvcihlcnJvck1lc3NhZ2UpO1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5zZXQoJ2VuYWJsZWQnLCBmYWxzZSk7XG4gICAgICAgIH0pLmFsd2F5cygoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLnNldCgnc3RhcnRlZCcsIHRydWUpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCBtb3JlIHNlYXJjaCByZXN1bHRzIGJ5IGluY3JlYXNpbmcgdGhlIHBhZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMuc2V0KCdwYWdlJywgdGhpcy5nZXQoJ3BhZ2UnKSArIDEpO1xuICAgICAgICB0aGlzLnJlc3VsdHMudXJsID0gdGhpcy5fYnVpbGRVcmwoKTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goeydyZW1vdmUnOiBmYWxzZX0pLmRvbmUoKHJlc3VsdHMpID0+IHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUuZG9uZSh0aGlzLl9pc0xvYWRNb3JlRW5hYmxlZChyZXN1bHRzKSk7XG4gICAgICAgIH0pLmZhaWwoKCkgPT4ge1xuICAgICAgICAgICAgbGV0IGVycm9yTWVzc2FnZSA9ICgoKChyZXN1bHQgfHwge30pLnJlc3BvbnNlSlNPTiB8fCB7fSkuZGF0YSB8fCB7fSlbMF0gfHwge30pLm1lc3NhZ2UgfHwgbnVsbDtcblxuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5lcnJvcihlcnJvck1lc3NhZ2UpO1xuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBnaXZlbiBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBtb2RlbFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQobW9kZWwpIHtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDppbXBvcnQtcmVzdWx0cy1pdGVtJywgbW9kZWwpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBCdWlsZCB0aGUgc2VhcmNoIEFQSSB1cmwgYmFzZWQgb24gdGhlIGdpdmVuIHBhcmFtZXRlcnMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge3N0cmluZ31cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9idWlsZFVybCgpIHtcbiAgICAgICAgcmV0dXJuIGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hamF4XG4gICAgICAgICAgICArIGA/YWN0aW9uPSR7dGhpcy5nZXQoJ2FjdGlvbicpfWBcbiAgICAgICAgICAgICsgYCZ0ZXJtPSR7dGhpcy5mb3JtLmdldCgndGVybScpfWBcbiAgICAgICAgICAgICsgYCZ0eXBlPSR7dGhpcy5mb3JtLmdldCgndHlwZScpfWBcbiAgICAgICAgICAgICsgYCZjYXRlZ29yeT0ke3RoaXMuZm9ybS5nZXQoJ2NhdGVnb3J5Jyl9YFxuICAgICAgICAgICAgKyBgJm1pbi1wcmljZT0ke3RoaXMuZm9ybS5nZXQoJ21pblByaWNlJyl9YFxuICAgICAgICAgICAgKyBgJm1heC1wcmljZT0ke3RoaXMuZm9ybS5nZXQoJ21heFByaWNlJyl9YFxuICAgICAgICAgICAgKyBgJmNvbmRpdGlvbj0ke3RoaXMuZm9ybS5nZXQoJ2NvbmRpdGlvbicpfWBcbiAgICAgICAgICAgICsgYCZzb3J0PSR7dGhpcy5mb3JtLmdldCgnc29ydCcpfWBcbiAgICAgICAgICAgICsgYCZ3aXRoLXZhcmlhbnRzPSR7dGhpcy5mb3JtLmdldCgnd2l0aFZhcmlhbnRzJyl9YFxuICAgICAgICAgICAgKyBgJnBhZ2U9JHt0aGlzLmdldCgncGFnZScpfWBcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQ2hlY2sgaWYgdGhlIGxvYWQgbW9yZSBidXR0b24gaXMgZW5hYmxlZCAodmlzaWJsZSkuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtBcnJheXxudWxsfSByZXN1bHRzXG4gICAgICogQHJldHVybnMge2Jvb2x9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaXNMb2FkTW9yZUVuYWJsZWQocmVzdWx0cykge1xuICAgICAgICByZXR1cm4gKHJlc3VsdHMgJiYgcmVzdWx0cy5kYXRhICYmIHJlc3VsdHMuZGF0YS5sZW5ndGggPiAwKVxuICAgICAgICAgICAgJiYgdGhpcy5nZXQoJ3BhZ2UnKSA8IDVcbiAgICAgICAgICAgICYmIHRoaXMuZm9ybS5nZXQoJ3R5cGUnKSA9PT0gJ2tleXdvcmRzJztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQ2hlY2sgaWYgdGhlcmUgYXJlIGFueSBvdGhlciByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDEuMS4yXG4gICAgICogQHBhcmFtIHtBcnJheXxudWxsfSByZXN1bHRzXG4gICAgICogQHJldHVybnMge2Jvb2x9XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaGFzUmVzdWx0cyhyZXN1bHRzKSB7XG4gICAgICAgIHJldHVybiByZXN1bHRzXG4gICAgICAgICAgICAmJiByZXN1bHRzLmRhdGFcbiAgICAgICAgICAgICYmIHJlc3VsdHMuZGF0YS5sZW5ndGggPiAwO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2g7XG4iLCJsZXQgQ29uZmlnID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWcnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInNob3BcIl0nOiAnY2hhbmdlU2hvcCcsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cIm5ldy1zaG9wLW5hbWVcIl0nOiAnY2hhbmdlU2hvcCcsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cImFjdGlvblwiXSc6ICdjaGFuZ2VBY3Rpb24nLFxuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJtZXJnZS1wcm9kdWN0LWlkXCJdJzogJ2NoYW5nZUFjdGlvbicsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInJlcGxhY2UtcHJvZHVjdC1pZFwiXSc6ICdjaGFuZ2VBY3Rpb24nLFxuICAgICAgICAnY2hhbmdlIGlucHV0W25hbWU9XCJzdGF0dXNcIl0nOiAnY2hhbmdlU3RhdHVzJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgY29uZmlnLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGUgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctdGVtcGxhdGUnKTtcbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGUuaHRtbCgpKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCgnc2VsZWN0ZWRTaG9wJywgalF1ZXJ5KHRlbXBsYXRlLmh0bWwoKSkuZmluZCgnaW5wdXRbbmFtZT1cInNob3BcIl0nKS5maXJzdCgpLnZhbCgpKTtcblxuICAgICAgICB0aGlzLm1vZGVsLm9uKCdhZmY6YW1hem9uLWltcG9ydDpjb25maWc6YWRkLXNob3AnLCB0aGlzLmFkZFNob3AsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7Q29uZmlnfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGxldCBodG1sID0gdGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpO1xuICAgICAgICB0aGlzLiRlbC5odG1sKGh0bWwpO1xuXG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtYW1hem9uLWltcG9ydC1jb25maWctZ3JvdXAtb3B0aW9uLW1lcmdlLXByb2R1Y3QtaWQsIC5hZmYtYW1hem9uLWltcG9ydC1jb25maWctZ3JvdXAtb3B0aW9uLXJlcGxhY2UtcHJvZHVjdC1pZCcpLnNlbGVjdGl6ZSh7XG4gICAgICAgICAgICBtYXhJdGVtczogMSxcbiAgICAgICAgICAgIHZhbHVlRmllbGQ6ICdpZCcsXG4gICAgICAgICAgICBsYWJlbEZpZWxkOiAnbmFtZScsXG4gICAgICAgICAgICBzZWFyY2hGaWVsZDogJ25hbWUnLFxuICAgICAgICAgICAgY3JlYXRlOiBmYWxzZSxcbiAgICAgICAgICAgIGxvYWQocXVlcnksIGNhbGxiYWNrKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFxdWVyeS5sZW5ndGgpIHJldHVybiBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgIGpRdWVyeS5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMuYXBpUm9vdCArICd3cC92Mi9hZmYtcHJvZHVjdHMvP3N0YXR1cz1wdWJsaXNoLGRyYWZ0JnNlYXJjaD0nICsgcXVlcnksXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgICAgICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAncG9zdF9wYXJlbnQnOiAwLFxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBiZWZvcmVTZW5kKHhocikge1xuICAgICAgICAgICAgICAgICAgICAgICAgeGhyLnNldFJlcXVlc3RIZWFkZXIoJ1gtV1AtTm9uY2UnLCBhZmZBZG1pbkFtYXpvbkltcG9ydFVybHMubm9uY2UpXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIGVycm9yKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2soKTtcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgc3VjY2VzcyhyZXN1bHRzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHRzID0gcmVzdWx0cy5tYXAoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdpZCc6IHJlc3VsdC5pZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ25hbWUnOiByZXN1bHQudGl0bGUucmVuZGVyZWRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2socmVzdWx0cyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBhIG5ldyBzaG9wXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IHNob3BcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgYWRkU2hvcChzaG9wKSB7XG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJ2lucHV0W3ZhbHVlPVwibmV3LXNob3BcIl0nKS5wYXJlbnQoKS5iZWZvcmUoYFxuICAgICAgICAgICAgPGxhYmVsIGNsYXNzPVwiYWZmLWltcG9ydC1jb25maWctZ3JvdXAtbGFiZWxcIiBmb3I9XCIke3Nob3Auc2x1Z31cIj5cbiAgICAgICAgICAgICAgICA8aW5wdXQgaWQ9XCIke3Nob3Auc2x1Z31cIiBjbGFzcz1cImFmZi1pbXBvcnQtY29uZmlnLWdyb3VwLW9wdGlvblwiIG5hbWU9XCJzaG9wXCIgdHlwZT1cInJhZGlvXCIgdmFsdWU9XCIke3Nob3Auc2x1Z31cIj5cbiAgICAgICAgICAgICAgICAke3Nob3AubmFtZX0gICAgICAgICBcbiAgICAgICAgICAgIDwvbGFiZWw+XG4gICAgICAgIGApO1xuXG4gICAgICAgIHRoaXMuJGVsLmZpbmQoYGlucHV0W25hbWU9XCJzaG9wXCJdW3ZhbHVlPVwiJHtzaG9wLnNsdWd9XCJdYCkucHJvcChcImNoZWNrZWRcIiwgdHJ1ZSk7XG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICduZXdTaG9wTmFtZSc6IG51bGwsXG4gICAgICAgICAgICAnc2VsZWN0ZWRTaG9wJzogc2hvcC5zbHVnLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IHNob3AgY29uZmlndXJhdGlvbiBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZVNob3AoKSB7XG4gICAgICAgIGxldCBzZWxlY3RlZFNob3AgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwic2hvcFwiXTpjaGVja2VkJyksXG4gICAgICAgICAgICBuZXdTaG9wTmFtZSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJuZXctc2hvcC1uYW1lXCJdJyk7XG5cbiAgICAgICAgc2VsZWN0ZWRTaG9wLnZhbCgpID09PSAnbmV3LXNob3AnID8gbmV3U2hvcE5hbWUucmVtb3ZlQXR0cignZGlzYWJsZWQnKSA6IG5ld1Nob3BOYW1lLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3NlbGVjdGVkU2hvcCc6IHNlbGVjdGVkU2hvcC52YWwoKSxcbiAgICAgICAgICAgICduZXdTaG9wTmFtZSc6IG5ld1Nob3BOYW1lLnZhbCgpLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogTG9hZCB0aGUgbmV3IGFjdGlvbiBjb25maWd1cmF0aW9uIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlQWN0aW9uKCkge1xuICAgICAgICBsZXQgc2VsZWN0ZWRBY3Rpb24gPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwiYWN0aW9uXCJdOmNoZWNrZWQnKSxcbiAgICAgICAgICAgIG1lcmdlUHJvZHVjdElkID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1lcmdlLXByb2R1Y3QtaWRcIl0nKSxcbiAgICAgICAgICAgIHJlcGxhY2VQcm9kdWN0SWQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwicmVwbGFjZS1wcm9kdWN0LWlkXCJdJyksXG4gICAgICAgICAgICBtZXJnZVNlbGVjdGl6ZSA9IG1lcmdlUHJvZHVjdElkLnNlbGVjdGl6ZSgpWzBdLnNlbGVjdGl6ZSxcbiAgICAgICAgICAgIHJlcGxhY2VTZWxlY3RpemUgPSByZXBsYWNlUHJvZHVjdElkLnNlbGVjdGl6ZSgpWzBdLnNlbGVjdGl6ZTtcblxuICAgICAgICBzZWxlY3RlZEFjdGlvbi52YWwoKSA9PT0gJ21lcmdlLXByb2R1Y3QnID8gbWVyZ2VTZWxlY3RpemUuZW5hYmxlKCkgOiBtZXJnZVNlbGVjdGl6ZS5kaXNhYmxlKCk7XG4gICAgICAgIHNlbGVjdGVkQWN0aW9uLnZhbCgpID09PSAncmVwbGFjZS1wcm9kdWN0JyA/IHJlcGxhY2VTZWxlY3RpemUuZW5hYmxlKCkgOiByZXBsYWNlU2VsZWN0aXplLmRpc2FibGUoKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc2VsZWN0ZWRBY3Rpb24nOiBzZWxlY3RlZEFjdGlvbi52YWwoKSxcbiAgICAgICAgICAgICdtZXJnZVByb2R1Y3RJZCc6IG1lcmdlUHJvZHVjdElkLnZhbCgpLFxuICAgICAgICAgICAgJ3JlcGxhY2VQcm9kdWN0SWQnOiByZXBsYWNlUHJvZHVjdElkLnZhbCgpXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc3RhdHVzIGNvbmZpZ3VyYXRpb24gaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2VTdGF0dXMoKSB7XG4gICAgICAgIGxldCBzZWxlY3RlZFN0YXR1cyA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzdGF0dXNcIl06Y2hlY2tlZCcpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzdGF0dXMnOiBzZWxlY3RlZFN0YXR1cy52YWwoKSxcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBDb25maWc7XG4iLCJpbXBvcnQgU2VhcmNoIGZyb20gJy4vc2VhcmNoJztcbmltcG9ydCBDb25maWcgZnJvbSAnLi9jb25maWcnO1xuXG5sZXQgSW1wb3J0ID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0JyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5zZWFyY2ggPSBuZXcgU2VhcmNoKHtcbiAgICAgICAgICAgIG1vZGVsOiB0aGlzLm1vZGVsLnNlYXJjaCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5jb25maWcgPSBuZXcgQ29uZmlnKHtcbiAgICAgICAgICAgIG1vZGVsOiB0aGlzLm1vZGVsLmNvbmZpZyxcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuc2VhcmNoLnJlbmRlcigpO1xuICAgICAgICB0aGlzLmNvbmZpZy5yZW5kZXIoKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IEltcG9ydDtcbiIsImxldCBTZWFyY2hGb3JtID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybScsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBzZWxlY3RbbmFtZT1cInR5cGVcIl0nOiAnY2hhbmdlJyxcbiAgICAgICAgJ2NoYW5nZSBzZWxlY3RbbmFtZT1cImNhdGVnb3J5XCJdJzogJ2NoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnc3VibWl0JyxcbiAgICB9LFxuXG4gICAgaW5pdGlhbEZvY3VzOiBmYWxzZSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLWZvcm0tdGVtcGxhdGUnKS5odG1sKCksXG4gICAgICAgICAgICBwcm92aWRlckNvbmZpZ3VyZWQgPSB0aGlzLiRlbC5kYXRhKCdwcm92aWRlci1jb25maWd1cmVkJyk7XG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZSA9IF8udGVtcGxhdGUodGVtcGxhdGVIdG1sKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCgncHJvdmlkZXJDb25maWd1cmVkJywgcHJvdmlkZXJDb25maWd1cmVkID09PSB0cnVlIHx8IHByb3ZpZGVyQ29uZmlndXJlZCA9PT0gJ3RydWUnKTtcbiAgICAgICAgdGhpcy5tb2RlbC5vbignY2hhbmdlJywgdGhpcy5yZW5kZXIsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBSZW5kZXIgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm5zIHtTZWFyY2hGb3JtfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICBsZXQgdGVybSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJ0ZXJtXCJdJyksXG4gICAgICAgICAgICB0eXBlID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0eXBlXCJdJyksXG4gICAgICAgICAgICBjYXRlZ29yeSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nKSxcbiAgICAgICAgICAgIG1pblByaWNlID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1pbi1wcmljZVwiXScpLFxuICAgICAgICAgICAgbWF4UHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWF4LXByaWNlXCJdJyksXG4gICAgICAgICAgICBjb25kaXRpb24gPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNvbmRpdGlvblwiXScpLFxuICAgICAgICAgICAgc29ydCA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwic29ydFwiXScpLFxuICAgICAgICAgICAgd2l0aFZhcmlhbnRzID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ3aXRoLXZhcmlhbnRzXCJdJyk7XG5cbiAgICAgICAgaWYoIXRoaXMuaW5pdGlhbEZvY3VzKSB7XG4gICAgICAgICAgICB0ZXJtLmZvY3VzKCk7XG4gICAgICAgICAgICB0aGlzLmluaXRpYWxGb2N1cyA9IHRydWU7XG4gICAgICAgIH1cblxuICAgICAgICB0eXBlLnZhbCh0aGlzLm1vZGVsLmdldCgndHlwZScpKTtcbiAgICAgICAgY2F0ZWdvcnkudmFsKHRoaXMubW9kZWwuZ2V0KCdjYXRlZ29yeScpKTtcbiAgICAgICAgbWluUHJpY2UudmFsKHRoaXMubW9kZWwuZ2V0KCdtaW5QcmljZScpKTtcbiAgICAgICAgbWF4UHJpY2UudmFsKHRoaXMubW9kZWwuZ2V0KCdtYXhQcmljZScpKTtcbiAgICAgICAgY29uZGl0aW9uLnZhbCh0aGlzLm1vZGVsLmdldCgnY29uZGl0aW9uJykpO1xuICAgICAgICBzb3J0LnZhbCh0aGlzLm1vZGVsLmdldCgnc29ydCcpKTtcbiAgICAgICAgd2l0aFZhcmlhbnRzLnZhbCh0aGlzLm1vZGVsLmdldCgnd2l0aFZhcmlhbnRzJykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7RXZlbnR9IGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgc3VibWl0KGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuY2hhbmdlKCk7XG4gICAgICAgIHRoaXMubW9kZWwuc3VibWl0KCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzZWFyY2ggcGFyYW1ldGVycyBpbnRvIHRoZSBtb2RlbCBvbiBmb3JtIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlKCkge1xuICAgICAgICBsZXQgdGVybSA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJ0ZXJtXCJdJyksXG4gICAgICAgICAgICB0eXBlID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ0eXBlXCJdJyksXG4gICAgICAgICAgICBjYXRlZ29yeSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwiY2F0ZWdvcnlcIl0nKSxcbiAgICAgICAgICAgIG1pblByaWNlID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm1pbi1wcmljZVwiXScpLFxuICAgICAgICAgICAgbWF4UHJpY2UgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWF4LXByaWNlXCJdJyksXG4gICAgICAgICAgICBjb25kaXRpb24gPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNvbmRpdGlvblwiXScpLFxuICAgICAgICAgICAgc29ydCA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwic29ydFwiXScpLFxuICAgICAgICAgICAgd2l0aFZhcmlhbnRzID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJ3aXRoLXZhcmlhbnRzXCJdJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3Rlcm0nOiB0ZXJtLmxlbmd0aCAhPT0gMCA/IHRlcm0udmFsKCkgOiB0aGlzLm1vZGVsLmdldCgndGVybScpLFxuICAgICAgICAgICAgJ3R5cGUnOiB0eXBlLmxlbmd0aCAhPT0gMCA/IHR5cGUudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgndHlwZScpLFxuICAgICAgICAgICAgJ21pblByaWNlJzogbWluUHJpY2UubGVuZ3RoICE9PSAwID8gbWluUHJpY2UudmFsKCkgOiB0aGlzLm1vZGVsLmdldCgnbWluUHJpY2UnKSxcbiAgICAgICAgICAgICdtYXhQcmljZSc6IG1heFByaWNlLmxlbmd0aCAhPT0gMCA/IG1heFByaWNlLnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ21heFByaWNlJyksXG4gICAgICAgICAgICAnY29uZGl0aW9uJzogY29uZGl0aW9uLmxlbmd0aCAhPT0gMCA/IGNvbmRpdGlvbi52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCdjb25kaXRpb24nKSxcbiAgICAgICAgICAgICdzb3J0Jzogc29ydC5sZW5ndGggIT09IDAgPyBzb3J0LnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ3NvcnQnKSxcbiAgICAgICAgICAgICdjYXRlZ29yeSc6IGNhdGVnb3J5Lmxlbmd0aCAhPT0gMCA/IGNhdGVnb3J5LnZhbCgpIDogdGhpcy5tb2RlbC5nZXQoJ2NhdGVnb3J5JyksXG4gICAgICAgICAgICAnd2l0aFZhcmlhbnRzJzogd2l0aFZhcmlhbnRzLmxlbmd0aCAhPT0gMCA/IHdpdGhWYXJpYW50cy52YWwoKSA6IHRoaXMubW9kZWwuZ2V0KCd3aXRoVmFyaWFudHMnKSxcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLWxvYWQtbW9yZS1idXR0b24nOiAnbG9hZCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCBsb2FkIG1vcmUuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtbG9hZC1tb3JlLXRlbXBsYXRlJykuaHRtbCgpO1xuXG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggbG9hZCBtb3JlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEByZXR1cm4ge1NlYXJjaExvYWRNb3JlfVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGhpcy50ZW1wbGF0ZSh0aGlzLm1vZGVsLmF0dHJpYnV0ZXMpKTtcblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRW5hYmxlIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5tb2RlbC5sb2FkKCk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaExvYWRNb3JlO1xuIiwibGV0IFNlYXJjaFJlc3VsdHNJdGVtID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIHRhZ05hbWU6ICdkaXYnLFxuXG4gICAgY2xhc3NOYW1lOiAnJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2xpY2sgLmFmZi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1zaG93LWFsbCc6ICdzaG93QWxsJyxcbiAgICAgICAgJ2NsaWNrIC5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tYWN0aW9ucy1pbXBvcnQnOiAnaW1wb3J0JyxcbiAgICAgICAgJ2NsaWNrIC5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tYWN0aW9ucy1yZXRyeS1pbXBvcnQnOiAnaW1wb3J0J1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS10ZW1wbGF0ZScpLmh0bWwoKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIHJlc3VsdHMgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJuIHtTZWFyY2hSZXN1bHRzSXRlbX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYWxsIGhpZGRlbiB2YXJpYW50cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93QWxsKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtc2hvdy1hbGwnKS5oaWRlKCk7XG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtaW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tdmFyaWFudHMtaXRlbScpLnNob3coKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW1wb3J0IHRoZSBzZWFyY2ggcmVzdWx0IGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIGVcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0KGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuaW1wb3J0KCk7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdHNJdGVtIGZyb20gJy4vc2VhcmNoLXJlc3VsdHMtaXRlbSc7XG5cbmxldCBTZWFyY2hSZXN1bHRzID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnI2FmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzJyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaCByZXN1bHRzLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7YXJyYXl9IG9wdGlvbnNcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbiA9IG9wdGlvbnMuY29sbGVjdGlvbjtcblxuICAgICAgICAvLyBCaW5kIHRoZSBjb2xsZWN0aW9uIGV2ZW50c1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgncmVzZXQnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ2FkZCcsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgncmVtb3ZlJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdzeW5jJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5fYWRkQWxsKCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBhbGwgc2VhcmNoIHJlc3VsdHMgaXRlbXMgdG8gdGhlIHZpZXcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYWRkQWxsKCkge1xuICAgICAgICB0aGlzLiRlbC5lbXB0eSgpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uZm9yRWFjaCh0aGlzLl9hZGRPbmUsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBZGQgb25lIHNlYXJjaCByZXN1bHRzIGl0ZW0gdG8gdGhlIHZpZXcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfYWRkT25lKHByb2R1Y3QpIHtcbiAgICAgICAgbGV0IHZpZXcgPSBuZXcgU2VhcmNoUmVzdWx0c0l0ZW0oe1xuICAgICAgICAgICAgbW9kZWw6IHByb2R1Y3QsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuJGVsLmFwcGVuZCh2aWV3LnJlbmRlcigpLmVsKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHM7XG4iLCJpbXBvcnQgU2VhcmNoRm9ybSBmcm9tICcuL3NlYXJjaC1mb3JtJztcbmltcG9ydCBTZWFyY2hMb2FkTW9yZSBmcm9tICcuL3NlYXJjaC1sb2FkLW1vcmUnO1xuaW1wb3J0IFNlYXJjaFJlc3VsdHMgZnJvbSAnLi9zZWFyY2gtcmVzdWx0cyc7XG5cbmxldCBTZWFyY2ggPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoJyxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIHNlYXJjaC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5mb3JtID0gbmV3IFNlYXJjaEZvcm0oe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuZm9ybSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzID0gbmV3IFNlYXJjaFJlc3VsdHMoe1xuICAgICAgICAgICAgY29sbGVjdGlvbjogdGhpcy5tb2RlbC5yZXN1bHRzLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmxvYWRNb3JlID0gbmV3IFNlYXJjaExvYWRNb3JlKHtcbiAgICAgICAgICAgIG1vZGVsOiB0aGlzLm1vZGVsLmxvYWRNb3JlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIHRoaXMuZm9ybS5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5yZXN1bHRzLnJlbmRlcigpO1xuXG4gICAgICAgIGlmKHRoaXMubW9kZWwuZ2V0KCdzdGFydGVkJykpIHtcbiAgICAgICAgICAgIHRoaXMubG9hZE1vcmUucmVuZGVyKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiJdfQ==
