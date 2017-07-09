(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _resultSearch = require('./model/resultSearch');

var _resultSearch2 = _interopRequireDefault(_resultSearch);

var _resultSearch3 = require('./view/resultSearch');

var _resultSearch4 = _interopRequireDefault(_resultSearch3);

var _searchForm = require('./model/searchForm');

var _searchForm2 = _interopRequireDefault(_searchForm);

var _searchForm3 = require('./view/searchForm');

var _searchForm4 = _interopRequireDefault(_searchForm3);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/*
jQuery(function($) {
    $.ajax({
        url : affAdminAmazonImportUrls.ajax,
        type : 'post',
        data : {
            action : 'aff_product_admin_amazon_search',
        },
        success : function( response ) {
            alert(response)
        }
    });
});
*/

var resultSearchForm = new _searchForm2.default();
var resultSearchFormView = new _searchForm4.default({
    model: resultSearchForm
});

resultSearchFormView.render();

var productSearch = new _resultSearch2.default({ page: 1 });
var productSearchView = new _resultSearch4.default({
    collection: productSearch,
    el: '.aff-amazon-import'
});

productSearch.fetch();
productSearchView.render();

},{"./model/resultSearch":3,"./model/searchForm":4,"./view/resultSearch":6,"./view/searchForm":7}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});
var product = Backbone.Model.extend({
    parse: function parse(response) {
        return response;
    }
});

exports.default = product;

},{}],3:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _result = require('./result');

var _result2 = _interopRequireDefault(_result);

var _searchForm = require('./searchForm');

var _searchForm2 = _interopRequireDefault(_searchForm);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = Backbone.Collection.extend({
    model: _result2.default,

    search: new _searchForm2.default(),

    initialize: function initialize(options) {
        var _this = this;

        if (options && options.search) {
            this.search = options.search;
        }

        if (options && options.page) {
            this.page = options.page;
        }

        this.on('change:selected', function () {
            _this.trigger('change');
        });

        this.search.on('change');
    },
    url: function url() {
        return affAdminAmazonImportUrls.ajax + '?action=aff_product_admin_amazon_search';
    }
});

},{"./result":2,"./searchForm":4}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var searchForm = Backbone.Model.extend({
    defaults: {
        'term': '',
        'type': 'keywords',
        'category': 'all'
    }
});

exports.default = searchForm;

},{}],5:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = Backbone.View.extend({
    template: _.template(jQuery('.aff-amazon-import-results-template').html()),

    tagName: 'div',

    className: '',

    events: {
        'click .aff-amazon-import-results-item-variants-show-all': 'showAll'
    },

    initialize: function initialize() {
        this.model.on('change', this.render);
        this.render();
    },

    render: function render() {
        this.setElement(this.template(this.model.attributes));
        return this;
    },
    showAll: function showAll(e) {
        e.preventDefault();
        this.$el.find('.aff-amazon-import-results-item-variants-show-all').hide();
        this.$el.find('.').removeClass('aff-amazon-import-results-item-variants-item-hidden');
    }
});

},{}],6:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _result = require('./result');

var _result2 = _interopRequireDefault(_result);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = Backbone.View.extend({
    initialize: function initialize(options) {
        this.collection = options.collection;
        this.page = options.page;
        this.results = this.$el.find('.aff-amazon-import-results');

        // Ensure our methods keep the `this` reference to the view itself
        _.bindAll(this, 'search', 'render', 'addOne');

        // Bind the collection events
        /*this.collection.bind('reset', this.render);
        this.collection.bind('add', this.render);
        this.collection.bind('remove', this.render);*/
        this.collection.bind('sync', this.render);

        // Trigger the search if the user completes typing.
        //this.searchInput.on('keyup', _.debounce(this.search, 400));
    },
    render: function render() {
        this.addAll();
    },
    addAll: function addAll() {
        this.results.empty();
        this.collection.forEach(this.addOne);
    },
    addOne: function addOne(product) {
        var view = new _result2.default({
            model: product
        });

        this.results.append(view.render().el);
    },
    search: function search(e) {
        if (e) {
            e.preventDefault();
        }

        var search = this.searchInput.val();
        this.collection.search = search && search.length > 0 ? search : false;
        this.collection.fetch({ remove: false }).done(function () {});
    }
});

},{"./result":5}],7:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var searchForm = Backbone.View.extend({
    el: '.aff-amazon-import-search',

    events: {
        'change': 'changed',
        'submit': 'submitted'
    },

    render: function render() {
        var html = jQuery('.aff-amazon-import-search-template').html(),
            template = _.template(html);

        this.$el.html(template(this.model.attributes));

        return this;
    },
    changed: function changed() {
        var term = this.$el.find('input[name="term"]').val(),
            type = this.$el.find('select[name="type"]').val(),
            category = this.$el.find('select[name="category"]').val();

        this.model.set('term', term);
        this.model.set('type', type);
        this.model.set('category', category);

        this.render();
    },
    submitted: function submitted(e) {
        e.preventDefault();

        this.trigger('search', e);
    }
});

