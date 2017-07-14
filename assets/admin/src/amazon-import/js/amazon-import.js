import Search from './model/search';
import SearchView from './view/search';

let search = new Search();
let searchView = new SearchView({model: search});

searchView.render();

import Config from './model/config';
import ConfigView from './view/config';

let config = new Config();
let configView = new ConfigView({model: config});

configView.render();
