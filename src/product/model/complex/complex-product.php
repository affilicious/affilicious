<?php
namespace Affilicious\Product\Model\Complex;

use Affilicious\Common\Exception\Invalid_Type_Exception;
use Affilicious\Common\Model\Image\Image;
use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Detail\Model\Detail_Group;
use Affilicious\Product\Model\Abstract_Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Review\Review_Interface;
use Affilicious\Product\Model\Type;
use Affilicious\Product\Model\Variant\Product_Variant_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Complex_Product extends Abstract_Product implements Complex_Product_Interface
{
    /**
     * Holds all product variants of the complex product.
     *
     * @var Product_Variant_Interface[]
     */
    protected $variants;

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
    public function __construct(Name $title, Slug $name, Key $key)
    {
        parent::__construct($title, $name, $key, Type::complex());
        $this->type = Type::complex();
        $this->variants = array();
        $this->image_gallery = array();
        $this->detail_groups = array();
        $this->related_products = array();
        $this->related_accessories = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_variant(Slug $name)
    {
        return isset($this->variants[$name->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_variant(Product_Variant_Interface $variant)
    {
        $this->variants[$variant->get_name()->get_value()] = $variant;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_variant(Slug $name)
    {
        unset($this->variants[$name->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7.1
     */
    public function has_variants()
    {
        return !empty($this->variants);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_variant(Slug $name)
    {
        if(!$this->has_variant($name)) {
            return null;
        }

        $variant = $this->variants[$name->get_value()];

        return $variant;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_default_variant()
    {
        foreach ($this->variants as $variant) {
            if($variant->is_default()) {
                return $variant;
            }
        }

        // If there is no default, just take out the first one
        if(count($this->variants) > 0) {
            return reset($this->variants);
        }

        return null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_variants()
    {
        $variants = array_values($this->variants);

        return $variants;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_variants($variants)
    {
        $this->variants = array();

        foreach ($variants as $variant) {
            $this->add_variant($variant);
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_detail_group(Slug $name)
    {
        return isset($this->detail_groups[$name->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_detail_group(Detail_Group $detail_group)
    {
        $this->detail_groups[$detail_group->get_name()->get_value()] = $detail_group;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_detail_group(Slug $name)
    {
        unset($this->detail_groups[$name->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_detail_group(Slug $name)
    {
        if(!$this->has_detail_group($name)) {
            return null;
        }

        $detail_group = $this->detail_groups[$name->get_value()];

        return $detail_group;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_detail_groups()
    {
        $detail_groups = array_values($this->detail_groups);

        return $detail_groups;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_detail_groups($detail_groups)
    {
        $this->detail_groups = array();

        foreach ($detail_groups as $detail) {
            $this->add_detail_group($detail);
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_review()
    {
        return $this->review !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_review()
    {
        return $this->review;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_review($review)
    {
        if($review !== null && !($review instanceof Review_Interface)) {
            throw new Invalid_Type_Exception($review, Review_Interface::class);
        }

        $this->review = $review;
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
                throw new Invalid_Type_Exception($image, Image::class);
            }
        }

        $this->image_gallery = $image_gallery;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_related_products()
    {
        return $this->related_products;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_related_products($related_products)
    {
        foreach ($related_products as $related_product) {
            if (!($related_product instanceof Product_Id)) {
                throw new Invalid_Type_Exception($related_product, Product_Id::class);
            }
        }

        $this->related_products = $related_products;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_related_accessories()
    {
        return $this->related_accessories;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_related_accessories($related_accessories)
    {
        foreach ($related_accessories as $related_accessory) {
            if (!($related_accessory instanceof Product_Id)) {
                throw new Invalid_Type_Exception($related_accessory, Product_Id::class);
            }
        }

        $this->related_accessories = $related_accessories;
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
            $this->get_variants() == $object->get_variants() &&
            $this->get_review()->is_equal_to($object->get_review()) &&
            $this->get_related_products() == $object->get_related_products() &&
            $this->get_detail_groups() == $object->get_detail_groups() &&
            $this->get_related_accessories() == $object->get_related_accessories() &&
            $this->get_image_gallery() == $this->get_image_gallery();
    }
}