exports.default = searchForm;

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvYW1hem9uLWltcG9ydC5qcyIsImFzc2V0cy9hZG1pbi9zcmMvYW1hem9uLWltcG9ydC9qcy9tb2RlbC9yZXN1bHQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvbW9kZWwvcmVzdWx0U2VhcmNoLmpzIiwiYXNzZXRzL2FkbWluL3NyYy9hbWF6b24taW1wb3J0L2pzL21vZGVsL3NlYXJjaEZvcm0uanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9yZXN1bHQuanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9yZXN1bHRTZWFyY2guanMiLCJhc3NldHMvYWRtaW4vc3JjL2FtYXpvbi1pbXBvcnQvanMvdmlldy9zZWFyY2hGb3JtLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7QUNBQTs7OztBQUNBOzs7O0FBRUE7Ozs7QUFDQTs7Ozs7O0FBRUE7Ozs7Ozs7Ozs7Ozs7OztBQWVBLElBQUksbUJBQW1CLDBCQUF2QjtBQUNBLElBQUksdUJBQXVCLHlCQUF5QjtBQUNoRCxXQUFPO0FBRHlDLENBQXpCLENBQTNCOztBQUlBLHFCQUFxQixNQUFyQjs7QUFFQSxJQUFJLGdCQUFnQiwyQkFBaUIsRUFBQyxNQUFNLENBQVAsRUFBakIsQ0FBcEI7QUFDQSxJQUFJLG9CQUFvQiwyQkFBcUI7QUFDekMsZ0JBQVksYUFENkI7QUFFekMsUUFBSTtBQUZxQyxDQUFyQixDQUF4Qjs7QUFLQSxjQUFjLEtBQWQ7QUFDQSxrQkFBa0IsTUFBbEI7Ozs7Ozs7O0FDbkNBLElBQUksVUFBVSxTQUFTLEtBQVQsQ0FBZSxNQUFmLENBQXNCO0FBQ2hDLFNBRGdDLGlCQUMxQixRQUQwQixFQUNqQjtBQUNYLGVBQU8sUUFBUDtBQUNIO0FBSCtCLENBQXRCLENBQWQ7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LE1BQWYsRUFBdUIsSUFBdkI7QUFDQSxhQUFLLEtBQUwsQ0FBVyxHQUFYLENBQWUsTUFBZixFQUF1QixJQUF2QjtBQUNBLGFBQUssS0FBTCxDQUFXLEdBQVgsQ0FBZSxVQUFmLEVBQTJCLFFBQTNCOztBQUVBLGFBQUssTUFBTDtBQUNILEtBM0JrQztBQTZCbkMsYUE3Qm1DLHFCQTZCekIsQ0E3QnlCLEVBNkJ0QjtBQUNULFVBQUUsY0FBRjs7QUFFQSxhQUFLLE9BQUwsQ0FBYSxRQUFiLEVBQXVCLENBQXZCO0FBQ0g7QUFqQ2tDLENBQXJCLENBQWxCOztrQkFvQ2UsVSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCJpbXBvcnQgUmVzdWx0U2VhcmNoIGZyb20gJy4vbW9kZWwvcmVzdWx0U2VhcmNoJztcbmltcG9ydCBSZXN1bHRTZWFyY2hWaWV3IGZyb20gJy4vdmlldy9yZXN1bHRTZWFyY2gnO1xuXG5pbXBvcnQgUmVzdWx0U2VhcmNoRm9ybSBmcm9tICcuL21vZGVsL3NlYXJjaEZvcm0nO1xuaW1wb3J0IFJlc3VsdFNlYXJjaEZvcm1WaWV3IGZyb20gJy4vdmlldy9zZWFyY2hGb3JtJztcblxuLypcbmpRdWVyeShmdW5jdGlvbigkKSB7XG4gICAgJC5hamF4KHtcbiAgICAgICAgdXJsIDogYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXgsXG4gICAgICAgIHR5cGUgOiAncG9zdCcsXG4gICAgICAgIGRhdGEgOiB7XG4gICAgICAgICAgICBhY3Rpb24gOiAnYWZmX3Byb2R1Y3RfYWRtaW5fYW1hem9uX3NlYXJjaCcsXG4gICAgICAgIH0sXG4gICAgICAgIHN1Y2Nlc3MgOiBmdW5jdGlvbiggcmVzcG9uc2UgKSB7XG4gICAgICAgICAgICBhbGVydChyZXNwb25zZSlcbiAgICAgICAgfVxuICAgIH0pO1xufSk7XG4qL1xuXG5sZXQgcmVzdWx0U2VhcmNoRm9ybSA9IG5ldyBSZXN1bHRTZWFyY2hGb3JtKCk7XG5sZXQgcmVzdWx0U2VhcmNoRm9ybVZpZXcgPSBuZXcgUmVzdWx0U2VhcmNoRm9ybVZpZXcoe1xuICAgIG1vZGVsOiByZXN1bHRTZWFyY2hGb3JtXG59KTtcblxucmVzdWx0U2VhcmNoRm9ybVZpZXcucmVuZGVyKCk7XG5cbmxldCBwcm9kdWN0U2VhcmNoID0gbmV3IFJlc3VsdFNlYXJjaCh7cGFnZTogMX0pO1xubGV0IHByb2R1Y3RTZWFyY2hWaWV3ID0gbmV3IFJlc3VsdFNlYXJjaFZpZXcoe1xuICAgIGNvbGxlY3Rpb246IHByb2R1Y3RTZWFyY2gsXG4gICAgZWw6ICcuYWZmLWFtYXpvbi1pbXBvcnQnXG59KTtcblxucHJvZHVjdFNlYXJjaC5mZXRjaCgpO1xucHJvZHVjdFNlYXJjaFZpZXcucmVuZGVyKCk7XG4iLCJsZXQgcHJvZHVjdCA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgcGFyc2UocmVzcG9uc2Upe1xuICAgICAgICByZXR1cm4gcmVzcG9uc2U7XG4gICAgfVxufSk7XG5cbmV4cG9ydCBkZWZhdWx0IHByb2R1Y3Q7XG4iLCJpbXBvcnQgUHJvZHVjdCBmcm9tICcuL3Jlc3VsdCc7XG5pbXBvcnQgU2VhcmNoIGZyb20gJy4vc2VhcmNoRm9ybSc7XG5cbmV4cG9ydCBkZWZhdWx0IEJhY2tib25lLkNvbGxlY3Rpb24uZXh0ZW5kKHtcbiAgICBtb2RlbDogUHJvZHVjdCxcblxuICAgIHNlYXJjaDogbmV3IFNlYXJjaCgpLFxuXG4gICAgaW5pdGlhbGl6ZShvcHRpb25zKSB7XG4gICAgICAgIGlmKG9wdGlvbnMgJiYgb3B0aW9ucy5zZWFyY2gpIHtcbiAgICAgICAgICAgIHRoaXMuc2VhcmNoID0gb3B0aW9ucy5zZWFyY2g7XG4gICAgICAgIH1cblxuICAgICAgICBpZihvcHRpb25zICYmIG9wdGlvbnMucGFnZSkge1xuICAgICAgICAgICAgdGhpcy5wYWdlID0gb3B0aW9ucy5wYWdlO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5vbignY2hhbmdlOnNlbGVjdGVkJywgKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5zZWFyY2gub24oJ2NoYW5nZScpXG5cblxuXG5cbiAgICB9LFxuXG4gICAgdXJsKCkge1xuICAgICAgICByZXR1cm4gYWZmQWRtaW5BbWF6b25JbXBvcnRVcmxzLmFqYXggKyAnP2FjdGlvbj1hZmZfcHJvZHVjdF9hZG1pbl9hbWF6b25fc2VhcmNoJztcbiAgICB9LFxufSk7XG4iLCJsZXQgc2VhcmNoRm9ybSA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG4gICAgZGVmYXVsdHM6IHtcbiAgICAgICAgJ3Rlcm0nOiAnJyxcbiAgICAgICAgJ3R5cGUnOiAna2V5d29yZHMnLFxuICAgICAgICAnY2F0ZWdvcnknOiAnYWxsJ1xuICAgIH1cbn0pO1xuXG5leHBvcnQgZGVmYXVsdCBzZWFyY2hGb3JtO1xuIiwiZXhwb3J0IGRlZmF1bHQgQmFja2JvbmUuVmlldy5leHRlbmQoe1xuICAgIHRlbXBsYXRlOiBfLnRlbXBsYXRlKGpRdWVyeSgnLmFmZi1hbWF6b24taW1wb3J0LXJlc3VsdHMtdGVtcGxhdGUnKS5odG1sKCkpLFxuXG4gICAgdGFnTmFtZTogJ2RpdicsXG5cbiAgICBjbGFzc05hbWU6ICcnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjbGljayAuYWZmLWFtYXpvbi1pbXBvcnQtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJzogJ3Nob3dBbGwnXG4gICAgfSxcblxuICAgIGluaXRpYWxpemU6IGZ1bmN0aW9uKCkge1xuICAgICAgICB0aGlzLm1vZGVsLm9uKCdjaGFuZ2UnLCB0aGlzLnJlbmRlcik7XG4gICAgICAgIHRoaXMucmVuZGVyKCk7XG4gICAgfSxcblxuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5zZXRFbGVtZW50KHRoaXMudGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG5cbiAgICBzaG93QWxsKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuYWZmLWFtYXpvbi1pbXBvcnQtcmVzdWx0cy1pdGVtLXZhcmlhbnRzLXNob3ctYWxsJykuaGlkZSgpO1xuICAgICAgICB0aGlzLiRlbC5maW5kKCcuJykucmVtb3ZlQ2xhc3MoJ2FmZi1hbWF6b24taW1wb3J0LXJlc3VsdHMtaXRlbS12YXJpYW50cy1pdGVtLWhpZGRlbicpO1xuICAgIH0sXG59KTtcbiIsImltcG9ydCBQcm9kdWN0VmlldyBmcm9tICcuL3Jlc3VsdCc7XG5cbmV4cG9ydCBkZWZhdWx0IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblxuICAgIGluaXRpYWxpemUob3B0aW9ucykge1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24gPSBvcHRpb25zLmNvbGxlY3Rpb247XG4gICAgICAgIHRoaXMucGFnZSA9IG9wdGlvbnMucGFnZTtcbiAgICAgICAgdGhpcy5yZXN1bHRzID0gdGhpcy4kZWwuZmluZCgnLmFmZi1hbWF6b24taW1wb3J0LXJlc3VsdHMnKTtcblxuICAgICAgICAvLyBFbnN1cmUgb3VyIG1ldGhvZHMga2VlcCB0aGUgYHRoaXNgIHJlZmVyZW5jZSB0byB0aGUgdmlldyBpdHNlbGZcbiAgICAgICAgXy5iaW5kQWxsKHRoaXMsICdzZWFyY2gnLCAncmVuZGVyJywgJ2FkZE9uZScpO1xuXG4gICAgICAgIC8vIEJpbmQgdGhlIGNvbGxlY3Rpb24gZXZlbnRzXG4gICAgICAgIC8qdGhpcy5jb2xsZWN0aW9uLmJpbmQoJ3Jlc2V0JywgdGhpcy5yZW5kZXIpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgnYWRkJywgdGhpcy5yZW5kZXIpO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uYmluZCgncmVtb3ZlJywgdGhpcy5yZW5kZXIpOyovXG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5iaW5kKCdzeW5jJywgdGhpcy5yZW5kZXIpO1xuXG4gICAgICAgIC8vIFRyaWdnZXIgdGhlIHNlYXJjaCBpZiB0aGUgdXNlciBjb21wbGV0ZXMgdHlwaW5nLlxuICAgICAgICAvL3RoaXMuc2VhcmNoSW5wdXQub24oJ2tleXVwJywgXy5kZWJvdW5jZSh0aGlzLnNlYXJjaCwgNDAwKSk7XG4gICAgfSxcblxuICAgIHJlbmRlcigpIHtcbiAgICAgICAgdGhpcy5hZGRBbGwoKTtcbiAgICB9LFxuXG4gICAgYWRkQWxsKCkge1xuICAgICAgICB0aGlzLnJlc3VsdHMuZW1wdHkoKTtcbiAgICAgICAgdGhpcy5jb2xsZWN0aW9uLmZvckVhY2godGhpcy5hZGRPbmUpO1xuICAgIH0sXG5cbiAgICBhZGRPbmUocHJvZHVjdCkge1xuICAgICAgICBsZXQgdmlldyA9IG5ldyBQcm9kdWN0Vmlldyh7XG4gICAgICAgICAgICBtb2RlbDogcHJvZHVjdCxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5yZXN1bHRzLmFwcGVuZCh2aWV3LnJlbmRlcigpLmVsKTtcbiAgICB9LFxuXG4gICAgc2VhcmNoKGUpIHtcbiAgICAgICAgaWYoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IHNlYXJjaCA9IHRoaXMuc2VhcmNoSW5wdXQudmFsKCk7XG4gICAgICAgIHRoaXMuY29sbGVjdGlvbi5zZWFyY2ggPSAoc2VhcmNoICYmIHNlYXJjaC5sZW5ndGggPiAwKSA/IHNlYXJjaCA6IGZhbHNlO1xuICAgICAgICB0aGlzLmNvbGxlY3Rpb24uZmV0Y2goe3JlbW92ZTogZmFsc2V9KS5kb25lKCgpID0+IHtcblxuICAgICAgICB9KTtcbiAgICB9LFxuXG59KTtcbiIsImxldCBzZWFyY2hGb3JtID0gIEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcbiAgICBlbDogJy5hZmYtYW1hem9uLWltcG9ydC1zZWFyY2gnLFxuXG4gICAgZXZlbnRzOiB7XG4gICAgICAgICdjaGFuZ2UnOiAnY2hhbmdlZCcsXG4gICAgICAgICdzdWJtaXQnOiAnc3VibWl0dGVkJ1xuICAgIH0sXG5cbiAgICByZW5kZXIoKSB7XG4gICAgICAgIGxldCBodG1sID0galF1ZXJ5KCcuYWZmLWFtYXpvbi1pbXBvcnQtc2VhcmNoLXRlbXBsYXRlJykuaHRtbCgpLFxuICAgICAgICAgICAgdGVtcGxhdGUgPSBfLnRlbXBsYXRlKGh0bWwpO1xuXG4gICAgICAgIHRoaXMuJGVsLmh0bWwodGVtcGxhdGUodGhpcy5tb2RlbC5hdHRyaWJ1dGVzKSk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcblxuICAgIGNoYW5nZWQoKSB7XG4gICAgICAgIGxldCB0ZXJtID0gdGhpcy4kZWwuZmluZCgnaW5wdXRbbmFtZT1cInRlcm1cIl0nKS52YWwoKSxcbiAgICAgICAgICAgIHR5cGUgPSB0aGlzLiRlbC5maW5kKCdzZWxlY3RbbmFtZT1cInR5cGVcIl0nKS52YWwoKSxcbiAgICAgICAgICAgIGNhdGVnb3J5ID0gdGhpcy4kZWwuZmluZCgnc2VsZWN0W25hbWU9XCJjYXRlZ29yeVwiXScpLnZhbCgpO1xuXG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCd0ZXJtJywgdGVybSk7XG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCd0eXBlJywgdHlwZSk7XG4gICAgICAgIHRoaXMubW9kZWwuc2V0KCdjYXRlZ29yeScsIGNhdGVnb3J5KTtcblxuICAgICAgICB0aGlzLnJlbmRlcigpO1xuICAgIH0sXG5cbiAgICBzdWJtaXR0ZWQoZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy50cmlnZ2VyKCdzZWFyY2gnLCBlKTtcbiAgICB9XG59KTtcblxuZXhwb3J0IGRlZmF1bHQgc2VhcmNoRm9ybTtcbiJdfQ==
