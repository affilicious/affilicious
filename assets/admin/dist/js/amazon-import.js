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
            'product': product.attributes,
            'config': this.config.attributes,
            'form': this.search.form.attributes
        };

        jQuery.ajax({
            type: 'POST',
            url: this._buildUrl(),
            data: data
        }).done(function (result) {

            console.log(result);

            if (result.data && result.data.shop_template) {

                console.log("Hier");
                console.log(result);
                _this.config.trigger('aff:amazon-import:config:add-shop', result.data.shop_template);
            }

            product.done();
        }).fail(function () {
            product.error();
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
    },


    /**
     * Show a submit error and stop the loading animation.
     *
     * @since 0.9
     * @public
     */
    error: function error() {
        this.set({
            'loading': false,
            'error': true
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
        'error': false
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
     * @public
     */
    error: function error() {
        this.set({
            'enabled': true,
            'loading': false,
            'error': true
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
        'done': false,
        'error': false
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
     * Finish the search result item import.
     *
     * @since 0.9
     * @public
     */
    done: function done() {
        this.set({
            'loading': false,
            'done': true
        });

        this.trigger('aff:amazon-import:search:results:item:done', this);
    },


    /**
     * Display an error for the search result item import.
     *
     * @since 0.9
     * @public
     */
    error: function error() {
        this.set({
            'loading': false,
            'error': true
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
            _this.form.done();
        }).fail(function () {
            _this.loadMore.set('enabled', false);
            _this.form.error();
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
            _this2.loadMore.error();
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
        var templateHtml = jQuery('#aff-amazon-import-config-template').html();
        this.template = _.template(templateHtml);

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
     * Add a new shop
     *
     * @since 0.9
     * @param {Object} shop
     * @public
     */
    addShop: function addShop(shop) {
        this.$el.find('input[value="new-shop"]').parent().before('\n            <label class="aff-amazon-import-config-group-label" for="' + shop.slug + '">\n                <input id="amazon" class="aff-amazon-import-config-group-option" name="shop" type="radio" value="' + shop.slug + '">\n                ' + shop.name + '         \n            </label>\n        ');

        this.$el.find('input[name="shop"][value="' + shop.slug + '"]').prop("checked", true);

        var newShopName = this.$el.find('input[name="new-shop-name"]');
        newShopName.selectize()[0].selectize.clear(true);
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvaW1wb3J0LmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1mb3JtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvc2VhcmNoLXJlc3VsdHMtaXRlbS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2gtcmVzdWx0cy5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9zZWFyY2guanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9jb25maWcuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9pbXBvcnQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy92aWV3L3NlYXJjaC1sb2FkLW1vcmUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2gtcmVzdWx0cy1pdGVtLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL3ZpZXcvc2VhcmNoLXJlc3VsdHMuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9z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LE1BQVY7QUFDQSxhQUFLLE9BQUwsQ0FBYSxNQUFiOztBQUVBLFlBQUcsS0FBSyxLQUFMLENBQVcsR0FBWCxDQUFlLFNBQWYsQ0FBSCxFQUE4QjtBQUMxQixpQkFBSyxRQUFMLENBQWMsTUFBZDtBQUNIOztBQUVELGVBQU8sSUFBUDtBQUNIO0FBeEM2QixDQUFyQixDQUFiOztrQkEyQ2UsTSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCJpbXBvcnQgSW1wb3J0IGZyb20gJy4vbW9kZWwvaW1wb3J0JztcbmltcG9ydCBJbXBvcnRWaWV3IGZyb20gJy4vdmlldy9pbXBvcnQnO1xuXG5sZXQgaW1wb3J0TW9kZWwgPSBuZXcgSW1wb3J0KCk7XG5sZXQgaW1wb3J0VmlldyA9IG5ldyBJbXBvcnRWaWV3KHttb2RlbDogaW1wb3J0TW9kZWx9KTtcblxuaW1wb3J0Vmlldy5yZW5kZXIoKTtcbiIsImxldCBDb25maWcgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdzZWxlY3RlZFNob3AnOiAnYW1hem9uJyxcbiAgICAgICAgJ25ld1Nob3BOYW1lJzogbnVsbCxcbiAgICAgICAgJ3NlbGVjdGVkQWN0aW9uJzogJ25ldy1wcm9kdWN0JyxcbiAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbnVsbCxcbiAgICAgICAgJ3JlcGxhY2VQcm9kdWN0SWQnOiBudWxsLFxuICAgICAgICAnc3RhdHVzJzogJ2RyYWZ0JyxcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9zZWFyY2gnO1xuaW1wb3J0IENvbmZpZyBmcm9tICcuL2NvbmZpZyc7XG5cbmxldCBJbXBvcnQgPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdhY3Rpb24nOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX2ltcG9ydCcsXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpemUgdGhlIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNlYXJjaCA9IG5ldyBTZWFyY2goKTtcbiAgICAgICAgdGhpcy5jb25maWcgPSBuZXcgQ29uZmlnKCk7XG5cbiAgICAgICAgdGhpcy5zZWFyY2gub24oJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCB0aGlzLmltcG9ydCwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgcHJvZHVjdC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gcHJvZHVjdFxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQocHJvZHVjdCkge1xuICAgICAgICBsZXQgZGF0YSA9IHtcbiAgICAgICAgICAgICdwcm9kdWN0JzogcHJvZHVjdC5hdHRyaWJ1dGVzLFxuICAgICAgICAgICAgJ2NvbmZpZyc6IHRoaXMuY29uZmlnLmF0dHJpYnV0ZXMsXG4gICAgICAgICAgICAnZm9ybSc6IHRoaXMuc2VhcmNoLmZvcm0uYXR0cmlidXRlcyxcbiAgICAgICAgfTtcblxuICAgICAgICBqUXVlcnkuYWpheCh7XG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICB1cmw6IHRoaXMuX2J1aWxkVXJsKCksXG4gICAgICAgICAgICBkYXRhOiBkYXRhLFxuICAgICAgICB9KS5kb25lKChyZXN1bHQpID0+IHtcblxuICAgICAgICAgICAgY29uc29sZS5sb2cocmVzdWx0KTtcblxuICAgICAgICAgICAgaWYocmVzdWx0LmRhdGEgJiYgcmVzdWx0LmRhdGEuc2hvcF90ZW1wbGF0ZSkge1xuXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coXCJIaWVyXCIpO1xuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKHJlc3VsdCk7XG4gICAgICAgICAgICAgICAgdGhpcy5jb25maWcudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6Y29uZmlnOmFkZC1zaG9wJywgcmVzdWx0LmRhdGEuc2hvcF90ZW1wbGF0ZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHByb2R1Y3QuZG9uZSgpO1xuICAgICAgICB9KS5mYWlsKCgpID0+IHtcbiAgICAgICAgICAgIHByb2R1Y3QuZXJyb3IoKTtcbiAgICAgICAgfSlcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQnVpbGQgdGhlIGltcG9ydCB1cmwgYmFzZWQgb24gdGhlIGdpdmVuIHBhcmFtZXRlcnMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge3N0cmluZ31cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9idWlsZFVybCgpIHtcbiAgICAgICAgcmV0dXJuIGFmZkFkbWluQW1hem9uSW1wb3J0VXJscy5hamF4XG4gICAgICAgICAgICArIGA/YWN0aW9uPSR7dGhpcy5nZXQoJ2FjdGlvbicpfWBcbiAgICAgICAgO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgSW1wb3J0O1xuIiwibGV0IFNlYXJjaEZvcm0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICd0ZXJtJzogJycsXG4gICAgICAgICd0eXBlJzogJ2tleXdvcmRzJyxcbiAgICAgICAgJ2NhdGVnb3J5JzogJ0FsbCcsXG4gICAgICAgICd3aXRoVmFyaWFudHMnOiAnbm8nLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnZXJyb3InOiBmYWxzZSxcbiAgICAgICAgJ3Byb3ZpZGVyQ29uZmlndXJlZCc6IGZhbHNlXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgZm9ybSB0aGUgZm9ybSBhbmQgdHJpZ2dlciB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06c3VibWl0JywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEZpbmlzaCB0aGUgc3VibWl0IGFuZCBzdG9wIHRoZSBsb2FkaW5nIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZG9uZSgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCBmYWxzZSk7XG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnNlYXJjaC1mb3JtOmRvbmUnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyBhIHN1Ym1pdCBlcnJvciBhbmQgc3RvcCB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGVycm9yKCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2Vycm9yJzogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6c2VhcmNoLWZvcm06ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoRm9ybTtcbiIsImxldCBTZWFyY2hMb2FkTW9yZSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ2VuYWJsZWQnOiB0cnVlLFxuICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAnbm9SZXN1bHRzJzogZmFsc2UsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBBY3RpdmF0ZSB0aGUgbG9hZGluZyBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ2xvYWRpbmcnLCB0cnVlKTtcbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmxvYWQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyB0aGUgbG9hZCBtb3JlIGJ1dHRvbiBhbmQgZGVhY3RpdmF0ZSB0aGUgc3Bpbm5lciBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtib29sZWFufSBlbmFibGVkXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRvbmUoZW5hYmxlZCA9IHRydWUpIHtcbiAgICAgICAgdGhpcy5zZXQoe1xuICAgICAgICAgICAgJ2xvYWRpbmcnOiBmYWxzZSxcbiAgICAgICAgICAgICdlbmFibGVkJzogZW5hYmxlZCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdhZmY6YW1hem9uLWltcG9ydDpzZWFyY2g6bG9hZC1tb3JlOmRvbmUnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU2hvdyB0aGUgbm8gcmVzdWx0cyBtZXNzYWdlIGFuZCBkZWFjdGl2YXRlIHRoZSBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbm9SZXN1bHRzKCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZycgOiBmYWxzZSxcbiAgICAgICAgICAgICdub1Jlc3VsdHMnOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6bm8tcmVzdWx0cycsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTaG93IGEgbG9hZCBtb3JlIGVycm9yIGFuZCBkZWFjdGl2YXRlIHRoZSBzcGlubmVyIGFuaW1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZXJyb3IoKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdlbmFibGVkJzogdHJ1ZSxcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3InOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpsb2FkLW1vcmU6ZXJyb3InLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoTG9hZE1vcmU7XG4iLCJsZXQgU2VhcmNoUmVzdWx0c0l0ZW0gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuICAgIGRlZmF1bHRzOiB7XG4gICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICdkb25lJzogZmFsc2UsXG4gICAgICAgICdlcnJvcic6IGZhbHNlLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIHNlYXJjaCByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0KCkge1xuICAgICAgICB0aGlzLnNldCgnbG9hZGluZycsIHRydWUpO1xuXG4gICAgICAgIHRoaXMudHJpZ2dlcignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOnJlc3VsdHM6aXRlbTppbXBvcnQnLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogRmluaXNoIHRoZSBzZWFyY2ggcmVzdWx0IGl0ZW0gaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBkb25lKCkge1xuICAgICAgICB0aGlzLnNldCh7XG4gICAgICAgICAgICAnbG9hZGluZyc6IGZhbHNlLFxuICAgICAgICAgICAgJ2RvbmUnOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06ZG9uZScsIHRoaXMpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBEaXNwbGF5IGFuIGVycm9yIGZvciB0aGUgc2VhcmNoIHJlc3VsdCBpdGVtIGltcG9ydC5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZXJyb3IoKSB7XG4gICAgICAgIHRoaXMuc2V0KHtcbiAgICAgICAgICAgICdsb2FkaW5nJzogZmFsc2UsXG4gICAgICAgICAgICAnZXJyb3InOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06ZXJyb3InLCB0aGlzKTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaFJlc3VsdHNJdGVtO1xuIiwiaW1wb3J0IFNlYXJjaFJlc3VsdEl0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG4gICAgbW9kZWw6IFNlYXJjaFJlc3VsdEl0ZW0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgdGhpcy5vbignc3luYycsIHRoaXMuaW5pdEltcG9ydExpc3RlbmVycywgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFBhcnNlIHRoZSBXb3JkcHJlc3MganNvbiBBamF4IHJlc3BvbnNlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7QXJyYXl9IHJlc3BvbnNlXG4gICAgICogQHJldHVybnMge0FycmF5fVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBwYXJzZTogZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgcmV0dXJuIHJlc3BvbnNlICYmIHJlc3BvbnNlLnN1Y2Nlc3MgPyByZXNwb25zZS5kYXRhIDogW107XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEltcG9ydCB0aGUgZ2l2ZW4gaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gbW9kZWxcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW1wb3J0SXRlbShtb2RlbCkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOmltcG9ydC1pdGVtJywgbW9kZWwpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0IHRoZSBpbXBvcnQgbGlzdGVuZXJzIGZvciBhbGwgcmVzdWx0cyBpdGVtcy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdEltcG9ydExpc3RlbmVycygpIHtcbiAgICAgICAgdGhpcy5mb3JFYWNoKHRoaXMuX2luaXRJbXBvcnRMaXN0ZW5lciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEluaXQgdGhlIGltcG9ydCBsaXN0ZW5lcnMgZm9yIHRoZSByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9pbml0SW1wb3J0TGlzdGVuZXIobW9kZWwpIHtcbiAgICAgICAgbW9kZWwub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOml0ZW06aW1wb3J0JywgdGhpcy5pbXBvcnRJdGVtLCB0aGlzKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3N0YXJ0ZWQnOiBmYWxzZSxcbiAgICAgICAgJ2FjdGlvbic6ICdhZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25fc2VhcmNoJyxcbiAgICAgICAgJ3BhZ2UnIDogMSxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHdpdGggdGhlIGdpdmVuIG9wdGlvbnMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqL1xuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSgpO1xuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cygpO1xuICAgICAgICB0aGlzLmxvYWRNb3JlID0gbmV3IFNlYXJjaExvYWRNb3JlKCk7XG4gICAgICAgIHRoaXMucGFnZSA9IG9wdGlvbnMgJiYgb3B0aW9ucy5wYWdlID8gb3B0aW9ucy5wYWdlIDogMTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpyZXN1bHRzOmltcG9ydC1pdGVtJywgdGhpcy5pbXBvcnQsIHRoaXMpO1xuICAgICAgICB0aGlzLmZvcm0ub24oJ2FmZjphbWF6b24taW1wb3J0OnNlYXJjaDpzZWFyY2gtZm9ybTpzdWJtaXQnLCB0aGlzLnN0YXJ0LCB0aGlzKTtcbiAgICAgICAgdGhpcy5sb2FkTW9yZS5vbignYWZmOmFtYXpvbi1pbXBvcnQ6c2VhcmNoOmxvYWQtbW9yZTpsb2FkJywgdGhpcy5sb2FkLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogU3RhcnQgdGhlIHNlYXJjaCB3aXRoIHRoZSBmaXJzdCBwYWdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzdGFydCgpIHtcbiAgICAgICAgaWYodGhpcy5mb3JtLmdldCgndGVybScpID09PSBudWxsKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNldCgncGFnZScsIDEpO1xuICAgICAgICB0aGlzLnJlc3VsdHMudXJsID0gdGhpcy5fYnVpbGRVcmwoKTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMuZmV0Y2goKS5kb25lKChyZXN1bHRzKSA9PiB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLnNldCgnZW5hYmxlZCcsIHRoaXMuX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpKTtcbiAgICAgICAgICAgIHRoaXMuZm9ybS5kb25lKCk7XG4gICAgICAgIH0pLmZhaWwoKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5zZXQoJ2VuYWJsZWQnLCBmYWxzZSk7XG4gICAgICAgICAgICB0aGlzLmZvcm0uZXJyb3IoKTtcbiAgICAgICAgfSkuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2V0KCdzdGFydGVkJywgdHJ1ZSk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIG1vcmUgc2VhcmNoIHJlc3VsdHMgYnkgaW5jcmVhc2luZyB0aGUgcGFnZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgbG9hZCgpIHtcbiAgICAgICAgdGhpcy5zZXQoJ3BhZ2UnLCB0aGlzLmdldCgncGFnZScpICsgMSk7XG4gICAgICAgIHRoaXMucmVzdWx0cy51cmwgPSB0aGlzLl9idWlsZFVybCgpO1xuXG4gICAgICAgIHRoaXMucmVzdWx0cy5mZXRjaCh7J3JlbW92ZSc6IGZhbHNlfSkuZG9uZSgocmVzdWx0cykgPT4ge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5kb25lKHRoaXMuX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpKTtcbiAgICAgICAgfSkuZmFpbCgoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLmxvYWRNb3JlLmVycm9yKCk7XG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIGdpdmVuIHNlYXJjaCByZXN1bHRzIGl0ZW0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IG1vZGVsXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGltcG9ydChtb2RlbCkge1xuICAgICAgICB0aGlzLnRyaWdnZXIoJ2FmZjphbWF6b24taW1wb3J0OmltcG9ydC1yZXN1bHRzLWl0ZW0nLCBtb2RlbCk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEJ1aWxkIHRoZSBzZWFyY2ggQVBJIHVybCBiYXNlZCBvbiB0aGUgZ2l2ZW4gcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2J1aWxkVXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXhcbiAgICAgICAgICAgICsgYD9hY3Rpb249JHt0aGlzLmdldCgnYWN0aW9uJyl9YFxuICAgICAgICAgICAgKyBgJnRlcm09JHt0aGlzLmZvcm0uZ2V0KCd0ZXJtJyl9YFxuICAgICAgICAgICAgKyBgJnR5cGU9JHt0aGlzLmZvcm0uZ2V0KCd0eXBlJyl9YFxuICAgICAgICAgICAgKyBgJmNhdGVnb3J5PSR7dGhpcy5mb3JtLmdldCgnY2F0ZWdvcnknKX1gXG4gICAgICAgICAgICArIGAmd2l0aC12YXJpYW50cz0ke3RoaXMuZm9ybS5nZXQoJ3dpdGhWYXJpYW50cycpfWBcbiAgICAgICAgICAgICsgYCZwYWdlPSR7dGhpcy5nZXQoJ3BhZ2UnKX1gXG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIENoZWNrIGlmIHRoZSBsb2FkIG1vcmUgYnV0dG9uIGlzIGVuYWJsZWQgKHZpc2libGUpLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSB7QXJyYXl8bnVsbH0gcmVzdWx0c1xuICAgICAqIEByZXR1cm5zIHtib29sfVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2lzTG9hZE1vcmVFbmFibGVkKHJlc3VsdHMpIHtcbiAgICAgICAgcmV0dXJuIChyZXN1bHRzICYmIHJlc3VsdHMuZGF0YSAmJiByZXN1bHRzLmRhdGEubGVuZ3RoID4gMClcbiAgICAgICAgICAgICYmIHRoaXMuZ2V0KCdwYWdlJykgPCA1XG4gICAgICAgICAgICAmJiB0aGlzLmZvcm0uZ2V0KCd0eXBlJykgPT09ICdrZXl3b3Jkcyc7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IFNlYXJjaDtcbiIsImxldCBDb25maWcgPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LWNvbmZpZycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwic2hvcFwiXSc6ICdjaGFuZ2VTaG9wJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwibmV3LXNob3AtbmFtZVwiXSc6ICdjaGFuZ2VTaG9wJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwiYWN0aW9uXCJdJzogJ2NoYW5nZUFjdGlvbicsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cIm1lcmdlLXByb2R1Y3QtaWRcIl0nOiAnY2hhbmdlQWN0aW9uJyxcbiAgICAgICAgJ2NoYW5nZSBpbnB1dFtuYW1lPVwicmVwbGFjZS1wcm9kdWN0LWlkXCJdJzogJ2NoYW5nZUFjdGlvbicsXG4gICAgICAgICdjaGFuZ2UgaW5wdXRbbmFtZT1cInN0YXR1c1wiXSc6ICdjaGFuZ2VTdGF0dXMnLFxuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1jb25maWctdGVtcGxhdGUnKS5odG1sKCk7XG4gICAgICAgIHRoaXMudGVtcGxhdGUgPSBfLnRlbXBsYXRlKHRlbXBsYXRlSHRtbCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5vbignYWZmOmFtYXpvbi1pbXBvcnQ6Y29uZmlnOmFkZC1zaG9wJywgdGhpcy5hZGRTaG9wLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge0NvbmZpZ31cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICBsZXQgaHRtbCA9IHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKTtcbiAgICAgICAgdGhpcy4kZWwuaHRtbChodG1sKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLWdyb3VwLW9wdGlvbi1tZXJnZS1wcm9kdWN0LWlkLCAuYWZmLWFtYXpvbi1pbXBvcnQtY29uZmlnLWdyb3VwLW9wdGlvbi1yZXBsYWNlLXByb2R1Y3QtaWQnKS5zZWxlY3RpemUoe1xuICAgICAgICAgICAgbWF4SXRlbXM6IDEsXG4gICAgICAgICAgICB2YWx1ZUZpZWxkOiAnaWQnLFxuICAgICAgICAgICAgbGFiZWxGaWVsZDogJ25hbWUnLFxuICAgICAgICAgICAgc2VhcmNoRmllbGQ6ICduYW1lJyxcbiAgICAgICAgICAgIGNyZWF0ZTogZmFsc2UsXG4gICAgICAgICAgICBsb2FkOiBmdW5jdGlvbihxdWVyeSwgY2FsbGJhY2spIHtcbiAgICAgICAgICAgICAgICBpZiAoIXF1ZXJ5Lmxlbmd0aCkgcmV0dXJuIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgalF1ZXJ5LmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6ICcvd3AtanNvbi93cC92Mi9hZmYtcHJvZHVjdHMvP3NlYXJjaD0nICsgcXVlcnksXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgICAgICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihyZXN1bHRzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHRzID0gcmVzdWx0cy5tYXAoKHJlc3VsdCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdpZCc6IHJlc3VsdC5pZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ25hbWUnOiByZXN1bHQudGl0bGUucmVuZGVyZWRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2socmVzdWx0cyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBhIG5ldyBzaG9wXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHtPYmplY3R9IHNob3BcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgYWRkU2hvcChzaG9wKVxuICAgIHtcbiAgICAgICAgdGhpcy4kZWwuZmluZCgnaW5wdXRbdmFsdWU9XCJuZXctc2hvcFwiXScpLnBhcmVudCgpLmJlZm9yZShgXG4gICAgICAgICAgICA8bGFiZWwgY2xhc3M9XCJhZmYtYW1hem9uLWltcG9ydC1jb25maWctZ3JvdXAtbGFiZWxcIiBmb3I9XCIke3Nob3Auc2x1Z31cIj5cbiAgICAgICAgICAgICAgICA8aW5wdXQgaWQ9XCJhbWF6b25cIiBjbGFzcz1cImFmZi1hbWF6b24taW1wb3J0LWNvbmZpZy1ncm91cC1vcHRpb25cIiBuYW1lPVwic2hvcFwiIHR5cGU9XCJyYWRpb1wiIHZhbHVlPVwiJHtzaG9wLnNsdWd9XCI+XG4gICAgICAgICAgICAgICAgJHtzaG9wLm5hbWV9ICAgICAgICAgXG4gICAgICAgICAgICA8L2xhYmVsPlxuICAgICAgICBgKTtcblxuICAgICAgICB0aGlzLiRlbC5maW5kKGBpbnB1dFtuYW1lPVwic2hvcFwiXVt2YWx1ZT1cIiR7c2hvcC5zbHVnfVwiXWApLnByb3AoXCJjaGVja2VkXCIsIHRydWUpXG5cbiAgICAgICAgbGV0IG5ld1Nob3BOYW1lID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm5ldy1zaG9wLW5hbWVcIl0nKTtcbiAgICAgICAgbmV3U2hvcE5hbWUuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplLmNsZWFyKHRydWUpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc2hvcCBjb25maWd1cmF0aW9uIGludG8gdGhlIG1vZGVsIG9uIGNoYW5nZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY2hhbmdlU2hvcCgpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkU2hvcCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJzaG9wXCJdOmNoZWNrZWQnKSxcbiAgICAgICAgICAgIG5ld1Nob3BOYW1lID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cIm5ldy1zaG9wLW5hbWVcIl0nKTtcblxuICAgICAgICBzZWxlY3RlZFNob3AudmFsKCkgPT09ICduZXctc2hvcCcgPyBuZXdTaG9wTmFtZS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpIDogbmV3U2hvcE5hbWUuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAnc2VsZWN0ZWRTaG9wJzogc2VsZWN0ZWRTaG9wLnZhbCgpLFxuICAgICAgICAgICAgJ25ld1Nob3BOYW1lJzogbmV3U2hvcE5hbWUudmFsKCksXG4gICAgICAgIH0pO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgYWN0aW9uIGNvbmZpZ3VyYXRpb24gaW50byB0aGUgbW9kZWwgb24gY2hhbmdlLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjaGFuZ2VBY3Rpb24oKSB7XG4gICAgICAgIGxldCBzZWxlY3RlZEFjdGlvbiA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJhY3Rpb25cIl06Y2hlY2tlZCcpLFxuICAgICAgICAgICAgbWVyZ2VQcm9kdWN0SWQgPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwibWVyZ2UtcHJvZHVjdC1pZFwiXScpLFxuICAgICAgICAgICAgcmVwbGFjZVByb2R1Y3RJZCA9IHRoaXMuJGVsLmZpbmQoJ2lucHV0W25hbWU9XCJyZXBsYWNlLXByb2R1Y3QtaWRcIl0nKSxcbiAgICAgICAgICAgIG1lcmdlU2VsZWN0aXplID0gbWVyZ2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplLFxuICAgICAgICAgICAgcmVwbGFjZVNlbGVjdGl6ZSA9IHJlcGxhY2VQcm9kdWN0SWQuc2VsZWN0aXplKClbMF0uc2VsZWN0aXplO1xuXG4gICAgICAgIHNlbGVjdGVkQWN0aW9uLnZhbCgpID09PSAnbWVyZ2UtcHJvZHVjdCcgPyBtZXJnZVNlbGVjdGl6ZS5lbmFibGUoKSA6IG1lcmdlU2VsZWN0aXplLmRpc2FibGUoKTtcbiAgICAgICAgc2VsZWN0ZWRBY3Rpb24udmFsKCkgPT09ICdyZXBsYWNlLXByb2R1Y3QnID8gcmVwbGFjZVNlbGVjdGl6ZS5lbmFibGUoKSA6IHJlcGxhY2VTZWxlY3RpemUuZGlzYWJsZSgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KHtcbiAgICAgICAgICAgICdzZWxlY3RlZEFjdGlvbic6IHNlbGVjdGVkQWN0aW9uLnZhbCgpLFxuICAgICAgICAgICAgJ21lcmdlUHJvZHVjdElkJzogbWVyZ2VQcm9kdWN0SWQudmFsKCksXG4gICAgICAgICAgICAncmVwbGFjZVByb2R1Y3RJZCc6IHJlcGxhY2VQcm9kdWN0SWQudmFsKClcbiAgICAgICAgfSk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIG5ldyBzdGF0dXMgY29uZmlndXJhdGlvbiBpbnRvIHRoZSBtb2RlbCBvbiBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZVN0YXR1cygpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkU3RhdHVzID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInN0YXR1c1wiXTpjaGVja2VkJyk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5zZXQoe1xuICAgICAgICAgICAgJ3N0YXR1cyc6IHNlbGVjdGVkU3RhdHVzLnZhbCgpLFxuICAgICAgICB9KTtcbiAgICB9LFxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IENvbmZpZztcbiIsImltcG9ydCBTZWFyY2ggZnJvbSAnLi9zZWFyY2gnO1xuaW1wb3J0IENvbmZpZyBmcm9tICcuL2NvbmZpZyc7XG5cbmxldCBJbXBvcnQgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgaW1wb3J0LlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLnNlYXJjaCA9IG5ldyBTZWFyY2goe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuc2VhcmNoLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLmNvbmZpZyA9IG5ldyBDb25maWcoe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwuY29uZmlnLFxuICAgICAgICB9KTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBpbXBvcnQuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zZWFyY2gucmVuZGVyKCk7XG4gICAgICAgIHRoaXMuY29uZmlnLnJlbmRlcigpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgSW1wb3J0O1xuIiwibGV0IFNlYXJjaEZvcm0gPSAgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIGVsOiAnLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1mb3JtJyxcblxuICAgIGV2ZW50czoge1xuICAgICAgICAnY2hhbmdlJzogJ2NoYW5nZScsXG4gICAgICAgICdzdWJtaXQnOiAnc3VibWl0JyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgIGxldCB0ZW1wbGF0ZUh0bWwgPSBqUXVlcnkoJyNhZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtZm9ybS10ZW1wbGF0ZScpLmh0bWwoKSxcbiAgICAgICAgICAgIHByb3ZpZGVyQ29uZmlndXJlZCA9IHRoaXMuJGVsLmRhdGEoJ3Byb3ZpZGVyLWNvbmZpZ3VyZWQnKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdwcm92aWRlckNvbmZpZ3VyZWQnLCBwcm92aWRlckNvbmZpZ3VyZWQgPT09IHRydWUgfHwgcHJvdmlkZXJDb25maWd1cmVkID09PSAndHJ1ZScpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGZvcm0uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHJldHVybnMge1NlYXJjaEZvcm19XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy4kZWwuaHRtbCh0aGlzLnRlbXBsYXRlKHRoaXMubW9kZWwuYXR0cmlidXRlcykpO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBTdWJtaXQgdGhlIHNlYXJjaCBmb3JtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwYXJhbSBlXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHN1Ym1pdChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLmNoYW5nZSgpO1xuICAgICAgICB0aGlzLm1vZGVsLnN1Ym1pdCgpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBMb2FkIHRoZSBuZXcgc2VhcmNoIHBhcmFtZXRlcnMgaW50byB0aGUgbW9kZWwgb24gZm9ybSBjaGFuZ2UuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNoYW5nZSgpIHtcbiAgICAgICAgbGV0IHRlcm0gPSB0aGlzLiRlbC5maW5kKCdpbnB1dFtuYW1lPVwidGVybVwiXScpLFxuICAgICAgICAgICAgdHlwZSA9IHRoaXMuJGVsLmZpbmQoJ3NlbGVjdFtuYW1lPVwidHlwZVwiXScpLFxuICAgICAgICAgICAgY2F0ZWdvcnkgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cImNhdGVnb3J5XCJdJyksXG4gICAgICAgICAgICB3aXRoVmFyaWFudHMgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cIndpdGgtdmFyaWFudHNcIl0nKTtcblxuICAgICAgICB0aGlzLm1vZGVsLnNldCh7XG4gICAgICAgICAgICAndGVybSc6IHRlcm0udmFsKCksXG4gICAgICAgICAgICAndHlwZSc6IHR5cGUudmFsKCksXG4gICAgICAgICAgICAnY2F0ZWdvcnknOiBjYXRlZ29yeS52YWwoKSxcbiAgICAgICAgICAgICd3aXRoVmFyaWFudHMnOiB3aXRoVmFyaWFudHMudmFsKClcbiAgICAgICAgfSk7XG4gICAgfSxcbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hGb3JtO1xuIiwibGV0IFNlYXJjaExvYWRNb3JlID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1sb2FkLW1vcmUnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWFtYXpvbi1pbXBvcnQtbG9hZC1tb3JlLWJ1dHRvbic6ICdsb2FkJyxcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgaW5pdGlhbGl6ZSgpIHtcbiAgICAgICAgbGV0IHRlbXBsYXRlSHRtbCA9IGpRdWVyeSgnI2FmZi1hbWF6b24taW1wb3J0LWxvYWQtbW9yZS10ZW1wbGF0ZScpLmh0bWwoKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIGxvYWQgbW9yZS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJuIHtTZWFyY2hMb2FkTW9yZX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEVuYWJsZSB0aGUgbG9hZGluZyBhbmltYXRpb24uXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGxvYWQoKSB7XG4gICAgICAgIHRoaXMubW9kZWwubG9hZCgpO1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBTZWFyY2hMb2FkTW9yZTtcbiIsImxldCBTZWFyY2hSZXN1bHRzSXRlbSA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICB0YWdOYW1lOiAnZGl2JyxcblxuICAgIGNsYXNzTmFtZTogJycsXG5cbiAgICBldmVudHM6IHtcbiAgICAgICAgJ2NsaWNrIC5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJzogJ3Nob3dBbGwnLFxuICAgICAgICAnY2xpY2sgLmFmZi1hbWF6b24taW1wb3J0LXNlYXJjaC1yZXN1bHRzLWl0ZW0tYWN0aW9ucy1pbXBvcnQnOiAnaW1wb3J0J1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplIHRoZSBzZWFyY2ggcmVzdWx0cyBpdGVtLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICBsZXQgdGVtcGxhdGVIdG1sID0galF1ZXJ5KCcjYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS10ZW1wbGF0ZScpLmh0bWwoKTtcblxuICAgICAgICB0aGlzLnRlbXBsYXRlID0gXy50ZW1wbGF0ZSh0ZW1wbGF0ZUh0bWwpO1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlciwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFJlbmRlciB0aGUgc2VhcmNoIHJlc3VsdHMgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcmV0dXJuIHtTZWFyY2hSZXN1bHRzSXRlbX1cbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLiRlbC5odG1sKHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIFNob3cgYWxsIGhpZGRlbiB2YXJpYW50cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBzaG93QWxsKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHRoaXMuJGVsLmZpbmQoJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJykuaGlkZSgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMtaXRlbS12YXJpYW50cy1pdGVtJykuc2hvdygpO1xuICAgIH0sXG5cbiAgICAvKipcbiAgICAgKiBJbXBvcnQgdGhlIHNlYXJjaCByZXN1bHQgaXRlbS5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcGFyYW0gZVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbXBvcnQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5tb2RlbC5pbXBvcnQoKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0c0l0ZW07XG4iLCJpbXBvcnQgU2VhcmNoUmVzdWx0c0l0ZW0gZnJvbSAnLi9zZWFyY2gtcmVzdWx0cy1pdGVtJztcblxubGV0IFNlYXJjaFJlc3VsdHMgPSBCYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXJlc3VsdHMnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoIHJlc3VsdHMuXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHBhcmFtIHthcnJheX0gb3B0aW9uc1xuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKG9wdGlvbnMpIHtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uID0gb3B0aW9ucy5jb2xsZWN0aW9uO1xuXG4gICAgICAgIC8vIEJpbmQgdGhlIGNvbGxlY3Rpb24gZXZlbnRzXG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZXNldCcsICgpID0+IHRoaXMucmVuZGVyKCkpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnYWRkJywgKCkgPT4gdGhpcy5yZW5kZXIoKSk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdyZW1vdmUnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3N5bmMnLCAoKSA9PiB0aGlzLnJlbmRlcigpKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2ggcmVzdWx0cy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgcmVuZGVyKCkge1xuICAgICAgICB0aGlzLl9hZGRBbGwoKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogQWRkIGFsbCBzZWFyY2ggcmVzdWx0cyBpdGVtcyB0byB0aGUgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRBbGwoKSB7XG4gICAgICAgIHRoaXMuJGVsLmVtcHR5KCk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5mb3JFYWNoKHRoaXMuX2FkZE9uZSwgdGhpcyk7XG4gICAgfSxcblxuICAgIC8qKlxuICAgICAqIEFkZCBvbmUgc2VhcmNoIHJlc3VsdHMgaXRlbSB0byB0aGUgdmlldy5cbiAgICAgKlxuICAgICAqIEBzaW5jZSAwLjlcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hZGRPbmUocHJvZHVjdCkge1xuICAgICAgICBsZXQgdmlldyA9IG5ldyBTZWFyY2hSZXN1bHRzSXRlbSh7XG4gICAgICAgICAgICBtb2RlbDogcHJvZHVjdCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy4kZWwuYXBwZW5kKHZpZXcucmVuZGVyKCkuZWwpO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoUmVzdWx0cztcbiIsImltcG9ydCBTZWFyY2hGb3JtIGZyb20gJy4vc2VhcmNoLWZvcm0nO1xuaW1wb3J0IFNlYXJjaExvYWRNb3JlIGZyb20gJy4vc2VhcmNoLWxvYWQtbW9yZSc7XG5pbXBvcnQgU2VhcmNoUmVzdWx0cyBmcm9tICcuL3NlYXJjaC1yZXN1bHRzJztcblxubGV0IFNlYXJjaCA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gnLFxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSB0aGUgc2VhcmNoLlxuICAgICAqXG4gICAgICogQHNpbmNlIDAuOVxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBpbml0aWFsaXplKCkge1xuICAgICAgICB0aGlzLmZvcm0gPSBuZXcgU2VhcmNoRm9ybSh7XG4gICAgICAgICAgICBtb2RlbDogdGhpcy5tb2RlbC5mb3JtLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnJlc3VsdHMgPSBuZXcgU2VhcmNoUmVzdWx0cyh7XG4gICAgICAgICAgICBjb2xsZWN0aW9uOiB0aGlzLm1vZGVsLnJlc3VsdHMsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMubG9hZE1vcmUgPSBuZXcgU2VhcmNoTG9hZE1vcmUoe1xuICAgICAgICAgICAgbW9kZWw6IHRoaXMubW9kZWwubG9hZE1vcmUsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMubW9kZWwub24oJ2NoYW5nZScsIHRoaXMucmVuZGVyLCB0aGlzKTtcbiAgICB9LFxuXG4gICAgLyoqXG4gICAgICogUmVuZGVyIHRoZSBzZWFyY2guXG4gICAgICpcbiAgICAgKiBAc2luY2UgMC45XG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5mb3JtLnJlbmRlcigpO1xuICAgICAgICB0aGlzLnJlc3VsdHMucmVuZGVyKCk7XG5cbiAgICAgICAgaWYodGhpcy5tb2RlbC5nZXQoJ3N0YXJ0ZWQnKSkge1xuICAgICAgICAgICAgdGhpcy5sb2FkTW9yZS5yZW5kZXIoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG59KTtcblxuZXhwb3J0IGRlZmF1bHQgU2VhcmNoO1xuIl19
