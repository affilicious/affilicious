<?php
namespace Affilicious\Product\Domain\Model\Complex;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Simple\Simple_Product;
use Affilicious\Product\Domain\Model\Type;
use Affilicious\Product\Domain\Model\Variant\Product_Variant_Interface;
use Affilicious\Shop\Domain\Model\Affiliate_Link;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Complex_Product extends Simple_Product implements Complex_Product_Interface
{
    /**
     * Holds all product variants of the complex product.
     *
     * @var Product_Variant_Interface[]
     */
    protected $variants;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Title $title, Name $name, Key $key)
    {
        parent::__construct($title, $name, $key);
        $this->type = Type::complex();
        $this->variants = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_shop(Affiliate_Link $affiliate_link)
    {
        $default_variant = $this->get_default_variant();
        if($default_variant === null) {
            return false;
        }

        return $default_variant->has_shop($affiliate_link);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_shop(Shop_Interface $shop)
    {
        $default_variant = $this->get_default_variant();
        if($default_variant === null) {
            return;
        }

        $default_variant->add_shop($shop);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_shop(Affiliate_Link $affiliate_link)
    {
        $default_variant = $this->get_default_variant();
        if($default_variant === null) {
            return;
        }

        $default_variant->remove_shop($affiliate_link);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_shop(Affiliate_Link $affiliate_link)
    {
        $default_variant = $this->get_default_variant();
        if($default_variant === null) {
            return null;
        }

        return $default_variant->get_shop($affiliate_link);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_cheapest_shop()
    {
        $default_variant = $this->get_default_variant();
        if($default_variant === null) {
            return null;
        }

        return $default_variant->get_cheapest_shop();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_shops()
    {
        $default_variant = $this->get_default_variant();
        if($default_variant === null) {
            return array();
        }

        return $default_variant->get_shops();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_shops($shops)
    {
        $default_variant = $this->get_default_variant();
        if($default_variant === null) {
            return;
        }

        $default_variant->set_shops($shops);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_variant(Name $name)
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
    public function remove_variant(Name $name)
    {
        unset($this->variants[$name->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_variant(Name $name)
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

        // add_variant checks for the type
        foreach ($variants as $variant) {
            $this->add_variant($variant);
        }
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
            $this->get_variants() == $object->get_variants();
    }
}
