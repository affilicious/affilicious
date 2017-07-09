import Search from './model/search';
import SearchView from './view/search';

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

let search = new Search();
let searchView = new SearchView({
   model: search,
});

searchView.render();
