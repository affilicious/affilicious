<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Attribute\Domain\Model\AttributeGroup;
use Affilicious\Product\Domain\Model\DetailGroup\DetailGroup;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Type;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductVariant extends Product
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
     * @var AttributeGroup
     */
    protected $attributeGroup;

    /**
     * @since 0.6
     * @param Product $parent
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param AttributeGroup $attributeGroup
     */
    public function __construct(Product $parent, Title $title, Name $name, Key $key, AttributeGroup $attributeGroup)
    {
        parent::__construct($title, $name, $key, Type::variant());
        $this->parent = $parent;
        $this->default = false;
        $this->attributeGroup = $attributeGroup;
    }

    /**
     * Get the parent product
     *
     * @since 0.6
     * @return Product
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function hasContent()
    {
        return $this->parent->hasContent();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getContent()
    {
        return $this->parent->getContent();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setContent($content)
    {
        $this->parent->setContent($content);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function hasExcerpt()
    {
        return $this->parent->hasExcerpt();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getExcerpt()
    {
        return $this->parent->getExcerpt();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setExcerpt($excerpt)
    {
        $this->parent->setExcerpt($excerpt);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function hasDetailGroup(Name $name)
    {
        return $this->parent->hasDetailGroup($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function addDetailGroup(DetailGroup $detailGroup)
    {
        $this->parent->addDetailGroup($detailGroup);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function removeDetailGroup(Name $name)
    {
        $this->parent->removeDetailGroup($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getDetailGroup(Name $name)
    {
        return $this->getDetailGroup($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getDetailGroups()
    {
        return $this->parent->getDetailGroups();
    }

    /**
     * Get the attribute groups which stores the attributes like color or size.
     *
     * @since 0.6
     * @return AttributeGroup
     */
    public function getAttributeGroup()
    {
        return $this->attributeGroup;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function hasReview()
    {
        return $this->parent->hasReview();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getReview()
    {
        return $this->parent->getReview();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setReview($review)
    {
        $this->parent->setReview($review);
    }

    /**
     * Set true, if you want to set the variant as the default one
     *
     * @since 0.6
     * @param $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * Check if the variant is the default one for the parent product
     *
     * @since 0.6
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     *  @inheritdoc
     * @since 0.6
     */
    public function getRelatedProducts()
    {
        return $this->parent->getRelatedProducts();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setRelatedProducts($relatedProducts)
    {
        $this->parent->setRelatedProducts($relatedProducts);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getRelatedAccessories()
    {
        return $this->parent->getRelatedAccessories();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setRelatedAccessories($relatedAccessories)
    {
        return $this->parent->setRelatedProducts($relatedAccessories);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            parent::isEqualTo($object) &&
            $this->getName()->isEqualTo($object->getName());
    }
}
