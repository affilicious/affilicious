import Import from './model/import';
import ImportView from './view/import';

let importModel = new Import();
let importView = new ImportView({model: importModel});

importView.render();
