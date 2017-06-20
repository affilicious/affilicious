import imageGalleryView from './views/image-gallery';
import tagsView from './views/tags';

window.carbon = window.carbon || {};

(function() {
    let carbon = window.carbon;
    if (typeof carbon.fields === 'undefined') {
        return false;
    }

    carbon.fields.View.ImageGallery = imageGalleryView;
    carbon.fields.View.Tags = tagsView;
}());
