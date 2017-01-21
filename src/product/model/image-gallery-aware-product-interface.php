<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Model\Image\Image;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Image_Gallery_Aware_Product_Interface extends Product_Interface
{
    /**
     * Get the IDs of the media attachments for the image gallery.
     *
     * @since 0.7
     * @return Image[]
     */
    public function get_image_gallery();

    /**
     * Set the IDs of the media attachments for the image gallery.
     * If you do this, the old images going to be replaced.
     *
     * @since 0.7
     * @param Image[] $image_gallery
     */
    public function set_image_gallery($image_gallery);
}
