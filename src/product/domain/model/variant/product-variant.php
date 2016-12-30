<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Attribute\Domain\Model\Attribute_Group;
use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail_Group;
use Affilicious\Product\Domain\Model\Abstract_Product;
use Affilicious\Product\Domain\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Domain\Model\Tag;
use Affilicious\Product\Domain\Model\Type;
use Affilicious\Shop\Domain\Model\Affiliate_Link;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Variant extends Abstract_Product implements Product_Variant_Interface
{
    /**
     * @var Complex_Product_Interface
     */
    protected $parent;

    /**
     * Indicates if the variant is the default one for the parent complex product.
     *
     * @var bool
     */
    protected $default;

    /**
     * @var Attribute_group
     */
    protected $attribute_group;

    /**
     * @var Tag[]
     */
    protected $tags;

    /**
     * @since 0.7
     * @param Complex_Product_Interface $parent
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
    public function __construct(Complex_Product_Interface $parent, Title $title, Name $name, Key $key)
    {
        parent::__construct($title, $name, $key, Type::variant());
        $this->parent = $parent;
        $this->default = false;
        $this->tags = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_parent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_content()
    {
        return $this->parent->has_content();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_content()
    {
        return $this->parent->get_content();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_content($content)
    {
        $this->parent->set_content($content);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_excerpt()
    {
        return $this->parent->has_excerpt();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_excerpt()
    {
        return $this->parent->get_excerpt();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_excerpt($excerpt)
    {
        $this->parent->set_excerpt($excerpt);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_shop(Affiliate_Link $affiliate_link)
    {
        return isset($this->shops[$affiliate_link->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_shop(Shop_Interface $shop)
    {
        $this->shops[$shop->get_affiliate_link()->get_value()] = $shop;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_shop(Affiliate_Link $affiliate_link)
    {
        unset($this->shops[$affiliate_link->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_shop(Affiliate_Link $affiliate_link)
    {
        if(!$this->has_shop($affiliate_link)) {
            return null;
        }

        $shop = $this->shops[$affiliate_link->get_value()];

        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_cheapest_shop()
    {
        /** @var Shop_Interface $cheapest_shop */
        $cheapest_shop = null;
        foreach ($this->shops as $shop) {
            if ($cheapest_shop === null ||
                ($cheapest_shop->has_price() && $cheapest_shop->get_price()->is_greater_than($shop->has_price()))) {
                $cheapest_shop = $shop;
            }
        }

        return $cheapest_shop;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_shops()
    {
        $shops = array_values($this->shops);

        return $shops;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_shops($shops)
    {
        $this->shops = array();

        foreach ($shops as $shop) {
            $this->add_shop($shop);
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_detail_group(Name $name)
    {
        return $this->parent->has_detail_group($name);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_detail_group(Detail_Group $detail_group)
    {
        $this->parent->add_detail_group($detail_group);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_detail_group(Name $name)
    {
        $this->parent->remove_detail_group($name);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_detail_group(Name $name)
    {
        return $this->get_detail_group($name);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_detail_groups()
    {
        return $this->parent->get_detail_groups();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_attribute_group()
    {
        return $this->attribute_group !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_attribute_group()
    {
        return $this->attribute_group;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_attribute_group(Attribute_Group $attribute_group)
    {
        $this->attribute_group = $attribute_group;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_review()
    {
        return $this->parent->has_review();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_review()
    {
        return $this->parent->get_review();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_review($review)
    {
        $this->parent->set_review($review);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_default($default)
    {
        $this->default = $default;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_default()
    {
        return $this->default;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_related_products()
    {
        return $this->parent->get_related_products();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_related_products($related_products)
    {
        $this->parent->set_related_products($related_products);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_related_accessories()
    {
        return $this->parent->get_related_accessories();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_related_accessories($related_accessories)
    {
        return $this->parent->set_related_products($related_accessories);
    }

    /**
     * @inheritdoc
     * @since 0.7.1
     */
    public function has_tags()
    {
        return !empty($this->tags);
    }

    /**
     * @inheritdoc
     * @since 0.7.1
     */
    public function get_tags()
    {
        return $this->tags;
    }

    /**
     * @inheritdoc
     * @since 0.7.1
     */
    public function set_tags($tags)
    {
        $this->tags = $tags;
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
            $this->is_default() == $object->is_default();
    }
}
