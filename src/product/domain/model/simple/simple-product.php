<?php
namespace Affilicious\Product\Domain\Model\Simple;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Abstract_Product;
use Affilicious\Product\Domain\Model\Type;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Simple_Product extends Abstract_Product implements Simple_Product_Interface
{
    /**
     * Stores the image gallery.
     *
     * @var Image[]
     */
    protected $image_gallery;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Title $title, Name $name, Key $key)
    {
        parent::__construct($title, $name, $key, Type::simple());
        $this->detail_groups = array();
        $this->related_products = array();
        $this->related_accessories = array();
        $this->image_gallery = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_image_gallery()
    {
        return $this->image_gallery;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_image_gallery($image_gallery)
    {
        foreach ($image_gallery as $image) {
            if (!($image instanceof Image)) {
                throw new Invalid_Type_Exception($image, 'Affilicious\Common\Domain\Model\Image\Image');
            }
        }

        $this->image_gallery = $image_gallery;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            parent::is_equal_to($object) &&
            $this->get_image_gallery() == $this->get_image_gallery();
    }
}
