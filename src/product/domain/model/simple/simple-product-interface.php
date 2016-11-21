<?php
namespace Affilicious\Product\Domain\Model\Simple;

use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Product_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Simple_Product_Interface extends Product_Interface
{
    /**
     * @since 0.7
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
    public function __construct(Title $title, Name $name, Key $key);

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
