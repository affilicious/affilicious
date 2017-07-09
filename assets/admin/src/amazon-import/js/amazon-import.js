import ResultSearch from './model/resultSearch';
import ResultSearchView from './view/resultSearch';

import ResultSearchForm from './model/searchForm';
import ResultSearchFormView from './view/searchForm';

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

let resultSearchForm = new ResultSearchForm();
let resultSearchFormView = new ResultSearchFormView({
    model: resultSearchForm
});

resultSearchFormView.render();

let productSearch = new ResultSearch({page: 1});
let productSearchView = new ResultSearchView({
    collection: productSearch,
    el: '.aff-amazon-import'
});

productSearch.fetch();
productSearchView.render();
