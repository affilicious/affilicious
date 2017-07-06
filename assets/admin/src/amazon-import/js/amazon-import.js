import ResultSearch from './model/resultSearch';
import ResultSearchView from './view/resultSearch';

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

let productSearch = new ResultSearch({page: 1});
let productSearchView = new ResultSearchView({
    collection: productSearch,
    el: '.aff-amazon-import'
});

productSearch.fetch();
productSearchView.render();
