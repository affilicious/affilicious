<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Attribute\Domain\Model\Attribute_Group;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail_Group;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Type;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Variant extends Product
{
    /**
     * @var Product
     */
    protected $parent;

    /**
     * True, if the variant is the default for the parent product
     *
     * @var bool
     */
    protected $default;

    /**
     * @var Attribute_group
     */
    protected $attribute_group;

    /**
     * @since 0.6
     * @param Product $parent
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Attribute_Group $attribute_group
     */
    public function __construct(Product $parent, Title $title, Name $name, Key $key, Attribute_Group $attribute_group)
    {
        parent::__construct($title, $name, $key, Type::variant());
        $this->parent = $parent;
        $this->default = false;
        $this->attribute_group = $attribute_group;
    }

    /**
     * Get the parent product
     *
     * @since 0.6
     * @return Product
     */
    public function get_parent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function has_content()
    {
        return $this->parent->has_content();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_content()
    {
        return $this->parent->get_content();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function set_content($content)
    {
        $this->parent->set_content($content);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function has_excerpt()
    {
        return $this->parent->has_excerpt();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_excerpt()
    {
        return $this->parent->get_excerpt();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function set_excerpt($excerpt)
    {
        $this->parent->set_excerpt($excerpt);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function has_detail_group(Name $name)
    {
        return $this->parent->has_detail_group($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function add_detail_group(Detail_Group $detail_group)
    {
        $this->parent->add_detail_group($detail_group);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function remove_detail_group(Name $name)
    {
        $this->parent->remove_detail_group($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_detail_group(Name $name)
    {
        return $this->get_detail_group($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_detail_groups()
    {
        return $this->parent->get_detail_groups();
    }

    /**
     * Get the attribute groups which stores the attributes like color or size.
     *
     * @since 0.6
     * @return Attribute_Group
     */
    public function get_attribute_group()
    {
        return $this->attribute_group;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function has_review()
    {
        return $this->parent->has_review();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_review()
    {
        return $this->parent->get_review();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function set_review($review)
    {
        $this->parent->set_review($review);
    }

    /**
     * Set true, if you want to set the variant as the default one
     *
     * @since 0.6
     * @param $default
     */
    public function set_default($default)
    {
        $this->default = $default;
    }

    /**
     * Check if the variant is the default one for the parent product
     *
     * @since 0.6
     * @return bool
     */
    public function is_default()
    {
        return $this->default;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_related_products()
    {
        return $this->parent->get_related_products();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function set_related_products($related_products)
    {
        $this->parent->set_related_products($related_products);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_related_accessories()
    {
        return $this->parent->get_related_accessories();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function set_related_accessories($related_accessories)
    {
        return $this->parent->set_related_products($related_accessories);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function is_equal_to($object)
    {
        return
            parent::is_equal_to($object) &&
            $this->get_name()->is_equal_to($object->get_name());
    }
}
