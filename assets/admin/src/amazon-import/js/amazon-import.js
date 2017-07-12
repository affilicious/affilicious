import Search from './model/search';
import SearchView from './view/search';

let search = new Search();
let searchView = new SearchView({model: search});

searchView.render();
